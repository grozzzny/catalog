<?php
use yii\helpers\Url;
use grozzzny\catalog\models\Category;

/**
 * @var \yii\web\View $this
 * @var Category $model
 */

$action = $this->context->action->id;
$module = $this->context->module->id;
?>

<ul class="nav nav-tabs">
    <li <?= ($action === 'edit') ? 'class="active"' : '' ?>>
        <a href="<?= Url::to(['/admin/'.$module.'/a/edit', 'id' => $model->primaryKey, 'slug' => $model::SLUG]) ?>">
            <?= Yii::t('easyii2', 'Edit') ?>
        </a>
    </li>


    <? if($model->enablePhotoManager()): ?>
    <li <?= ($action === 'photos') ? 'class="active"' : '' ?>>
        <a href="<?= Url::to(['/admin/'.$module.'/a/photos', 'id' => $model->primaryKey, 'slug' => $model::SLUG]) ?>">
            <span class="glyphicon glyphicon-camera"></span>
            <?= Yii::t('easyii2', 'Photos') ?>
        </a>
    </li>
    <? endif;?>

    <? if($model::SUBMENU_FILES): ?>
        <li <?= ($action === 'files') ? 'class="active"' : '' ?>>
            <a href="<?= Url::to(['/admin/'.$module.'/a/files', 'id' => $model->primaryKey, 'slug' => $model::SLUG]) ?>">
                Аудиозаписи
            </a>
        </li>
    <? endif;?>

    <? if(Yii::$app->controller->actionParams['slug'] == Category::SLUG): ?>
        <li <?= (Yii::$app->controller->route === 'admin/'.$module.'/properties/fields') ? 'class="active"' : '' ?>>
            <a href="<?= Url::to(['/admin/'.$module.'/properties', 'id' => $model->primaryKey, 'slug' => $model::SLUG]) ?>">
                <?=Yii::t('gr', 'Properties')?>
            </a>
        </li>
    <? endif;?>

</ul>
<br>