<?php


namespace grozzzny\catalog\widgets\fileinput;


use grozzzny\catalog\models\Properties;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\InputWidget;
use Yii;

class FileInputWidget extends InputWidget
{

    public $uploadUrl = '/admin/newcatalog/properties/file-upload';
    public $deleteUrl = '/admin/newcatalog/properties/file-delete';

    public $settings;
    public $type;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->registerJs();
    }

    private function jsFnUploaded()
    {
        if($this->settings->multiple){
            return 'function(event, data, previewId, index){addValues(event, data, previewId, index);}';
        }else{
            return 'function(event, data, previewId, index){addValue(event, data, previewId, index);}';
        }
    }

    private function jsFnDeleted()
    {
        if($this->settings->multiple){
            return 'function(event, key, jqXHR, data){removeValues(event, key, jqXHR, data);}';
        }else{
            return 'function(event, key, jqXHR, data){removeValue(event, key, jqXHR, data);}';
        }
    }

    private function preview()
    {
        $values = $this->model->{$this->attribute};
        return is_array($values) ? $values : [$values];
    }

    private function previewConfig()
    {
        $initialPreviewConfig = [];
        foreach ($this->preview() as $value){
            $initialPreviewConfig[] =  [
                'type' => $this->type == Properties::TYPE_IMAGE ? 'image' : 'object',
                'filetype' => mime_content_type(Yii::getAlias('@webroot').$value),
                'caption' => basename($value),
                'size' => filesize(Yii::getAlias('@webroot').$value),
                'width' => '120px',
                'url' => $this->deleteUrl,
                'key' => $value
            ];
        }
        return $initialPreviewConfig;
    }

    private function inputFileModel()
    {
        $html = Html::beginTag('div', ['class' => 'list_files_'.$this->attribute]);
        if($this->settings->multiple){
            foreach ($this->model->{$this->attribute} as $value){
                $html .= Html::hiddenInput($this->model->formName().'['.$this->attribute.'][]', $value);
            }
        }else{
            $html .= Html::activeHiddenInput($this->model, $this->attribute);
        }
        $html .= Html::endTag('div');
        return $html;
    }


    private function registerJs()
    {
        $script = <<< JS
            function addValues(event, data, previewId, index) {
                $('.list_files_'+data.extra.attribute).append(
                    $('<input />')
                        .attr('type','hidden')
                        .attr('name', 'DataProperties['+data.extra.attribute+'][]')
                        .val(data.response.initialPreviewConfig[0].key)
                )
            }
            function addValue(event, data, previewId, index) {    
                $('#dataproperties-'+data.extra.attribute).val(data.response.initialPreviewConfig[0].key);
            }
            function removeValue(event, key, jqXHR, data) {
                $('[value="'+key+'"]').val('');
            }
            function removeValues(event, key, jqXHR, data) {
                $('[value="'+key+'"]').remove();
            }
JS;
        $view = $this->getView();
        $view->registerJs($script, $view::POS_READY);
    }

    public function run()
    {
        $html = $this->inputFileModel();
        $html .= FileInput::widget([
            'name' => 'file_input_' . $this->attribute,
            'options' => [
                'accept' => $this->type == Properties::TYPE_IMAGE ? 'image/*' : '*',
                'multiple' => $this->settings->multiple
            ],
            'pluginOptions' => [
                'uploadUrl' => Url::to([$this->uploadUrl]),
                'autoOrientImage' => true,
                'uploadExtraData' => [
                    'attribute' => $this->attribute,
                    'deleteUrl' => $this->deleteUrl,
                    'append' => $this->settings->multiple === true
                ],
                //'maxFileCount' => 10,
                'initialPreview' => $this->preview(),
                'initialPreviewAsData'=> true,
                'initialPreviewConfig' => $this->previewConfig(),
                'overwriteInitial' => false,
            ],
            'pluginEvents' => [
                'fileuploaded' => $this->jsFnUploaded(),
                'filedeleted' => $this->jsFnDeleted(),
                'filebatchselected' => "function(event, files){ 
                    $(event.target).parents('.input-group-btn').find('.fileinput-upload').click();
                }"
            ]
        ]);

        return $html;
    }


}