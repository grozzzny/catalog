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


$this->title = Yii::t('catalog', 'Catalog');
?>

<?= $this->render('../default/_breadcrumbs', ['currentCategory' => $currentCategory, 'title' => $title]) ?>

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
            <caption><?=Yii::t('catalog', 'Properties category: «{category}»', ['category' => $category->title])?></caption>
            <tr>
                <th><?=Yii::t('catalog','Title')?></th>
                <th><?=Yii::t('catalog','Slug')?></th>
                <th><?=Yii::t('catalog','Type')?></th>
                <th width="120"><?=Yii::t('catalog','Params')?></th>
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

        <?= Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('catalog', 'Save'), [
            'class' => 'btn btn-primary',
            'onclick' => 'properties.save(this);'
        ]) ?>

        <?= Html::button('<i class="glyphicon glyphicon-plus font-12"></i> '.Yii::t('catalog', 'Add property'), [
            'class' => 'btn btn-default',
            'onclick' => 'properties.clone(this);'
        ]) ?>

        <?=Html::endForm() ?>
    </div>
</div>


<?
$i18n = json_encode([
    'select_category' => Yii::t('catalog','Select category..'),
    'key' => Yii::t('catalog','Key'),
    'value' => Yii::t('catalog','Value'),
    'add_option' => Yii::t('catalog','Add option'),
    'remove_option' => Yii::t('catalog','Remove option'),
    'settings_type_not_apply' => Yii::t('catalog','Settings to this type do not apply'),
    'options_type_not_apply' => Yii::t('catalog','Options to this type do not apply'),
    'multiple' => Yii::t('catalog','Multiple'),
    'filter_range' => Yii::t('catalog','Filter range'),
    'group' => Yii::t('catalog','Group'),
    'name' => Yii::t('catalog','Name'),
    'params' => Yii::t('catalog','Params'),
    'add_validation_rule' => Yii::t('catalog','Add validation rule:'),
    'remove_rule' => Yii::t('catalog','Remove rule'),
    'integer_number' => Yii::t('catalog','Integer number'),
    'minimum_value' => Yii::t('catalog','Minimum value'),
    'maximum_value' => Yii::t('catalog','Maximum value'),
    'floating_point_number' => Yii::t('catalog','Floating point number'),
    'boolean_true_false' => Yii::t('catalog','Boolean (true or false)'),
    'number' => Yii::t('catalog','Number'),
    'string' => Yii::t('catalog','String'),
    'date' => Yii::t('catalog','Date'),
    'format_date' => Yii::t('catalog','Format. Example: dd-mm-yy'),
    'required' => Yii::t('catalog','Required'),
    'email' => Yii::t('catalog','Email'),
    'url' => Yii::t('catalog','Url'),
    'image' => Yii::t('catalog','Image'),
    'extensions_image' => Yii::t('catalog','Extensions. Example: png, jpg, gif'),
    'extensions_file' => Yii::t('catalog','Extensions. Example: pdf, doc'),
    'file' => Yii::t('catalog','File'),
    'unique' => Yii::t('catalog','Unique'),
    'filter' => Yii::t('catalog','Filter'),
    'filter_trim' => Yii::t('catalog','Trim the lines on both sides'),
    'filter_register' => Yii::t('catalog','Transform the register'),
    'compare_validator' => Yii::t('catalog','Comparison Validator for value OR atribute'),
    'operator_equally' => Yii::t('catalog','Equally'),
    'operator_not_equal' => Yii::t('catalog','Not equal'),
    'operator_more' => Yii::t('catalog','More'),
    'operator_more_or_equal' => Yii::t('catalog','More or equal'),
    'operator_less' => Yii::t('catalog','Less'),
    'operator_less_or_equal' => Yii::t('catalog','Less or equal'),
    'select_property' => Yii::t('catalog','Select property..'),
    'regular_expression_validator' => Yii::t('catalog','Regular Expression Validator'),
    'pattern' => Yii::t('catalog','Pattern. Example: /^[a-z]\\w*$/i'),
    'validator_default' => Yii::t('catalog','Validator assigning a default value'),
    'validator_safe' => Yii::t('catalog','Validator safe'),
    'filter_show' => Yii::t('catalog','Show in filter'),
    'filter_show_admin' => Yii::t('catalog','Show in filter in Admin panel'),
    'characteristic' => Yii::t('catalog','Characteristic'),
    'variations' => Yii::t('catalog','Variations'),
    'scenarios' => Yii::t('catalog','Scenarios'),
    'description' => Yii::t('catalog','Description'),
    'example_1' => Yii::t('catalog','Example #1'),
    'example_2' => Yii::t('catalog','Example #2'),
    'search_id' => Yii::t('catalog','ID input Search'),
], JSON_UNESCAPED_UNICODE);

$script = <<< JS
    var properties_i18n = $i18n;
JS;

$this->registerJs($script, \yii\web\View::POS_BEGIN);
?>
