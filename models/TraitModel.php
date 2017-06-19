<?php


namespace grozzzny\catalog\models;

use Yii;

trait TraitModel
{

    /**
     * Проверяет, имеется ли данный валидатор у атрибута или нет
     * @param $validators
     * @param $attribute
     * @return bool
     */
    public function hasValidator($validators, $attribute)
    {
        $validators = is_array($validators) ? $validators : [$validators];

        foreach ($this->rules() as $rule){
            $attributes = is_array($rule[0]) ? $rule[0] : [$rule[0]];
            if(in_array($attribute, $attributes) && in_array($rule[1], $validators)){
                return true;
            }
        }

        return false;
    }


    public function saveDataRelationsTable($viaTable, $condition, $data)
    {
        Yii::$app->db->createCommand()
            ->delete($viaTable, $condition)
            ->execute();

        foreach ($data as $key => $val){
            if (!empty($val)) {
                foreach ($val as $v) {
                    Yii::$app->db->createCommand()
                        ->insert($viaTable, [
                                $key => $v
                            ] + $condition)
                        ->execute();
                }
            }
        }
    }

}