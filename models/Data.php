<?php
namespace grozzzny\catalog\models;


use Yii;

class Data extends Base
{
    const CACHE_KEY = 'gr_catalog_data';

    const PRIMARY_MODEL = false;

    public static function tableName()
    {
        return 'gr_catalog_data';
    }

    public function rules()
    {
        return [
            ['id', 'number', 'integerOnly' => true],
            [[
                'property_slug',
            ], 'string'],
            [[
                'item_id',
            ], 'integer'],
            [['value'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gr', 'ID'),
            'property_slug' => Yii::t('gr', 'Slug'),
            'item_id' => Yii::t('gr', 'Element'),
            'value' => Yii::t('gr', 'Value'),
        ];
    }

    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    public function getProperty()
    {
        return $this->hasOne(Properties::className(), ['slug' => 'property_slug']);
    }

}
