<?php

namespace grozzzny\catalog\widgets\date_time_picker;

use yii\easyii\helpers\Data;
use yii\helpers\Html;
use yii\helpers\Json;

class DateTimePicker extends \yii\easyii\widgets\DateTimePicker
{

    public $inputId;
    public $placeholder;

    private $_defaultOptions = [
        'showTodayButton' => true,
    ];

    public function init()
    {
        $this->options = array_merge($this->_defaultOptions, $this->options);

        $this->widgetId = 'dtp-'.$this->name;
        $this->inputId = 'dtp-input-'.$this->name;
        $this->registerAssetBundle();
        $this->registerScript();
    }

    public function run()
    {
        echo '
            <div class="input-group date" id="'.$this->widgetId.'">
                '.Html::textInput('', '', ['class' => 'form-control', 'placeholder' => $this->placeholder]).'
                '.Html::hiddenInput($this->name, $this->value, ['id' => $this->inputId]).'
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
        ';
    }

    public function registerScript()
    {
        if(empty($this->options['locale'])){
            $this->options['locale'] = Data::getLocale();
        }
        $clientOptions = (count($this->options)) ? Json::encode($this->options) : '';
        $time = $this->value ? $this->value : time();
        $this->getView()->registerJs('
            (function(){    
                var dtpContainer = $("#'.$this->widgetId.'");
    
                dtpContainer.datetimepicker('.$clientOptions.')
                .on("dp.change", function (e) {
                    $("#'.$this->inputId.'").val(e.date.unix());
                })
                .data("DateTimePicker")
                '.(!empty($this->value) ? '.date(moment('.($time * 1000).'))' : '').';
    
                $("[type=text]", dtpContainer).focus(function(e){
                    dtpContainer.data("DateTimePicker").show();
                });
            })();
        ');
    }

}