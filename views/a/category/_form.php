<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use grozzzny\widgets\switch_checkbox\SwitchCheckbox;
use yii\easyii\widgets\DateTimePicker;
use grozzzny\catalog\models\Category;
use kartik\select2\Select2;
use yii\easyii\widgets\Redactor;
use yii\helpers\Url;

$module = $this->context->module->id;

if(!empty(Yii::$app->request->get('category'))) $current_model->parent_id = Yii::$app->request->get('category');


?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'model-form']
]); ?>

<?= $this->render('../_image_file', ['model' => $current_model, 'attribute' => 'image_file'])?>
<?= $form->field($current_model, 'image_file')->fileInput() ?>


<?= $form->field($current_model, 'title')->input('text', [
    'onkeyup' => "$('#category-slug').val(translit(this.value))",
    'onblur' => "$('#category-slug').val(translit(this.value))",
]) ?>
<?= $form->field($current_model, 'slug') ?>

<?= $form->field($current_model, 'parent_id')->widget(Select2::className(),[
    'data' => Category::listCategories(),
    'options' => ['placeholder' => 'Введите значение ...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>

<?= $form->field($current_model, 'views')->input('text',['disabled' => true]) ?>

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