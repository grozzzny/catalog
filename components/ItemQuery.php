<?php
namespace grozzzny\catalog\components;

use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Data;
use grozzzny\catalog\models\Properties;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ItemQuery extends ActiveQuery
{

    public $_category;

    /**
     * Установит категорию. И осуществит поиск элементов определенной категории
     * @param Category $category
     * @return $this
     */
    public function category(Category $category)
    {
        $this->_category = $category;

        $this->joinWith('categories');
        $this->andFilterWhere(['gr_catalog_categories.id' => $this->_category->id]);

        return $this;
    }

    /**
     * Условия поиска по title и description
     * @param $search
     * @return $this
     */
    public function whereSearch($search)
    {
        if(!empty($search)){
            $this->andFilterWhere([
                'OR',
                ['LIKE', 'gr_catalog_items.title', $search],
                ['gr_catalog_items.id' => $search],
                ['LIKE', 'gr_catalog_items.description', $search],
            ]);
        }
        return $this;
    }

    /**
     * Условия диапазона значений
     * @param array $attributes
     * @return $this
     */
    public function whereRange(Array $attributes)
    {
        foreach ($attributes as $attribute => $value){
            $from = $value[0];
            $to = $value[1];

            if(!empty($from) || !empty($to)){
                if(!empty($from) && empty($to)){
                    $this->andFilterWhere(['>=', $attribute, (int)$from]);
                } elseif(!empty($to) && empty($from)) {
                    $this->andFilterWhere(['<=', $attribute, (int)$to]);
                } else {
                    $this->andFilterWhere(['between', $attribute, (int)$from, (int)$to]);
                }
            }
        }
        return $this;
    }

    /**
     * Поиск элементов по свойствам
     * @param $condition
     * @return $this
     */
    public function whereProperties($condition)
    {
        $this->distinct('gr_catalog_items.id');

        $properties = (isset($this->_category)) ? $this->_category->allProperties : [];

        $filtersApplied = 0;
        $subQuery = Data::find()->select('item_id, COUNT(*) as filter_matched')->groupBy('item_id');

        /**
         * Если установлена категория и определены свойства
         */
        if(!empty($properties)) {
            foreach ($properties as $property) {
                if ($property->settings->filter_range) {

                    $value_from = ArrayHelper::getValue($condition, $property->slug . '_from', '');
                    $value_to = ArrayHelper::getValue($condition, $property->slug . '_to', '');

                    if (empty($value_from) && empty($value_to)) {
                        continue;
                    }

                    if (!$value_from) {
                        $additionalCondition = ['<=', 'value', (int)$value_to];
                    } elseif (!$value_to) {
                        $additionalCondition = ['>=', 'value', (int)$value_from];
                    } else {
                        $additionalCondition = ['between', 'value', (int)$value_from, (int)$value_to];
                    }

                    $subQuery->orFilterWhere(['and', ['property_slug' => $property->slug], $additionalCondition]);

                    $filtersApplied++;
                } else {
                    $value = ArrayHelper::getValue($condition, $property->slug, '');

                    if (empty($value)) {
                        continue;
                    }

                    switch ($property->type) {
                        case Properties::TYPE_DATETIME:
                            $subQuery->orFilterWhere([
                                'and',
                                ['property_slug' => $property->slug],
                                ['=', 'FROM_UNIXTIME(`value`,\'%Y-%m-%d\')', date('Y-m-d', $value)]
                            ]);
                            break;
                        case Properties::TYPE_CHECKBOX:
                        case Properties::TYPE_FILE:
                        case Properties::TYPE_IMAGE:
                            $subQuery->orFilterWhere([
                                'and',
                                ['property_slug' => $property->slug],
                                ['not', ['value' => null]]
                            ]);
                            break;
                        default:
                            $subQuery->orFilterWhere([
                                'and',
                                ['property_slug' => $property->slug],
                                ['value' => $value]
                            ]);
                    }
                    $filtersApplied += is_array($value) ? count($value) : 1;
                }

            }
        }else{
            $properties_slug = ArrayHelper::getColumn(Properties::find()->select(['slug'])->asArray()->all(), 'slug');

            foreach ($condition as $attribute => $value){

                if(!in_array($attribute, $properties_slug)) continue;

                $subQuery->orFilterWhere([
                    'and',
                    ['property_slug' => $attribute],
                    ['value' => $value]
                ]);
                $filtersApplied += is_array($value) ? count($value) : 1;
            }
        }

        if($filtersApplied) {
            $this->join('LEFT JOIN', ['f' => $subQuery], 'f.item_id = gr_catalog_items.id');
            $this->andFilterWhere(['f.filter_matched' => $filtersApplied]);
        }
        return $this;
    }

}