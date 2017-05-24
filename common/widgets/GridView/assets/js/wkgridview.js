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

    var defaults = {};

    var Localization = function (LANG) {
        if (typeof WK_WIDGET_GRIDVIEW_I18N !== "undefined") {
            return $.extend(LANG, WK_WIDGET_GRIDVIEW_I18N);
        }
    };

    var eventsApply = function ($pjax) {
        $pjax.on('mouseenter', 'td.wk-nowrap', function (e) {
            if (parseInt($(this).children('span').css('max-width')) == $(this).children('span').outerWidth()) {
                $(this).tooltip({
                    container: $(this).children('span'),
                    title: $(this).text(),
                    delay: {show: 1000, hide: 100}
                }).tooltip('show');
            }
        });

        $pjax.on('click', 'td[data-col-seq]', function (e) {
            if (!$(e.target).hasClass('kv-row-checkbox')) {
                $(e.target).parentsUntil('tbody').find('input.kv-row-checkbox').trigger('click');
            }
        });

        $pjax.on('dblclick', 'td[data-col-seq]', function (e) {
            //    $(this).css('background-color','red');
        });
    };

    var makeDialog = function ($pjax) {
        var gridID = $pjax.data('wkgridview').grid[0].id;
        var $dialog = $('<div tabindex="-1" class="modal fade ' + gridID + '-wk-dialog" style="display: none;" aria-hidden="true">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<h2 class="pmd-card-title-text wk-dialog-title"></h2>' +
            '</div>' +
            '<div class="modal-body">' +
            '<p class="wk-dialog-text"></p>' +
            '</div>' +
            '<div class="pmd-modal-action pmd-modal-bordered text-right">' +
            '<button data-dismiss="modal" type="button" class="btn pmd-btn-flat pmd-ripple-effect btn-default">CLOSE</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');

        $dialog.insertAfter($pjax);
    };

    var makeConfirm = function ($pjax) {
        var gridID = $pjax.data('wkgridview').grid[0].id;
        var $dialog = $('<div tabindex="-1" class="modal fade ' + gridID + '-wk-confirm" style="display: none;" aria-hidden="true">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<h2 class="pmd-card-title-text wk-confirm-title"></h2>' +
            '</div>' +
            '<div class="modal-body">' +
            '<p class="wk-confirm-text"></p>' +
            '</div>' +
            '<div class="pmd-modal-action pmd-modal-bordered text-right">' +
            '<button data-dismiss="modal" type="button" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">CLOSE</button>' +
            '<button type="button" class="btn pmd-btn-flat pmd-ripple-effect btn-default wk-btn-confirm-ok">OK</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');

        $dialog.insertAfter($pjax);
    };

    var makeButtonUpdateEvent = function ($pjax) {
        var gridID = $pjax.data('wkgridview').grid[0].id;
        $pjax.on('click', 'a.wk-btn-update', function (event) {
            var selectedRows = $pjax.gridselected2storage('selectedRows');
            if (selectedRows == 1) {
                var selectedRowID = $pjax.gridselected2storage('selectedRowID');
                if (selectedRowID === false) {
                    event.preventDefault();
                    $pjax.nextAll('.' + gridID + '-wk-dialog').find('.wk-dialog-title').text('Error');
                    $pjax.nextAll('.' + gridID + '-wk-dialog').find('.wk-dialog-text').html('<span>Go to the page where you selected the row.</span>');
                    $pjax.nextAll('.' + gridID + '-wk-dialog').modal();
                } else {
                    event.target.href += '?id=' + $pjax.gridselected2storage('selectedRowID');
                    return true;
                }
            } else {
                event.preventDefault();
                $pjax.nextAll('.' + gridID + '-wk-dialog').find('.wk-dialog-title').text('Error');
                $pjax.nextAll('.' + gridID + '-wk-dialog').find('.wk-dialog-text').html('<span>You must select one role. You selected <b>' + selectedRows + '</b>.</span>');
                $pjax.nextAll('.' + gridID + '-wk-dialog').modal();
            }
        });
    };

    var makeButtonCustomizeDialogEvent = function ($pjax) {
        $pjax.on('click', 'a.wk-btn-customizeDialog', function (event) {
            var $dialog = $pjax.children('.wk-customizeDialog');
            event.preventDefault();
            $dialog.modal();
        });

        $pjax.on('click', 'button.wk-customizeDialog-btn-save', function (event) {
            var $dialog = $pjax.children('.wk-customizeDialog');
            var $grid = $pjax.find('.grid-view');
            event.preventDefault();
            $dialog.modal('hide');
            $grid.yiiGridView('applyFilter');
        });
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $pjax = $(this);
                var $grid = $pjax.find('.grid-view');
                if ($pjax.data('wkgridview')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                LANG = Localization(LANG);

                $pjax.data('wkgridview', {
                    pjax: $pjax,
                    grid: $grid,
                    settings: settings
                });

                eventsApply($pjax);

                makeDialog($pjax);
                makeConfirm($pjax);

                makeButtonUpdateEvent($pjax);

                makeButtonCustomizeDialogEvent($pjax);
            });
        },
        destroy: function () {
            return this.each(function () {
                var $pjax = $(this),
                    data = $pjax.data('wkgridview');

                $(window).unbind('.wkgridview');
                data.tooltip.remove();
                $pjax.removeData('wkgridview');
            })
        }
    };

})(jQuery);