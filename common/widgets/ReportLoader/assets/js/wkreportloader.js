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
        clearButtonMessage: 'Clear',
        downloadButtonMessage: 'Download File',
        deleteConfirmMessage: 'Delete Report. Are you sure?',
        cancelConfirmMessage: 'Cancel Report. Are you sure?',
        clearConfirmMessage: 'Delete All Reports. Are you sure?',
        errorAlertMessage: 'Error'
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
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block; float: right;">' +
            '<button class="btn pmd-btn-flat pmd-ripple-effect btn-danger wk-report-loader-clear" type="button">' + $widget.data('wkreportloader').settings.clearButtonMessage + '</button>' +
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
            ? '<button title="' + $widget.data('wkreportloader').settings.cancelButtonMessage + '" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-report-loader-cancel"><i class="fa fa-2x fa-close"></i></button>'
            : '<button title="' + $widget.data('wkreportloader').settings.deleteButtonMessage + '" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-report-loader-delete"><i class="fa fa-2x fa-trash"></i></button>';

        var downloadButton = item.status == '2' ? '<a href="report-loader/report/download?id=' + item.id + '" target="_blank" title="' + $widget.data('wkreportloader').settings.downloadButtonMessage + '" class="btn pmd-btn-fab pmd-ripple-effect btn-default wk-report-loader-download"><i class="fa fa-2x fa-download"></i></a>' : '';

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
                        var $li = $('li[report-id="' + this.id + '"]');
                        if ($li.length) {
                            var filtered = $widget.data('wkreportloader').circles.filter(function (elem) {
                                return elem.id = this.id;
                            }, this);

                            if (this.status == '1') {
                                filtered[0].circle.animate(this.percent / 100);
                            }

                            if (this.status != '1' && !$li.children('div.wk-report-loader-item-action.wk-report-loader-item-complete').length) {
                                filtered[0].circle.destroy();
                                $li.children('div.wk-report-loader-item-action').addClass('wk-report-loader-item-complete');
                                $li.children('div.wk-report-loader-item-action.wk-report-loader-item-complete').append('<a href="report-loader/report/download?id=' + this.id + '" target="_blank" title="' + $widget.data('wkreportloader').settings.downloadButtonMessage + '" class="btn pmd-btn-fab pmd-ripple-effect btn-default wk-report-loader-download"><i class="fa fa-2x fa-download"></i></a>');
                                $li.find('.wk-report-loader-cancel').remove();
                                $li.append('<button title="' + $widget.data('wkreportloader').settings.deleteButtonMessage + '" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-report-loader-delete"><i class="fa fa-2x fa-trash"></i></button>');
                            }

                        } else {
                            makeItem($widget, this);
                        }


                        if (this.status == '1') {
                            loadAgain = true;
                        }
                    });

                    $('.wk-report-loader-content').show();
                }

                $('.wk-report-loader-wait').hide();

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

                $widget.on('hidden.bs.modal', function (e) {
                    clearItems($widget);
                });

                $widget.on('click', 'button.wk-report-loader-delete', function (e) {
                    var $li = $(this).parent('li');
                    wkwidget.confirm({
                        message: '<span>' + $widget.data('wkreportloader').settings.deleteConfirmMessage + '</span>',
                        yes: function () {
                            $('.wk-report-loader-content').hide();
                            $('.wk-report-loader-wait').show();
                            $.ajax({
                                url: 'report-loader/report/delete',
                                data: {id: $li.attr('report-id')},
                                success: function (response) {
                                    $('.wk-report-loader-wait').hide();
                                    $('.wk-report-loader-content').show();
                                    if (response) {
                                        if ($('.wk-report-loader-content').children().length === 1) {
                                            $('.wk-report-loader-content').fadeOut(function () {
                                                $.each($widget.data('wkreportloader').circles, function () {
                                                    this.circle.destroy();
                                                });
                                                $('.wk-report-loader-content').html('');
                                                $widget.data('wkreportloader').circles = [];
                                            });
                                        } else {
                                            $li.fadeOut(function () {
                                                $li.remove();
                                            });
                                        }
                                    }
                                },
                                error: function (jqXHR) {
                                    $('.wk-report-loader-wait').hide();
                                    $('.wk-report-loader-content').show();
                                    wkwidget.alert({
                                        title: $widget.data('wkreportloader').settings.errorAlertMessage,
                                        message: '<span>' + jqXHR.responseJSON.message + '</span>'
                                    });
                                }
                            });
                        }
                    });
                });

                $widget.on('click', 'button.wk-report-loader-clear', function (e) {
                    var $li = $(this).parent('li');
                    wkwidget.confirm({
                        message: '<span>' + $widget.data('wkreportloader').settings.clearConfirmMessage + '</span>',
                        yes: function () {
                            $('.wk-report-loader-content').hide();
                            $('.wk-report-loader-wait').show();
                            $.ajax({
                                url: 'report-loader/report/delete-all',
                                success: function (response) {
                                    $('.wk-report-loader-wait').hide();
                                    $('.wk-report-loader-content').show();
                                    if (response) {
                                        $('.wk-report-loader-content').fadeOut(function () {
                                            $('.wk-report-loader-content').html('');
                                            $widget.data('wkreportloader').circles = [];
                                        });
                                    }
                                },
                                error: function (jqXHR) {
                                    $('.wk-report-loader-wait').hide();
                                    $('.wk-report-loader-content').show();
                                    wkwidget.alert({
                                        title: $widget.data('wkreportloader').settings.errorAlertMessage,
                                        message: '<span>' + jqXHR.responseJSON.message + '</span>'
                                    });
                                }
                            });
                        }
                    });
                });

                $widget.on('click', 'button.wk-report-loader-cancel', function (e) {
                    var $li = $(this).parent('li');
                    wkwidget.confirm({
                        message: '<span>' + $widget.data('wkreportloader').settings.cancelConfirmMessage + '</span>',
                        yes: function () {
                            $('.wk-report-loader-content').hide();
                            $('.wk-report-loader-wait').show();
                            $.ajax({
                                url: 'report-loader/report/cancel',
                                data: {id: $li.attr('report-id')},
                                success: function (response) {
                                    $('.wk-report-loader-wait').hide();
                                    $('.wk-report-loader-content').show();
                                    if (response) {

                                        if ($('.wk-report-loader-content').children().length === 1) {
                                            $('.wk-report-loader-content').fadeOut(function () {
                                                $.each($widget.data('wkreportloader').circles, function () {
                                                    this.circle.destroy();
                                                });
                                                $('.wk-report-loader-content').html('');
                                                $widget.data('wkreportloader').circles = [];
                                            });
                                        } else {
                                            $li.fadeOut(function () {
                                                var filtered = $widget.data('wkreportloader').circles.filter(function (elem) {
                                                    return elem.id = this.id;
                                                }, this);

                                                if (filtered.length) {
                                                    filtered[0].circle.destroy();
                                                }
                                                $li.remove();
                                            });
                                        }
                                    }
                                },
                                error: function (jqXHR) {
                                    $('.wk-report-loader-wait').hide();
                                    $('.wk-report-loader-content').show();
                                    wkwidget.alert({
                                        title: $widget.data('wkreportloader').settings.errorAlertMessage,
                                        message: '<span>' + jqXHR.responseJSON.message + '</span>'
                                    });
                                }
                            });
                        }
                    });
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