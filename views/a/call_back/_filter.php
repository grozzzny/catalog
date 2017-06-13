<?
use yii\bootstrap\Html;
use yii\helpers\Url;
?>

<?=Html::beginForm(Url::toRoute(['a/', 'alias' => $current_model::ALIAS]), 'get');?>

    <li style="float:right; margin-left: 20px;">
        <?=Html::input('string', 'text', Yii::$app->request->get('text'),[
            'placeholder'=> 'Поиск...',
            'class'=> 'form-control',
        ])?>
    </li>

<?=Html::endForm();?>