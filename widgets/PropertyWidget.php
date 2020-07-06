<?php
namespace grozzzny\catalog\widgets;

use bl\ace\AceWidget;
use grozzzny\catalog\CatalogModule;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use grozzzny\catalog\models\Properties;
use grozzzny\catalog\widgets\fileinput\FileInputWidget;
use grozzzny\widgets\map\MapConstructorWidget;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use kartik\select2\Select2;
use grozzzny\catalog\widgets\date_time_picker\DateTimePicker;
use yii\redactor\widgets\Redactor;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
use yii\web\JsExpression;
use yii\widgets\InputWidget;


class PropertyWidget extends InputWidget
{

    public function run()
    {
        $type = $this->model->getType($this->attribute);
        $settings = $this->model->getSettings($this->attribute);
        $options = $this->model->getOptions($this->attribute);

        $name_module = 'catalog';

        switch ($type){
            case Properties::TYPE_STRING:
                if($settings->multiple){
                    return Select2::widget([
                        'model' => $this->model,
                        'attribute' => $this->attribute,
                        'options' => [
                            'placeholder' => Yii::t('catalog', 'Enter value..'),
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'tags' => true,
                        ],
                    ]);
                }else{
                    return Html::activeInput('string', $this->model, $this->attribute, ['class' => 'form-control']);
                }
            case Properties::TYPE_INTEGER:
                return Html::activeInput('number', $this->model, $this->attribute, ['class' => 'form-control']);
            case Properties::TYPE_SELECT:

                return Select2::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'data' => $options,
                    'options' => [
                        'placeholder' => Yii::t('catalog', 'Enter value..'),
                        'multiple' => $settings->multiple ? true : false,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);

            case Properties::TYPE_CHECKBOX:

                return SwitchCheckbox::widget([
                    'model' => $this->model,
                    'attributes' => [
                        $this->attribute
                    ]
                ]);

            case Properties::TYPE_HTML:

               return Redactor::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'options' => [
                        'minHeight' => 200,
                        'imageUpload' => Url::to(['/admin/redactor/upload', 'dir' => Yii::$app->controller->module->id]),
                        'fileUpload' => Url::to(['/admin/redactor/upload', 'dir' => Yii::$app->controller->module->id]),
                        'plugins' => ['fullscreen']
                    ]
                ]);

            case Properties::TYPE_CODE:

                return AceWidget::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'language' => 'html',
                    'attributes' => ['style' => 'width: 100%;min-height: 200px;']
                ]);

            case Properties::TYPE_MAP_PLACEMARK:
            case Properties::TYPE_MAP_POLYGON:
            case Properties::TYPE_MAP_POLYLINE:
            case Properties::TYPE_MAP_ROUTE:

                return MapConstructorWidget::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'search_id' => $settings->search_id,
                    'type' => preg_replace('/map_/', '', $type)
                ]);

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
                        $url = '/admin/'.$name_module.'/properties/get-list-items-category';
                        break;
                    case Properties::TYPE_MULTICATEGORY:
                        $url = '/admin/'.$name_module.'/properties/get-list-multicategories';
                        break;
                    case Properties::TYPE_CATEGORY:
                        $url = '/admin/'.$name_module.'/properties/get-list-categories';
                        break;
                }

                return Select2::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'data' => $data,
                    'options' => [
                        'placeholder' => Yii::t('catalog', 'Enter value..'),
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
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                ]);

            case Properties::TYPE_IMAGE:
            case Properties::TYPE_FILE:

                return FileInputWidget::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'settings' => $settings,
                    'type' => $type
                ]);

            default:
                return Html::activeInput('string', $this->model, $this->attribute, ['class' => 'form-control']);
        }

    }
}
