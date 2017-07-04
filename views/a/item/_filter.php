<?
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use grozzzny\catalog\models\Category;
use yii\bootstrap\Modal;
use grozzzny\catalog\widgets\FilterWidget;
use grozzzny\catalog\models\DataProperties;
use grozzzny\catalog\models\Properties;
?>

<?=Html::beginForm(Url::toRoute(['a/', 'slug' => $current_model::SLUG]), 'get');?>

    <li style="float:right; margin-left: 20px;">
        <?=Html::input('string', 'text', Yii::$app->request->get('text'),[
            'placeholder'=> Yii::t('gr', 'Search..'),
            'class'=> 'form-control',
        ])?>
    </li>

    <? if(!empty(Yii::$app->request->get('category'))): ?>
        <li style="float:right; margin-left: 20px;">

            <button class="form-control" onclick="$('#modal_filter').modal('show'); event.preventDefault();">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </button>

            <?
            Modal::begin([
                'header' => '<h2>'.Yii::t('gr','Filter').'</h2>',
                'options' => ['class' => 'options', 'id' => 'modal_filter'],
                'footer' => '<button onclick="$(this).parents(\'form\').submit();" type="button" class="btn btn-default" data-dismiss="modal">'.Yii::t('gr','Apply').'</button>'
            ]);
            ?>


                <?
                $current_model->categories = [Yii::$app->request->get('category')];
                $dataProperties = new DataProperties($current_model->properties);
                $dataProperties->setAttributes(Yii::$app->request->get());
                ?>
                <? foreach ($dataProperties->getAttributes() as $attribute => $value):?>
                    <div class="form-group">
                        <?= FilterWidget::widget([
                            'model' => $dataProperties,
                            'attribute' => $attribute,
                            'query_param' => Yii::$app->request->get()
                        ])?>
                    </div>
                <? endforeach;?>

            <? Modal::end(); ?>
        </li>
    <? endif;?>

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
                'width' => 400
            ],
        ]); ?>

    </li>

<?=Html::endForm();?>