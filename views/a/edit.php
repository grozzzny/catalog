<?
use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);

?>

<? $this->title = 'Редактировать';?>

<?= $this->render('_menu', ['current_model' => $current_model]) ?>

<?= $this->render('_submenu', ['current_model' => $current_model]) ?>

<?= $this->render($current_model::SLUG.'/_form', ['current_model' => $current_model]) ?>