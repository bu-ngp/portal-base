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

    var defaults = {};

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
                $widget.data('gridselected2storage').storage = $.parseJSON(localStorage[obj]);
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
        });

        $('#' + idGrid).parent().on('click', 'input.kv-row-checkbox', function () {
            saveToStorageSelectedRow($widget, $(this));
        });
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
                $(document).on('pjax:complete', function (e) {
                    if (e.target.id == $widget[0].id) {
                        selectRowsFromStorage($widget);
                    }
                });

                eventsApply($widget);
            });
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