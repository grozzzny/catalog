<?php
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\DataProperties;
use grozzzny\catalog\widgets\FilterWidget;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var Category|null $currentCategory
 */

$dataProperties = new DataProperties($currentCategory->allProperties);
$dataProperties->setAttributes(Yii::$app->request->get());

Modal::begin([
    'header' => '<h2>'.Yii::t('catalog','Filter').'</h2>',
    'options' => ['class' => 'options', 'id' => 'modal_filter'],
    'footer' => '<button onclick="$(this).parents(\'form\').submit();" type="button" class="btn btn-default" data-dismiss="modal">'.Yii::t('catalog','Apply').'</button>'
]);

$i = 0;
foreach ($dataProperties->attributes as $attribute => $value){
    if(!$dataProperties->getSettings($attribute)->filter_show_admin) continue;
    $i++;
    echo Html::beginTag('div', ['class' => 'form-group']);
    echo FilterWidget::widget([
            'model' => $dataProperties,
            'attribute' => $attribute,
            'query_param' => Yii::$app->request->get()
        ]);
    echo Html::endTag('div');
}

if($i == 0) echo Html::tag('p', Yii::t('catalog', 'Filters not found'));

Modal::end();
?>

