<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use yii\easyii\widgets\DateTimePicker;
use yii\easyii\widgets\Redactor;
use yii\helpers\Url;

$module = $this->context->module->id;
?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
]); ?>

<?= $this->render('../_image_file', ['model' => $current_model, 'attribute' => 'image_file'])?>
<?= $form->field($current_model, 'image_file')->fileInput() ?>

<?= $form->field($current_model, 'title') ?>
<?= $form->field($current_model, 'slug') ?>
<?= $form->field($current_model, 'price') ?>
<?= $form->field($current_model, 'discount') ?>
<?= $form->field($current_model, 'views') ?>

<?= $form->field($current_model, 'short')->textarea() ?>

<?= $form->field($current_model, 'description')->widget(Redactor::className(),[
    'options' => [
        'minHeight' => 400,
        'imageUpload' => Url::to(['/admin/redactor/upload', 'dir' => Yii::$app->controller->module->id]),
        'fileUpload' => Url::to(['/admin/redactor/upload', 'dir' => Yii::$app->controller->module->id]),
        'plugins' => ['fullscreen']
    ]
])?>

<?=SwitchCheckbox::widget([
    'model' => $current_model,
    'attributes' => [
        'status'
    ]
])?>

<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
