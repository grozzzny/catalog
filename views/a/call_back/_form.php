<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use yii\easyii\widgets\DateTimePicker;

$module = $this->context->module->id;
?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
]); ?>


<?= $form->field($current_model, 'name') ?>
<?= $form->field($current_model, 'email') ?>
<?= $form->field($current_model, 'phone') ?>
<?= $form->field($current_model, 'ip') ?>

<?= $form->field($current_model, 'description')->textarea() ?>

<?= $form->field($current_model, 'datetime')->widget(DateTimePicker::className()); ?>

<?=SwitchCheckbox::widget([
    'model' => $current_model,
    'attributes' => [
        'status'
    ]
])?>

<?= Html::submitButton(Yii::t('easyii', 'Save'), ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
