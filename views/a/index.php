<?php
use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);

$this->title = Yii::t('gr', $current_model::TITLE);
?>

<?= $this->render('_menu', ['current_model' => $current_model]) ?>

<? if($data->count > 0) : ?>

    <?= $this->render($current_model::SLUG.'/_list', [
        'data' => $data,
        'current_model' => $current_model
    ]) ?>

    <?= yii\widgets\LinkPager::widget([
        'pagination' => $data->pagination
    ]) ?>

<? else : ?>
    <p><?= Yii::t('easyii', 'No records found') ?></p>
<? endif; ?>