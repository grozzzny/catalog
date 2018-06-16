/**
 * Created by grozzzny on 23.06.2017.
 * MIT license
 */
var properties = {

    i18n:{
        select_category: 'Select category..',
        key: 'Key',
        value: 'Value',
        add_option: 'Add option',
        remove_option: 'Remove option',
        settings_type_not_apply: 'Settings to this type do not apply',
        options_type_not_apply: 'Options to this type do not apply',
        multiple: 'Multiple',
        filter_range: 'Filter range',
        group: 'Group',
        name: 'Name',
        params: 'Params',
        add_validation_rule: 'Add validation rule:',
        remove_rule: 'Remove rule',
        integer_number: 'Integer number',
        minimum_value: 'Minimum value',
        maximum_value: 'Maximum value',
        floating_point_number: 'Floating point number',
        boolean_true_false: 'Boolean (true or false)',
        number: 'Number',
        string: 'String',
        date: 'Date',
        format_date: 'Format. Example: dd-mm-yy',
        required: 'Required',
        email: 'Email',
        url: 'Url',
        image: 'Image',
        extensions_image: 'Extensions. Example: png, jpg, gif',
        extensions_file: 'Extensions. Example: pdf, doc',
        file: 'File',
        unique: 'Unique',
        filter: 'Filter',
        filter_trim: 'Trim the lines on both sides',
        filter_register: 'Transform the register',
        compare_validator: 'Comparison Validator for value OR atribute',
        operator_equally: 'Equally',
        operator_not_equal: 'Not equal',
        operator_more: 'More',
        operator_more_or_equal: 'More or equal',
        operator_less: 'Less',
        operator_less_or_equal: 'Less or equal',
        select_property: 'Select property..',
        regular_expression_validator: 'Regular Expression Validator',
        pattern: 'Pattern. Example: /^[a-z]\\w*$/i',
        validator_default: 'Validator assigning a default value',
        validator_safe: 'Validator safe',
        filter_show: 'Show in filter',
        filter_show_admin: 'Show in filter in Admin panel',
        characteristic: 'Characteristic',
        scenarios: 'Scenarios',
        description: 'Description',
        example_1: 'Example #1',
        example_2: 'Example #2'
    },

    init:function () {
        if(properties_i18n) properties.i18n = $.extend(properties.i18n, properties_i18n);
    },

    /**
     * Поиск дочерних элементов от родителя
     * @param ob
     * @param selector
     * @returns {*|jQuery}
     */
    find: function (ob, selector) {
        var property = $(ob);
        if (!property.hasClass('property')) property = property.parents('.property');
        return property.find(selector);
    },


    /**
     * Получит "Тип" свойства
     * @param ob
     * @returns {*|jQuery}
     */
    getType: function (ob) {
        var property = $(ob);
        if (!property.hasClass('property')) property = property.parents('.property');
        return property.find('[name="type"]').val();
    },

    /**
     * При выборе типа данных, устанавливается дефолтная валидация
     * @param ob
     */
    selectType: function (ob) {
        var property = $(ob).parents('.property');
        var type = $(ob).val();
        var old_type = property.attr('data-old-type');

        //Если пользователь вернул прежний тип данных
        var new_data = (type == old_type) ? property.attr('data-old-validations') : JSON.stringify(properties.validations.defaultValidations[type]);

        property.attr('data-validations', new_data);
    },

    /**
     * Метод очистки свойства до исходного
     * @param elem
     */
    clear: function (elem) {
        elem.attr('data-id', '');
        elem.attr('data-options', '{}');
        elem.attr('data-settings', '{}');
        elem.attr('data-validations', '[["string"]]');
        elem.attr('data-old-type', 'string');
        elem.attr('data-old-validations', '[["string"]]');

        elem.find('[name="title"]').val('');
        elem.find('[name="slug"]').val('');
        elem.find('[name="type"]').val('string');

        return elem;
    },

    /**
     * Метод клонирования свойства
     * @param ob
     */
    clone: function (ob) {
        var item = $(ob).parents('tr.property');
        if (item.length == 0) item = $(ob).parents('.properties-all-categories').find('tr:last');
        var item_clone = item.clone();

        item_clone = properties.clear(item_clone);

        item.after(item_clone);

        //Пересчитать индекс
        properties.reIndex(ob);
    },

    add: function (ob, data) {
        var elem = $(ob).parents('.property');
        elem.attr('data-id', data.id);
        elem.attr('data-options', JSON.stringify(data.options));
        elem.attr('data-settings', JSON.stringify(data.settings));
        elem.attr('data-validations', JSON.stringify(data.validations));
        elem.attr('data-old-type', data.type);
        elem.attr('data-old-validations', JSON.stringify(data.validations));

        //elem.find('[name="title"]').val('');
        setTimeout(
            function () {
                elem.find('[name="slug"]').val(data.slug)
            }, 300
        );

        elem.find('[name="type"]').val(data.type);
    },


    get controllerUrl() {
        return window.location.pathname.replace(/[^\/]*$/i,'');
    },

    initAutoComplete: function (ob) {
        $(ob).autocomplete({
            source: function(request, response) {
                var allProperties = [];
                $.each($('.property [name="slug"]'), function () {
                    var slug = $(this).val();
                    if(slug != '') allProperties.push(slug);
                });
                $.getJSON(properties.controllerUrl + "get-data-properties",
                    {
                        term: request.term,
                        properties: allProperties
                    },
                    response
                );
            },
            autoFill: true,
            select: function( event, ui ) {
                properties.add(event.target, ui.item)
            }
        });
    },

    /**
     * Метод удаления опции
     * @param ob
     */
    remove: function (ob) {
        var item = $(ob).parents('tr.property');

        var property_id = item.attr('data-id');
        var category_id = item.parents('.properties-category').attr('data-category');

        if(property_id != '') {
            var response = $.ajax({
                url: properties.controllerUrl + "remove-property",
                data: ({
                    id: property_id,
                    category_id: category_id
                }),
                async: false
            }).responseText;

            response = JSON.parse(response);

            if (response.status != 'success') {
                console.error(response);
                return false;
            }
        }
        //Если элемент не остался единственным
        if (item.parent().children().length != 1) {
            item.remove();
        } else {
            properties.clear(item);
        }

        //Пересчитать индекс
        properties.reIndex(ob);
    },


    save: function (ob) {

        //Праверка на валидацию
        var form = $('.properties-all-categories').get(0);
        if (form['checkValidity']) {
            form.reportValidity();
            if (!form.checkValidity()) return false;
        }


        var data = [];
        $.each($('.properties-category'), function () {
            var category = $(this);
            var category_id = category.attr('data-category');

            $.each(category.find('.property'), function () {
                var property = $(this);

                var title = property.find('[name="title"]').val();
                var slug = property.find('[name="slug"]').val();

                var item = {
                    category_id: category_id,
                    id: property.attr('data-id'),
                    index: property.attr('data-index'),
                    title: title,
                    slug: slug,
                    type: property.find('[name="type"]').val(),
                    options: JSON.parse(property.attr('data-options')),
                    settings: JSON.parse(property.attr('data-settings')),
                    validations: JSON.parse(property.attr('data-validations'))
                };

                if(title != '' && slug != '') data.push(item);
            });

        });

        //Формируем ajax запрос
        $.ajax({
            type: "POST",
            url: properties.controllerUrl + "save",
            data: {data: JSON.stringify(data)},
            success: function (data) {
                var res = JSON.parse(data);

                if(res.status == 'success'){
                    $('.response-server')
                        .addClass('alert-success')
                        .removeClass('alert-danger')
                        .show()
                        .text(res.response.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }else{
                    var errors = res.response.errors;
                    $.each(errors, function (attribute, arr) {
                        $('.response-server')
                            .addClass('alert-danger')
                            .removeClass('alert-success')
                            .show()
                            .text(arr[0]);
                        return true;
                    });
                }
            }
        });



    },


    /**
     * Опции
     */
    options: {

        /**
         * Получение значений "Опции"
         * @param ob
         * @returns {*|jQuery}
         */
        data: function (ob) {
            var property = $(ob);
            if (!property.hasClass('property')) property = property.parents('.property');
            return JSON.parse(property.attr('data-options'));
        },

        /**
         * Построение модального окна свойства с типом "Категория"
         * @param ob
         */
        buildingCategory: function (ob) {
            var data = properties.options.data(ob);

            var modal = properties.find(ob, '.modal.options .modal-body');
            //var type = properties.getType(ob);

            var select = $('<select class="form-control list-categories"></select>');

            if (data.category_id != undefined) {
                //Получаем имя категории. Запрос синхронный
                var category_title = $.ajax({
                    url: properties.controllerUrl + "get-title-categories",
                    data: ({id: data.category_id}),
                    async: false
                }).responseText;

                select.append(
                    $('<option selected id="' + data.category_id + '">' + category_title + '</option>')
                );
            }

            modal.append(select);

            modal.find('.list-categories').select2({
                theme: 'bootstrap',
                placeholder: properties.i18n.select_category,
                ajax: {
                    url: properties.controllerUrl + 'get-list-categories',
                    processResults: function (data) {
                        return {
                            results: JSON.parse(data).results
                        };
                    }
                }
            });

        },

        /**
         * Построение модального окна свойства с типом "Строка"
         * @param ob
         */
        buildingSelect: function (ob) {
            var data_options = properties.options.data(ob);

            var modal = properties.find(ob, '.modal.options .modal-body');
            var table = $('<table class="table table-hover"></table>');
            var thead = $('<thead></thead>').append($('<th></th>').text(properties.i18n.key)).append($('<th></th>').text(properties.i18n.value)).append($('<th width="100"></th>'));
            var tbody = $('<tbody></tbody>');

            modal.append(table.append(thead).append(tbody));

            $.each(data_options, function (key, value) {
                tbody.append(properties.options.item(key, value));
            });

            //Если объект со значениями пустой
            if (jQuery.isEmptyObject(data_options)) tbody.append(properties.options.item('', ''));

            modal.append(table.append(thead).append(tbody));
            modal.append($('<button onclick="properties.options.cloneOption(this);" type="button" class="btn btn-default"><i class="glyphicon glyphicon-plus font-12"></i> ' + properties.i18n.add_option + '</button>'));

        },

        /**
         * Метод создания одной опции key=>value
         * @param key
         * @param value
         */
        item: function (key, value) {
            var tr = $('<tr class="item"></tr>');

            tr.append(
                $('<td></td>').append(
                    $('<input name="key" type="text" class="form-control" value="' + key + '">')
                        .attr('required', true)
                        .attr('pattern', "^[a-z0-9\-_]+")
                        .attr('size', 100)
                )
            );
            tr.append(
                $('<td></td>').append(
                    $('<input name="value" type="text" class="form-control" value="' + value + '">')
                        .attr("onkeyup", "properties.options.translit(this);")
                        .attr("onblur", "properties.options.translit(this);")
                        .attr('required', true)
                        .attr('size', 100)
                )
            );


            var btnGroup = $('<div class="btn-group btn-group-sm"></div>');
            var btnClone = $('<a onclick="properties.options.cloneOption(this);" class="btn btn-default" style="color: green;" title="'+properties.i18n.add_option+'"><span class="glyphicon glyphicon-plus"></span></a>');
            var btnRemove = $('<a onclick="properties.options.removeOption(this);" class="btn btn-default" style="color: #a94442;" title="'+properties.i18n.remove_option+'"><span class="glyphicon glyphicon-remove"></span></a>');

            return tr.append($('<td></td>').append(btnGroup.append(btnClone).append(btnRemove)));
        },

        /**
         * Метод клонирования опции
         * @param ob
         */
        cloneOption: function (ob) {
            var item = $(ob).parents('tr.item');
            if (item.length == 0) item = $(ob).parents('.modal').find('tr:last');
            var item_clone = item.clone();
            item_clone.find('input').val('');
            item.after(item_clone);
        },

        /**
         * Метод удаления опции
         * @param ob
         */
        removeOption: function (ob) {
            var item = $(ob).parents('tr.item');
            if (item.parent().children().length != 1) item.remove();
        },

        save: function (ob) {
            var modal = properties.find(ob, '.modal.options .modal-body');
            var type = properties.getType(ob);

            var new_data = {};

            if (type == 'select') {
                $.each(modal.find('.item'), function (i, ob) {

                    var value_elem = $(ob).find('[name="value"]');
                    var key_elem = $(ob).find('[name="key"]');

                    //Праверка на валидацию
                    if (value_elem.get(0)['checkValidity']) {
                        value_elem.get(0).reportValidity();
                        key_elem.get(0).reportValidity();

                        if (!value_elem.get(0).checkValidity()) {
                            event.stopPropagation();
                            return false;
                        }
                        if (!key_elem.get(0).checkValidity()) {
                            event.stopPropagation();
                            return false;
                        }
                    }

                    var value = value_elem.val();
                    var key = key_elem.val();
                    if (value != "" && key != "") new_data[key] = value;
                });
            } else {
                var value = modal.find('.list-categories').val();
                if (value != '') new_data['category_id'] = value;
            }

            $(ob).parents('.property').attr('data-options', JSON.stringify(new_data));
        },

        /**
         * Транслитерация наименования ключей свойств
         * @param ob
         */
        translit: function (ob) {
            $(ob).parents('.item').find('[name="key"]').val(translitCatalog(ob.value))
        }
    },

    /**
     * Настройки
     */
    settings: {
        /**
         * Получение значений "Настройки"
         * @param ob
         * @returns {*|jQuery}
         */
        data: function (ob) {
            var property = $(ob);
            if (!property.hasClass('property')) property = property.parents('.property');
            return JSON.parse(property.attr('data-settings'));
        },

        /**
         * Построение модального окна "Настройки"
         * @param ob
         */
        building: function (ob) {
            var data = properties.settings.data(ob);
            var modal = properties.find(ob, '.modal.settings .modal-body');

            var type = properties.getType(ob);

            modal.append(properties.settings.input('group', data.group, properties.i18n.group));

            modal.append(properties.settings.input('scenarios', data.scenarios, properties.i18n.scenarios));

            modal.append(properties.settings.input('description', data.description, properties.i18n.description));
            modal.append(properties.settings.input('example_1', data.example_1, properties.i18n.example_1));
            modal.append(properties.settings.input('example_2', data.example_2, properties.i18n.example_2));

            modal.append(properties.settings.checkbox('filter_show', data.filter_show, properties.i18n.filter_show));

            modal.append(properties.settings.checkbox('filter_show_admin', data.filter_show_admin, properties.i18n.filter_show_admin));

            modal.append(properties.settings.checkbox('characteristic', data.characteristic, properties.i18n.characteristic));

            //Параметр - множественности
            if (jQuery.inArray(type, [
                'string',
                'select',
                'category',
                'multicategory',
                'itemscategory',
                'image',
                'file'
            ]) != -1) modal.append(properties.settings.checkbox('multiple', data.multiple, properties.i18n.multiple));

            //Параметр диапазона для фильтра
            if (jQuery.inArray(type, [
                    'integer',
                    'datetime'
                ]) != -1) modal.append(properties.settings.checkbox('filter_range', data.filter_range, properties.i18n.filter_range));

        },

        /**
         * Создание нового параметра для "Настроек"
         * @param key
         * @param value - true | false
         * @param label - Описание свойства
         */
        checkbox: function (key, value, label) {
            var li = $('<li class="list-group-item"></li>').text(label);
            var div = $('<div class="material-switch pull-right">&nbsp;</div>').on('click', function () {
                $(this).find('[name="settings-key"]').click();
            });

            div.append($('<input name="settings-key" type="checkbox"/>').attr('data-key', key).prop('checked', value));
            div.append($('<label class="label-success"></label>'));

            return li.append(div);
        },

        /**
         * Создание нового параметра для "Настроек"
         * @param key
         * @param value - true | false
         * @param label - Описание свойства
         */
        input: function (key, value, label) {
            var li = $('<li class="list-group-item" style="overflow: hidden;"></li>').text(label);
            var div = $('<div style="width: 200px;" class="pull-right"></div>');

            div.append(
                $('<input name="settings-key" type="text"/>')
                .addClass('form-control')
                .attr('data-key', key)
                .val(value)
            );

            return li.append(div);
        },

        /**
         * Сохранение значений "Настройка"
         * @param ob
         */
        save: function (ob) {
            var modal = properties.find(ob, '.modal.settings .modal-body');
            var new_data = {};

            $.each(modal.find('[name="settings-key"]'), function (i, ob) {
                new_data[$(ob).attr('data-key')] = $(ob).attr('type') == 'checkbox' ? $(ob).prop('checked') : $(ob).val();
            });

            $(ob).parents('.property').attr('data-settings', JSON.stringify(new_data));
        }
    },

    /**
     * Валидация
     */
    validations: {
        /**
         * Получение значений "Правила валидации"
         * @param ob
         * @returns {*|jQuery}
         */
        data: function (ob) {
            var property = $(ob);
            if (!property.hasClass('property')) property = property.parents('.property');
            return JSON.parse(property.attr('data-validations'));
        },

        building: function (ob) {
            var data = properties.validations.data(ob);

            var modal = properties.find(ob, '.modal.validations .modal-body');
            //var type = properties.getType(ob);

            var table = $('<table class="table table-hover"></table>');
            var thead = $('<thead></thead>').append($('<th></th>').text(properties.i18n.name)).append($('<th></th>').text(properties.i18n.params)).append($('<th width="40"></th>'));
            var tbody = $('<tbody></tbody>');

            modal.append(table.append(thead).append(tbody));

            //Создание правил при клике на список
            var select = $('<select class="form-control list-validators"></select>').on('change', function () {
                properties.validations.library[$(this).val()].building(this, {});
                $(this).find(":selected").prop('disabled', true);
                $(this).find(":first-child").prop('selected', true);
            });

            select.append($('<option value="" selected disabled></option>').text(properties.i18n.add_validation_rule));

            $.each(properties.validations.library, function (key, obj) {
                var option = $('<option value="' + key + '">' + obj.title + '</option>');
                select.append(option);
            });

            modal.append(select);

            //Создание правил из ранее сохраненных
            $.each(data, function (i, item) {
                var validator = item[0];
                var params = (item[1]) ? item[1] : {};

                properties.validations.library[validator].building(ob, params);
                properties.find(ob, '.modal.validations [value="' + validator + '"]').prop('disabled', true);
            });

        },

        removeValidator: function (ob) {
            var item = $(ob).parents('tr.item');
            item.parents('.modal.validations').find('.list-validators option[value="' + item.data('validator') + '"]').prop('disabled', false);
            $(ob).parents('tr.item').remove();
        },

        btnGroup: function () {
            var btnGroup = $('<div class="btn-group btn-group-sm"></div>');
            var btnRemove = $('<a onclick="properties.validations.removeValidator(this);" class="btn btn-default" style="color: #a94442;" title="' + properties.i18n.remove_rule + '"><span class="glyphicon glyphicon-remove"></span></a>');
            return $('<td></td>').append(btnGroup.append(btnRemove));
        },

        defaultValidations: {
            string: [['string']],
            integer: [['integer']],
            select: [['safe']],
            checkbox: [['boolean']],
            html: [['safe']],
            code: [['safe']],
            category: [['safe']],
            multicategory: [['safe']],
            itemscategory: [['safe']],
            datetime: [['integer']],
            image: [['safe']],
            file: [['safe']]
        },

        /**
         * Библиотека правил валидации
         */
        library: {
            integer: {
                validator: 'integer',
                get title() { return properties.i18n.integer_number;},
                params: ['min', 'max'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'integer')
                        .append($('<td></td>').text(properties.i18n.integer_number))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'number')
                                        .attr('placeholder', properties.i18n.minimum_value)
                                        .attr('data-checkbox', 'min')
                                        .val(params.min ? params.min : null)
                                )
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'number')
                                        .attr('placeholder', properties.i18n.maximum_value)
                                        .attr('data-checkbox', 'max')
                                        .val(params.max ? params.max : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            double: {
                validator: 'double',
                get title() { return properties.i18n.floating_point_number;},
                params: null,
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .data('validator', 'double')
                        .append($('<td></td>').text(properties.i18n.floating_point_number))
                        .append($('<td></td>'))
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            boolean: {
                validator: 'boolean',
                get title() { return properties.i18n.boolean_true_false;},
                params: null,
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .data('validator', 'double')
                        .append($('<td></td>').text(properties.i18n.boolean_true_false))
                        .append($('<td></td>'))
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            number: {
                validator: 'number',
                get title() { return properties.i18n.number;},
                params: ['min', 'max'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'number')
                        .append($('<td></td>').text(properties.i18n.number))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'number')
                                        .attr('placeholder', properties.i18n.minimum_value)
                                        .attr('data-checkbox', 'min')
                                        .val(params.min ? params.min : null)
                                )
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'number')
                                        .attr('placeholder', properties.i18n.maximum_value)
                                        .attr('data-checkbox', 'max')
                                        .val(params.max ? params.max : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            string: {
                validator: 'string',
                get title() { return properties.i18n.string;},
                params: ['min', 'max'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'string')
                        .append($('<td></td>').text(properties.i18n.string))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'number')
                                        .attr('placeholder', properties.i18n.minimum_value)
                                        .attr('data-checkbox', 'min')
                                        .val(params.min ? params.min : null)
                                )
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'number')
                                        .attr('placeholder', properties.i18n.maximum_value)
                                        .attr('data-checkbox', 'max')
                                        .val(params.max ? params.max : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            date: {
                validator: 'date',
                get title() { return properties.i18n.date;},
                params: ['format'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'date')
                        .append($('<td></td>').text(properties.i18n.date))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'string')
                                        .attr('placeholder', properties.i18n.format_date)
                                        .attr('data-checkbox', 'format')
                                        .val(params.format ? params.format : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            required: {
                validator: 'required',
                get title() { return properties.i18n.required;},
                params: null,
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'required')
                        .append($('<td></td>').text(properties.i18n.required))
                        .append($('<td></td>'))
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            email: {
                validator: 'email',
                get title() { return properties.i18n.email;},
                params: null,
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'email')
                        .append($('<td></td>').text(properties.i18n.email))
                        .append($('<td></td>'))
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            url: {
                validator: 'url',
                get title() { return properties.i18n.url;},
                params: null,
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'url')
                        .append($('<td></td>').text(properties.i18n.url))
                        .append($('<td></td>'))
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            image: {
                validator: 'image',
                get title() { return properties.i18n.image;},
                params: ['extensions'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'image')
                        .append($('<td></td>').text(properties.i18n.image))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'string')
                                        .attr('placeholder', properties.i18n.extensions_image)
                                        .attr('data-checkbox', 'extensions')
                                        .val(params.extensions ? params.extensions : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            file: {
                validator: 'file',
                get title() { return properties.i18n.file;},
                params: ['extensions'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'file')
                        .append($('<td></td>').text(properties.i18n.file))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'string')
                                        .attr('placeholder', properties.i18n.extensions_file)
                                        .attr('data-checkbox', 'extensions')
                                        .val(params.extensions ? params.extensions : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            unique: {
                validator: 'unique',
                get title() { return properties.i18n.unique;},
                params: null,
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'unique')
                        .append($('<td></td>').text(properties.i18n.unique))
                        .append($('<td></td>'))
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            filter: {
                validator: 'filter',
                get title() { return properties.i18n.filter;},
                params: ['filter'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'filter')
                        .append($('<td></td>').text(properties.i18n.filter))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<select name="checkbox" class="form-control">')
                                        .attr('data-checkbox', 'filter')
                                        .append(
                                            $('<option></option>')
                                                .val('trim')
                                                .text(properties.i18n.filter_trim)
                                        )
                                        .append(
                                            $('<option></option>')
                                                .val('strtolower')
                                                .text(properties.i18n.filter_register)
                                        )
                                        .val(params.filter ? params.filter : 'trim')
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            compare: {
                validator: 'compare',
                get title() { return properties.i18n.compare_validator;},
                params: ['compareValue', 'operator'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var select = $('<select name="checkbox" class="form-control list-properties">')
                        .attr('data-checkbox', 'compareAttribute');

                    if (params.compareAttribute) {
                        //Получаем имя свойства. Запрос синхронный
                        var property_title = $.ajax({
                            url: properties.controllerUrl + "get-title-property",
                            data: ({slug: params.compareAttribute}),
                            async: false
                        }).responseText;

                        select.append(
                            $('<option selected id="' + params.compareAttribute + '">' + property_title + '</option>')
                        );
                    }

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'compare')
                        .append($('<td></td>').text(properties.i18n.compare_validator))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'number')
                                        .attr('placeholder', properties.i18n.value)
                                        .attr('data-checkbox', 'compareValue')
                                        .val(params.compareValue ? params.compareValue : null)
                                )
                                .append(select)
                                .append(
                                    $('<select name="checkbox" class="form-control">')
                                        .attr('data-checkbox', 'operator')
                                        .append($('<option></option>').val('=').text(properties.i18n.operator_equally))
                                        .append($('<option></option>').val('!=').text(properties.i18n.operator_not_equal))
                                        .append($('<option></option>').val('>').text(properties.i18n.operator_more))
                                        .append($('<option></option>').val('>=').text(properties.i18n.operator_more_or_equal))
                                        .append($('<option></option>').val('<').text(properties.i18n.operator_less))
                                        .append($('<option></option>').val('<=').text(properties.i18n.operator_less_or_equal))
                                        .val(params.operator ? params.operator : '=')
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);

                    //Инициализация Select2
                    tbody.find('.list-properties').select2({
                        theme: 'bootstrap',
                        allowClear: true,
                        placeholder: properties.i18n.select_property,
                        ajax: {
                            url: properties.controllerUrl + 'get-list-properties',
                            processResults: function (data) {
                                return {
                                    results: JSON.parse(data)
                                };
                            }
                        }
                    });

                }
            },
            match: {
                validator: 'match',
                get title() { return properties.i18n.regular_expression_validator;},
                params: ['pattern'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'match')
                        .append($('<td></td>').text(properties.i18n.regular_expression_validator))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'string')
                                        .attr('placeholder', properties.i18n.pattern)
                                        .attr('data-checkbox', 'pattern')
                                        .val(params.pattern ? params.pattern : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            default: {
                validator: 'default',
                get title() { return properties.i18n.validator_default;},
                params: ['value'],
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'default')
                        .append($('<td></td>').text(properties.i18n.validator_default))
                        .append(
                            $('<td></td>')
                                .append(
                                    $('<input name="checkbox" class="form-control">')
                                        .attr('type', 'string')
                                        .attr('placeholder', properties.i18n.value)
                                        .attr('data-checkbox', 'value')
                                        .val(params.value ? params.value : null)
                                )
                        )
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            },
            safe: {
                validator: 'safe',
                get title() { return properties.i18n.validator_safe;},
                params: null,
                building: function (ob, params) {
                    var tbody = properties.find(ob, '.modal.validations').find('tbody');

                    var tr = $('<tr class="item"></tr>')
                        .attr('data-validator', 'safe')
                        .append($('<td></td>').text(properties.i18n.validator_safe))
                        .append($('<td></td>'))
                        .append(properties.validations.btnGroup());

                    tbody.append(tr);
                }
            }
        },

        save: function (ob) {
            var modal = properties.find(ob, '.modal.validations .modal-body');

            var new_data = [];

            $.each(modal.find('[data-validator]'), function (i, ob) {
                var validator_arr = [$(ob).data('validator')];

                var params = {};
                $.each($(ob).find('[data-checkbox]'), function (q, elem) {
                    var elem_param = $(elem);
                    if (elem_param.val() != '') params[elem_param.data('checkbox')] = elem_param.val();
                });

                if (!$.isEmptyObject(params)) validator_arr.push(params);

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
    modal: {

        /**
         * Набор методов открытия модальных окон
         */
        open: {

            options: function (ob) {
                var modal = properties.find(ob, '.modal.options');
                modal.modal('show');
                modal.find('.modal-body').empty();

                switch (properties.getType(ob)) {
                    case 'select':
                        properties.options.buildingSelect(ob);
                        break;
                    case 'category':
                    case 'multicategory':
                    case 'itemscategory':
                        properties.options.buildingCategory(ob);
                        break;
                    default:
                        modal.find('.modal-body').html(properties.i18n.options_type_not_apply);
                }
            },

            settings: function (ob) {
                var modal = properties.find(ob, '.modal.settings');
                modal.modal('show');
                modal.find('.modal-body').empty();

                properties.settings.building(ob);
            },

            validations: function (ob) {
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
    translit: function (ob) {
        var $slug = properties.find(ob, '[name="slug"]'),
            value = $slug.val();

        if (value === '') $slug.val(translitCatalog(ob.value))
    },

    moveUp:function (ob) {
        var property = $(ob).parents('.property');
        property.insertBefore(property.prev());
        properties.reIndex(ob);
    },

    moveDown:function (ob) {
        var property = $(ob).parents('.property');
        property.insertAfter(property.next());
        properties.reIndex(ob);
    },

    reIndex:function (ob) {
        $.each($(ob).parents('.properties-category').find('.property'), function (i, elem) {
            $(elem).attr('data-index', i);
        });
    }
};
properties.init();