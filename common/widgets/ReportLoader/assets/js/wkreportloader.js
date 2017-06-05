/**
 * Created by VOVANCHO on 05.06.2017.
 */
;(function ($) {
    jQuery.fn.wkreportloader = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkreportloader');
        }
    };

    var defaults = {
        titleDialogMessage: 'Report Loader',
        closeButtonMessage: 'Close'
    };

    var makeReportLoaderDialog = function ($widget) {
        $widget.attr("tabindex", "-1");
        $widget.attr("aria-hidden", "true");
        $widget.css("display", "none");
        $widget.addClass("modal fade wk-ReportLoaderDialog");
        $widget.append(
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="modal-header pmd-modal-bordered">' +
            '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>' +
            '<h3 class="pmd-card-title-text"><i class="fa fa-download"></i> ' + $widget.data('wkreportloader').settings.titleDialogMessage + '</h3>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="row">' +
            '<div class="col-xs-12 wk-ReportLoaderDialog-content">' +
            '<div class="list-group pmd-z-depth pmd-list pmd-card-list">' +
            '<a class="list-group-item" id="t1" href="javascript:void(0);"></a>' +
            '<a class="list-group-item" id="t2" href="javascript:void(0);"></a>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="pmd-modal-action">' +
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block;">' +
            '<button data-dismiss="modal"  class="btn pmd-ripple-effect btn-default" type="button">' + $widget.data('wkreportloader').settings.closeButtonMessage + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>'
        );
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('wkreportloader')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                $widget.data('wkreportloader', {
                    widget: $widget,
                    settings: settings
                });

                makeReportLoaderDialog($widget);


                $('#t1').append('<div class="t1" style="width: 60px; height: 60px; display: inline-block;"></div>');
                $('#t2').append('<div class="t2" style="width: 60px; height: 60px; display: table-cell; vertical-align: middle;"></div>');

                $('#t1, #t2').css("display", "table");
                $('#t1, #t2').css("width", "100%");

                $('#t1').append('<i class="fa fa-2x fa-file-excel-o" style="display: table-cell; vertical-align: middle; padding-left: 15px; color: #6eb36e;"></i><div class="media-body" style="display: table-cell; vertical-align: middle; padding-left: 15px; width: 100%;"><h4 class="list-group-item-heading">Отчет 1</h4><span class="list-group-item-text">от 05.06.2017 14:00</span></div>');
                $('#t2').append('<i class="fa fa-2x fa-file-pdf-o" style="display: table-cell; vertical-align: middle; padding-left: 15px; color: #ff6161;"></i><div class="media-body" style="display: table-cell; vertical-align: middle; padding-left: 15px; width: 100%;"><h4 class="list-group-item-heading">Отчет 2 (Дополнительный)</h4><span class="list-group-item-text">от 05.06.2017 14:12</span></div>');
                $('#t1').append('<button title="Отменить операцию" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger" style="float: right;"><i class="fa fa-2x fa-close"></i></button>');
                $('#t2').append('<button title="Удалить файл" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger" style="float: right;"><i class="fa fa-2x fa-trash"></i></button>');

                var circle = new ProgressBar.Circle('.t1', {
                    color: '#FCB03C',
                    strokeWidth: 3,
                    trailWidth: 1,
                    text: {
                        value: '50%'
                    }
                });

                var circle2 = new ProgressBar.Circle('.t2', {
                    color: '#FCB03C',
                    strokeWidth: 3,
                    trailWidth: 1,
                    text: {
                        value: '<button title="Скачать файл" class="btn pmd-btn-fab pmd-ripple-effect btn-default" type="button"><i class="fa fa-2x fa-download" style="color: #6e9aff;"></i></button>'
                    }
                });

                $(document).on('shown.bs.modal', function (e) {
                    circle.animate(0.5);
                    circle2.destroy();
                    $('.t2').html('<button title="Скачать файл" class="btn pmd-btn-fab pmd-ripple-effect btn-primary" type="button"><i class="fa fa-2x fa-download"></i></button>');
                });


            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this),
                    data = $widget.data('wkreportloader');

                $(window).unbind('.wkreportloader');
                data.tooltip.remove();
                $widget.removeData('wkreportloader');
            })
        }
    };

})(jQuery);