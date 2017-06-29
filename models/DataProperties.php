<?php


namespace grozzzny\catalog\models;


use yii\base\DynamicModel;
use yii\easyii\helpers\Image;
use yii\easyii\helpers\Upload;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class DataProperties extends DynamicModel
{
    const PRIMARY_MODEL = false;

    private $labels = [];
    private $types = [];
    private $settings = [];
    private $options = [];

    public function __construct(array $properties = [], array $data = [], $config = [])
    {
        $attributes = [];
        $values = [];

        foreach ($data as $data_property){
            if($data_property->property->settings->multiple){
                $values[$data_property->property_slug][] = $data_property->value;
            }else{
                $values[$data_property->property_slug] = $data_property->value;
            }
        }

        foreach ($properties as $property){
            $attributes[$property->slug] = $property->settings->multiple ? [] : null;

            if (!empty(ArrayHelper::getValue($values, $property->slug, null))) $attributes[$property->slug] = $values[$property->slug];

            $this->labels[$property->slug] = $property->title;
            $this->types[$property->slug] = $property->type;
            $this->settings[$property->slug] = $property->settings;
            $this->options[$property->slug] = $property->options;

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
        return $this->types[$slug];
    }

    public function getSettings($slug)
    {
        return $this->settings[$slug];
    }

    public function getOptions($slug)
    {
        return $this->options[$slug];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return $this->labels;
    }

}