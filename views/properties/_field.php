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
        ['min' => 2,'max' => 10] //'string','max'=>1000
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


<tr class='property' data-id='' data-category='' data-options='<?=$json_options?>' data-settings='<?=$json_settings?>' data-validations='<?=$json_validations?>' data-old-type="<?=Properties::TYPE_STRING?>" data-old-validations='<?=$json_validations?>' >
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
        <?=Html::dropDownList('type',Properties::TYPE_STRING, Properties::getListType(),['class' => 'form-control', 'onchange' => 'properties.selectType(this);']) ?>
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
         * При выборе типа данных, устанавливается дефолтная валидация
         * @param ob
         */
        selectType:function (ob) {
            var property = $(ob).parents('.property');
            var type = $(ob).val();
            var old_type = property.attr('data-old-type');

            //Если пользователь вернул прежний тип данных
            var new_data = (type == old_type) ? property.attr('data-old-validations') : JSON.stringify(properties.validations.defaultValidations[type]);

            property.attr('data-validations', new_data);
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
                return JSON.parse(property.attr('data-validations'));
            },

            building:function (ob) {
                var data = properties.validations.data(ob);

                var modal = properties.find(ob, '.modal.validations .modal-body');
                //var type = properties.getType(ob);

                var table = $('<table class="table table-hover"></table>');
                var thead = $('<thead></thead>').append($('<th>Name</th>')).append($('<th>Params</th>')).append($('<th width="40"></th>'));
                var tbody = $('<tbody></tbody>');

                modal.append(table.append(thead).append(tbody));

                //Создание правил при клике на список
                var select = $('<select class="form-control list-validators"></select>').on('change', function () {
                    properties.validations.library[$(this).val()].building(this, {});
                    $(this).find(":selected").prop('disabled',true);
                    $(this).find(":first-child").prop('selected',true);
                });

                select.append($('<option value="" selected disabled>Add validation rule</option>'));

                $.each(properties.validations.library, function (key, obj) {
                    var option = $('<option value="'+key+'">'+obj.title+'</option>');
                    select.append(option);
                });

                modal.append(select);

                //Создание правил из ранее сохраненных
                $.each(data, function (i, item) {
                    var validator = item[0];
                    var params = (item[1]) ? item[1] : {};

                    properties.validations.library[validator].building(ob, params);
                    properties.find(ob, '.modal.validations [value="'+validator+'"]').prop('disabled',true);
                });

            },

            removeValidator:function (ob) {
                var item = $(ob).parents('tr.item');
                item.parents('.modal.validations').find('.list-validators option[value="'+item.data('validator')+'"]').prop('disabled', false);
                $(ob).parents('tr.item').remove();
            },

            btnGroup:function () {
                var btnGroup = $('<div class="btn-group btn-group-sm"></div>');
                var btnRemove = $('<a onclick="properties.validations.removeValidator(this);" class="btn btn-default" style="color: #a94442;" title="Remove rule"><span class="glyphicon glyphicon-remove"></span></a>');
                return $('<td></td>').append(btnGroup.append(btnRemove));
            },

            defaultValidations:{
                string:[['string']],
                integer:[['integer']],
                select:[['safe']],
                checkbox:[['boolean']],
                html:[['safe']],
                category:[['safe']],
                datetime:[['integer']],
                image:[['image']],
                file:[['file']]
            },

            /**
             * Библиотека правил валидации
             */
            library:{
                integer:{
                    validator:'integer',
                    title:'Integer number',
                    params:['min','max'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','integer')
                            .append($('<td></td>').text('Integer number'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','number')
                                            .attr('placeholder','Minimum value')
                                            .attr('data-parametr','min')
                                            .val(params.max ? params.max : null)
                                    )
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','number')
                                            .attr('placeholder','Maximum value')
                                            .attr('data-parametr','max')
                                            .val(params.max ? params.max : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                double:{
                    validator:'double',
                    title:'Floating point number',
                    params:null,
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .data('validator','double')
                            .append($('<td></td>').text('Floating point number'))
                            .append($('<td></td>'))
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                boolean:{
                    validator:'boolean',
                    title:'Boolean (true or false)',
                    params:null,
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .data('validator','double')
                            .append($('<td></td>').text('Boolean (true or false)'))
                            .append($('<td></td>'))
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                number:{
                    validator:'number',
                    title:'Number',
                    params:['min','max'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','number')
                            .append($('<td></td>').text('Number'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','number')
                                            .attr('placeholder','Minimum value')
                                            .attr('data-parametr','min')
                                            .val(params.min ? params.min : null)
                                    )
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','number')
                                            .attr('placeholder','Maximum value')
                                            .attr('data-parametr','max')
                                            .val(params.max ? params.max : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                string:{
                    validator:'string',
                    title:'String',
                    params:['min','max'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','string')
                            .append($('<td></td>').text('String'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','number')
                                            .attr('placeholder','Minimum value')
                                            .attr('data-parametr','min')
                                            .val(params.min ? params.min : null)
                                    )
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','number')
                                            .attr('placeholder','Maximum value')
                                            .attr('data-parametr','max')
                                            .val(params.max ? params.max : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                date:{
                    validator:'date',
                    title:'Date',
                    params:['format'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','date')
                            .append($('<td></td>').text('Date'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','string')
                                            .attr('placeholder','Format. Example: dd-mm-yy')
                                            .attr('data-parametr','format')
                                            .val(params.format ? params.format : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                required:{
                    validator:'required',
                    title:'Required',
                    params:null,
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','required')
                            .append($('<td></td>').text('Required'))
                            .append($('<td></td>'))
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                email:{
                    validator:'email',
                    title:'Email',
                    params:null,
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','email')
                            .append($('<td></td>').text('Email'))
                            .append($('<td></td>'))
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                url:{
                    validator:'url',
                    title:'Url',
                    params:null,
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','url')
                            .append($('<td></td>').text('Url'))
                            .append($('<td></td>'))
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                image:{
                    validator:'image',
                    title:'Image',
                    params:['extensions'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','image')
                            .append($('<td></td>').text('Image'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','string')
                                            .attr('placeholder','Extensions. Example: png, jpg, gif')
                                            .attr('data-parametr','extensions')
                                            .val(params.extensions ? params.extensions : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                file:{
                    validator:'file',
                    title:'File',
                    params:['extensions'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','file')
                            .append($('<td></td>').text('File'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','string')
                                            .attr('placeholder','Extensions. Example: pdf, doc')
                                            .attr('data-parametr','extensions')
                                            .val(params.extensions ? params.extensions : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                unique:{
                    validator:'unique',
                    title:'Unique',
                    params:null,
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','unique')
                            .append($('<td></td>').text('Unique'))
                            .append($('<td></td>'))
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                filter:{
                    validator:'filter',
                    title:'Filter',
                    params:['filter'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','filter')
                            .append($('<td></td>').text('Filter'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<select name="parametr" class="form-control">')
                                            .attr('data-parametr','filter')
                                            .append(
                                                $('<option></option>')
                                                    .val('trim')
                                                    .text('Trim the lines on both sides')
                                            )
                                            .append(
                                                $('<option></option>')
                                                    .val('strtolower')
                                                    .text('Transform the register')
                                            )
                                            .val(params.filter ? params.filter : 'trim')
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                compare:{
                    validator:'compare',
                    title:'Comparison Validator for value OR atribute',
                    params:['compareValue','operator'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','compare')
                            .append($('<td></td>').text('Comparison Validator for value OR atribute'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','number')
                                            .attr('placeholder','Value')
                                            .attr('data-parametr','compareValue')
                                            .val(params.compareValue ? params.compareValue : null)
                                    )
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','string')
                                            .attr('placeholder','Attribute')
                                            .attr('data-parametr','compareAttribute')
                                            .val(params.compareAttribute ? params.compareAttribute : null)
                                    )
                                    .append(
                                        $('<select name="parametr" class="form-control">')
                                            .attr('data-parametr','operator')
                                            .append($('<option></option>').val('=').text('Equally'))
                                            .append($('<option></option>').val('!=').text('Not equal'))
                                            .append($('<option></option>').val('>').text('More'))
                                            .append($('<option></option>').val('>=').text('More or equal'))
                                            .append($('<option></option>').val('<').text('Less'))
                                            .append($('<option></option>').val('<=').text('Less or equal'))
                                            .val(params.operator ? params.operator : '=')
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                match:{
                    validator:'match',
                    title:'Regular Expression Validator',
                    params:['pattern'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','match')
                            .append($('<td></td>').text('Regular Expression Validator'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','string')
                                            .attr('placeholder','Pattern. Example: /^[a-z]\\w*$/i')
                                            .attr('data-parametr','pattern')
                                            .val(params.pattern ? params.pattern : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                default:{
                    validator:'default',
                    title:'Validator assigning a default value',
                    params:['value'],
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','default')
                            .append($('<td></td>').text('Validator assigning a default value'))
                            .append(
                                $('<td></td>')
                                    .append(
                                        $('<input name="parametr" class="form-control">')
                                            .attr('type','string')
                                            .attr('placeholder','Value')
                                            .attr('data-parametr','value')
                                            .val(params.value ? params.value : null)
                                    )
                            )
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                },
                safe:{
                    validator:'safe',
                    title:'Validator safe',
                    params:null,
                    building:function (ob, params) {
                        var tbody = properties.find(ob, '.modal.validations').find('tbody');

                        var tr = $('<tr class="item"></tr>')
                            .attr('data-validator','safe')
                            .append($('<td></td>').text('Validator safe'))
                            .append($('<td></td>'))
                            .append(properties.validations.btnGroup());

                        tbody.append(tr);
                    }
                }
            },

            save:function (ob) {
                var modal = properties.find(ob, '.modal.validations .modal-body');

                var new_data = [];

                $.each(modal.find('[data-validator]'), function (i, ob) {
                    var validator_arr = [$(ob).data('validator')];

                    var params = {};
                    $.each($(ob).find('[data-parametr]'), function (q, elem) {
                        var elem_param = $(elem);
                        if(elem_param.val() != '') params[elem_param.data('parametr')] = elem_param.val();
                    });

                    if(!$.isEmptyObject(params)) validator_arr.push(params);

                    new_data.push(validator_arr);
                });

                var data_json = JSON.stringify(new_data);

                var new_type = properties.getType(ob);

                $(ob).parents('.property').attr('data-validations', data_json);
                $(ob).parents('.property').attr('data-old-validations', data_json);
                $(ob).parents('.property').attr('data-old-type', new_type);
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

                    properties.validations.building(ob);
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


