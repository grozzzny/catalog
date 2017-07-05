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


    /**
     * ДИНАМИЧЕСКАЯ МОДЕЛЬ С ДАННЫМИ
     * Возвратит динамическую модель со значениями
     * @return DataProperties|null
     */
    public function getDataProperties()
    {
        $this->_dataProperties = (empty($this->_dataProperties)) ? new DataProperties($this->properties, $this->data) : $this->_dataProperties;
        return $this->_dataProperties;
    }


    /**
     * СВОЙСТВА, НЕОБХОДИМЫЕ ДЛЯ ПОСТРОЕНИЯ ДИНАМИЧЕСКОЙ МОДЕЛИ
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
        $ret = parent::load($data, $formName); // TODO: Change the autogenerated stub

        $_data = $data[(new DataProperties())->formName()];
        if(!empty($_data)) $this->dataProperties->setAttributes($_data);

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
        $attributes = $this->dataProperties->getAttributes();
        if(empty($attributes)) return false;

        foreach ($attributes as $slug => $values)
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
     * КОНЕЦ СОХРАНЕНИЯ ДАННЫХ
     */








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

    /**
     * Параметры фильтра
     * @param $query
     * @param $get
     */
    public function queryFilter(&$query, $get)
    {
        $query->distinct('gr_catalog_items.id');

        $query->joinWith('categories');

        if(!empty($get['text'])){
            $query->andFilterWhere(['LIKE', 'gr_catalog_items.title', $get['text']]);
        }

        if(!empty($get['category'])){
            $query->andFilterWhere(['gr_catalog_categories.id' => $get['category']]);

            $this->categories = [$get['category']];

            $filtersApplied = 0;
            $subQuery = Data::find()->select('item_id, COUNT(*) as filter_matched')->groupBy('item_id');

            foreach ($this->properties as $property){

                if ($property->settings->filter_range){

                    $value_from = ArrayHelper::getValue($get, $property->slug . '_from', '');
                    $value_to = ArrayHelper::getValue($get, $property->slug . '_to', '');

                    if(empty($value_from) && empty($value_to)) continue;

                    if(!$value_from){
                        $additionalCondition = ['<=', 'value', (int)$value_to];
                    } elseif(!$value_to) {
                        $additionalCondition = ['>=', 'value', (int)$value_from];
                    } else {
                        $additionalCondition = ['between', 'value', (int)$value_from, (int)$value_to];
                    }

                    $subQuery->orFilterWhere(['and', ['property_slug' => $property->slug], $additionalCondition]);

                }else{
                    $value = ArrayHelper::getValue($get, $property->slug, '');

                    if(empty($value)) continue;

                    switch ($property->type){
                        case Properties::TYPE_DATETIME:
                            $subQuery->orFilterWhere(['and', ['property_slug' => $property->slug], ['=', 'FROM_UNIXTIME(`value`,\'%Y-%m-%d\')', date('Y-m-d',$value)]]);
                            break;
                        case Properties::TYPE_CHECKBOX:
                        case Properties::TYPE_FILE:
                        case Properties::TYPE_IMAGE:
                            $subQuery->orFilterWhere(['and', ['property_slug' => $property->slug], ['not', ['value' => null]]]);
                            break;
                        default:
                            $subQuery->orFilterWhere(['and', ['property_slug' => $property->slug], ['value' => $value]]);
                    }
                }
                $filtersApplied++;
            }

            if($filtersApplied) {
                $query->join('LEFT JOIN', ['f' => $subQuery], 'f.item_id = gr_catalog_items.id');
                $query->andFilterWhere(['f.filter_matched' => $filtersApplied]);
            }
        }
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

        if(self::ORDER_NUM){
            $sort = $sort + ['defaultOrder' => ['order_num' => SORT_DESC]];
            $attributes = $attributes + ['order_num'];
        }

        $sort = $sort + ['attributes' => $attributes];

        $provider->setSort($sort);
    }

}
