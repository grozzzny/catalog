<?php

use grozzzny\admin\widgets\file_input\ImageInputWidget;
use grozzzny\catalog\models\Item;
use yii\helpers\Html;
use yii\redactor\widgets\Redactor;
use yii\widgets\ActiveForm;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use yii\helpers\Url;
use kartik\select2\Select2;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\widgets\PropertyWidget;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * @var \yii\web\View $this
 * @var Item $model
 * @var Category $currentCategory
 */

/**
 * @var Category $model_category
 */
$model_category = Yii::createObject(['class' => Category::class]);
?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
]); ?>

<?= $form->field($model, 'image_file')->widget(ImageInputWidget::className()) ?>

<?= $form->field($model, 'title')->input('text', [
    //'onkeyup' => "if($('#item-slug').val() ==='') $('#item-slug').val(translit(this.value))",
    'onblur' => "if($('#item-slug').val() ==='') $('#item-slug').val(translitCatalog(this.value))",
]) ?>
<?= $form->field($model, 'slug') ?>

<?=$form->field($model, 'categories')->widget(Select2::className(),[
    'data' => ArrayHelper::map($model_category::findAll(['id' => $model->categories]), 'id', 'fullTitle'),
    'pluginOptions' => [
        'placeholder' => Yii::t('catalog', 'Select category..'),
        'allowClear' => true,
        'multiple' => true,
        'ajax' => [
            'url' => Url::to(['properties/get-list-categories']),
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { 
               return {
                    q:params.term
                }; 
            }'),
        ],
    ],
])->label(Yii::t('catalog', 'Area of visibility'));;
?>

<? foreach ($model->dataProperties->getAttributes() as $attribute => $value):?>
    <?= $form->field($model->dataProperties, $attribute)->widget(PropertyWidget::className()) ?>
<? endforeach;?>

<?= $form->field($model, 'price') ?>
<?= $form->field($model, 'discount') ?>
<?= $form->field($model, 'views')->input('text',['disabled' => true]) ?>

<?= $form->field($model, 'order_num') ?>

<?= $form->field($model, 'short')->textarea() ?>

<?= $form->field($model, 'description')->widget(Redactor::className(), [
    'clientOptions' => [
        'minHeight' => '400px',
        'imageManagerJson' => ['/redactor/upload/image-json'],
        'imageUpload' => ['/redactor/upload/image'],
        'fileUpload' => ['/redactor/upload/file'],
        'lang' => 'ru',
        'plugins' => [
            'clips',
            'counter',
            'definedlinks',
            'filemanager',
            'fontcolor',
            'fontfamily',
            'fontsize',
            'fullscreen',
            'imagemanager',
            'limiter',
            'table',
            'textdirection',
            'textexpander',
            'video',
        ]
    ]
])?>

<?=SwitchCheckbox::widget([
    'model' => $model,
    'attributes' => [
        'status'
    ]
])?>

<br>

<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
