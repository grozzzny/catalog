<?
use yii\helpers\Html;
use grozzzny\catalog\models\Properties;
?>

<?=Html::beginTag('tr', [
    'class' => 'property',
    'data-id' => $property->id,
    'data-options' => empty($property->optionsJson) ? '{}' : $property->optionsJson,
    'data-settings' => empty($property->settingsJson) ? '{}' : $property->settingsJson,
    'data-validations' => empty($property->validationsJson) ? '[["string"]]' : $property->validationsJson,
    'data-old-validations' => empty($property->validationsJson) ? '[["string"]]' : $property->validationsJson,
    'data-old-type' => empty($property->type) ? Properties::TYPE_STRING : $property->type
]) ?>

    <td>
        <?=Html::input('text', 'title', $property->title, [
            'class' => 'form-control',
            'onkeyup' => "properties.translit(this)",
            'onblur' => "properties.translit(this)",
        ]) ?>
    </td>

    <td>
        <?=Html::input('text', 'slug', $property->slug, ['class' => 'form-control']) ?>
    </td>

    <td>
        <?=Html::dropDownList('type',((empty($property->type)) ? Properties::TYPE_STRING : $property->type), Properties::getListType(),['class' => 'form-control', 'onchange' => 'properties.selectType(this);']) ?>
    </td>

    <td>
        <?= $this->render('_modal_options'); ?>
        <?= $this->render('_modal_settings'); ?>
        <?= $this->render('_modal_validation'); ?>

        <div class="btn-group btn-group-sm" role="group">
            <a onclick="properties.modal.open.options(this);" class="btn btn-default" title="<?=Yii::t('gr','Options')?>">
                <span class="glyphicon glyphicon-list-alt"></span>
            </a>

            <a onclick="properties.modal.open.settings(this);" class="btn btn-default" title="<?=Yii::t('gr','Settings')?>">
                <span class="glyphicon glyphicon-cog"></span>
            </a>

            <a onclick="properties.modal.open.validations(this);" class="btn btn-default" title="<?=Yii::t('gr','Validation rule')?>">
                <span class="glyphicon glyphicon-bullhorn"></span>
            </a>
        </div>
    </td>

    <td class="text-right">
        <div class="btn-group btn-group-sm" role="group" style="text-align: left;">
<!--            <a href="#" class="btn btn-default move-up" title="Переместить выше">-->
<!--                <span class="glyphicon glyphicon-arrow-up"></span>-->
<!--            </a>-->
<!---->
<!--            <a href="#" class="btn btn-default move-down" title="Переместить ниже">-->
<!--                <span class="glyphicon glyphicon-arrow-down"></span>-->
<!--            </a>-->

            <a onclick="properties.clone(this);" class="btn btn-default" style="color: green;" title="<?=Yii::t('gr','Add property')?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

            <a onclick="properties.remove(this);" class="btn btn-default color-red delete-field" title="<?=Yii::t('gr','Remove property')?>">
                <span class="glyphicon glyphicon-remove"></span>
            </a>

        </div>
    </td>

<?= Html::endTag('tr')?>

