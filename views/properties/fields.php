<?php
use yii\helpers\Html;

use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;
use grozzzny\widgets\switch_checkbox\assets\SwitchCheckboxAsset;
use kartik\select2\Select2Asset;
use kartik\select2\ThemeBootstrapAsset;
use grozzzny\catalog\assets\PropertiesAsset;
use yii\jui\JuiAsset;

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);
SwitchCheckboxAsset::register($this);
Select2Asset::register($this);
ThemeBootstrapAsset::register($this);
PropertiesAsset::register($this);
JuiAsset::register($this);

/**
 * @var \yii\web\View $this
 * @var \grozzzny\catalog\models\Category $model
 * @var \grozzzny\catalog\models\Category|null $currentCategory
 * @var string $title
 */


$this->title = Yii::t('gr', 'Catalog');
?>

<?= $this->render('../a/_breadcrumbs', ['currentCategory' => $currentCategory, 'title' => $title]) ?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title ?></h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?=Html::beginForm('', 'post', ['class' => 'properties-all-categories']) ?>

        <? foreach ($model->parentsCategories as $category):  ?>

            <?=Html::beginTag('table', [
                'class' => 'table table-hover properties-category',
                'data-category' => $category->id
            ])?>

            <thead>
            <caption><?=Yii::t('gr', 'Properties category: «{category}»', ['category' => $category->title])?></caption>
            <tr>
                <th><?=Yii::t('gr','Title')?></th>
                <th><?=Yii::t('gr','Slug')?></th>
                <th><?=Yii::t('gr','Type')?></th>
                <th width="120"><?=Yii::t('gr','Params')?></th>
                <th width="150"></th>
            </tr>
            </thead>
            <tbody>

            <? foreach ($category->properties as $property):?>
                <?=$this->render('_field', ['property' => $property]);?>
            <? endforeach;?>

            <? if(empty($category->properties)):?>
                <?=$this->render('_field', ['property' => null]);?>
            <? endif;?>

            </tbody>

            <?=Html::endTag('table')?>

        <? endforeach;?>

        <?= Html::tag('div','', ['class' => 'alert response-server', 'style' => 'display:none;']) ?>

        <?= Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('gr', 'Save'), [
            'class' => 'btn btn-primary',
            'onclick' => 'properties.save(this);'
        ]) ?>

        <?= Html::button('<i class="glyphicon glyphicon-plus font-12"></i> '.Yii::t('gr', 'Add property'), [
            'class' => 'btn btn-default',
            'onclick' => 'properties.clone(this);'
        ]) ?>

        <?=Html::endForm() ?>
    </div>
</div>


<?
$i18n = json_encode([
    'select_category' => Yii::t('gr','Select category..'),
    'key' => Yii::t('gr','Key'),
    'value' => Yii::t('gr','Value'),
    'add_option' => Yii::t('gr','Add option'),
    'remove_option' => Yii::t('gr','Remove option'),
    'settings_type_not_apply' => Yii::t('gr','Settings to this type do not apply'),
    'options_type_not_apply' => Yii::t('gr','Options to this type do not apply'),
    'multiple' => Yii::t('gr','Multiple'),
    'filter_range' => Yii::t('gr','Filter range'),
    'group' => Yii::t('gr','Group'),
    'name' => Yii::t('gr','Name'),
    'params' => Yii::t('gr','Params'),
    'add_validation_rule' => Yii::t('gr','Add validation rule:'),
    'remove_rule' => Yii::t('gr','Remove rule'),
    'integer_number' => Yii::t('gr','Integer number'),
    'minimum_value' => Yii::t('gr','Minimum value'),
    'maximum_value' => Yii::t('gr','Maximum value'),
    'floating_point_number' => Yii::t('gr','Floating point number'),
    'boolean_true_false' => Yii::t('gr','Boolean (true or false)'),
    'number' => Yii::t('gr','Number'),
    'string' => Yii::t('gr','String'),
    'date' => Yii::t('gr','Date'),
    'format_date' => Yii::t('gr','Format. Example: dd-mm-yy'),
    'required' => Yii::t('gr','Required'),
    'email' => Yii::t('gr','Email'),
    'url' => Yii::t('gr','Url'),
    'image' => Yii::t('gr','Image'),
    'extensions_image' => Yii::t('gr','Extensions. Example: png, jpg, gif'),
    'extensions_file' => Yii::t('gr','Extensions. Example: pdf, doc'),
    'file' => Yii::t('gr','File'),
    'unique' => Yii::t('gr','Unique'),
    'filter' => Yii::t('gr','Filter'),
    'filter_trim' => Yii::t('gr','Trim the lines on both sides'),
    'filter_register' => Yii::t('gr','Transform the register'),
    'compare_validator' => Yii::t('gr','Comparison Validator for value OR atribute'),
    'operator_equally' => Yii::t('gr','Equally'),
    'operator_not_equal' => Yii::t('gr','Not equal'),
    'operator_more' => Yii::t('gr','More'),
    'operator_more_or_equal' => Yii::t('gr','More or equal'),
    'operator_less' => Yii::t('gr','Less'),
    'operator_less_or_equal' => Yii::t('gr','Less or equal'),
    'select_property' => Yii::t('gr','Select property..'),
    'regular_expression_validator' => Yii::t('gr','Regular Expression Validator'),
    'pattern' => Yii::t('gr','Pattern. Example: /^[a-z]\\w*$/i'),
    'validator_default' => Yii::t('gr','Validator assigning a default value'),
    'validator_safe' => Yii::t('gr','Validator safe'),
    'filter_show' => Yii::t('gr','Show in filter'),
    'filter_show_admin' => Yii::t('gr','Show in filter in Admin panel'),
    'characteristic' => Yii::t('gr','Characteristic'),
    'scenarios' => Yii::t('gr','Scenarios'),
    'description' => Yii::t('gr','Description'),
    'example_1' => Yii::t('gr','Example #1'),
    'example_2' => Yii::t('gr','Example #2'),
], JSON_UNESCAPED_UNICODE);

$script = <<< JS
    var properties_i18n = $i18n;
JS;

$this->registerJs($script, \yii\web\View::POS_BEGIN);
?>