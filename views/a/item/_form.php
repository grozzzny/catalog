<?php

use grozzzny\catalog\CatalogModule;
use grozzzny\catalog\models\Base;
use grozzzny\catalog\models\Item;
use yii\easyii2\widgets\SeoForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use yii\easyii2\widgets\Redactor;
use yii\helpers\Url;
use kartik\select2\Select2;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\widgets\PropertyWidget;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * @var \yii\web\View $this
 * @var Item $model
 */

/**
 * @var Category $model_category
 */
$model_category = Base::getModel('category');
?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
]); ?>

<?= $this->render('../_image_file', ['model' => $model, 'attribute' => 'image_file'])?>
<?= $form->field($model, 'image_file')->fileInput() ?>

<?= $form->field($model, 'title')->input('text', [
    'onkeyup' => "$('#item-slug').val(translit(this.value))",
    'onblur' => "$('#item-slug').val(translit(this.value))",
]) ?>
<?= $form->field($model, 'slug') ?>

<?=$form->field($model, 'categories')->widget(Select2::className(),[
    'data' => ArrayHelper::map($model_category::findAll(['id' => $model->categories]), 'id', 'fullTitle'),
    'pluginOptions' => [
        'placeholder' => Yii::t('gr', 'Select category..'),
        'allowClear' => true,
        'multiple' => true,
        'ajax' => [
            'url' => Url::to(['/admin/'.CatalogModule::getInstance()->id.'/properties/get-list-categories']),
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { 
               return {
                    q:params.term
                }; 
            }'),
        ],
    ],
])->label(Yii::t('gr', 'Area of visibility'));;
?>

<? foreach ($model->dataProperties->getAttributes() as $attribute => $value):?>
    <?= $form->field($model->dataProperties, $attribute)->widget(PropertyWidget::className()) ?>
<? endforeach;?>

<?= $form->field($model, 'price') ?>
<?= $form->field($model, 'discount') ?>
<?= $form->field($model, 'views')->input('text',['disabled' => true]) ?>

<?= $form->field($model, 'short')->textarea() ?>

<?= $form->field($model, 'description')->widget(Redactor::className(),[
    'options' => [
        'minHeight' => 400,
        'imageUpload' => Url::to(['/admin/redactor/upload', 'dir' => Yii::$app->controller->module->id]),
        'fileUpload' => Url::to(['/admin/redactor/upload', 'dir' => Yii::$app->controller->module->id]),
        'plugins' => [
            "alignment",
            "clips",
            "counter",
            "definedlinks",
            "fontcolor",
            "fontfamily",
            "fontsize",
            "fullscreen",
            "filemanager",
            "imagemanager",
            "inlinestyle",
            "limiter",
            "properties",
            //"source",
            "table",
            //"textdirection",
            "textexpander",
            "video",
            "codemirror",
        ],
        'codemirror:' => [
            'lineNumbers' => true,
            'mode' => 'xml',
            'indentUnit' => 4
        ],
    ]
])?>

<?=SwitchCheckbox::widget([
    'model' => $model,
    'attributes' => [
        'status'
    ]
])?>

<?= SeoForm::widget(['model' => $model]) ?>

<?= Html::submitButton(Yii::t('easyii2', 'Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
