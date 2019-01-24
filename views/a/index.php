<?php

use grozzzny\catalog\models\Item;
use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;
use grozzzny\catalog\models\Category;
use yii\widgets\LinkPager;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataCategory
 * @var \yii\data\ActiveDataProvider $dataItem
 * @var Category|null $currentCategory
 */

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);
$this->title = Yii::t('gr', 'Catalog');
?>

<?= $this->render('_breadcrumbs', ['currentCategory' => $currentCategory, 'title' => null]) ?>

<div class="row">
    <?= $this->render('_menu', ['currentCategory' => $currentCategory]) ?>
    <?= $this->render('_filter', ['currentCategory' => $currentCategory]) ?>
</div>
<br/>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= empty($currentCategory) ? Yii::t('gr', 'List of top-level categories') : Yii::t('gr', 'List of subcategories of the category <b>«{0}»</b>', [$currentCategory->title]) ?></h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <? if($dataCategory->count > 0) : ?>

            <?= $this->render(Category::SLUG.'/_list', [
                'data' => $dataCategory,
                'current_model' => $currentCategory
            ]) ?>

            <?= LinkPager::widget([
                'pagination' => $dataCategory->pagination
            ]) ?>

        <? else : ?>
            <p><?= Yii::t('gr', 'Categories not found') ?></p>
        <? endif; ?>
    </div>

</div>

<? if ($currentCategory != null):?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><?= empty($currentCategory) ? Yii::t('gr', 'List all items') : Yii::t('gr', 'List of items of the category <b>«{0}»</b>', [$currentCategory->title]) ?></h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <? if($dataItem->count > 0) : ?>

            <?= $this->render(Item::SLUG.'/_list', [
                'data' => $dataItem,
                'currentCategory' => $currentCategory
            ]) ?>

        <? if($dataItem->pagination): ?>
            <?= LinkPager::widget([
                'pagination' => $dataItem->pagination
            ]) ?>
        <? endif;?>

        <? else : ?>
            <p><?= Yii::t('gr', 'Items not found') ?></p>
        <? endif; ?>
    </div>
</div>
<? endif;?>
