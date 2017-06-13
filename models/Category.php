<?php


namespace grozzzny\catalog\models;


class Category extends Base
{

    public function getItems()
    {
        return $this->hasMany(Item::className(), ['category_id' => 'id']);
    }

}