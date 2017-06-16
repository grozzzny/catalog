<?
use yii\helpers\Url;
use yii\helpers\Html;
use grozzzny\catalog\models\Base;

$module = $this->context->module->id;

$sort = $data->getSort();
?>
<table class="table table-hover">
    <thead>
    <tr>
        <th width="50">
            <?=$sort->link('id');?>
        </th>
        <th><?=$sort->link('title');?></th>
        <th><?=$sort->link('slug');?></th>

        <th width="100"><?=$sort->link('status');?></th>

        <th width="40"></th>

    </tr>
    </thead>
    <tbody>
    <? foreach($data->models as $item) : ?>
    <tr>
        <td><?= $item->primaryKey ?></td>
        <td>
            <a href="<?= Url::to(['/admin/'.$module.'/a/edit', 'id' => $item->id, 'slug' => $item::SLUG]) ?>">
                <?= $item->title ?>
            </a>
        </td>
        <td>
            <a href="<?= Url::to(['/admin/'.$module.'/a/edit', 'id' => $item->id, 'slug' => $item::SLUG]) ?>">
                <?= $item->slug ?>
            </a>
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

                <? if($item::ORDER_NUM):?>
                    <a href="<?= Url::to(['/admin/'.$module.'/a/up', 'id' => $item->primaryKey, 'slug' => $item::SLUG]) ?>" class="btn btn-default move-up" title="<?= Yii::t('easyii', 'Move up') ?>"><span class="glyphicon glyphicon-arrow-up"></span></a>
                    <a href="<?= Url::to(['/admin/'.$module.'/a/down', 'id' => $item->primaryKey, 'slug' => $item::SLUG]) ?>" class="btn btn-default move-down" title="<?= Yii::t('easyii', 'Move down') ?>"><span class="glyphicon glyphicon-arrow-down"></span></a>
                <? endif;?>

                <a href="<?= Url::to(['/admin/'.$module.'/a/delete', 'id' => $item->primaryKey, 'slug' => $item::SLUG]) ?>" class="btn btn-default confirm-delete" title="<?= Yii::t('easyii', 'Delete item') ?>"><span class="glyphicon glyphicon-remove"></span></a>
            </div>
        </td>
    <tr>
    <? endforeach; ?>
    </tbody>
</table>