<?php
namespace grozzzny\catalog\components;


use grozzzny\catalog\models\Item;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class ItemCategoriesBehavior extends Behavior
{
    /** @var Item */
    public $owner;

    /** @var array */
    public $categories;

    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT => 'setCategories',
        ];
    }

    public function setCategories()
    {
        if(!isset(Yii::$app->request)) return false;

        $category_id = Yii::$app->request->get('category_id');

        if(empty($category_id) && !isset(Yii::$app->controller)) return false;

        if(Yii::$app->controller->action->id == 'create') $this->owner->categories = [$category_id];
    }

}