/**
 * Created by VOVANCHO on 23.05.2017.
 */
;(function ($) {
    jQuery.fn.gridselected2storage = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkgridview');
        }
    };

    var defaults = {
        selectedPanelClass: ''
    };

    var saveToStorageSelectedRow = function ($widget, $checkbox) {
        var idPjax = $widget[0].id;
        var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));

        var obj1 = $widget.data('gridselected2storage').storage;

        if (obj1[idGrid].checkAll) {
            if ($checkbox.prop('checked')) {
                var ind1 = obj1[idGrid].excluded.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
                if (ind1 >= 0) {
                    obj1[idGrid].excluded.splice(ind1, 1);
                }
            } else {
                obj1[idGrid].excluded.push($checkbox.parent('td').parent('tr').attr('data-key'));
            }
        } else {
            if ($checkbox.prop('checked')) {
                obj1[idGrid].included.push($checkbox.parent('td').parent('tr').attr('data-key'));
            } else {
                var ind2 = obj1[idGrid].included.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
                if (ind2 >= 0) {
                    obj1[idGrid].included.splice(ind2, 1);
                }
            }

        }

        saveToStorage($widget, $widget.data('gridselected2storage').settings.storage);
    };

    var saveToStorage = function ($widget, obj) {
        if (typeof obj == 'string') {

            localStorage[obj] = JSON.stringify($widget.data('gridselected2storage').storage);
        } else if (typeof obj == 'object' && "selector" in obj && obj.is('input')) {
            obj.val(JSON.stringify($widget.data('gridselected2storage').storage));
        }
    };

    var readFromStorage = function ($widget, obj) {
        var idPjax = $widget[0].id;
        var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));
        var objTmp = {};
        objTmp[idGrid] = {checkAll: false, included: [], excluded: []};
        if (typeof obj == 'string') {
            if (typeof localStorage[obj] == 'undefined') {
                localStorage[obj] = JSON.stringify(objTmp);
                $widget.data('gridselected2storage').storage = objTmp;
            } else {
                var tmpObj2 = $.parseJSON(localStorage[obj]);
                if (!(idGrid in tmpObj2)) {
                    tmpObj2[idGrid] = {checkAll: false, included: [], excluded: []};
                    localStorage[obj] = JSON.stringify(tmpObj2);
                    $widget.data('gridselected2storage').storage = tmpObj2;
                } else {
                    $widget.data('gridselected2storage').storage = $.parseJSON(localStorage[obj]);
                }
            }
        } else if (typeof obj == 'object' && "selector" in obj && obj.is('input')) {
            if (obj.val() == '') {
                obj.val(JSON.stringify(objTmp));
                $widget.data('gridselected2storage').storage = objTmp;
            } else {
                $widget.data('gridselected2storage').storage = $.parseJSON(obj.val());
            }
        }
    };

    var selectRowsFromStorage = function ($widget) {
        var idPjax = $widget[0].id;
        var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));
        var obj1 = $widget.data('gridselected2storage').storage;
        var $checkboxes = $('#' + idGrid).find('input.kv-row-checkbox');

        if (obj1[idGrid].checkAll) {
            $checkboxes.parent('td').parent('tr').addClass('info');
            $checkboxes.prop('checked', true);
            $.each($checkboxes, function () {
                if (obj1[idGrid].excluded.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parent('td').parent('tr').removeClass('info');
                    $(this).prop('checked', false);
                }
            });

            if (($checkboxes.length - $checkboxes.not(':checked').length) == $checkboxes.length) {
                $('#' + idGrid).find('input.select-on-check-all').prop('checked', true);
            }
        } else {
            $checkboxes.parent('td').parent('tr').removeClass('info');
            $checkboxes.prop('checked', false);
            $.each($checkboxes, function () {
                if (obj1[idGrid].included.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parent('td').parent('tr').addClass('info');
                    $(this).prop('checked', true);
                }
            });
        }
    };

    var eventsApply = function ($widget) {
        var idPjax = $widget[0].id;
        var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));

        $('#' + idGrid).parent().on('click', 'input.select-on-check-all', function () {
            var obj1 = $widget.data('gridselected2storage').storage;
            obj1[idGrid].checkAll = $(this).prop('checked');
            obj1[idGrid].included = [];
            obj1[idGrid].excluded = [];

            saveToStorage($widget, $widget.data('gridselected2storage').settings.storage);
            selectedPanelSet($widget);
        });

        $('#' + idGrid).parent().on('click', 'input.kv-row-checkbox', function () {
            saveToStorageSelectedRow($widget, $(this));
            selectedPanelSet($widget);
        });
    };

    var selectedPanelSet = function ($widget) {
        if ($widget.data('gridselected2storage').settings.selectedPanelClass != '') {
            var $selectedPanel = $('.' + $widget.data('gridselected2storage').settings.selectedPanelClass);
            if ($selectedPanel.length == 1) {
                var idPjax = $widget[0].id;
                var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));
                var all = parseInt($widget.find('div.summary > b:nth-child(2)').text());

                var from = 0;
                var storage = $widget.data('gridselected2storage').storage;
                if (storage[idGrid].checkAll) {
                    from = all - storage[idGrid].excluded.length;
                } else {
                    from = storage[idGrid].included.length;
                }

                if (from > 0) {
                    $selectedPanel.html('<div>Records selected <b>' + from + '</b> from <b>' + all + '</b></div>');
                    $selectedPanel.show();
                } else {
                    $selectedPanel.html('');
                    $selectedPanel.hide();
                }
            }
        }
    };

    var resetSelected = function ($widget) {
        var idPjax = $widget[0].id;
        var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));

        var $filter = $widget.find('.filters').children();
        var arr2 = [];
        if (typeof $widget.data('gridselected2storage').storage.filterValues == 'undefined') {
            $widget.data('gridselected2storage').storage.filterValues = [];
            saveToStorage($widget, $widget.data('gridselected2storage').settings.storage);
        } else {
            arr2 = $widget.data('gridselected2storage').storage.filterValues;

        }

        var arr1 = [];
        $.each($filter, function () {
            var $input = $(this).find('input');
            if ($input.length && $input.val() != '') {
                arr1.push($input.val());
            }
        });

        var diff = ($.extend([], arr1, arr2).length != Math.min(arr1.length, arr2.length));

        if (diff) {
            $widget.data('gridselected2storage').storage.filterValues = arr1;
            saveToStorage($widget, $widget.data('gridselected2storage').settings.storage);

            var obj1 = $widget.data('gridselected2storage').storage;
            obj1[idGrid].checkAll = false;
            obj1[idGrid].included = [];
            obj1[idGrid].excluded = [];

            saveToStorage($widget, $widget.data('gridselected2storage').settings.storage);
            selectedPanelSet($widget);
        }
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('gridselected2storage')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});
                if (typeof(settings.storage) == "undefined") {
                    $.error('Settings storage must be passed');
                }

                $widget.data('gridselected2storage', {
                    widget: $widget,
                    settings: settings
                });

                readFromStorage($widget, settings.storage);
                selectRowsFromStorage($widget);
                selectedPanelSet($widget);
                var tmp1 = [];
                $(document).on('pjax:complete', function (e, xhr, status, response) {
                    if (e.target.id == $widget[0].id) {
                        // console.debug(xhr);
                        //  console.debug(response);
                        var str1 = '';
                        $.each(response.data, function () {
                            if (this.name != '_pjax') {
                                str1 += this.name + '=' + this.value + ' | ';
                            }
                        });

                        console.debug(str1);

                        selectRowsFromStorage($widget);
                        selectedPanelSet($widget);
                    }
                });

                $(document).on('pjax:send', function (e, xhr, response) {

                    if (e.target.id == $widget[0].id) {
                        // console.debug('--pjax:send--');
                        //  console.debug(response.data);
                        /*    var str1 = '';
                         $.each(response.data, function () {
                         if (this.name != '_pjax') {
                         str1 += this.name + '=' + this.value + ' | ';
                         }
                         });

                         console.debug('beforeSend: '+str1);

                         selectRowsFromStorage($widget);
                         selectedPanelSet($widget);*/
                    }
                });

                $(document).on('pjax:success', function (e, data, status, xhr, response) {

                    if (e.target.id == $widget[0].id) {
                        //   console.debug('--pjax:success--');
                        //  console.debug(response);
                        /*    var str1 = '';
                         $.each(response.data, function () {
                         if (this.name != '_pjax') {
                         str1 += this.name + '=' + this.value + ' | ';
                         }
                         });

                         console.debug('beforeSend: '+str1);

                         selectRowsFromStorage($widget);
                         selectedPanelSet($widget);*/
                    }
                });

                $widget.parent().on('afterFilter', '#' + $widget[0].id, function () {
                    resetSelected($widget);
                });

                eventsApply($widget);
            });
        },
        selectedRows: function () {
            var $widget = $(this);
            var idPjax = $widget[0].id;
            var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));

            var all = parseInt($widget.find('div.summary > b:nth-child(2)').text());
            var selectedRows = 0;

            var storage = $widget.data('gridselected2storage').storage;

            if (storage[idGrid].checkAll) {
                selectedRows = all - storage[idGrid].excluded.length;
            } else {
                selectedRows = storage[idGrid].included.length;
            }

            return selectedRows;
        },
        selectedRowID: function () {
            var $widget = $(this);
            var idPjax = $widget[0].id;
            var idGrid = idPjax.substr(0, idPjax.indexOf('-pjax'));

            var all = parseInt($widget.find('div.summary > b:nth-child(2)').text());
            var selectedRows = 0;

            var storage = $widget.data('gridselected2storage').storage;

            if (storage[idGrid].checkAll) {
                selectedRows = all - storage[idGrid].excluded.length;
                if (selectedRows == 1) {
                    // TODO
                }
            } else {
                selectedRows = storage[idGrid].included.length;
                if (selectedRows == 1) {
                    return storage[idGrid].included;
                }
            }

            return selectedRows;
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this),
                    data = $widget.data('gridselected2storage');

                $(window).unbind('.gridselected2storage');
                data.tooltip.remove();
                $widget.removeData('gridselected2storage');
            })
        }
    };

})(jQuery);