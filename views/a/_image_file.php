<?
use yii\easyii\helpers\Image;
use yii\helpers\Url;
?>

<? if($model->$attribute) : ?>
    <div class="form-group">
        <img src="<?= Image::thumb($model->$attribute, 240) ?>">
    </div>
    <div class="form-group">
        <a href="<?= Url::to([
            '/admin/'.Yii::$app->controller->module->id.'/a/clear-file',
            'id' => $model->id,
            'slug' => $model::SLUG,
            'attribute' => $attribute
        ]) ?>" class="text-danger confirm-delete" title="<?= Yii::t('easyii', 'Clear image')?>"><?= Yii::t('easyii', 'Clear image')?></a>
    </div>
<? endif; ?>