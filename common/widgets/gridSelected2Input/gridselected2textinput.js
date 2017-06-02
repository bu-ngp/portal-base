/**
 * Created by VOVANCHO on 23.05.2017.
 */
;(function ($) {
    jQuery.fn.gridselected2textinput = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkgridview');
        }
    };

    var defaults = {};

    var saveToStorageSelectedRow = function ($pjax, $checkbox) {
        var iOf;
        var storage = $pjax.data('gridselected2textinput').storage;

        var arrayKeys = (storage.checkAll ? storage.excluded : storage.included);

        if (($checkbox.prop('checked') && storage.checkAll) || !($checkbox.prop('checked') || storage.checkAll)) {
            iOf = arrayKeys.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
            if (iOf >= 0) {
                arrayKeys.splice(iOf, 1);
            }
        } else {
            arrayKeys.push($checkbox.parent('td').parent('tr').attr('data-key'));
        }

        storage.checkAll ? storage.excluded = arrayKeys : storage.included = arrayKeys;

        $pjax.data('gridselected2storage').storage = storage;
        saveToStorage($pjax);
    };

    var saveToStorage = function ($pjax) {
        var obj = $pjax.data('gridselected2textinput').settings.storage;

        if (typeof obj == 'object' && "selector" in obj && obj.is('input')) {
            obj.val(JSON.stringify($pjax.data('gridselected2textinput').storage));
        }
    };

    var readFromStorage = function ($pjax) {
        var obj = $pjax.data('gridselected2textinput').settings.storage;
        var initObj = {checkAll: false, included: [], excluded: []};
        if (typeof obj == 'object' && "selector" in obj && obj.is('input')) {
            if (obj.val() == '') {
                $pjax.data('gridselected2textinput').storage = initObj;
                obj.val(JSON.stringify(initObj));
            } else {
                $pjax.data('gridselected2textinput').storage = $.parseJSON(obj.val());
            }
        }
    };

    var selectRowsFromStorage = function ($pjax) {
        var gridID = '#' + $pjax.data('gridselected2textinput').gridID;
        var storage = $pjax.data('gridselected2textinput').storage;
        var $checkboxes = $pjax.find('input.kv-row-checkbox');

        if (storage.checkAll) {
            $checkboxes.parentsUntil('tbody', 'tr').addClass('info');
            $checkboxes.prop('checked', true);
            $.each($checkboxes, function () {
                if (storage.excluded.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parentsUntil('tbody', 'tr').removeClass('info');
                    $(this).prop('checked', false);
                }
            });

            if (($checkboxes.length - $checkboxes.not(':checked').length) == $checkboxes.length) {
                $(gridID).find('input.select-on-check-all').prop('checked', true);
            }
        } else {
            $checkboxes.parentsUntil('tbody', 'tr').removeClass('info');
            $checkboxes.prop('checked', false);
            $.each($checkboxes, function () {
                if (storage.included.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parentsUntil('tbody', 'tr').addClass('info');
                    $(this).prop('checked', true);
                }
            });
        }
    };

    var eventsApply = function ($pjax) {

        $pjax.on('click', 'input.select-on-check-all', function () {
            var storage = $pjax.data('gridselected2textinput').storage;
            storage.checkAll = $(this).prop('checked');
            storage.included = [];
            storage.excluded = [];

            saveToStorage($pjax);
        });

        $pjax.on('click', 'input.kv-row-checkbox', function () {
            saveToStorageSelectedRow($pjax, $(this));
        });
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $pjax = $(this);
                var $grid = $pjax.find('.grid-view');
                if ($pjax.data('gridselected2textinput')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});
                if (typeof(settings.storage) == "undefined") {
                    $.error('Settings storage must be passed');
                }

                $pjax.data('gridselected2textinput', {
                    pjax: $pjax,
                    gridID: $grid[0].id,
                    settings: settings
                });

                readFromStorage($pjax);
                selectRowsFromStorage($pjax);
                $(document).on('pjax:complete', function (e) {
                    if (e.target.id == $pjax[0].id) {
                        selectRowsFromStorage($pjax);
                    }
                });

                eventsApply($pjax);
            });
        },
        destroy: function () {
            return this.each(function () {
                var $pjax = $(this),
                    data = $pjax.data('gridselected2textinput');

                $(window).unbind('.gridselected2textinput');
                data.tooltip.remove();
                $pjax.removeData('gridselected2textinput');
            })
        }
    };

})(jQuery);