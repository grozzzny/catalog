<?php
namespace grozzzny\catalog\models;


use grozzzny\catalog\controllers\TraitController;
use yii\behaviors\BlameableBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\easyii\helpers\Image;
use yii\easyii\helpers\Upload;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class Item extends Base
{
    const PRIMARY_MODEL = true;

    const CACHE_KEY = 'gr_catalog_items';

    const TITLE = 'Elements';
    const SLUG = 'item';

    const SUBMENU_PHOTOS = true;
    const SUBMENU_FILES = false;
    const ORDER_NUM = false;

    private $_data = [];
    private $_categories = [];

    private $_dataProperties = null;

    public function behaviors()
    {
        $behaviors = parent::behaviors(); // TODO: Change the autogenerated stub

        $behaviors[] = BlameableBehavior::className();
        $behaviors[] = TimestampBehavior::className();

        return $behaviors;
    }


    public static function tableName()
    {
        return 'gr_catalog_items';
    }

    public function rules()
    {
        return [
            ['id', 'number', 'integerOnly' => true],
            ['slug', 'match', 'pattern' => '/^[\w\-]+$/'],
            ['slug', 'unique'],
            [[
                'title',
                'short',
            ], 'string'],
            [[
                'price',
                'discount',
                'user_id',
            ], 'integer'],
            ['image_file', 'image'],
            [['description', 'categories'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_ON],
            [['order_num'], 'integer'],
            [['title', 'slug'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gr', 'ID'),
            'slug' => Yii::t('gr', 'Slug'),
            'title' => Yii::t('gr', 'Title'),
            'image_file' => Yii::t('gr', 'Image'),
            'views' => Yii::t('gr', 'Count Views'),
            'short' => Yii::t('gr', 'Short text'),
            'description' => Yii::t('gr', 'Description'),
            'status' => Yii::t('gr', 'Status'),
            'order_num' => Yii::t('gr', 'Sort Index'),
            'price' => Yii::t('gr', 'Price'),
            'discount' => Yii::t('gr', 'Discount'),
            'created_time' => Yii::t('gr', 'Date created'),
            'updated_time' => Yii::t('gr', 'Date updated'),
            'user_id' => Yii::t('gr', 'User'),
            'categories' => Yii::t('gr', 'Categories'),
        ];
    }


    public function load($data, $formName = null)
    {
        $this->_data = $data[(new DataProperties())->formName()];
        if(!empty($this->_data)) $this->dataProperties->setAttributes($this->_data);
        return parent::load($data, $formName); // TODO: Change the autogenerated stub
    }


    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])
            ->viaTable('gr_catalog_relations_categories_items', ['item_id' => 'id']);
    }

    public function setCategories($value)
    {
        if (!empty($value)) {
            //Найдем все id родительских категорий
            $arr_id = [];
            foreach ($value as $category_id) {
                $arr_id = ArrayHelper::merge($arr_id, Category::findOne($category_id)->allParentId);
            }
            $value = array_unique($arr_id);
        } else {
            $value = [];
        }

        $this->_categories = $value;
        return $this->categories = $value;
    }


    public function getData()
    {
        return $this->hasMany(Data::className(), ['item_id' => 'id']);
    }


    public function setData($value)
    {
        return $this->_data = $value;
    }

    /**
     * Расширенная валидация. + валидация доп. значений
     */
    public function afterValidate()
    {
        parent::afterValidate(); // TODO: Change the autogenerated stub

        $this->dataProperties->validate();

        $this->addErrors($this->dataProperties->getErrors());

    }

    /**
     * Возвратит список свойств
     * @return array
     */
    public function getProperties()
    {
        $categories = (!empty($this->_categories)) ? Category::find()->where(['IN', 'id', $this->_categories])->orderBy(['parent_id' => SORT_ASC])->all() : $this->categories;
        $properties = [];
        foreach ($categories as $category){
            $properties = ArrayHelper::merge($properties, $category->properties);
        }
        return $properties;
    }

    /**
     * Возвратит динамическую модель со значениями
     * @return DataProperties|null
     */
    public function getDataProperties()
    {
        $this->_dataProperties = (empty($this->_dataProperties)) ? new DataProperties($this->properties, $this->data) : $this->_dataProperties;
        return $this->_dataProperties;
    }


    /**
     * После сохранения элемента, установить связи с категориями и сохранить значение в таблицу "Data"
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        //Сохранение связи в релативную таблицу
        $this->saveDataRelationsTable('gr_catalog_relations_categories_items', ['item_id' => $this->id], ['category_id' => $this->categories]);

        //Сохранение значений в таблицу "Data"
        $this->saveData();
    }


    /**
     * Сохранение значений в таблицу "Data"
     * @return bool
     */
    private function saveData()
    {
        if(empty($this->_data)) return false;

        foreach ($this->_data as $slug => $values)
        {
            Data::deleteAll(['item_id' => $this->id, 'property_slug' => $slug]);
            $values = (is_array($values)) ? $values : [$values];

            foreach ($values as $value){

                $value = (is_array($value)) ? array_values($value)[0] : $value;

                $data = new Data();
                $data->value = $value;
                $data->property_slug = $slug;
                $data->item_id = $this->id;
                if(!empty($value)) $data->save();
            }
        }
    }


    /**
     * Параметры фильтра
     * @param $query
     * @param $get
     */
    public static function queryFilter(&$query, $get)
    {
        $query->joinWith('categories');

        if(!empty($get['text'])){
            $query->andFilterWhere(['LIKE', 'gr_catalog_items.title', $get['text']]);
        }

        if(!empty($get['category'])){
            $query->andFilterWhere(['gr_catalog_categories.id' => $get['category']]);
        }
    }


    /**
     * Параметры сортировки
     * @param $provider
     */
    public static function querySort(&$provider)
    {
        $sort = ['defaultOrder' => ['id' => SORT_DESC]];

        $attributes = [
            'id',
            'status',
            'title',
            'slug',
        ];

        if(self::ORDER_NUM){
            $sort = $sort + ['defaultOrder' => ['order_num' => SORT_DESC]];
            $attributes = $attributes + ['order_num'];
        }

        $sort = $sort + ['attributes' => $attributes];

        $provider->setSort($sort);
    }

}
