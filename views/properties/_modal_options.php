<?
use yii\bootstrap\Modal;
?>

<?
Modal::begin([
    'header' => '<h2>'.Yii::t('gr','Options').'</h2>',
    'options' => ['class' => 'options'],
    'footer' => '<button onclick="properties.options.save(this);" type="button" class="btn btn-default" data-dismiss="modal">'.Yii::t('gr','Save').'</button>'
]);
Modal::end();
?>