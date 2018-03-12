<?php
namespace grozzzny\catalog\widgets;

use grozzzny\catalog\CatalogModule;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use grozzzny\catalog\models\Properties;
use kartik\select2\Select2;
use grozzzny\catalog\widgets\date_time_picker\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use Yii;


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
                    return Html::input('string', $this->attribute, $value, ['class' => 'form-control', 'placeholder' => $label]);
                }
            case Properties::TYPE_INTEGER:
                if($settings->filter_range){
                    $html = Html::beginTag('div', ['class' => 'row']);
                    $html .= Html::tag('div',
                        Html::input('number', $this->attribute . '_from', $this->query_param[$this->attribute . '_from'], ['class' => 'form-control', 'placeholder' => $label . Yii::t('gr',' from')])
                        , ['class' => 'col-xs-5']);
                    $html .= Html::tag('div', ' - ', ['class' => 'col-xs-2', 'style' => 'text-align: center;']);
                    $html .= Html::tag('div',
                        Html::input('number', $this->attribute . '_to', $this->query_param[$this->attribute . '_to'], ['class' => 'form-control', 'placeholder' => $label . Yii::t('gr',' to')])
                        , ['class' => 'col-xs-5']);
                    $html .= Html::endTag('div');
                    return $html;
                }else{
                    return Html::input('number', $this->attribute, $value, ['class' => 'form-control', 'placeholder' => $label]);
                }
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

//                $html = '<ul class="list-group">
//                            <li class="list-group-item">
//                                '.$label.'
//                                <div class="material-switch pull-right">
//                                    &nbsp;
//                                    '.
//                                    Html::checkbox($this->attribute, $value, ['uncheck' => 0, 'label' => false, 'id' => 'checkbox_'.$this->attribute]).
//                                    Html::label('', 'checkbox_'.$this->attribute, ['class' => 'label-success'])
//                                    .'
//                                </div>
//                            </li>
//                       </ul>';

                $html = '<ul class="list-group">
                            <li class="list-group-item" style="padding: 7px 15px;">
                                '.$label.'
                                <div class="pull-right" style="width: 170px; margin-top: -8px; margin-right: -12px;">
                                    <div class="input-group-btn">
                                        <label class="btn btn-default" style="width: 70px">
                                            '.Html::radio($this->attribute, $value === '1', ['value' => '1']).'
                                            '.Yii::t('gr', 'Yes').'
                                        </label>
                                        <label class="btn btn-default">
                                            '.Html::radio($this->attribute, $value !== '1' && $value !== '0', ['value' => '']).'
                                        </label>
                                        <label class="btn btn-default" style="width: 70px">
                                            '.Html::radio($this->attribute, $value === '0', ['value' => '0']).'
                                            '.Yii::t('gr', 'No').'
                                        </label>
                                    </div>
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
                        $url = '/admin/'.CatalogModule::getInstance()->id.'/properties/get-list-items-category';
                        break;
                    case Properties::TYPE_MULTICATEGORY:
                        $url = '/admin/'.CatalogModule::getInstance()->id.'/properties/get-list-multicategories';
                        break;
                    case Properties::TYPE_CATEGORY:
                        $url = '/admin/'.CatalogModule::getInstance()->id.'/properties/get-list-categories';
                        break;
                }

                return Select2::widget([
                    'name' => $this->attribute,
                    'value' => $this->query_param[$this->attribute],
                    'data' => $data,
                    'options' => [
                        'multiple' => $settings->multiple ? true : false,
                    ],
                    'pluginOptions' => [
                        'placeholder' => $label,
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
                if($settings->filter_range){
                    $html = Html::beginTag('div', ['class' => 'row']);
                    $html .= Html::tag('div',
                        DateTimePicker::widget([
                            'name' => $this->attribute . '_from',
                            'value' => $this->query_param[$this->attribute . '_from'],
                            'placeholder' => $label . Yii::t('gr', ' from'),
                        ])
                        , ['class' => 'col-xs-5']);
                    $html .= Html::tag('div', ' - ', ['class' => 'col-xs-2', 'style' => 'text-align: center;']);
                    $html .= Html::tag('div',
                        DateTimePicker::widget([
                            'name' => $this->attribute . '_to',
                            'value' => $this->query_param[$this->attribute . '_to'],
                            'placeholder' => $label . Yii::t('gr', ' to'),
                        ])
                        , ['class' => 'col-xs-5']);
                    $html .= Html::endTag('div');
                    return $html;
                }else{
                    return DateTimePicker::widget([
                        'name' => $this->attribute,
                        'value' => $value,
                        'placeholder' => $label,
                    ]);
                }
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
                return Html::input('string', $this->attribute, $value, ['class' => 'form-control', 'placeholder' => $label]);
        }

    }
}