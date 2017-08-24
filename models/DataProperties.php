<?php


namespace grozzzny\catalog\models;


use Yii;
use yii\base\DynamicModel;
use yii\easyii\helpers\Image;
use yii\easyii\helpers\Upload;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class DataProperties extends DynamicModel
{
    const PRIMARY_MODEL = false;

    private $_labels = [];
    private $_types = [];
    private $_settings = [];
    private $_options = [];
    private $_scenarios = [];

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
            $attributes[$property->slug] = $property->settings->multiple ? [] : null;

            if (!empty(ArrayHelper::getValue($values, $property->slug, null))) $attributes[$property->slug] = $values[$property->slug];

            if (!empty($property->settings->scenario)) $this->_scenarios[$property->settings->scenario][] = $property->slug;

            $this->_labels[$property->slug] = $property->title;
            $this->_types[$property->slug] = $property->type;
            $this->_settings[$property->slug] = $property->settings;
            $this->_options[$property->slug] = $property->options;

            foreach ($property->validations as $validation){
                $validator = $validation[0];
                $params = ArrayHelper::getValue($validation, '1', []);
                $this->addRule($property->slug, $validator, $params);
            }

        }

        parent::__construct($attributes, $config);
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
                return ArrayHelper::getColumn(Item::findAll(['id' => $this->$slug]), 'title');

            case Properties::TYPE_MULTICATEGORY:
            case Properties::TYPE_CATEGORY:
                return ArrayHelper::getColumn(Category::findAll(['id' => $this->$slug]), 'fullTitle');

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

    public function setAttributes($values, $safeOnly = true)
    {
        $new_values = [];
        if (is_array($values)) {
            foreach ($values as $name => $value) {
                if($this->getType($name) == Properties::TYPE_MULTICATEGORY) {
                    $values = (is_array($value)) ? $value : [$value];
                    $categories_arr = [];
                    foreach ($values as $val) {
                        $categories_arr = ArrayHelper::merge($categories_arr, [$val] + Category::getOnlyParentId($val));
                    }
                    $new_values[$name] = $categories_arr;
                }else{
                    $new_values[$name] = $value;
                }
            }
        }
        parent::setAttributes($new_values, $safeOnly); // TODO: Change the autogenerated stub
    }
}