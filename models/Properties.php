<?php
namespace grozzzny\catalog\models;


use grozzzny\catalog\CatalogModule;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use stdClass;

/**
 * Properties ActiveRecord model.
 *
 * Database fields:
 * @property integer $id
 * @property string  $slug
 * @property string  $title
 * @property string  $type
 * @property stdClass  $settings
 * @property array  $validations
 * @property stdClass  $options
 * @property integer  $order_num
 * @property integer  $index
 *
 * Defined relations:
 * @property Category[]  $categories
 * @property Category  $category
 * @property string  $validationsJson
 * @property string  $optionsJson
 * @property string  $settingsJson
 */
class Properties extends Base
{
    const PRIMARY_MODEL = false;

    const CACHE_KEY = 'gr_catalog_properties';

    const TITLE = 'All properties';
    const SLUG = 'all_properties';

    const SUBMENU_PHOTOS = false;
    const SUBMENU_FILES = false;
    const ORDER_NUM = true;


    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_HTML = 'html';
    const TYPE_CATEGORY = 'category';
    const TYPE_MULTICATEGORY = 'multicategory';
    const TYPE_ITEMSCATEGORY = 'itemscategory';
    const TYPE_DATETIME = 'datetime';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_CODE = 'code';

    const TYPE_MAP_PLACEMARK = 'map_placemark';
    const TYPE_MAP_POLYGON = 'map_polygon';
    const TYPE_MAP_POLYLINE = 'map_polyline';
    const TYPE_MAP_ROUTE = 'map_route';

    public $category_id = null;

    public static function tableName()
    {
        return 'gr_catalog_properties';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors(); // TODO: Change the autogenerated stub

        /**
         * Settings
         */
        $behaviors[] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'settings',
                ActiveRecord::EVENT_BEFORE_UPDATE => 'settings',
            ],
            'value' => function () {
                return json_encode($this->settings, JSON_UNESCAPED_UNICODE);
            },
        ];

        $behaviors[] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_AFTER_INSERT => 'settings',
                ActiveRecord::EVENT_AFTER_UPDATE => 'settings',
                ActiveRecord::EVENT_AFTER_FIND => 'settings',
            ],
            'value' => function () {
                return json_decode($this->settings);
            },
        ];


        /**
         * validations
         */
        $behaviors[] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'validations',
                ActiveRecord::EVENT_BEFORE_UPDATE => 'validations',
            ],
            'value' => function () {
                return json_encode($this->validations, JSON_UNESCAPED_UNICODE);
            },
        ];

        $behaviors[] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_AFTER_INSERT => 'validations',
                ActiveRecord::EVENT_AFTER_UPDATE => 'validations',
                ActiveRecord::EVENT_AFTER_FIND => 'validations',
            ],
            'value' => function () {
                return json_decode($this->validations, true);
            },
        ];


        /**
         * Options
         */
        $behaviors[] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'options',
                ActiveRecord::EVENT_BEFORE_UPDATE => 'options',
            ],
            'value' => function () {
                return json_encode($this->options, JSON_UNESCAPED_UNICODE);
            },
        ];

        $behaviors[] = [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_AFTER_INSERT => 'options',
                ActiveRecord::EVENT_AFTER_UPDATE => 'options',
                ActiveRecord::EVENT_AFTER_FIND => 'options',
            ],
            'value' => function () {
                return json_decode($this->options);
            },
        ];

        return $behaviors;
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
                'category_id',
                'order_num',
                'index',
            ], 'integer'],
            [['order_num'],'default', 'value' => 0],
            [['settings','validations','options'], 'safe'],
            ['category_id', 'required']
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
            'validations' => Yii::t('gr', 'Validation Rule'),
            'order_num' => Yii::t('gr', 'Sort Index'),
            'index' => Yii::t('gr', 'Index'),
        ];
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if (!$this->getCategories()->where(['id' => $this->category_id])->exists()){
            $categoryModel = static::getCategoryModel();
            $categoryModel::findOne($this->category_id)->link('properties', $this);
        }
    }

    /**
     * @return string | Category
     */
    protected static function getCategoryModel()
    {
        return Category::className();
    }

    public static function getListType()
    {
        return [
            self::TYPE_STRING => Yii::t('gr','String'),
            self::TYPE_INTEGER => Yii::t('gr','Integer'),
            self::TYPE_SELECT => Yii::t('gr','Select'),
            self::TYPE_CHECKBOX => Yii::t('gr','Checkbox'),
            self::TYPE_HTML => Yii::t('gr','HTML'),
            self::TYPE_CATEGORY => Yii::t('gr','Category'),
            self::TYPE_MULTICATEGORY => Yii::t('gr','Multi category'),
            self::TYPE_ITEMSCATEGORY => Yii::t('gr','Items category'),
            self::TYPE_DATETIME => Yii::t('gr','Datetime'),
            self::TYPE_IMAGE => Yii::t('gr','Image'),
            self::TYPE_FILE => Yii::t('gr','File'),
            self::TYPE_CODE => Yii::t('gr','Code'),
            self::TYPE_MAP_PLACEMARK => Yii::t('gr','Map. Placemark'),
            self::TYPE_MAP_POLYGON => Yii::t('gr','Map. Polygon'),
            self::TYPE_MAP_POLYLINE => Yii::t('gr','Map. Polyline'),
            self::TYPE_MAP_ROUTE => Yii::t('gr','Map. Route'),
        ];
    }

    public function getRelationsCategoriesProperties()
    {
        return $this->hasMany(RelationsCategoriesProperties::className(), ['property_id' => 'id']);
    }

    public function getCategories()
    {
        $categoryModel = static::getCategoryModel();
        return $this->hasMany($categoryModel::className(), ['id' => 'category_id'])
            ->via('relationsCategoriesProperties');
    }

    /**
     * Only type "TYPE_CATEGORY"
     * @return null|\yii\db\ActiveQuery
     */
    public function getCategory()
    {
        $categoryModel = static::getCategoryModel();
        return ($this->type == self::TYPE_CATEGORY) ? $this->hasOne($categoryModel::className(), ['id' => $this->options->category_id]) : null;
    }


    public function getValidationsJson()
    {
        return (empty($this->validations)) ? '{}' : json_encode($this->validations, JSON_UNESCAPED_UNICODE);
    }

    public function getOptionsJson()
    {
        return (empty($this->options)) ? '{}' : json_encode($this->options, JSON_UNESCAPED_UNICODE);
    }

    public function getSettingsJson()
    {
        return (empty($this->settings)) ? '{}' : json_encode($this->settings, JSON_UNESCAPED_UNICODE);
    }
}
