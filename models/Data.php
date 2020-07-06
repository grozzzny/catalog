<?php
namespace grozzzny\catalog\models;


use Yii;
use yii\db\ActiveRecord;

/**
 * Data ActiveRecord model.
 *
 * Database fields:
 * @property integer $id
 * @property integer $item_id
 * @property string  $property_slug
 * @property string  $value
 *
 * Defined relations:
 * @property-read Item         $item
 * @property-read Properties   $property
 */
class Data extends ActiveRecord
{
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
            'id' => Yii::t('catalog', 'ID'),
            'property_slug' => Yii::t('catalog', 'Slug'),
            'item_id' => Yii::t('catalog', 'Element'),
            'value' => Yii::t('catalog', 'Value'),
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

    public static function deleteAllData($id, $slug)
    {
        static::deleteAll(['item_id' => $id, 'property_slug' => $slug]);
    }

}
