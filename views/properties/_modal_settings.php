<?
use yii\bootstrap\Modal;
?>

<?
Modal::begin([
    'header' => '<h2>'.Yii::t('gr','Settings').'</h2>',
    'options' => ['class' => 'settings'],
    'footer' => '<button onclick="properties.settings.save(this);" type="button" class="btn btn-default" data-dismiss="modal">'.Yii::t('gr','Apply').'</button>'
]);
Modal::end();
?>