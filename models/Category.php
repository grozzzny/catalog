<?php
namespace grozzzny\catalog\models;

use grozzzny\admin\helpers\Image;
use grozzzny\admin\widgets\file_input\components\FileBehavior;
use grozzzny\catalog\components\CategoryParent;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


/**
 * Category ActiveRecord model.
 *
 * Database fields:
 * @property integer $id
 * @property string  $slug
 * @property string  $title
 * @property integer $parent_id
 * @property string  $image_file
 * @property integer $views
 * @property string  $short
 * @property string  $description
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $order_num
 * @property integer $parents
 *
 * Defined relations:
 * @property Category[]  $parentsCategories
 * @property Category[]  $allChildren
 * @property Category  $parentCategory
 * @property Category[]  $children
 * @property Category[]  $childrenActive
 * @property array  $breadcrumbs
 * @property Properties[]  $properties
 * @property Properties[]  $allProperties
 * @property Item[]  $items
 * @property string  $fullTitle
 * @property array  $allParentId
 * @property array  $listItems
 * @property string  $link
 * @property string  $linkCreateElement
 * @property string  $linkCreate
 * @property string  $linkList
 * @property string  $linkAdmin
 * @property string  $linkEdit
 * @property string  $linkProperties
 * @property string  $linkDelete
 * @property Item[]  $activeItems
 *
 */
class Category extends ActiveRecord
{
    const STATUS_ON = true;
    const PRIMARY_MODEL = true;

    const TITLE = 'Categories';
    const SLUG = 'category';

    const SUBMENU_PHOTOS = false;
    const SUBMENU_FILES = false;
    const ORDER_NUM = true;

    private $_parentsCategories;

    public static function tableName()
    {
        return 'gr_catalog_categories';
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'blameable' => BlameableBehavior::className(),
            'timestamp' => TimestampBehavior::className(),
            'parents' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'parents',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'parents',
                ],
                'value' => function () {
                    return implode(",", self::getOnlyParentId($this->parent_id));
                },
            ],
            'categoryParent' => CategoryParent::className(),
            'image' => [
                'class' => FileBehavior::className(),
                'fileAttribute' => 'image_file',
                'uploadPath' => '/uploads/categories',
            ],
        ]);
    }

    public function rules()
    {
        return [
            'id' => ['id', 'number', 'integerOnly' => true],
            'slug_match' => ['slug', 'match', 'pattern' => '/^[\w\-]+$/'],
            'slug_unique' => ['slug', 'unique'],
            'string' => [[
                'title',
                'short',
            ], 'string'],
            'integer' => [[
                'parent_id',
            ], 'integer'],
            'image' => ['image_file', 'image'],
            'default_off' => [['views', 'order_num', 'parent_id'],'default', 'value' => 0],
            'safe' => [['description'], 'safe'],
            'default_on' => ['status', 'default', 'value' => self::STATUS_ON],
            'order_num' => [['order_num'], 'integer'],
            'required' => [['title','slug'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('catalog', 'ID'),
            'slug' => Yii::t('catalog', 'Slug'),
            'title' => Yii::t('catalog', 'Title'),
            'parent_id' => Yii::t('catalog', 'Parent Category'),
            'image_file' => Yii::t('catalog', 'Image'),
            'views' => Yii::t('catalog', 'Count Views'),
            'short' => Yii::t('catalog', 'Short text'),
            'description' => Yii::t('catalog', 'Description'),
            'status' => Yii::t('catalog', 'Status'),
            'order_num' => Yii::t('catalog', 'Sort Index'),
        ];
    }

    public function getParentsCategories()
    {
        if(!empty($this->_parentsCategories)) return $this->_parentsCategories;

        $categories_arr = [$this];
        $parent = $this->parentCategory;

        while ($parent){
            $categories_arr[] = $parent;
            $parent = $parent->parentCategory;
        };

        return $this->_parentsCategories = array_reverse($categories_arr, true);
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
    public static function queryFilter(ActiveQuery &$query, array $get)
    {
        if(!empty($get['text'])){
            $query->andFilterWhere(['LIKE', 'title', $get['text']]);
        }else{
            if(!empty($get['category_id'])){
                $query->andFilterWhere(['parent_id' => $get['category_id']]);
            }else {
                $query->andFilterWhere(['parent_id' => 0]);
            }
        }
    }


    public function getBreadcrumbs($lastLink = false, $adminPanel = true)
    {
        $breadcrumbs[] = [
            'label' => $this->title,
            'url' => $lastLink ? ($adminPanel ? $this->linkAdmin : $this->link) : null
        ];

        $parent = $this->parentCategory;

        while ($parent){
            array_push($breadcrumbs, [
                'url' => $adminPanel ? $parent->linkAdmin : $parent->link,
                'label' => $parent->title
            ]);
            $parent = $parent->parentCategory;
        };

        if(!$adminPanel) array_pop($breadcrumbs);

        return array_reverse($breadcrumbs, true);
    }


    /**
     * Сортировка
     * @param $provider
     */
    public function querySort(&$provider)
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
            $sort = $sort + ['defaultOrder' => ['id' => SORT_ASC]];
            $attributes = $attributes + ['order_num'];
        }

        $sort = $sort + ['attributes' => $attributes];

        $provider->setSort($sort);
    }

    /** relation models */
    public function modelRelationsCategoriesItems()
    {
        return RelationsCategoriesItems::className();
    }

    public function modelRelationsCategoriesProperties()
    {
        return RelationsCategoriesProperties::className();
    }

    public function modelProperties()
    {
        return Properties::className();
    }

    public function modelItem()
    {
        return Item::className();
    }
    /** end */

    public function getRelationsCategoriesProperties()
    {
        return $this->hasMany(static::modelRelationsCategoriesProperties(), ['category_id' => 'id']);
    }

    public function getProperties()
    {
        return $this->hasMany(static::modelProperties(), ['id' => 'property_id'])
            ->via('relationsCategoriesProperties')
            ->orderBy(['index' => SORT_ASC]);
    }


    public function getAllProperties()
    {
        $properties = [];
        foreach ($this->parentsCategories as $category){
            $properties = ArrayHelper::merge($properties, $category->properties);
        }
        return $properties;
    }

    public function getRelationsCategoriesItems()
    {
        return $this->hasMany(static::modelRelationsCategoriesItems(), ['category_id' => 'id']);
    }

    public function getItems()
    {
        $modelItem = static::modelItem();
        return $this->hasMany($modelItem, ['id' => 'item_id'])
            ->via('relationsCategoriesItems');
    }

    public function getAllChildren($condition = null)
    {
        $query = static::find()->where(['!=', 'FIND_IN_SET(\''.$this->id.'\', parents)', '0']);
        if(!empty($condition)) $query->andWhere($condition);
        return $query->all();
    }

    public function getChildren()
    {
        return $this->hasMany(static::className(), ['parent_id' => 'id']);
    }

    public function getChildrenActive()
    {
        $query = $this->getChildren()->where(['status' => self::STATUS_ON]);

        return $query;
    }

    public function afterDelete()
    {
        /**
         * @var Category $model_category
         */
        $model_category = Yii::createObject(['class' => Category::class]);

        $model_category::deleteAll(['!=', 'FIND_IN_SET(\''.$this->id.'\', parents)', '0']);
        parent::afterDelete(); // TODO: Change the autogenerated stub
    }



    /**
     * For special widget
     */
    public function getListItems()
    {
        $item_arr = [];
        foreach ($this->items as $item)
        {
            $item_arr[$item->id] = $item->title;
        }
        return $item_arr;
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

        return join(' → ', $arr_name);
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
     * @param $parent_id
     * @return array [3,2,1]
     */
    public static function getOnlyParentId($parent_id)
    {
        $arr_id = [];
        $parent = static::findOne($parent_id);

        while ($parent){
            $arr_id[] = $parent->id;
            $parent = $parent->parentCategory;
        };

        return $arr_id;
    }

    /**
     * НЕ АКТУАЛЕН
     */
//    public static function listCategories()
//    {
//        $categories_arr = [];
//        $categories = self::find()
//            //->where(['status' => self::STATUS_ON])
//            ->orderBy('title')
//            ->all();
//
//        foreach ($categories as $category){
//            $categories_arr[$category->id] = $category->fullTitle;
//        }
//
//        return $categories_arr;
//    }


    /**
     * @param null $width
     * @param null $height
     * @param bool $crop
     * @return string
     */
    public function getImage($width = null, $height = null, $crop = true){
        $image = empty($this->image_file)? Yii::$app->params['nophoto'] : $this->image_file;
        return Image::thumb($image, $width, $height, $crop);
    }


    public function getActiveItems()
    {
        $modelItem = static::modelItem();
        return $modelItem::find()->statusOn()->category($this)->all();
    }


    public function getLink()
    {
        return Url::to(['/category/' . $this->slug ]);
    }


    public function getLinkCreateElement()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id . '/a/create', 'slug' => Item::SLUG, 'category_id' => $this->id]);
    }


    public function getLinkCreate()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id . '/a/create', 'slug' => self::SLUG, 'category_id' => $this->id]);
    }

    public function getLinkList()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id, 'slug' => Item::SLUG, 'category_id' => $this->id]);
    }

    public function getLinkAdmin()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id, 'category_id' => $this->id]);
    }

    public function getLinkEdit()
    {
        return Url::to(['default/edit', 'id' => $this->id, 'category_id' => $this->parent_id, 'slug' => self::SLUG]);
    }

    public function getLinkProperties()
    {
        return Url::to(['/admin/'.Yii::$app->controller->module->id.'/properties', 'id' => $this->id, 'category_id' => $this->parent_id, 'slug' => self::SLUG]);
    }

    public function getLinkDelete()
    {
        return Url::to(['default/delete', 'id' => $this->primaryKey, 'slug' => self::SLUG]);
    }

    /**
     * @param $id
     * @return Category|null
     */
    public static function getMainCategoryById($id)
    {
        if(empty($id)) return null;

        return current(static::findOne($id)->parentsCategories);
    }

}
