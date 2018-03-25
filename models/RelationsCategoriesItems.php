<?php


namespace grozzzny\catalog\models;


use yii\db\ActiveRecord;

class RelationsCategoriesItems extends ActiveRecord
{
    const PRIMARY_MODEL = false;

    public static function tableName()
    {
        return 'gr_catalog_relations_categories_items';
    }
}