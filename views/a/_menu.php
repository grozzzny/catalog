<?php

use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var Category|null $currentCategory
 */
$action = $this->context->action->id;
$module = $this->context->module->id;
?>


<? if($action === 'index'):?>

    <div class="col-sm-2">
        <a href="<?= Url::to([
                '/admin/'.$module.'/a/create',
                'slug' => Category::SLUG,
                'category_id' => $currentCategory->id
            ] + ['category' => Yii::$app->request->get('category')]) ?>" class="btn btn-primary btn-block">
            <?= Yii::t('gr', 'Add category') ?>
        </a>
    </div>

    <div class="col-sm-2">
        <a href="<?= Url::to([
                '/admin/'.$module.'/a/create',
                'slug' => Item::SLUG
            ] + ['category' => Yii::$app->request->get('category')]) ?>" class="btn btn-success btn-block">
            <?= Yii::t('gr', 'Add item') ?>
        </a>
    </div>

<? endif;?>