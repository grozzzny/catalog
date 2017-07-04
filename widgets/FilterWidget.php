<?php
namespace grozzzny\catalog\widgets;

use bl\ace\AceWidget;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use grozzzny\catalog\models\Properties;
use grozzzny\catalog\widgets\fileinput\FileInputWidget;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use kartik\select2\Select2;
use grozzzny\catalog\widgets\date_time_picker\DateTimePicker;
use yii\easyii\widgets\Redactor;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
use yii\web\JsExpression;
use yii\widgets\InputWidget;


class FilterWidget extends InputWidget
{

    public $query_param = [];

    public function run()
    {
        $type = $this->model->getType($this->attribute);
        $settings = $this->model->getSettings($this->attribute);
        $options = $this->model->getOptions($this->attribute);
        $label = $this->model->attributeLabels()[$this->attribute];
        $value = $this->model->{$this->attribute};

        switch ($type){
            case Properties::TYPE_STRING:
                if($settings->multiple){
                    return Select2::widget([
                        'name' => $this->attribute,
                        'value' => $value,
                        'options' => [
                            'placeholder' => $label,
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => true,
                        ],
                    ]);
                }else{
                    return Html::input('string', $this->attribute, $this->value, ['class' => 'form-control', 'placeholder' => $label]);
                }
            case Properties::TYPE_INTEGER:
                return Html::input('number', $this->attribute, $value, ['class' => 'form-control']);
            case Properties::TYPE_SELECT:

                return Select2::widget([
                    'name' => $this->attribute,
                    'value' => $value,
                    'data' => $options,
                    'options' => [
                        'placeholder' => $label,
                        'multiple' => $settings->multiple ? true : false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);

            case Properties::TYPE_CHECKBOX:

                $html = '<ul class="list-group">
                            <li class="list-group-item">
                                '.$label.'
                                <div class="material-switch pull-right">
                                    &nbsp;
                                    '.
                                    Html::checkbox($this->attribute, $value, ['uncheck' => 0, 'label' => false, 'id' => 'checkbox_'.$this->attribute]).
                                    Html::label('', 'checkbox_'.$this->attribute, ['class' => 'label-success'])
                                    .'
                                </div>
                            </li>
                       </ul>';

                return $html;

            case Properties::TYPE_ITEMSCATEGORY:
            case Properties::TYPE_MULTICATEGORY:
            case Properties::TYPE_CATEGORY:

                if( in_array($type, [Properties::TYPE_CATEGORY, Properties::TYPE_MULTICATEGORY])) {
                    $data = ArrayHelper::map(Category::findAll(['id' => $this->model{$this->attribute}]), 'id', 'fullTitle');
                }else{
                    $data = ArrayHelper::map(Item::findAll(['id' => $this->model{$this->attribute}]), 'id', 'title');
                }

                switch ($type){
                    case Properties::TYPE_ITEMSCATEGORY:
                        $url = '/admin/newcatalog/properties/get-list-items-category';
                        break;
                    case Properties::TYPE_MULTICATEGORY:
                        $url = '/admin/newcatalog/properties/get-list-multicategories';
                        break;
                    case Properties::TYPE_CATEGORY:
                        $url = '/admin/newcatalog/properties/get-list-categories';
                        break;
                }

                return Select2::widget([
                    'name' => $this->attribute,
                    'value' => $this->query_param[$this->attribute],
                    'data' => $data,
                    'options' => [
                        'placeholder' => $label,
                        'multiple' => $settings->multiple ? true : false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'ajax' => [
                            'url' => $url,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) {
                               return {
                                    q:params.term'.((empty($options->category_id)) ? '': ', category_id:'.$options->category_id).'
                                };
                            }'),
                        ],
                    ],
                ]);

            case Properties::TYPE_DATETIME:

                return DateTimePicker::widget([
                    'name' => $this->attribute,
                    'value' => $value,
                ]);

            case Properties::TYPE_IMAGE:
            case Properties::TYPE_FILE:

            $html = '<ul class="list-group">
                            <li class="list-group-item">
                                '.$label.'
                                <div class="material-switch pull-right">
                                    &nbsp;
                                    '.
                                    Html::checkbox($this->attribute, $value, ['uncheck' => 0, 'label' => false, 'id' => 'checkbox_'.$this->attribute]).
                                    Html::label('', 'checkbox_'.$this->attribute, ['class' => 'label-success'])
                                    .'
                                </div>
                            </li>
                       </ul>';

            return $html;

            default:
                return Html::input('string', $this->attribute, $this->value, ['class' => 'form-control', 'placeholder' => $label]);
        }

    }
}