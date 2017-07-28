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
        selectedPanelClass: '',
        recordsSelectedMessage: 'Records selected <b>{from}</b> from <b>{all}</b>'
    };

    var saveToStorageSelectedRow = function ($pjax, $checkbox) {
        var iOf;
        var storage = $pjax.data('gridselected2storage').storage;

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
        var gridID = $pjax.data('gridselected2storage').gridID;
        var storageKey = $pjax.data('gridselected2storage').settings.storage + '_' + gridID;
        localStorage[storageKey] = JSON.stringify($pjax.data('gridselected2storage').storage);
    };

    var readFromStorage = function ($pjax) {
        var gridID = $pjax.data('gridselected2storage').gridID;
        var storageKey = $pjax.data('gridselected2storage').settings.storage + '_' + gridID;
        var initObj = {checkAll: false, included: [], excluded: [], filterValues: []};

        if (typeof localStorage[storageKey] == 'undefined') {
            $pjax.data('gridselected2storage').storage = initObj;
            localStorage[storageKey] = JSON.stringify(initObj);
        } else {
            $pjax.data('gridselected2storage').storage = $.parseJSON(localStorage[storageKey]);
        }
    };

    var selectRowsFromStorage = function ($pjax) {
        var gridID = '#' + $pjax.data('gridselected2storage').gridID;
        var storage = $pjax.data('gridselected2storage').storage;
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
        $pjax.on('click', 'td[data-col-seq]', function (e) {
            if (!$(e.target).hasClass('wk-widget-row-checkbox')) {
                $(e.target).parentsUntil('tbody').find('input.kv-row-checkbox').trigger('click');
            }
        });

        $pjax.on('click', '.wk-widget-all-select input.select-on-check-all', function () {
            var storage = $pjax.data('gridselected2storage').storage;
            storage.checkAll = $(this).prop('checked');
            storage.included = [];
            storage.excluded = [];
            storage.filterValues = [];

            saveToStorage($pjax);
            selectedPanelSet($pjax);
        });

        $pjax.on('click', 'input.kv-row-checkbox.wk-widget-row-checkbox', function () {
            saveToStorageSelectedRow($pjax, $(this));
            selectedPanelSet($pjax);
        });

        $pjax.on('pjax:complete', function (e) {
            console.debug('pjax:complete');
            if (e.target.id == $pjax[0].id) {
                resetSelected($pjax);
                selectRowsFromStorage($pjax);
                selectedPanelSet($pjax);
            }
        });
    };

    var selectedPanelSet = function ($pjax) {
        if ($pjax.data('gridselected2storage').settings.selectedPanelClass != '') {
            var $selectedPanel = $pjax.find('.' + $pjax.data('gridselected2storage').settings.selectedPanelClass);
            if ($selectedPanel.length == 1) {
                var all = parseInt($pjax.find('div.summary > b:nth-child(2)').text());

                var from = 0;
                var storage = $pjax.data('gridselected2storage').storage;

                if (storage.checkAll) {
                    from = all - storage.excluded.length;
                } else {
                    from = storage.included.length;
                }

                if (from > 0) {
                    var recordSelected = $pjax.data('gridselected2storage').settings.recordsSelectedMessage;
                    recordSelected = recordSelected.replace('{from}', from);
                    recordSelected = recordSelected.replace('{all}', all);

                    $selectedPanel.html('<div>' + recordSelected + '</div>');
                    $selectedPanel.show();
                } else {
                    $selectedPanel.html('');
                    $selectedPanel.hide();
                }
            }
        }
    };

    var resetSelected = function ($pjax) {
        var $filter = $pjax.find('.filters').children();
        var filterFromStorage = $pjax.data('gridselected2storage').storage.filterValues;

        var filterFromGrid = [];
        $.each($filter, function () {
            var $input = $(this).find('input');
            if ($input.length && $input.val() != '') {
                var field = {};
                field[$input.attr("name")] = $input.val();
                filterFromGrid.push(field);
            }
        });

        var diff = ($.extend([], filterFromGrid, filterFromStorage).length != Math.min(filterFromGrid.length, filterFromStorage.length));

        if (diff) {
            $pjax.data('gridselected2storage').storage.filterValues = filterFromGrid;
            $pjax.data('gridselected2storage').storage.checkAll = false;
            $pjax.data('gridselected2storage').storage.included = [];
            $pjax.data('gridselected2storage').storage.excluded = [];

            saveToStorage($pjax);
            selectedPanelSet($pjax);
        }
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $pjax = $(this);
                var $grid = $pjax.find('.grid-view');
                if ($pjax.data('gridselected2storage')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});
                if (typeof(settings.storage) == "undefined") {
                    $.error('Settings storage must be passed');
                }

                $pjax.data('gridselected2storage', {
                    pjax: $pjax,
                    gridID: $grid[0].id,
                    settings: settings
                });

                readFromStorage($pjax);
                eventsApply($pjax);
            });
        },
        selectedRows: function () {
            var $pjax = $(this);
            var all = parseInt($pjax.find('div.summary > b:nth-child(2)').text());
            var selectedRows = 0;

            var storage = $pjax.data('gridselected2storage').storage;

            if (storage.checkAll) {
                selectedRows = all - storage.excluded.length;
            } else {
                selectedRows = storage.included.length;
            }

            return selectedRows;
        },
        selectedRowID: function () {
            var $pjax = $(this);

            var all = parseInt($pjax.find('div.summary > b:nth-child(2)').text());
            var selectedRows = 0;

            var storage = $pjax.data('gridselected2storage').storage;

            if (storage.checkAll) {
                selectedRows = all - storage.excluded.length;
                if (selectedRows == 1) {
                    var $selectedRow = $pjax.find('input.kv-row-checkbox.wk-widget-row-checkbox:checked').parentsUntil('tbody', 'tr');
                    return $selectedRow.length ? $selectedRow.attr('data-key') : false;
                }
            } else {
                selectedRows = storage.included.length;
                if (selectedRows == 1) {
                    return storage.included;
                }
            }

            return selectedRows;
        },
        clearSelected: function () {
            var $pjax = $(this);
            $pjax.data('gridselected2storage').storage.filterValues = [];
            $pjax.data('gridselected2storage').storage.checkAll = false;
            $pjax.data('gridselected2storage').storage.included = [];
            $pjax.data('gridselected2storage').storage.excluded = [];

            saveToStorage($pjax);
            selectedPanelSet($pjax);
        },
        destroy: function () {
            return this.each(function () {
                var $pjax = $(this),
                    data = $pjax.data('gridselected2storage');

                $(window).unbind('.gridselected2storage');
                //   data.tooltip.remove();
                $pjax.removeData('gridselected2storage');
            })
        }
    };

})(jQuery);