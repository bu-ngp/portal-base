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
    };

    var makeDialog = function ($widget) {
        var $dialog = $('<div tabindex="-1" class="modal fade ' + $widget[0].id + '-wk-dialog" style="display: none;" aria-hidden="true">' +
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

        $dialog.insertAfter($widget.parent());
    };

    var makeConfirm = function ($widget) {
        var $dialog = $('<div tabindex="-1" class="modal fade ' + $widget[0].id + '-wk-confirm" style="display: none;" aria-hidden="true">' +
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

        $dialog.insertAfter($widget.parent());
    };

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

                if (settings.selectionStorage) {

                }

                var $pjax = $widget.parent();

                eventsApply($widget[0].id);

                makeDialog($widget);
                makeConfirm($widget);

                $pjax.on('click', 'a.wk-btn-update', function (event) {
                    event.preventDefault();
                    var selectedRows = $pjax.gridselected2storage('selectedRows');
                    if (selectedRows == 1) {
                        event.target.href += '?id=' + $pjax.gridselected2storage('selectedRowID');
                        return true;
                    } else {
                        event.preventDefault();
                        $pjax.nextAll('.' + $widget[0].id + '-wk-dialog').find('.wk-dialog-title').text('Error');
                        $pjax.nextAll('.' + $widget[0].id + '-wk-dialog').find('.wk-dialog-text').html('<span>You must select one role. You selected <b>' + selectedRows + '</b>.</span>');
                        $pjax.nextAll('.' + $widget[0].id + '-wk-dialog').modal();
                    }
                });
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