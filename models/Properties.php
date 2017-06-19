<?php
namespace grozzzny\catalog\models;


use Yii;

class Properties extends Base
{
    const PRIMARY_MODEL = true;

    const CACHE_KEY = 'gr_catalog_properties';

    const TITLE = 'All properties';
    const SLUG = 'all_properties';

    const SUBMENU_PHOTOS = false;
    const SUBMENU_FILES = false;
    const ORDER_NUM = true;


    const TYPE_STRING = 'string';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_HTML = 'html';
    const TYPE_CATEGORY = 'category';
    const TYPE_DATETIME = 'datetime';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';


    public static function tableName()
    {
        return 'gr_catalog_properties';
    }

    public function rules()
    {
        return [
            ['id', 'number', 'integerOnly' => true],
            ['slug', 'match', 'pattern' => '/^[\w\-]+$/'],
            ['slug', 'unique'],
            [[
                'title',
                'type',
            ], 'string'],
            [[
                'order_num',
            ], 'integer'],
            [['settings','validation_rule','options'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gr', 'ID'),
            'slug' => Yii::t('gr', 'Slug'),
            'title' => Yii::t('gr', 'Title'),
            'type' => Yii::t('gr', 'Type'),
            'settings' => Yii::t('gr', 'Settings'),
            'validation_rule' => Yii::t('gr', 'Validation Rule'),
            'order_num' => Yii::t('gr', 'Sort Index'),
        ];
    }

    public static function getListType()
    {
        return [
            self::TYPE_STRING => Yii::t('gr','String'),
            self::TYPE_SELECT => Yii::t('gr','Select'),
            self::TYPE_CHECKBOX => Yii::t('gr','Checkbox'),
            self::TYPE_HTML => Yii::t('gr','HTML'),
            self::TYPE_CATEGORY => Yii::t('gr','Category'),
            self::TYPE_DATETIME => Yii::t('gr','Datetime'),
            self::TYPE_IMAGE => Yii::t('gr','Image'),
            self::TYPE_FILE => Yii::t('gr','File'),
        ];
    }
}
