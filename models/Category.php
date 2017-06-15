<?php
namespace grozzzny\catalog\models;

use Yii;

class Category extends Base
{
    const PRIMARY_MODEL = true;

    const CACHE_KEY = 'gr_catalog_categories';

    const TITLE = 'Categories';
    const SLUG = 'category';

    const SUBMENU_PHOTOS = false;
    const SUBMENU_FILES = false;
    const ORDER_NUM = true;

    public static function tableName()
    {
        return 'gr_catalog_categories';
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
                'parent_id',
                'views',
            ], 'integer'],
            ['image_file', 'image'],
            [['description'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_ON],
            [['order_num'], 'integer'],
            [['title','slug'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gr', 'ID'),
            'slug' => Yii::t('gr', 'Slug'),
            'title' => Yii::t('gr', 'Title'),
            'parent_id' => Yii::t('gr', 'Parent Category'),
            'image_file' => Yii::t('gr', 'Image'),
            'views' => Yii::t('gr', 'Count Views'),
            'short' => Yii::t('gr', 'Short text'),
            'description' => Yii::t('gr', 'Description'),
            'status' => Yii::t('gr', 'Status'),
            'order_num' => Yii::t('gr', 'Sort Index'),
        ];
    }

    public static function queryFilter(&$query, $get)
    {
//        if(!empty($get['text'])){
//            $query->andFilterWhere([
//                'OR',
//                ['LIKE', 'name', $get['text']],
//                ['LIKE', 'email', $get['text']],
//                ['LIKE', 'phone', $get['text']],
//                ['LIKE', 'description', $get['text']],
//            ]
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
            'order_num'
        ];

        if(self::ORDER_NUM){
            $sort = $sort + ['defaultOrder' => ['order_num' => SORT_DESC]];
            $attributes = $attributes + ['order_num'];
        }

        $sort = $sort + ['attributes' => $attributes];

        $provider->setSort($sort);
    }

}
