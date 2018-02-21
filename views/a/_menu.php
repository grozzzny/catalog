<?php

use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var Category|null $currentCategory
 */
?>


<? if(Yii::$app->controller->action->id === 'index'):?>

    <div class="col-sm-2">
        <a href="<?= Url::to(['/admin/'.Yii::$app->controller->module->id . '/a/create', 'slug' => Category::SLUG, 'category_id' => $currentCategory->id]) ?>" class="btn btn-primary btn-block">
            <?= Yii::t('gr', 'Add category') ?>
        </a>
    </div>

    <div class="col-sm-2">
        <a href="<?= Url::to(['/admin/'.Yii::$app->controller->module->id . '/a/create', 'slug' => Item::SLUG, 'category_id' => $currentCategory->id]) ?>" class="btn btn-success btn-block">
            <?= Yii::t('gr', 'Add item') ?>
        </a>
    </div>

<? endif;?>