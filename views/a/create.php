<?
use yii\bootstrap\BootstrapPluginAsset;
use grozzzny\catalog\assets\ModuleAsset;
use grozzzny\catalog\models\Category;
use yii\easyii2\widgets\Photos;

BootstrapPluginAsset::register($this);
ModuleAsset::register($this);

/**
 * @var \yii\web\View $this
 * @var \grozzzny\catalog\models\Category|\grozzzny\catalog\models\Item $model
 * @var \grozzzny\catalog\models\Category|null $currentCategory
 * @var string $title
 */

$this->title = Yii::t('gr', 'Catalog');
?>

<?= $this->render('_breadcrumbs', ['currentCategory' => $currentCategory, 'title' => $title]) ?>

<div class="box <?= $model::SLUG == Category::SLUG ? 'box-primary' : 'box-success'?>">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title ?></h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?= $this->render($model::SLUG.'/_form', ['model' => $model]) ?>
    </div>
</div>


<? if($model->enablePhotoManager() && Yii::$app->controller->action->id == 'edit'): ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Yii::t('gr', 'Add photos') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= Photos::widget(['model' => $model])?>
        </div>
    </div>
<? endif;?>