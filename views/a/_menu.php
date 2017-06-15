<?php
use yii\helpers\Url;

$action = $this->context->action->id;
$module = $this->context->module->id;

$items = [];
foreach ($current_model->models as $model){
    $items[] = [
        'label' => Yii::t('gr', $model::TITLE),
        'url' => ['a/', 'slug' => $model::SLUG],
        'active' => $model::SLUG == $current_model::SLUG,
    ];
}

?>

<?=\yii\bootstrap\Nav::widget([
    'items' => $items,
    'options' => ['class' =>'nav nav-tabs', 'style'=> 'margin-bottom: 30px;']
]);?>



<ul class="nav nav-pills">

    <li <?= ($action === 'index') ? 'class="active"' : '' ?>>
        <a href="<?= Url::to(['/admin/'.$module, 'slug' => $current_model::SLUG]) ?>">
            <?php if($action != 'index') : ?>
                <i class="glyphicon glyphicon-chevron-left font-12"></i>
            <?php endif; ?>
            <?= Yii::t('easyii', 'List') ?>
        </a>
    </li>
    <li <?= ($action === 'create') ? 'class="active"' : '' ?>>
        <a href="<?= Url::to([
        '/admin/'.$module.'/a/create',
        'slug' => $current_model::SLUG
        ]) ?>">
            <?= Yii::t('easyii', 'Create') ?>
        </a>
    </li>

    <? if($action === 'index'):?>

        <?= $this->render($current_model::SLUG.'/_filter', [
            'current_model' => $current_model
        ]) ?>

    <? endif;?>

</ul>
<br/>