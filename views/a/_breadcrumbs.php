<?php
use grozzzny\catalog\models\Category;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View $this
 * @var Category $currentCategory
 */
?>

<?= Breadcrumbs::widget([
    'links' => $currentCategory->breadcrumbs,
    'homeLink' => [
        'label' => 'Список категорий',
        'url' => '/admin/' . Yii::$app->controller->module->id
    ],
    'encodeLabels' => false,
    'tag' => 'ol'
])?>

