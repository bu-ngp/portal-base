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
        closeButtonMessage: 'Close',
        cancelButtonMessage: 'Cancel Operation',
        deleteButtonMessage: 'Remove File',
        downloadButtonMessage: 'Download File'
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
            '<ul class="list-group pmd-z-depth pmd-list pmd-card-list wk-report-loader-content" style="display: none;">' +
            '</ul>' +
            '<div class="wk-report-loader-wait" style="width: 100%; height: 100%; text-align: center;">wait</div>' +
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

    var makeItem = function ($widget, item) {
        var type = item.type === 'Excel2007' ? 'fa-file-excel-o' : 'fa-file-pdf-o';
        var typeClass = item.type === 'Excel2007' ? 'wk-excel' : 'wk-pdf';

        var button = item.status == '1'
            ? '<button title="' + $widget.data('wkreportloader').settings.cancelButtonMessage + '" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger"><i class="fa fa-2x fa-close"></i></button>'
            : '<button title="' + $widget.data('wkreportloader').settings.deleteButtonMessage + '" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger"><i class="fa fa-2x fa-trash"></i></button>';

        var downloadButton = item.status == '2' ? '<button title="' + $widget.data('wkreportloader').settings.downloadButtonMessage + '" class="btn pmd-btn-fab pmd-ripple-effect btn-default wk-report-loader-download" type="button"><i class="fa fa-2x fa-download"></i></button>' : '';

        $('.wk-report-loader-content').append(
            '<li class="list-group-item" report-id="' + item.id + '">' +
            '<div class="wk-report-loader-item-action' + (item.status == '1' ? '' : ' wk-report-loader-item-complete') + '">' +
            downloadButton +
            '</div>' +
            '<i class="' + typeClass + ' fa fa-2x ' + type + '"></i>' +
            '<div class="media-body">' +
            '<h4 class="list-group-item-heading">' +
            item.displayName +
            '</h4>' +
            '<span class="list-group-item-text">' +
            item.start +
            '</span>' +
            '</div>' +
            button +
            '</li>'
        );

        if (item.status == '1') {
            var circle = new ProgressBar.Circle('.wk-report-loader-content > li:last-child > div.wk-report-loader-item-action', {
                color: '#FCB03C',
                strokeWidth: 3,
                trailWidth: 1,
                text: {
                    value: item.percent + '%'
                },
                step: function (state, circle) {
                    var value = Math.round(circle.value() * 100);
                    if (value === 0) {
                        circle.setText('');
                    } else {
                        circle.setText(value + '%');
                    }
                }
            });
            $widget.data('wkreportloader').circles.push({id: item.id, circle: circle});

            circle.animate(item.percent / 100);
        }

    };

    var clearItems = function ($widget) {
        $('.wk-report-loader-content').hide();
        $('.wk-report-loader-wait').show();
        $.each($widget.data('wkreportloader').circles, function () {
            this.circle.destroy();
        });
        $('.wk-report-loader-content').html('');
        $widget.data('wkreportloader').circles = [];
    };

    var loadItems = function ($widget) {
        $.ajax({
            url: 'report-loader/report/items',
            success: function (items) {
                var loadAgain = false;

                if (items.length > 0) {
                    $.each(items, function () {
                        if ($('li[report-id="' + this.id + '"]').length) {
                            if (this.status == '1') {
                                var filtered = $widget.data('wkreportloader').circles.filter(function (elem) {
                                    return elem.id = this.id;
                                }, this);

                                filtered[0].circle.animate(this.percent / 100);
                            }

                        } else {
                            makeItem($widget, this);
                        }


                        if (this.status == '1') {
                            loadAgain = true;
                        }
                    });

                    $('.wk-report-loader-content').show();
                    $('.wk-report-loader-wait').hide();
                }

                if (loadAgain && $("#wk-Report-Loader").data('bs.modal').isShown) {
                    setTimeout(function () {
                        loadItems($widget);
                    }, 2000);
                }
            }
        });
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
                    settings: settings,
                    circles: []
                });

                makeReportLoaderDialog($widget);

                $widget.on('shown.bs.modal', function (e) {
                    loadItems($widget);

                });

                $widget.on('hide.bs.modal', function (e) {
                    clearItems($widget);
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