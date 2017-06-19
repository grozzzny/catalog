<?
use yii\helpers\Html;
use grozzzny\catalog\models\Properties;
use yii\bootstrap\Modal;
?>

<tr class="property" data-id="" data-category="" data-options="" data-settings="" data-vakidation_rule="">
    <td>
        <?=Html::input('text', 'title','',[
            'class' => 'form-control',
            'onkeyup' => "properties.translit(this)",
            'onblur' => "properties.translit(this)",
        ]) ?>
    </td>

    <td>
        <?=Html::input('text', 'slug','',['class' => 'form-control']) ?>
    </td>

    <td>
        <?=Html::dropDownList('type',Properties::TYPE_STRING, Properties::getListType(),['class' => 'form-control']) ?>
    </td>

    <td>
        <?= $this->render('_modal_options'); ?>
        <?= $this->render('_modal_settings'); ?>
        <?= $this->render('_modal_validation'); ?>

        <div class="btn-group btn-group-sm" role="group">
            <a href="#" onclick="properties.open_modal(this, '.modal_options');" class="btn btn-default" title="Значения">
                <span class="glyphicon glyphicon-list-alt"></span>
            </a>

            <a href="#" onclick="properties.open_modal(this, '.modal_settings');" class="btn btn-default" title="Настройки">
                <span class="glyphicon glyphicon-cog"></span>
            </a>

            <a href="#" onclick="properties.open_modal(this, '.modal_validation');" class="btn btn-default" title="Правила валидации">
                <span class="glyphicon glyphicon-bullhorn"></span>
            </a>
        </div>
    </td>

    <td class="text-right">
        <div class="btn-group btn-group-sm" role="group" style="text-align: left;">
            <a href="#" class="btn btn-default move-up" title="Переместить выше">
                <span class="glyphicon glyphicon-arrow-up"></span>
            </a>

            <a href="#" class="btn btn-default move-down" title="Переместить ниже">
                <span class="glyphicon glyphicon-arrow-down"></span>
            </a>

            <a href="#" class="btn btn-default" style="color: green;" title="Добавить запись">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

            <a href="#" class="btn btn-default color-red delete-field" title="Удалить запись">
                <span class="glyphicon glyphicon-remove"></span>
            </a>

        </div>
    </td>
</tr>


<script>
    var properties = {
        open_modal:function (ob, modal_class) {
            $(ob).parents('.property').find(modal_class).modal('show');
        },
        translit:function (ob) {
            $(ob).parents('.property').find('[name="slug"]').val(translit(ob.value))
        }
    }
</script>