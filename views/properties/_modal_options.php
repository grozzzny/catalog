<?
use yii\bootstrap\Modal;
?>

<?
Modal::begin([
    'header' => '<h2>Options</h2>',
    'options' => ['class' => 'modal_options']
]);

echo 'Say hello...';

Modal::end();
?>