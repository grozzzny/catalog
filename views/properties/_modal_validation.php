<?
use yii\bootstrap\Modal;
?>

<?
Modal::begin([
    'header' => '<h2>Validation</h2>',
    'options' => ['class' => 'modal_validation']
]);

echo 'Say hello...';

Modal::end();
?>