<?
use yii\helpers\Url;
use yii\helpers\Html;
use grozzzny\catalog\models\Base;

/**
 * @var \yii\web\View $this
 * @var \grozzzny\catalog\models\Category $item
 */
$module = $this->context->module->id;

$sort = $data->getSort();
?>
<table class="table table-hover">
    <thead>
    <tr>
        <th width="40">
            <?=$sort->link('id');?>
        </th>

        <th>
            <?=$sort->link('title');?>
        </th>

        <th>
            <?=$sort->link('slug');?>
        </th>

        <th>
            <?=$sort->link('order_num');?>
        </th>

        <th width="100">
            <?=$sort->link('status');?>
        </th>


        <th width="120"></th>


    </tr>
    </thead>
    <tbody>
    <? foreach($data->models as $item) : ?>
    <tr>
        <td>
            <?= $item->primaryKey ?>
        </td>

        <td>
            <a href="<?= $item->linkAdmin ?>">
                <?= $item->title ?>
            </a>
        </td>

        <td>
            <?= $item->slug ?>
        </td>

        <td>
            <?= $item->order_num ?>
        </td>

        <td class="status vtop">
            <?= Html::checkbox('', $item->status == Base::STATUS_ON, [
                'class' => 'my-switch',
                'data-slug' => $item::SLUG,
                'data-id' => $item->id,
                'data-link' => Url::to(['/admin/'.$module.'/a/']),
            ]) ?>
        </td>

        <td>
            <div class="btn-group btn-group-sm" role="group">

                <a href="<?= $item->linkEdit ?>" class="btn btn-default" title="<?= Yii::t('easyii2', 'Edit') ?>">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>

                <a href="<?= $item->linkProperties ?>" class="btn btn-default" title="<?= Yii::t('gr', 'Properties') ?>">
                    <span class="glyphicon glyphicon-inbox"></span>
                </a>

                <a href="<?= $item->linkDelete ?>" class="btn btn-default confirm-delete" title="<?= Yii::t('easyii2', 'Delete item') ?>">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>

            </div>
        </td>
    <tr>
    <? endforeach; ?>
    </tbody>
</table>