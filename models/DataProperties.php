<?php


namespace grozzzny\catalog\models;


use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;

class DataProperties extends DynamicModel
{
    const PRIMARY_MODEL = false;

    private $labels = [];

    public function __construct(array $properties = [], array $data = [], $config = [])
    {
        $attributes = [];
        $labels = [];

        $values = [];
        foreach ($data as $data_property){
            if($data_property->property->settings->multiple){
                $values[$data_property->property_slug][] = (!empty($data_property->key)) ? $data_property->key : $data_property->value;
            }else{
                $values[$data_property->property_slug] = (!empty($data_property->key)) ? $data_property->key : $data_property->value;
            }
        }

        foreach ($properties as $property){
            $attributes[$property->slug] = $property->settings->multiple ? [] : null;

            if (!empty(ArrayHelper::getValue($values, $property->slug, null))) $attributes[$property->slug] = $values[$property->slug];

            $labels[$property->slug] = $property->title;

            foreach ($property->validations as $validation){
                $validator = $validation[0];
                $params = ArrayHelper::getValue($validation, '1', []);
                $this->addRule($property->slug, $validator, $params);
            }

        }
        $this->labels = $labels;

        parent::__construct($attributes, $config);
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return $this->labels;
    }
}