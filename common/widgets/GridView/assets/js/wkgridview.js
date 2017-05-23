;(function ($) {
    jQuery.fn.wkgridview = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkgridview');
        }
    };

    var LANG = {};

    var defaults = {
        selectionStorage: false
    };

    var Localization = function (LANG) {
        if (typeof WK_WIDGET_GRIDVIEW_I18N !== "undefined") {
            return $.extend(LANG, WK_WIDGET_GRIDVIEW_I18N);
        }
    };

    var clickRow = function () {
        if (parseInt($(this).children('span').css('max-width')) == $(this).children('span').outerWidth()) {
            $(this).tooltip({
                container: $(this).children('span'),
                title: $(this).text()
            }).tooltip('show');
        }
    };


    var eventsApply = function (gridid) {
        var $widget = $('#' + gridid);

        $widget.parent().on('mouseenter', 'td.wk-nowrap', function (e) {
            if (parseInt($(this).children('span').css('max-width')) == $(this).children('span').outerWidth()) {
                $(this).tooltip({
                    container: $(this).children('span'),
                    title: $(this).text()
                }).tooltip('show');
            }
        });

        $widget.parent().on('click', 'td[data-col-seq]', function (e) {
            if (!$(e.target).hasClass('kv-row-checkbox')) {
                $(e.target).parentsUntil('tbody').find('input.kv-row-checkbox').trigger('click');
            }
        });

        $widget.parent().on('dblclick', 'td[data-col-seq]', function (e) {
            //    $(this).css('background-color','red');
        });

     /*   $widget.parent().on('click', 'input.select-on-check-all', function () {
            var obj1 = $.parseJSON(localStorage.selectedRows);
            obj1[$widget[0].id].checkAll = $(this).prop('checked');
            obj1[$widget[0].id].included = [];
            obj1[$widget[0].id].excluded = [];
            localStorage.selectedRows = JSON.stringify(obj1);
        });

        $widget.parent().on('click', 'input.kv-row-checkbox', function () {
            saveToStorageSelectedRow($widget[0].id, $(this));
        });*/
    };

 /*   var saveToStorageSelectedRow = function (gridid, $checkbox) {
        var $grid = $('#' + gridid);
        var obj1 = $.parseJSON(localStorage.selectedRows);

        if (obj1[$grid[0].id].checkAll) {
            if ($checkbox.prop('checked')) {
                var ind1 = obj1[$grid[0].id].excluded.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
                if (ind1 >= 0) {
                    obj1[$grid[0].id].excluded.splice(ind1, 1);
                }
            } else {
                obj1[$grid[0].id].excluded.push($checkbox.parent('td').parent('tr').attr('data-key'));
            }
        } else {
            if ($checkbox.prop('checked')) {
                obj1[$grid[0].id].included.push($checkbox.parent('td').parent('tr').attr('data-key'));
            } else {
                var ind2 = obj1[$grid[0].id].included.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
                if (ind2 >= 0) {
                    obj1[$grid[0].id].included.splice(ind2, 1);
                }
            }

        }

        localStorage.selectedRows = JSON.stringify(obj1);
    };

    var selectRowsFromStorage = function (gridid) {
        var obj1 = $.parseJSON(localStorage.selectedRows);
        var $this = $('#' + gridid);
        var $checkboxes = $this.find('input.kv-row-checkbox');

        if (obj1[$this[0].id].checkAll) {
            $checkboxes.parent('td').parent('tr').addClass('info');
            $checkboxes.prop('checked', true);
            $.each($checkboxes, function () {
                if (obj1[$this[0].id].excluded.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parent('td').parent('tr').removeClass('info');
                    $(this).prop('checked', false);
                }
            });

            if (($checkboxes.length - $checkboxes.not(':checked').length) == $checkboxes.length) {
                $this.find('input.select-on-check-all').prop('checked', true);
            }
        } else {
            $.each($checkboxes, function () {
                if (obj1[$this[0].id].included.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parent('td').parent('tr').addClass('info');
                    $(this).prop('checked', true);
                }
            });
        }
    };*/

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('wkgridview')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                LANG = Localization(LANG);

                $widget.data('wkgridview', {
                    widget: $widget,
                    settings: settings
                });

               /* if (typeof localStorage.selectedRows == 'undefined') {
                    var selectedRows = {};
                    selectedRows[$widget[0].id] = {
                        checkAll: false,
                        included: [],
                        excluded: []
                    };
                    localStorage.selectedRows = JSON.stringify(selectedRows);
                }*/

          /*      var tmp1 = $.parseJSON(localStorage.selectedRows);
                if (typeof tmp1[$widget[0].id] == 'undefined') {
                    tmp1[$widget[0].id] = {
                        checkAll: false,
                        included: [],
                        excluded: []
                    };
                    localStorage.selectedRows = JSON.stringify(tmp1);
                }*/

                if (settings.selectionStorage) {
                  /*  selectRowsFromStorage($widget[0].id);
                    $(document).on('pjax:complete', function (e) {
                        if (e.target.id == $widget[0].id + '-pjax') {
                            selectRowsFromStorage($widget[0].id);
                        }
                    });*/


                }
                eventsApply($widget[0].id);
            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this),
                    data = $widget.data('wkgridview');

                $(window).unbind('.wkgridview');
                data.tooltip.remove();
                $widget.removeData('wkgridview');
            })
        }
    };

})(jQuery);