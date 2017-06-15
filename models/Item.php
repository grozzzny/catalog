<?php
namespace grozzzny\catalog\models;


use yii\behaviors\BlameableBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

class Item extends Base
{
    const PRIMARY_MODEL = true;

    const CACHE_KEY = 'gr_catalog_items';

    const TITLE = 'Elements';
    const SLUG = 'item';

    const SUBMENU_PHOTOS = true;
    const SUBMENU_FILES = false;
    const ORDER_NUM = false;


    public function behaviors()
    {
        $behaviors = parent::behaviors(); // TODO: Change the autogenerated stub

        $behaviors[] = BlameableBehavior::className();
        $behaviors[] = TimestampBehavior::className();

        return $behaviors;
    }


    public static function tableName()
    {
        return 'gr_catalog_items';
    }

    public function rules()
    {
        return [
            ['id', 'number', 'integerOnly' => true],
            ['slug', 'match', 'pattern' => '/^[\w\-]+$/'],
            ['slug', 'unique'],
            [[
                'title',
                'short',
            ], 'string'],
            [[
                'price',
                'views',
                'created_at',
                'updated_at',
                'discount',
                'user_id',
            ], 'integer'],
            ['image_file', 'image'],
            [['description'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_ON],
            [['order_num'], 'integer'],
            [['title', 'slug'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gr', 'ID'),
            'slug' => Yii::t('gr', 'Slug'),
            'title' => Yii::t('gr', 'Title'),
            'image_file' => Yii::t('gr', 'Image'),
            'views' => Yii::t('gr', 'Count Views'),
            'short' => Yii::t('gr', 'Short text'),
            'description' => Yii::t('gr', 'Description'),
            'status' => Yii::t('gr', 'Status'),
            'order_num' => Yii::t('gr', 'Sort Index'),
            'price' => Yii::t('gr', 'Price'),
            'discount' => Yii::t('gr', 'Discount'),
            'created_time' => Yii::t('gr', 'Date created'),
            'updated_time' => Yii::t('gr', 'Date updated'),
            'user_id' => Yii::t('gr', 'User'),
        ];
    }

    public static function queryFilter(&$query, $get)
    {
//        if(!empty($get['text'])){
//            $query->andFilterWhere([
//                    'OR',
//                    ['LIKE', 'name', $get['text']],
//                    ['LIKE', 'email', $get['text']],
//                    ['LIKE', 'phone', $get['text']],
//                    ['LIKE', 'description', $get['text']],
//                ]
//            );
//        }
    }

    public static function querySort(&$provider)
    {
        $sort = [];

        $attributes = [
            'id',
            'status',
            'title',
            'slug',
        ];

        if(self::ORDER_NUM){
            $sort = $sort + ['defaultOrder' => ['order_num' => SORT_DESC]];
            $attributes = $attributes + ['order_num'];
        }

        $sort = $sort + ['attributes' => $attributes];

        $provider->setSort($sort);
    }

}
