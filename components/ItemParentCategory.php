<?php
namespace grozzzny\catalog\components;


use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class ItemParentCategory extends Behavior
{
    /** @var Item */
    public $owner;

    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT => 'setParentCategory',
        ];
    }

    public function setParentCategory()
    {
        $category_id = Yii::$app->request->get('category_id');

        if(empty($category_id)) return false;

        $this->owner->parent_category_slug = Category::findOne($category_id)->slug;
    }
}