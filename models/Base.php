<?php
namespace grozzzny\catalog\models;

use grozzzny\catalog\CatalogModule;
use yii\easyii2\behaviors\CacheFlush;
use Yii;
use yii\easyii2\behaviors\SeoBehavior;
use yii\easyii2\components\ActiveRecord;
use yii\easyii2\models\Photo;
use yii\easyii2\modules\gallery\api\PhotoObject;
use yii\helpers\ArrayHelper;

/**
 * Class Base
 * @package grozzzny\catalog\models
 *
 * @property-read PhotoObject[] $photos
 */
class Base extends \yii\easyii2\components\ActiveRecord
{
    use TraitModel;

    private $_photos;

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

        $settings = CatalogModule::getInstance()->settings;

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
        return (Yii::$app->controller->layout == '@easyii2/views/layouts/main')? true: false;
    }

    public function seo($attribute, $default = ''){
        return !empty($this->seo->{$attribute}) ? $this->seo->{$attribute} : $default;
    }


    public function enablePhotoManager()
    {
        return false;
    }

    public function getPhotos()
    {
        if(empty($this->_photos) && $this->enablePhotoManager()){
            $modelPhoto = ActiveRecord::getModelByName('Photo', 'admin');
            $photos = $modelPhoto::find()
                ->where([
                    'class' => self::className(),
                    'item_id' => $this->id
                ])
                ->sort()
                ->all();

            foreach($photos as $model){
                $this->_photos[] = new PhotoObject($model);
            }
        }
        return $this->_photos;
    }
}
