<?
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use grozzzny\catalog\models\Category;
?>

<?=Html::beginForm(Url::toRoute(['a/', 'slug' => $current_model::SLUG]), 'get');?>

    <li style="float:right; margin-left: 20px;">
        <?=Html::input('string', 'text', Yii::$app->request->get('text'),[
            'placeholder'=> Yii::t('gr', 'Search..'),
            'class'=> 'form-control',
        ])?>
    </li>

    <li style="float:right; margin-left: 20px;">

        <?= Select2::widget([
            'name' => 'category',
            'value' => Yii::$app->request->get('category'),
            'data' => Category::listCategories(),
            'options' => [
                'placeholder' => Yii::t('gr', 'Select category..'),
                'onchange' => 'submit();',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'width' => 480
            ],
        ]); ?>

    </li>

<?=Html::endForm();?>