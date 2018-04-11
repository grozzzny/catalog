<?php
namespace grozzzny\catalog;

use grozzzny\catalog\models\RelationsCategoriesItems;
use grozzzny\catalog\models\RelationsCategoriesProperties;
use Yii;
use yii\easyii2\AdminModule;
use yii\easyii2\models\ModuleEasyii2Interface;
use yii\helpers\ArrayHelper;

class CatalogModule extends \yii\easyii2\components\Module implements ModuleEasyii2Interface
{
    public $settings = [
        'modelItem' => '',
        'modelCategory' => '',
        'modelData' => ''
    ];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        self::registerTranslation();
    }

    public static function registerTranslation()
    {
        Yii::$app->i18n->translations['gr*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@grozzzny/catalog/messages',
        ];
    }

    public function getTitle()
    {
        // TODO: Implement getTitle() method.
        return Yii::t('gr', 'Catalog');
    }

    public function getName()
    {
        // TODO: Implement getName() method.
        return $this->id;
    }

    public function getIcon()
    {
        // TODO: Implement getIcon() method.
        return 'globe';
    }

    public static function getNameModule()
    {
        return 'catalog';
    }
}