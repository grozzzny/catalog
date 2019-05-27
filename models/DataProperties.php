<?php


namespace grozzzny\catalog\models;


use Yii;
use yii\base\DynamicModel;
use yii\easyii2\helpers\Image;
use yii\easyii2\helpers\Upload;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class DataProperties
 * @package grozzzny\catalog\models
 *
 * @property-read array $all = [
 *      'attribute' => [
 *          'slug' => 'slug',
 *          'value' => 'null',
 *          'label' => 'string',
 *          'type' => 'string | integer | select | checkbox | html | category | multicategory | itemscategory | datetime | image | file | code',
 *          'settings' => [
 *              'group' => 'my_group',
 *              'scenarios' => 'user',
 *              'description' => '',
 *              'example_1' => '',
 *              'example_2' => '',
 *              'filter_show' => true,
 *              'filter_show_admin' => true,
 *              'characteristic' => true,
 *              'multiple' => true,
 *              'variations' => true,
 *          ],
 *          'options' => [
 *              'key' => 'value'
 *          ],
 *      ]
 * ]
 */
class DataProperties extends DynamicModel
{
    const PRIMARY_MODEL = false;

    private $_all = [];

    private $_labels = [];
    private $_types = [];
    private $_settings = [];
    private $_options = [];
    private $_scenarios = [];

    private $_insertedAttributesMulticategory = [];

    public function __construct(array $properties = [], array $data = [], $config = [])
    {
        $attributes = [];
        $values = [];

        foreach ($data as $data_property){

            $value = $data_property->value;

            if($data_property->property->type == Properties::TYPE_MULTICATEGORY){

                if(empty($old_category_id[$data_property->property_slug])){
                    $old_category_id[$data_property->property_slug] = $value;
                    $values[$data_property->property_slug][] = $value;
                }

                if($value > $old_category_id[$data_property->property_slug]) {
                    $values[$data_property->property_slug][] = $value;
                }

                $old_category_id[$data_property->property_slug] = $value;

            }elseif($data_property->property->settings->multiple){
                $values[$data_property->property_slug][] = $value;
            }else{
                $values[$data_property->property_slug] = $value;
            }
        }

        foreach ($properties as $property){
            $val = $property->settings->multiple ? [] : null;

            if (!empty(ArrayHelper::getValue($values, $property->slug, null))) $val = $values[$property->slug];

            $attributes[$property->slug] = $val;

            if (!empty($property->settings->scenarios)){
                foreach(explode(' ', $property->settings->scenarios) as $scenario){
                    $this->_scenarios[$scenario][] = $property->slug;
                }
            }

            $this->_labels[$property->slug] = $property->title;
            $this->_types[$property->slug] = $property->type;
            $this->_settings[$property->slug] = $property->settings;
            $this->_options[$property->slug] = $property->options;

            $this->_all[$property->slug] = [
                'slug' => $property->slug,
                'value' => $val,
                'label' => $property->title,
                'type' => $property->type,
                'settings' => (array)$property->settings,
                'options' => $property->options,
            ];

            foreach ($property->validations as $validation){
                $validator = $validation[0];
                $params = ArrayHelper::getValue($validation, '1', []);
                $this->addRule($property->slug, $validator, $params);
            }

        }

        parent::__construct($attributes, $config);
    }

    public function getAll()
    {
        if(!empty($this->_all)) return $this->_all;

        return $this->_all;
    }

    public function getType($slug)
    {
        return $this->_types[$slug];
    }

    public function getSettings($slug)
    {
        return $this->_settings[$slug];
    }

    public function getOptions($slug)
    {
        return $this->_options[$slug];
    }

    public function getParseValue($slug)
    {
        if (empty($this->$slug)) return null;

        $values = is_array($this->$slug) ? $this->$slug : [$this->$slug];

        switch ($this->getType($slug)){
            case Properties::TYPE_STRING:
            case Properties::TYPE_INTEGER:
            case Properties::TYPE_IMAGE:
            case Properties::TYPE_FILE:
                 return $values;

            case Properties::TYPE_SELECT:
                $options = ArrayHelper::toArray($this->getOptions($slug));
                $values_arr = [];
                foreach ($values as $value){
                    $values_arr[$value] = ArrayHelper::getValue($options, $value, '');
                }
                return $values_arr;

            case Properties::TYPE_CHECKBOX:
                return $this->$slug == true ? Yii::t('app', 'yes') : Yii::t('app', 'no');

            case Properties::TYPE_ITEMSCATEGORY:
                $classItem = static::getClassItem();
                return ArrayHelper::getColumn($classItem::findAll(['id' => $this->$slug]), 'title');

            case Properties::TYPE_MULTICATEGORY:
            case Properties::TYPE_CATEGORY:
                $classCategory = static::getClassCategory();
                return ArrayHelper::getColumn($classCategory::findAll(['id' => $this->$slug]), 'fullTitle');

            case Properties::TYPE_DATETIME:
                return date('d.m.Y', $this->$slug);
            //default:
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return $this->_labels;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios = ArrayHelper::merge($scenarios, $this->_scenarios);
        return $scenarios;
    }

    public function __set($name, $value)
    {
        if ($this->getType($name) == Properties::TYPE_MULTICATEGORY){
            $this->_insertedAttributesMulticategory[] = $name;
            $value = $this->parseValueMulticategory($value);
        }

        parent::__set($name, $value); // TODO: Change the autogenerated stub
    }

//    public function __get($name)
//    {
//        //Если получаем значение мультикатегорий, то проверить.. была ли вставка этих значений, или нет
//        if ($this->getType($name) == Properties::TYPE_MULTICATEGORY && !in_array($name, $this->_insertedAttributesMulticategory)) {
//            return $this->parseValueMulticategory(parent::__get($name));
//        }
//
//        return parent::__get($name); // TODO: Change the autogenerated stub
//    }

    public static function parseValueMulticategory($value)
    {
        $values = (is_array($value)) ? $value : [$value];
        $categories_arr = [];
        $classCategory = static::getClassCategory();
        foreach ($values as $val) {
            $categories_arr = ArrayHelper::merge($categories_arr, [$val] + $classCategory::getOnlyParentId($val));
        }
        return $categories_arr;
    }

    public function getAttributesForSave($item_id)
    {
        $new_attributes = [];
        $attributes = $this->getAttributes();

        foreach ($attributes as $slug => $values)
        {
            if ($this->getType($slug) == Properties::TYPE_MULTICATEGORY){
                if($this->hasSetValueMulticategory($slug)){
                    $new_attributes[$slug] = $values;
                } else {
                    $classData = static::getClassData();
                    $values = ArrayHelper::getColumn($classData::findAll(['item_id' => $item_id, 'property_slug' => $slug]), 'value');
                    $new_attributes[$slug] = $values;
                }
            }else {
                $new_attributes[$slug] = $values;
            }
        }
        return $new_attributes;
    }

    private function hasSetValueMulticategory($slug)
    {
        return in_array($slug, $this->_insertedAttributesMulticategory);
    }


    /**
     * Сохранение значений в таблицу "Data"
     * @return bool
     */
    public function saveData($item)
    {
        $attributes = $item->dataProperties->getAttributesForSave($item->id);
        if(empty($attributes)) return false;

        $classData = static::getClassData();

        foreach ($attributes as $slug => $values)
        {

            $classData::deleteAllData($item->id, $slug);
            $values = (is_array($values)) ? $values : [$values];

            foreach ($values as $value){

                $value = (is_array($value)) ? array_values($value)[0] : $value;

                static::saveDataModel($value, $slug, $item->id);
            }
        }
    }

    public static function saveDataModel($value, $slug, $id)
    {
        $classData = static::getClassData();

        $data = new $classData([
            'value' => $value,
            'property_slug' => $slug,
            'item_id' => $id,
        ]);

        if(!empty($value)) return $data->save();

        return false;
    }

    /**
     * @return Data | string
     */
    public static function getClassData()
    {
        return Data::className();
    }

    /**
     * @return Category | string
     */
    public static function getClassCategory()
    {
        return Category::className();
    }

    /**
     * @return Item | string
     */
    public static function getClassItem()
    {
        return Item::className();
    }

//    public static function reParseValueMulticategory($values)
//    {
//        $values = is_array($values) ? $values : [$values];
//
//        $new_values = [];
//
//        $old_value = null;
//        foreach ($values as $value){
//            if($old_value != null && $value > $old_value) {
//                $new_values[] = $old_value;
//            }
//            $old_value = $value;
//        }
//
////        echo '<pre>';
////        print_r($new_values);
////        echo '</pre>';
//
////        $old_category_id[$data_property->property_slug] = $value;
////
////        return $value;
//    }

}