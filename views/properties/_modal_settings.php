<?
use yii\bootstrap\Modal;
?>

<?
Modal::begin([
    'header' => '<h2>Settings</h2>',
    'options' => ['class' => 'modal_settings']
]);

echo 'Say hello...';

Modal::end();
?>