<?php
use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;
use yii\widgets\Breadcrumbs;
use grozzzny\catalog\models\Category;
use grozzzny\widgets\switch_checkbox\assets\SwitchCheckboxAsset;

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);

$this->title = Yii::t('gr', $current_model::TITLE);
?>

<?= $this->render('_menu', ['current_model' => $current_model]) ?>

<? if(Yii::$app->request->get('slug') == Category::SLUG):?>
    <?
    $category = Category::findOne(Yii::$app->request->get('category'));
    echo Breadcrumbs::widget([
        'links' => $category->breadcrumbs,
        'homeLink' => [
            'label' => 'Список категорий',
            'url' => '/admin/' . Yii::$app->controller->module->id
        ],
        'encodeLabels' => false,
        'tag' => 'ol'
    ])?>
<? endif;?>

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