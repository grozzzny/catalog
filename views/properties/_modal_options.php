<?
use yii\bootstrap\Modal;
?>

<?
Modal::begin([
    'header' => '<h2>'.Yii::t('catalog','Options').'</h2>',
    'options' => ['class' => 'options', 'tabindex'=>''],
    'footer' => '<button onclick="properties.options.save(this);" type="button" class="btn btn-default" data-dismiss="modal">'.Yii::t('catalog','Apply').'</button>'
]);
Modal::end();
?>
