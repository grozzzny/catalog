<?
use yii\bootstrap\Modal;
?>

<?
Modal::begin([
    'header' => '<h2>'.Yii::t('gr','Validation rule').'</h2>',
    'options' => ['class' => 'validations'],
    'footer' => '<button onclick="properties.validations.save(this);" type="button" class="btn btn-default" data-dismiss="modal">'.Yii::t('gr','Apply').'</button>'
]);
Modal::end();
?>