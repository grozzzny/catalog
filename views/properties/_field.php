<?
use yii\helpers\Html;
use grozzzny\catalog\models\Properties;
use kartik\select2\Select2;
use yii\jui\AutoComplete;
use yii\helpers\Url;
use yii\web\JsExpression;
?>

<?=Html::beginTag('tr', [
    'class' => 'property',
    'data-id' => empty($property->id) ? : $property->id,
    'data-index' => empty($property->index) ? 0 : $property->index,
    'data-options' => empty($property->optionsJson) ? '{}' : $property->optionsJson,
    'data-settings' => empty($property->settingsJson) ? '{}' : $property->settingsJson,
    'data-validations' => empty($property->validationsJson) ? '[["string"]]' : $property->validationsJson,
    'data-old-validations' => empty($property->validationsJson) ? '[["string"]]' : $property->validationsJson,
    'data-old-type' => empty($property->type) ? Properties::TYPE_STRING : $property->type
]) ?>

    <td>
        <?=Html::input('text', 'title', $property->title, [
            'class' => 'form-control',
          //  'required' => true,
            'size' => 100,
            'onfocus' => "properties.initAutoComplete(this)",
            'onblur' => "properties.translit(this)",
            //'onblur' => "properties.translit(this)",
        ]) ?>
    </td>

    <td>
        <?=Html::input('text', 'slug', $property->slug, [
            'class' => 'form-control',
           // 'required' => true,
            'pattern' => "^[a-z_]{1}[a-z0-9_]*",
            'size' => 100,
        ]) ?>
    </td>

    <td>
        <?=Html::dropDownList('type',((empty($property->type)) ? Properties::TYPE_STRING : $property->type), Properties::getListType(),['class' => 'form-control', 'onchange' => 'properties.selectType(this);']) ?>
    </td>

    <td>
        <?= $this->render('_modal_options'); ?>
        <?= $this->render('_modal_settings'); ?>
        <?= $this->render('_modal_validation'); ?>

        <div class="btn-group btn-group-sm" role="group">
            <a onclick="properties.modal.open.options(this);" class="btn btn-default" title="<?=Yii::t('catalog','Options')?>">
                <span class="glyphicon glyphicon-list-alt"></span>
            </a>

            <a onclick="properties.modal.open.settings(this);" class="btn btn-default" title="<?=Yii::t('catalog','Settings')?>">
                <span class="glyphicon glyphicon-cog"></span>
            </a>

            <a onclick="properties.modal.open.validations(this);" class="btn btn-default" title="<?=Yii::t('catalog','Validation rule')?>">
                <span class="glyphicon glyphicon-bullhorn"></span>
            </a>
        </div>
    </td>

    <td class="text-right">
        <div class="btn-group btn-group-sm" role="group" style="text-align: left;">
            <a onclick="properties.moveUp(this);" class="btn btn-default" title="<?=Yii::t('catalog','Move up')?>">
                <span class="glyphicon glyphicon-arrow-up"></span>
            </a>

            <a onclick="properties.moveDown(this);" class="btn btn-default" title="<?=Yii::t('catalog','Move down')?>">
                <span class="glyphicon glyphicon-arrow-down"></span>
            </a>

            <a onclick="properties.clone(this);" class="btn btn-default" style="color: green;" title="<?=Yii::t('catalog','Add property')?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

            <a onclick="properties.remove(this);" class="btn btn-default color-red delete-field" title="<?=Yii::t('catalog','Remove property')?>">
                <span class="glyphicon glyphicon-remove"></span>
            </a>

        </div>
    </td>

<?= Html::endTag('tr')?>

