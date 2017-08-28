<?php
namespace grozzzny\catalog\models;

use yii\easyii\behaviors\CacheFlush;
use Yii;
use yii\easyii\behaviors\SeoBehavior;
use yii\helpers\ArrayHelper;

class Base extends \yii\easyii\components\ActiveRecord
{
    use TraitModel;

    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    public function behaviors()
    {
        return [
            CacheFlush::className(),
            'seoBehavior' => SeoBehavior::className(),
            //SortableModel::className()
        ];
    }

    public static function getModels()
    {
        $models = [];

        $settings = Yii::$app->getModule('admin')->activeModules[Yii::$app->controller->module->id]->settings;

        foreach (glob(__DIR__ . "/*.php") as $file){
            $file_name = basename($file, '.php');

            if($file_name == 'Base') continue;

            $alternative_class_name = ArrayHelper::getValue($settings, 'model'.$file_name, '');

            $class_name = !empty($alternative_class_name) ? $alternative_class_name : __NAMESPACE__ . '\\' . $file_name;

            if(!class_exists($class_name)) continue;

            $class = Yii::createObject($class_name);

            if(!$class::PRIMARY_MODEL) continue;

            $models[$class::SLUG] = $class;
        }

        return $models;
    }

    public static function getModel($slug)
    {
        $models = self::getModels();
        return empty($slug) ? current($models) : $models[$slug];
    }


    /**
     * Используется при отчистке ранее загруженных файлов
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(!$insert){
                foreach ($this->getAttributes() as $attribute => $value){
                    if($this->hasValidator(['image', 'file'], $attribute)) {
                        if($this->$attribute !== $this->oldAttributes[$attribute]){
                            @unlink(Yii::getAlias('@webroot') . $this->oldAttributes[$attribute]);
                        }
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Используется при отчистке файлов
     */
    public function afterDelete()
    {
        parent::afterDelete();

        foreach ($this->getAttributes() as $attribute => $value){
            if($this->hasValidator(['image', 'file'], $attribute)) {
                @unlink(Yii::getAlias('@webroot').$this->$attribute);
            }
        }
    }

    public function hasAdminPanel()
    {
        return (Yii::$app->controller->layout == '@easyii/views/layouts/main')? true: false;
    }

    public function seo($attribute, $default = ''){
        return !empty($this->seo->{$attribute}) ? $this->seo->{$attribute} : $default;
    }

}
