<?php
use yii\helpers\Url;
use grozzzny\catalog\models\Category;

$action = $this->context->action->id;
$module = $this->context->module->id;
?>

<ul class="nav nav-tabs">
    <li <?= ($action === 'edit') ? 'class="active"' : '' ?>>
        <a href="<?= Url::to(['/admin/'.$module.'/a/edit', 'id' => $current_model->primaryKey, 'slug' => $current_model::SLUG]) ?>">
            <?= Yii::t('easyii', 'Edit') ?>
        </a>
    </li>


    <? if($current_model::SUBMENU_PHOTOS): ?>
    <li <?= ($action === 'photos') ? 'class="active"' : '' ?>>
        <a href="<?= Url::to(['/admin/'.$module.'/a/photos', 'id' => $current_model->primaryKey, 'slug' => $current_model::SLUG]) ?>">
            <span class="glyphicon glyphicon-camera"></span>
            <?= Yii::t('easyii', 'Photos') ?>
        </a>
    </li>
    <? endif;?>

    <? if($current_model::SUBMENU_FILES): ?>
        <li <?= ($action === 'files') ? 'class="active"' : '' ?>>
            <a href="<?= Url::to(['/admin/'.$module.'/a/files', 'id' => $current_model->primaryKey, 'slug' => $current_model::SLUG]) ?>">
                Аудиозаписи
            </a>
        </li>
    <? endif;?>

    <? if(Yii::$app->controller->actionParams['slug'] == Category::SLUG): ?>
        <li <?= (Yii::$app->controller->route === 'admin/newcatalog/properties/fields') ? 'class="active"' : '' ?>>
            <a href="<?= Url::to(['/admin/'.$module.'/properties', 'id' => $current_model->primaryKey, 'slug' => $current_model::SLUG]) ?>">
                Свойства
            </a>
        </li>
    <? endif;?>

</ul>
<br>