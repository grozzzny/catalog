<?php
namespace grozzzny\catalog\components;


use grozzzny\catalog\models\Category;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class CategoryParent extends Behavior
{
    /** @var Category */
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

        $this->owner->parent_id = $category_id;
    }
}