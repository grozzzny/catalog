<?php
use grozzzny\catalog\models\Category;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View $this
 * @var Category $currentCategory
 * @var string|null $title
 */

$breadcrumbs_arr = [];
if(!empty($currentCategory)){
    if(Yii::$app->controller->action->id != 'index'){
        $breadcrumbs_arr = $currentCategory->getBreadcrumbs(true);
        $breadcrumbs_arr += [
            'label' => $title
        ];
    } else {
        $breadcrumbs_arr = $currentCategory->getBreadcrumbs(false);
    }
} else {
    if(Yii::$app->controller->action->id != 'index'){
        $breadcrumbs_arr += [
            'label' => $title
        ];
    }
}

echo  Breadcrumbs::widget([
    'links' => $breadcrumbs_arr,
    'homeLink' => [
        'label' => Yii::t('gr', 'Categories'),
        'url' => ['/admin/' . Yii::$app->controller->module->id]
    ],
    'encodeLabels' => false,
    'tag' => 'ol'
])?>

