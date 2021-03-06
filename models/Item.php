<?php
namespace grozzzny\catalog\models;


use grozzzny\admin\helpers\Image;
use grozzzny\admin\widgets\file_input\components\FileBehavior;
use grozzzny\catalog\components\ItemCategoriesBehavior;
use grozzzny\catalog\components\ItemParentCategory;
use grozzzny\catalog\components\ItemQuery;
use yii\behaviors\BlameableBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;


/**
 * Item ActiveRecord model.
 *
 * Database fields:
 * @property integer $id
 * @property string  $slug
 * @property string  $image_file
 * @property string  $parent_category_slug
 * @property integer $title
 * @property string  $short
 * @property string  $description
 * @property string  $price
 * @property string  $discount
 * @property integer $views
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $user_id
 * @property integer $status
 * @property integer $order_num
 *
 * Defined relations:
 * @property-read DataProperties $dataProperties
 * @property-read Properties[]   $properties
 * @property Category[]          $categories
 * @property-read Data[]         $data
 * @property-read ActiveRecord   $updatedBy
 * @property-read ActiveRecord   $createdBy
 * @property-read string         $categoriesToString
 * @property-read Category       $mainCategory
 * @property-read Category       $parentCategory
 *
 */
class Item extends ActiveRecord
{
    const STATUS_ON = true;
    use TraitModel;

    const PRIMARY_MODEL = true;

    const TITLE = 'Elements';
    const SLUG = 'item';

    const EVENT_ITEM_AFTER_INSERT = 'item_after_insert';
    const EVENT_ITEM_AFTER_UPDATE = 'item_after_update';

    private $_categories = [];

    private $_dataProperties = null;

    private $_photos;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'blameable' => BlameableBehavior::className(),
            'timestamp' => TimestampBehavior::className(),
            'itemCategories' => ItemCategoriesBehavior::className(),
            'itemParentCategory' => ItemParentCategory::className(),
            'image' => [
                'class' => FileBehavior::className(),
                'fileAttribute' => 'image_file',
                'uploadPath' => '/uploads/categories',
            ],
        ]);
    }

    public static function tableName()
    {
        return 'gr_catalog_items';
    }

    public function rules()
    {
        return [
            'id' => ['id', 'number', 'integerOnly' => true],
            'slug_pattern' => ['slug', 'match', 'pattern' => '/^[\w\-]+$/'],
            'slug_unique' => ['slug', 'unique'],
            'string' => [[
                'title',
                'short',
                'parent_category_slug',
            ], 'string'],
            'integer' => [[
                'price',
                'discount',
                'user_id',
                'order_num',
            ], 'integer'],
            'image_file' => ['image_file', 'image'],
            'safe' => [['description', 'categories'], 'safe'],
            'default' => [['views', 'order_num'],'default', 'value' => 0],
            'default_status' => ['status', 'default', 'value' => self::STATUS_ON],
            'required' => [['title', 'slug'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('catalog', 'ID'),
            'slug' => Yii::t('catalog', 'Slug'),
            'title' => Yii::t('catalog', 'Title'),
            'image_file' => Yii::t('catalog', 'Image'),
            'views' => Yii::t('catalog', 'Count Views'),
            'short' => Yii::t('catalog', 'Short text'),
            'description' => Yii::t('catalog', 'Description'),
            'status' => Yii::t('catalog', 'Status'),
            'order_num' => Yii::t('catalog', 'Sort Index'),
            'price' => Yii::t('catalog', 'Price'),
            'discount' => Yii::t('catalog', 'Discount'),
            'created_time' => Yii::t('catalog', 'Date created'),
            'updated_time' => Yii::t('catalog', 'Date updated'),
            'user_id' => Yii::t('catalog', 'User'),
            'categories' => Yii::t('catalog', 'Categories'),
            'parent_category_slug' => Yii::t('catalog', 'Category'),
        ];
    }

    public function getParentCategory()
    {
        return $this->hasOne(static::modelCategory(), ['slug' => 'parent_category_slug']);
    }


    /**
     * ДИНАМИЧЕСКАЯ МОДЕЛЬ С ДАННЫМИ
     * Возвратит динамическую модель со значениями
     * @return DataProperties|null
     */
    public function getDataProperties()
    {
        $modelDataProperties = static::modelDataProperties();
        $this->_dataProperties = (empty($this->_dataProperties)) ? new $modelDataProperties($this->properties, $this->data) : $this->_dataProperties;
        return $this->_dataProperties;
    }


    /**
     * СВОЙСТВА, НЕОБХОДИМЫЕ ДЛЯ ПОСТРОЕНИЯ ДИНАМИЧЕСКОЙ МОДЕЛИ
     * Возвратит список свойств
     * @return array
     */
    public function getProperties()
    {
        $modelCategory = static::modelCategory();
        $categories = (!empty($this->_categories)) ? $modelCategory::find()->where(['IN', 'id', $this->_categories])->orderBy(['parent_id' => SORT_ASC])->all() : $this->categories;
        $properties = [];
        foreach ($categories as $category){
            $properties = ArrayHelper::merge($properties, $category->properties);
        }
        return $properties;
    }

    /**
     * НАЧАЛО СОХРАНЕНИЯ ДАННЫХ
     */

    /**
     * 1 ШАГ. ЗАГРУЖАЕМ ДВЕ МОДЕЛИ (ОСНОВНУЮ И МОДЕЛЬ С ДАННЫМИ)
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        $modelDataProperties = static::modelDataProperties();
        $ret = parent::load($data, $formName); // TODO: Change the autogenerated stub
        $scope = $formName === null ? (new $modelDataProperties())->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $this->dataProperties->setAttributes($data);
        } elseif (isset($data[$scope])) {
            $_data = $data[$scope];
            if(!empty($_data)) $this->dataProperties->setAttributes($_data);
        }
        return $ret;
    }


    /**
     * 2 ШАГ. ВАЛИДИРУЕМ МОДЕЛЬ С ДАННЫМИ
     * Расширенная валидация. + валидация доп. значений
     */
    public function afterValidate()
    {
        parent::afterValidate(); // TODO: Change the autogenerated stub

        $this->dataProperties->validate();

        $this->addErrors($this->dataProperties->getErrors());
    }


    /**
     * 3 ШАГ. ПОСЛЕ СОХРАНЕНИЯ ОСНОВНОЙ МОДЕЛИ, ПРИСТУПАЕМ К СОХРАНЕНИЮ МОДЕЛИ С ДАННЫМИ
     * После сохранения элемента, установить связи с категориями и сохранить значение в таблицу "Data"
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        //Сохранение связи в релативную таблицу
        if(!empty($this->_categories)){
            $modelRelationsCategoriesItems = static::modelRelationsCategoriesItems();
            $this->saveDataRelationsTable( $modelRelationsCategoriesItems::tableName(), ['item_id' => $this->id], ['category_id' => $this->categories]);
        }

        //Сохранение значений в таблицу "Data"
        $this->dataProperties->saveData($this);

        $this->trigger($insert ? self::EVENT_ITEM_AFTER_INSERT : self::EVENT_ITEM_AFTER_UPDATE);
    }

    /**
     * КОНЕЦ СОХРАНЕНИЯ ДАННЫХ
     */



    /** relation models */
    public function modelRelationsCategoriesItems()
    {
        $model = Yii::createObject(['class' => RelationsCategoriesItems::class]);
        return $model::className();
    }

    public function modelRelationsCategoriesProperties()
    {
        $model = Yii::createObject(['class' => RelationsCategoriesProperties::class]);
        return $model::className();
    }

    public function modelProperties()
    {
        $model = Yii::createObject(['class' => Properties::class]);
        return $model::className();
    }

    public function modelCategory()
    {
        $model = Yii::createObject(['class' => Category::class]);
        return $model::className();
    }

    public function modelDataProperties()
    {
        $model = Yii::createObject(['class' => DataProperties::class]);
        return $model::className();
    }

    public function modelData()
    {
        $model = Yii::createObject(['class' => Data::class]);
        return $model::className();
    }
    /** end */


    public function setScenario($value)
    {
        if(array_key_exists($value, $this->dataProperties->scenarios())) $this->dataProperties->setScenario($value);
        parent::setScenario($value); // TODO: Change the autogenerated stub
    }


    public function getRelationsCategoriesItems()
    {
        return $this->hasMany($this->modelRelationsCategoriesItems(), ['item_id' => 'id']);
    }

    public function getCategories()
    {
        return $this->hasMany($this->modelCategory(), ['id' => 'category_id'])
            ->via('relationsCategoriesItems');
    }

    public function setCategories($value)
    {
        if (!empty($value)) {
            //Найдем все id родительских категорий
            $arr_id = [];
            foreach ($value as $category_id) {
                $arr_id = ArrayHelper::merge($arr_id, $this->getCategoryById($category_id)->allParentId);
            }
            $value = array_unique($arr_id);
        } else {
            $value = [];
        }

        $this->_categories = $value;
        return $this->categories = $value;
    }


    public function getCategoryById($category_id)
    {
        $modelCategory = $this->modelCategory();
        return $modelCategory::findOne($category_id);
    }


    public function getData()
    {
        $modelData = $this->modelData();
        return $this->hasMany($modelData, ['item_id' => 'id']);
    }


    public function getCreatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'created_by']);
    }


    public function getUpdatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'updated_by']);
    }


    /**
     * Параметры фильтра
     * @param ItemQuery $query
     * @param $get
     */
    public static function queryFilter(ItemQuery &$query, array $get)
    {
        $query->whereSearch($get['search_text']);

        $query->whereRange(['price' => [ArrayHelper::getValue($get, 'price_from', ''), ArrayHelper::getValue($get, 'price_to', '')]]);

        if(empty($query->_category) && !empty($get['category'])) $query->category(Category::findOne($get['category']));

        $query->whereProperties($get);

    }


    public function getImage($width = null, $height = null, $crop = true)
    {
        $image = empty($this->image_file) ? Yii::$app->params['nophoto'] : $this->image_file;
        return Image::thumb($image, $width, $height, $crop);
    }


    /**
     * Параметры сортировки
     * @param $provider
     */
    public function querySort(&$provider)
    {
        $sort = ['defaultOrder' => ['id' => SORT_DESC]];

        $attributes = [
            'id',
            'status',
            'title',
            'slug',
        ];

        $sort = $sort + ['attributes' => $attributes];

        $provider->setSort($sort);
    }


    /**
     * @inheritdoc
     * @return ItemQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(ItemQuery::className(), [get_called_class()]);
    }

    /**
     * Увеличивает показатель просмотров
     */
    public function addViews()
    {
        self::updateAll(['views' => $this->views + 1], ['id' => $this->id]);
    }

    public function getCategoriesToString()
    {
        return implode(', ', ArrayHelper::getColumn($this->getCategories()->all(), 'title'));
    }

    public function getMainCategory ()
    {
        return $this->getCategories()->limit(1)->one();
    }
}
