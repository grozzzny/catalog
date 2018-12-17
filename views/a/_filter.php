<?php

use grozzzny\catalog\CatalogModule;
use grozzzny\catalog\models\Category;
use grozzzny\catalog\models\Item;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var \yii\web\View $this
 * @var Category|null $currentCategory
 */
?>

<?=Html::beginForm(Url::toRoute(['a/']), 'get');?>

    <div class="col-sm-1 pull-right">
        <?=Html::submitButton('Ok', [
            'class'=> 'form-control',
        ])?>
    </div>

    <div class="col-sm-2 pull-right">
        <?=Html::input('string', 'search_text', Yii::$app->request->get('search_text'),[
            'placeholder'=> Yii::t('gr', 'Search..'),
            'class'=> 'form-control',
        ])?>
    </div>

    <div class="col-sm-3 pull-right">

        <?=Select2::widget([
            'name' => 'category_id',
            'value' => $currentCategory->id,
            'data' => [$currentCategory->id => $currentCategory->fullTitle],
            'options' => [
                'onchange' => 'submit();',
            ],
            'pluginOptions' => [
                'placeholder' => Yii::t('gr', 'Select category..'),
                'allowClear' => true,
                //'width' => 400,
                'ajax' => [
                    'url' => '/admin/'.CatalogModule::getNameModule().'/properties/get-list-categories',
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { 
                       return {
                            q:params.term
                        }; 
                    }'),
                ],
            ],
        ]);?>

    </div>

    <? if(!empty($currentCategory)): ?>
        <div class="col-sm-1 pull-right">

            <button type="button" class="form-control" onclick="$('#modal_filter').modal('show'); event.preventDefault();">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </button>

            <?= $this->render('_modal_filter', ['currentCategory' => $currentCategory]) ?>
        </div>
    <? endif;?>

<?=Html::endForm();?>
