<?php
use yii\helpers\Html;

use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;
use grozzzny\widgets\switch_checkbox\assets\SwitchCheckboxAsset;
use kartik\select2\Select2Asset;
use kartik\select2\ThemeBootstrapAsset;
use grozzzny\catalog\assets\PropertiesAsset;

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);
SwitchCheckboxAsset::register($this);
Select2Asset::register($this);
ThemeBootstrapAsset::register($this);
PropertiesAsset::register($this);



$this->title = Yii::t('gr', 'Properties');
?>

<?= $this->render('../a/_menu', ['current_model' => $current_model]) ?>
<?= $this->render('../a/_submenu', ['current_model' => $current_model]) ?>

<?=Html::beginForm('', 'post', ['class' => 'properties-all-categories']) ?>

<? foreach ($current_model->parentsCategories as $category):  ?>

    <?=Html::beginTag('table', [
        'class' => 'table table-hover properties-category',
        'data-category' => $category->id
    ])?>

        <thead>
            <caption><?=Yii::t('easyii', 'Properties category: «{category}»', ['category' => $category->title])?></caption>
            <tr>
                <th><?=Yii::t('gr','Title')?></th>
                <th><?=Yii::t('gr','Slug')?></th>
                <th><?=Yii::t('gr','Type')?></th>
                <th width="120"><?=Yii::t('gr','Params')?></th>
                <th width="100"></th>
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

<?= Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('easyii', 'Save fields'), [
    'class' => 'btn btn-primary',
    'onclick' => 'properties.save(this);'
]) ?>

<?= Html::button('<i class="glyphicon glyphicon-plus font-12"></i> '.Yii::t('easyii', 'Add property'), [
    'class' => 'btn btn-default',
    'onclick' => 'properties.clone(this);'
]) ?>

<?=Html::endForm() ?>

<?
$i18n = json_encode([
    'name' => Yii::t('gr','dsf')
], JSON_UNESCAPED_UNICODE);

$script = <<< JS
    var properties_i18n = {
        i18n : $i18n
    };
JS;

$this->registerJs($script, \yii\web\View::POS_BEGIN);
?>


