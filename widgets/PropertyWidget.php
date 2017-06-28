<?php
namespace grozzzny\catalog\widgets;

use bl\ace\AceWidget;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Properties;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\easyii\widgets\DateTimePicker;
use yii\easyii\widgets\Redactor;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
use yii\widgets\InputWidget;


class PropertyWidget extends InputWidget
{

    public function run()
    {
        $type = $this->model->getType($this->attribute);
        $settings = $this->model->getSettings($this->attribute);
        $options = $this->model->getOptions($this->attribute);

        switch ($type){
            case Properties::TYPE_STRING:
                return Html::activeInput('string', $this->model, $this->attribute, ['class' => 'form-control']);
            case Properties::TYPE_INTEGER:
                return Html::activeInput('number', $this->model, $this->attribute, ['class' => 'form-control']);
            case Properties::TYPE_SELECT:

                return Select2::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'data' => $options,
                    'options' => ['placeholder' => Yii::t('gr', 'Enter value..')],
                    'pluginOptions' => [
                        'allowClear' => true,
                        //'multiple' => true,
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

            case Properties::TYPE_CATEGORY:

                return Select2::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                    'data' => Category::findOne([$options->category_id])->listItems,
                    'options' => ['placeholder' => Yii::t('gr', 'Enter value..')],
                    'pluginOptions' => [
                        'allowClear' => true,
                        //'multiple' => true,
                    ],
                ]);

            case Properties::TYPE_DATETIME:

                return DateTimePicker::widget([
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                ]);

            case Properties::TYPE_IMAGE:
            case Properties::TYPE_FILE:

                $html = Html::beginTag('div', ['class' => 'list_files_'.$this->attribute]);
                if($settings->multiple){
                    $initialPreview = [];
                    foreach ($this->model->{$this->attribute} as $value){
                        $html .= Html::hiddenInput($this->model->formName().'['.$this->attribute.'][]', $value);
                        $initialPreview[]= $value;
                    }
                    $js_function = 'function(event, data, previewId, index){addValues(event, data, previewId, index);}';
                    $js_function_remove = 'function(event, key, jqXHR, data){removeValues(event, key, jqXHR, data);}';
                }else{
                    $html .= Html::activeHiddenInput($this->model, $this->attribute);
                    $js_function = 'function(event, data, previewId, index){addValue(event, data, previewId, index);}';
                    $js_function_remove = 'function(event, key, jqXHR, data){removeValue(event, key, jqXHR, data);}';
                    $initialPreview = [$this->model->{$this->attribute}];
                    $initialPreviewConfig = [
                        [
                            'caption' => basename($this->model->{$this->attribute}),
                            'size' => filesize(Yii::getAlias('@webroot').$this->model->{$this->attribute}),
                            'width' => '120px',
                            'url' => '/admin/newcatalog/properties/file-delete',
                            'key' => $this->model->{$this->attribute}
                        ]
                    ];
                }
                $html .= Html::endTag('div');

                $html .= FileInput::widget([
                        'name' => 'file_input_' . $this->attribute,
                        'pluginOptions' => [
                            'uploadUrl' => Url::to(['/admin/newcatalog/properties/file-upload']),
                            'uploadExtraData' => [
                                'attribute' => $this->attribute,
                                'append' => $settings->multriple === true
                            ],
                            //'maxFileCount' => 10,
                            'initialPreview' => $initialPreview,
                            'initialPreviewAsData'=>true,
                            'initialPreviewConfig' => $initialPreviewConfig,
                            'overwriteInitial'=>false,
                        ],
                        'pluginEvents' => [
                            'fileuploaded' => $js_function,
                            'filedeleted' => $js_function_remove,
                        ]
                    ]);

                $script = <<< JS
                function addValues(event, data, previewId, index) {
                    console.log(data);
                }
                function addValue(event, data, previewId, index) {
                    $('#dataproperties-{$this->attribute}').val(data.response.initialPreview);
                }
                function removeValue(event, key, jqXHR, data) {
                    $('#dataproperties-{$this->attribute}').val('');
                }
                function removeValues(event, key, jqXHR, data) {
                    console.log(data);
                }
JS;
                $view = $this->getView();
                $view->registerJs($script, $view::POS_READY);

                return $html;

            default:
                return Html::activeInput('string', $this->model, $this->attribute, ['class' => 'form-control']);
        }

    }
}