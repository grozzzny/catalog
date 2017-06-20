<?php
use yii\helpers\Html;

use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);



$this->title = Yii::t('gr', 'Properties');
?>

<?= $this->render('../a/_menu', ['current_model' => $current_model]) ?>
<?= $this->render('../a/_submenu', ['current_model' => $current_model]) ?>

<?= Html::button('<i class="glyphicon glyphicon-plus font-12"></i> '.Yii::t('easyii', 'Add field'), ['class' => 'btn btn-default', 'id' => 'addField']) ?>

<?=Html::beginForm() ?>

<?=Html::beginTag('table', ['class' => 'table table-hover'])?>

    <thead>
        <tr>
            <th><?=Yii::t('gr','Title')?></th>
            <th><?=Yii::t('gr','Slug')?></th>
            <th><?=Yii::t('gr','Type')?></th>
            <th width="120"><?=Yii::t('gr','Params')?></th>
            <th width="150"></th>
        </tr>
    </thead>
    <tbody>

    <?=$this->render('_field');?>

    </tbody>


<?=Html::endTag('table')?>

<?= Html::button('<i class="glyphicon glyphicon-ok"></i> '.Yii::t('easyii', 'Save fields'), ['class' => 'btn btn-primary', 'id' => 'saveCategoryBtn']) ?>


<?=Html::endForm() ?>


