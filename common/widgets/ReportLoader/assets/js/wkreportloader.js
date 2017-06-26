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
        errorAlertMessage: 'Error',
        emptyMessage: 'Empty'
    };

    var makeReportLoaderDialog = function ($widget) {
        $widget.attr("tabindex", "-1");
        $widget.attr("aria-hidden", "true");
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
            '<ul class="list-group pmd-z-depth pmd-list pmd-card-list wk-report-loader-content"></ul>' +
            '<div class="wk-report-loader-empty">' + $widget.data('wkreportloader').settings.emptyMessage + '</div>' +
            '</div>' +
            '<div class="wk-report-loader-wait"></div>' +
            '</div>' +
            '</div>' +
            '<div class="pmd-modal-action">' +
            '<div class="btn-toolbar wk-report-loader-toolbar-main" role="toolbar">' +
            '<button data-dismiss="modal"  class="btn pmd-ripple-effect btn-default" type="button">' + $widget.data('wkreportloader').settings.closeButtonMessage + '</button>' +
            '</div>' +
            '<div class="btn-toolbar wk-report-loader-toolbar-right" role="toolbar">' +
            '<button class="btn pmd-btn-flat pmd-ripple-effect btn-danger wk-report-loader-clear" disabled type="button">' + $widget.data('wkreportloader').settings.clearButtonMessage + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>'
        );
    };

    var typeItem = function (type) {
        switch (type) {
            case 'Excel2007':
                return {
                    classIcon: 'fa-file-excel-o'
                };
            case 'PDF':
                return {
                    classIcon: 'fa-file-pdf-o'
                };
            default:
                return {
                    classIcon: 'fa-file'
                };
        }
    };

    var itemStatus = function (status) {
        switch (status) {
            case '1':
                return 'PROGRESS';
            case '2':
                return 'COMPLETE';
            case '3':
                return 'CANCEL';
        }

        return false;
    };

    var disableClearButton = function (disable) {
        $('button.wk-report-loader-clear').prop('disabled', disable || $('.wk-report-loader-content').children().length === 0);
    };

    var animateDownloadIcon = function ($li) {
        $li.find('.wk-report-loader-download').find('i').animate({fontSize: '4em'}, 100).animate({fontSize: '3em'}, 100);
    };

    var itemProcessButton = function ($widget, status) {
        var content = itemStatus(status) === 'PROGRESS' ? {
            message: $widget.data('wkreportloader').settings.cancelButtonMessage,
            styleClass: 'wk-report-loader-cancel',
            icon: 'fa-close'
        } : {
            message: $widget.data('wkreportloader').settings.deleteButtonMessage,
            styleClass: 'wk-report-loader-delete',
            icon: 'fa-trash'
        };

        return '<button title="' + content.message + '" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger ' + content.styleClass + '"><i class="fa fa-2x ' + content.icon + '"></i></button>';
    };

    var itemDownloadButton = function (status) {
        return itemStatus(status) === 'COMPLETE' ?
            '<i class="wk-report-loader-download fa fa-3x fa-check-circle"></i>' : '';
    };

    var itemDownloadClass = function (status) {
        return itemStatus(status) === 'COMPLETE' ? 'wk-report-loader-item-download' : '';
    };

    var makeItem = function ($widget, item) {
        var type = typeItem(item.type);

        $('.wk-report-loader-content').append(
            '<li class="list-group-item ' + itemDownloadClass(item.status) + '" report-id="' + item.id + '">' +
            '<div class="wk-report-loader-item-action' + (itemStatus(item.status) === 'COMPLETE' ? ' wk-report-loader-item-complete' : '') + '">' +
            itemDownloadButton(item.status) +
            '</div>' +
            '<i class="fa fa-2x ' + type.classIcon + '"></i>' +
            '<div class="media-body">' +
            '<h4 class="list-group-item-heading">' +
            item.displayName +
            '</h4>' +
            '<span class="list-group-item-text">' +
            item.start +
            '</span>' +
            '</div>' +
            itemProcessButton($widget, item.status) +
            '</li>'
        );

        if (itemStatus(item.status) === 'PROGRESS') {
            var circle = new ProgressBar.Circle('.wk-report-loader-content > li:last-child > div.wk-report-loader-item-action', {
                color: '#FCB03C',
                strokeWidth: 3,
                trailWidth: 1,
                text: {
                    value: item.percent + '%'
                },
                step: function (state, circle) {
                    var value = Math.round(circle.value() * 100);
                    circle.setText(value === 0 ? '' : value + '%');
                }
            });

            $widget.data('wkreportloader').circles[item.id] = {circle: circle};
            circle.animate(item.percent / 100);
        }

    };

    var clearItems = function ($widget) {
        $.each($widget.data('wkreportloader').circles, function () {
            this.circle.destroy();
        });

        $('.wk-report-loader-content').html('');
        $widget.data('wkreportloader').circles = {};
        disableClearButton(true);
    };

    var destroyCircle = function ($widget, id) {
        if (id in $widget.data('wkreportloader').circles) {
            $widget.data('wkreportloader').circles[id].circle.destroy();
            delete $widget.data('wkreportloader').circles[id];
        }
    };

    var progressFinish = function ($li, status) {
        return !(itemStatus(status) === 'PROGRESS' || $li.children('div.wk-report-loader-item-action.wk-report-loader-item-complete').length);
    };

    var convertCompleteItem = function ($widget, id) {
        var $li = $('li[report-id="' + id + '"]');
        destroyCircle($widget, id);
        $li.addClass('wk-report-loader-item-download');
        $li.children('div.wk-report-loader-item-action').addClass('wk-report-loader-item-complete');
        $li.children('div.wk-report-loader-item-action.wk-report-loader-item-complete').append('<i class="wk-report-loader-download fa fa-3x fa-check-circle"></i>');
        $li.find('.wk-report-loader-cancel').remove();
        $li.append('<button title="' + $widget.data('wkreportloader').settings.deleteButtonMessage + '" class="btn pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-report-loader-delete"><i class="fa fa-2x fa-trash"></i></button>');
        animateDownloadIcon($li);
    };

    var animateItem = function ($widget, id, percent) {
        $widget.data('wkreportloader').circles[id].circle.animate(percent / 100);
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
                            if (itemStatus(this.status) === 'PROGRESS') {
                                animateItem($widget, this.id, this.percent);
                            }

                            if (progressFinish($li, this.status)) {
                                convertCompleteItem($widget, this.id);
                            }
                        } else {
                            makeItem($widget, this);
                        }

                        loadAgain = itemStatus(this.status) === 'PROGRESS' ? true : loadAgain;
                    });

                    $('ul.wk-report-loader-content').show();
                }

                disableClearButton(false);
                $('.wk-report-loader-wait').fadeOut(200, function () {
                    if (!items.length) {
                        $('.wk-report-loader-empty').fadeIn(200);
                    }
                });

                if (loadAgain && $("#wk-Report-Loader").data('bs.modal').isShown) {
                    setTimeout(function () {
                        loadItems($widget);
                    }, 2000);
                }
            }
        });
    };

    var removeItem = function ($widget, $li) {
        if ($('.wk-report-loader-content').children().length === 1) {
            $('.wk-report-loader-content').fadeOut(function () {
                clearItems($widget);
                $('.wk-report-loader-empty').fadeIn(200);
            });
        } else {
            $li.fadeOut(function () {
                $(this).remove();
            });
        }
    };

    var removeItems = function ($widget) {
        $('.wk-report-loader-content').fadeOut(function () {
            clearItems($widget);
            $('.wk-report-loader-empty').fadeIn(200);
        });
    };

    var cancelItem = function ($widget, $li, id) {
        if ($('.wk-report-loader-content').children().length === 1) {
            $('.wk-report-loader-content').fadeOut(function () {
                clearItems($widget);
                $('.wk-report-loader-empty').fadeIn(200);
            });
        } else {
            $li.fadeOut(function () {
                destroyCircle($widget, id);
                $li.remove();
            });
        }
    };

    var ajaxOpts = {
        error: function (jqXHR) {
            wkwidget.alert({
                title: $widget.data('wkreportloader').settings.errorAlertMessage,
                message: jqXHR.responseJSON.message
            });
        },
        complete: function () {
            $('.wk-report-loader-wait').fadeOut(200);
        }
    };

    var applyEvents = function ($widget) {

        $widget.on('shown.bs.modal', function (e) {
            loadItems($widget);
        });

        $widget.on('hidden.bs.modal', function (e) {
            $('.wk-report-loader-wait').show();
            $('.wk-report-loader-empty').hide();
            disableClearButton(true);
            clearItems($widget);
        });

        $widget.on('click', 'button.wk-report-loader-delete', function (e) {
            e.stopPropagation();
            var $li = $(this).parent('li');
            wkwidget.confirm({
                message: $widget.data('wkreportloader').settings.deleteConfirmMessage,
                yes: function () {
                    disableClearButton(true);
                    $('.wk-report-loader-wait').fadeIn(200);

                    $.ajax($.extend(ajaxOpts, {
                        url: 'report-loader/report/delete',
                        data: {id: $li.attr('report-id')},
                        success: function (response) {
                            if (response) {
                                removeItem($widget, $li);
                            }

                            disableClearButton(false);
                        }
                    }));
                }
            });
        });

        $widget.on('click', 'button.wk-report-loader-cancel', function (e) {
            e.stopPropagation();
            var $li = $(this).parent('li');
            wkwidget.confirm({
                message: $widget.data('wkreportloader').settings.cancelConfirmMessage,
                yes: function () {
                    disableClearButton(true);
                    $('.wk-report-loader-wait').fadeIn(200);

                    $.ajax($.extend(ajaxOpts, {
                        url: 'report-loader/report/cancel',
                        data: {id: $li.attr('report-id')},
                        success: function (response) {
                            if (response) {
                                cancelItem($widget, $li, $li.attr('report-id'));
                            }

                            disableClearButton(false);
                        }
                    }));
                }
            });
        });

        $widget.on('click', 'button.wk-report-loader-clear', function (e) {
            wkwidget.confirm({
                message: $widget.data('wkreportloader').settings.clearConfirmMessage,
                yes: function () {
                    disableClearButton(true);
                    $('.wk-report-loader-wait').fadeIn(200);

                    $.ajax($.extend(ajaxOpts, {
                        url: 'report-loader/report/delete-all',
                        success: function (response) {
                            if (response) {
                                removeItems($widget);
                            }

                            disableClearButton(false);
                        }
                    }));
                }
            });
        });

        $(document).on("ajaxSend", function (event, jqXHR) {
            try {
                var $el = $(event.target.activeElement);
                var isButtonLoading = $el.is("button[wk-loading]") || $el.is("a[wk-loading]");

                if (!isButtonLoading) {
                    return;
                }

                if (typeof $el.data('buttonHTML') == 'undefined') {
                    $el.data('buttonHTML', $el.html());
                }

                if (!("$buttonLoading" in jqXHR)) {
                    jqXHR.$buttonLoading = $el;
                }

                $el.html($el.data('buttonHTML') + '<div class="wk-widget-loading-block"><div></div></div>');

                $el.find('div.wk-widget-loading-block').one('click', function (event) {
                    $('#wk-Report-Loader').modal();

                    if ($(event.currentTarget).parent().parent().parent('.dropdown-menu').length) {
                        $('body').click();
                    }

                    event.stopPropagation();
                    event.preventDefault();
                });

            } catch (ex) {
                console.error(ex);
            }
        }).on("ajaxComplete", function (event, jqXHR) {
            try {
                if ("$buttonLoading" in jqXHR) {
                    jqXHR.$buttonLoading.find('.wk-widget-loading-block').remove();
                    delete jqXHR["$buttonLoading"];
                }
            } catch (ex) {
                console.error(ex);
            }
        });

        $widget.on('click', 'li.wk-report-loader-item-download', function (e) {
            if ($(this).is('[report-id]')) {
                window.open('report-loader/report/download?id=' + $(this).attr('report-id'));
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
                    circles: {}
                });

                makeReportLoaderDialog($widget);
                applyEvents($widget);
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