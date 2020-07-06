<?

use grozzzny\catalog\models\Category;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var \yii\data\ActiveDataProvider $data
 * @var \grozzzny\catalog\models\Item $item
 * @var Category|null $currentCategory
 */
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
        <th><?=Yii::t('catalog', 'Area of visibility')?></th>

        <th width="100"><?=$sort->link('status');?></th>

        <th width="40"></th>

    </tr>
    </thead>
    <tbody>
    <? foreach($data->models as $item) : ?>
    <tr>
        <td><?= $item->primaryKey ?></td>
        <td>
            <a href="<?= Url::to(['default/edit', 'id' => $item->id, 'slug' => $item::SLUG, 'category_id' => $currentCategory->id]) ?>">
                <?= $item->title ?>
            </a>
        </td>
        <td>
            <?= $item->slug ?>
        </td>
        <td>
            <?= $item->categoriesToString ?>
        </td>

        <td class="status vtop">
            <?= Html::checkbox('', $item->status == true, [
                'class' => 'my-switch',
                'data-slug' => $item::SLUG,
                'data-id' => $item->id,
                'data-link' => Url::to(['default/']),
            ]) ?>
        </td>
        <td>
            <div class="btn-group btn-group-sm" role="group">
                <a href="<?= Url::to(['default/delete', 'id' => $item->primaryKey, 'slug' => $item::SLUG]) ?>" class="btn btn-default confirm-delete" title="<?= Yii::t('catalog', 'Delete item') ?>"><span class="glyphicon glyphicon-remove"></span></a>
            </div>
        </td>
    <tr>
    <? endforeach; ?>
    </tbody>
</table>
