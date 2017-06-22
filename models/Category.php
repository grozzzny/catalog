<?php
namespace grozzzny\catalog\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
            ], 'integer'],
            ['image_file', 'image'],
            ['parent_id','default', 'value' => 0],
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

    public function getParentsCategories()
    {
        $categories_arr = [$this];
        $parent = $this->parentCategory;

        while ($parent){
            $categories_arr[] = $parent;
            $parent = $parent->parentCategory;
        };

        return array_reverse($categories_arr, true);
    }

    public function getParentCategory()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * Фильтр
     * @param $query
     * @param $get
     */
    public static function queryFilter(&$query, $get)
    {
        if(!empty($get['text'])){
            $query->andFilterWhere(['LIKE', 'title', $get['text']]);
        }else{
            if(!empty($get['category'])){
                $query->andFilterWhere(['parent_id' => $get['category']]);
            }else {
                $query->andFilterWhere(['parent_id' => 0]);
            }
        }
    }


    public function getBreadcrumbs()
    {
        $breadcrumbs = ['label' => $this->title];
        $parent = $this->parentCategory;

        while ($parent){
            array_push($breadcrumbs, [
                'url' => $parent->linkAdmin,
                'label' => $parent->title
            ]);
            $parent = $parent->parentCategory;
        };

        return array_reverse($breadcrumbs, true);
    }


    /**
     * Сортировка
     * @param $provider
     */
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


    public function getProperties()
    {
        return $this->hasMany(Properties::className(), ['id' => 'property_id'])
            ->viaTable('gr_catalog_relations_categories_properties', ['category_id' => 'id']);
    }


    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])
            ->viaTable('gr_catalog_relations_categories_items', ['category_id' => 'id']);
    }

    public function getFullTitle()
    {
        $arr_name = [$this->title];
        $parent = $this->parentCategory;

        while ($parent){
            array_push($arr_name, $parent->title);
            $parent = $parent->parentCategory;
        };

        $arr_name = array_reverse($arr_name, true);

        return join('→', $arr_name);
    }

    public function getAllParentId()
    {
        $arr_id = [$this->id];
        $parent = $this->parentCategory;

        while ($parent){
            $arr_id[] = $parent->id;
            $parent = $parent->parentCategory;
        };

        return $arr_id;
    }

    /**
     * Возвращает список всех категорий в алфавитном порядке
     * @return array
     */
    public static function listCategories()
    {
        $categories_arr = [];
        $categories = self::find()
            //->where(['status' => self::STATUS_ON])
            ->orderBy('title')
            ->all();

        foreach ($categories as $category){
            $categories_arr[$category->id] = $category->fullTitle;
        }

        return $categories_arr;
    }

    public function getLinkCreateElement()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id . '/a/create', 'slug' => Item::SLUG, 'category' => $this->id]);
    }

    public function getLinkList()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id, 'slug' => Item::SLUG, 'category' => $this->id]);
    }

    public function getLinkAdmin()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id, 'slug' => self::SLUG, 'category' => $this->id]);
    }

    public function getLinkEdit()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id.'/a/edit', 'id' => $this->id, 'slug' => self::SLUG]);
    }

    public function getLinkProperties()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id.'/properties', 'id' => $this->id, 'slug' => self::SLUG]);
    }

    public function getLinkDelete()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id.'/a/delete', 'id' => $this->primaryKey, 'slug' => self::SLUG]);
    }

}
