<?
use yii\helpers\Html;
use grozzzny\catalog\models\Properties;
?>

<?

$options = [
    '1' => '1 Этаж',
    '2' => '2 Этаж',
    '3' => '3 Этаж',
];
$json_options = json_encode($options, JSON_UNESCAPED_UNICODE);

$validations = [
    [
        'string', //double, boolean, number, string, date, required
        'max' => 10 //'string','max'=>1000
    ],
    [
        'required',
    ]
];
$json_validations = json_encode($validations, JSON_UNESCAPED_UNICODE);


$settings = [
    'multiple' => true,
    'filter_range' => false
];
$json_settings = json_encode($settings, JSON_UNESCAPED_UNICODE);
?>


<tr class='property' data-id='' data-category='' data-options='<?=$json_options?>' data-settings='<?=$json_settings?>' data-validations='<?=$json_validations?>'>
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


<style>
    .material-switch > input[type="checkbox"] {
        display: none;
    }

    .material-switch > label {
        cursor: pointer;
        height: 0px;
        position: relative;
        width: 40px;
    }

    .material-switch > label::before {
        background: rgb(0, 0, 0);
        box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
        border-radius: 8px;
        content: '';
        height: 16px;
        margin-top: -8px;
        position:absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 40px;
    }
    .material-switch > label::after {
        background: rgb(255, 255, 255);
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 24px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 24px;
    }
    .material-switch > input[type="checkbox"]:checked + label::before {
        background: inherit;
        opacity: 0.5;
    }
    .material-switch > input[type="checkbox"]:checked + label::after {
        background: inherit;
        left: 20px;
    }

</style>

<script>
    var properties = {
        /**
         * Поиск дочерних элементов от родителя
         * @param ob
         * @param selector
         * @returns {*|jQuery}
         */
        find:function (ob, selector) {
            var property = $(ob);
            if(!property.hasClass('property')) property = property.parents('.property');
            return property.find(selector);
        },


        /**
         * Получит "Тип" свойства
         * @param ob
         * @returns {*|jQuery}
         */
        getType:function (ob) {
            var property = $(ob);
            if(!property.hasClass('property')) property = property.parents('.property');
            return property.find('[name="type"]').val();
        },


        /**
         * Опции
         */
        options:{

            /**
             * Получение значений "Опции"
             * @param ob
             * @returns {*|jQuery}
             */
            data:function (ob) {
                var property = $(ob);
                if(!property.hasClass('property')) property = property.parents('.property');
                return property.data('options');
            },

            /**
             * Построение модального окна свойства с типом "Категория"
             * @param ob
             */
            buildingCategory:function (ob) {
            },

            /**
             * Построение модального окна свойства с типом "Строка"
             * @param ob
             */
            buildingSelect:function (ob) {
                var data_options = properties.options.data(ob);

                var modal = properties.find(ob, '.modal.options .modal-body');
                var table = $('<table class="table table-hover"></table>');
                var thead = $('<thead></thead>').append($('<th>Key</th>')).append($('<th>Value</th>')).append($('<th width="100"></th>'));
                var tbody = $('<tbody></tbody>');

                modal.append(table.append(thead).append(tbody));

                $.each(data_options, function (key, value) {
                    tbody.append(properties.options.item(key, value));
                });

                //Если объект со значениями пустой
                if(jQuery.isEmptyObject(data_options)) tbody.append(properties.options.item('', ''));

                modal.append(table.append(thead).append(tbody));
                modal.append($('<button onclick="properties.options.cloneOption(this);" type="button" class="btn btn-default"><i class="glyphicon glyphicon-plus font-12"></i> Add option</button>'));

            },

            /**
             * Метод создания одной опции key=>value
             * @param key
             * @param value
             */
            item:function (key, value) {
                var tr = $('<tr class="item"></tr>');

                tr.append($('<td><input name="key" type="text" class="form-control" value="'+key+'"></td>'));
                tr.append($('<td><input onkeyup="properties.options.translit(this);" onblur="properties.options.translit(this);" name="value" type="text" class="form-control" value="'+value+'"></td>'));


                var btnGroup = $('<div class="btn-group btn-group-sm"></div>');
                var btnClone = $('<a onclick="properties.options.cloneOption(this);" class="btn btn-default" style="color: green;" title="Add option"><span class="glyphicon glyphicon-plus"></span></a>');
                var btnRemove = $('<a onclick="properties.options.removeOption(this);" class="btn btn-default" style="color: #a94442;" title="Add option"><span class="glyphicon glyphicon-remove"></span></a>');

                return tr.append($('<td></td>').append(btnGroup.append(btnClone).append(btnRemove)));
            },

            /**
             * Метод клонирования опции
             * @param ob
             */
            cloneOption:function (ob) {
                var item = $(ob).parents('tr.item');
                if(item.length == 0) item = $(ob).parents('.modal').find('tr:last');
                var item_clone = item.clone();
                item_clone.find('input').val('');
                item.after(item_clone);
            },

            /**
             * Метод удаления опции
             * @param ob
             */
            removeOption:function (ob) {
                var item = $(ob).parents('tr.item');
                if(item.parent().children().length != 1) item.remove();
            },

            save:function (ob) {
                var modal = properties.find(ob, '.modal.options .modal-body');

                var new_data = {};

                $.each(modal.find('.item'), function (i, ob) {
                    var value = $(ob).find('[name="value"]').val();
                    var key = $(ob).find('[name="key"]').val();
                    if(value != "" && key != "") new_data[key] = value;
                });

                $(ob).parents('.property').data('options', new_data);
            },

            /**
             * Транслитерация наименования ключей свойств
             * @param ob
             */
            translit:function (ob) {
                $(ob).parents('.item').find('[name="key"]').val(translit(ob.value))
            }
        },

        /**
         * Настройки
         */
        settings:{
            /**
             * Получение значений "Настройки"
             * @param ob
             * @returns {*|jQuery}
             */
            data:function (ob) {
                var property = $(ob);
                if(!property.hasClass('property')) property = property.parents('.property');
                return property.data('settings');
            },

            /**
             * Построение модального окна "Настройки"
             * @param ob
             */
            building:function (ob) {
                var data = properties.settings.data(ob);
                var modal = properties.find(ob, '.modal.settings .modal-body');

                var type = properties.getType(ob);

                if(jQuery.inArray(type, ['html', 'datetime']) != -1) modal.html('Settings to this type do not apply');

                //Параметр - множественности
                if(jQuery.inArray(type, ['string', 'select', 'checkbox', 'category', 'image', 'file']) != -1) modal.append(properties.settings.parametr('multiple', data.multiple, 'Multiple'));

                //Параметр диапазона для фильтра
                if(type == 'integer') modal.append(properties.settings.parametr('filter_range', data.filter_range, 'Filter range'));

            },

            /**
             * Создание нового параметра для "Настроек"
             * @param key
             * @param value - true | false
             * @param label - Описание свойства
             */
            parametr:function (key, value, label) {
                var li = $('<li class="list-group-item"></li>').text(label);
                var div = $('<div class="material-switch pull-right">&nbsp;</div>');
                var id = "settings-" + key;
                div.append($('<input id="'+id+'" name="settings-key" type="checkbox"/>').attr('data-key', key).prop('checked', value));
                div.append($('<label for="'+id+'" class="label-success"></label>'));

                return li.append(div);
            },

            /**
             * Сохранение значений "Настройка"
             * @param ob
             */
            save:function (ob) {
                var modal = properties.find(ob, '.modal.settings .modal-body');

                var new_data = {};

                $.each(modal.find('[name="settings-key"]'), function (i, ob) {
                    new_data[$(ob).data('key')] = $(ob).prop('checked');
                });

                $(ob).parents('.property').data('settings', new_data);
            }
        },

        /**
         * Валидация
         */
        validations:{
            /**
             * Получение значений "Правила валидации"
             * @param ob
             * @returns {*|jQuery}
             */
            data:function (ob) {
                var property = $(ob);
                if(!property.hasClass('property')) property = property.parents('.property');
                return property.data('validations');
            },

            save:function (ob) {

            }
        },

        /**
         * Модальное окно
         */
        modal:{

            /**
             * Набор методов открытия модальных окон
             */
            open:{

                options:function (ob) {
                    var modal = properties.find(ob, '.modal.options');
                    modal.modal('show');
                    modal.find('.modal-body').empty();

                    switch (properties.getType(ob)){
                        case 'select':
                            properties.options.buildingSelect(ob);
                            break;
                        case 'category':
                            properties.options.buildingCategory(ob);
                            break;
                        default:
                            modal.find('.modal-body').html('Options to this type do not apply');
                    }
                },

                settings:function (ob) {
                    var modal = properties.find(ob, '.modal.settings');
                    modal.modal('show');
                    modal.find('.modal-body').empty();

                    properties.settings.building(ob);
                },

                validations:function (ob) {
                    var modal = properties.find(ob, '.modal.validations');
                    modal.modal('show');
                    modal.find('.modal-body').empty();
                    modal.find('.modal-body').html('Validations to this type do not apply');
                }
            }
        },


        /**
         * Транслитерация наименования свойства
         * @param ob
         */
        translit:function (ob) {
            properties.find(ob, '[name="slug"]').val(translit(ob.value))
        }
    };
</script>


