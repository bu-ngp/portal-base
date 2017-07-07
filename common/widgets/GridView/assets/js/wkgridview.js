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

    var defaults = {
        messages: {
            titleCrudCreateDialogMessage: 'Choose rows',
            applyButtonMessage: 'Apply',
            closeButtonMessage: 'Close',
            redirectToGridButtonCrudCreateDialogMessage: 'Follow to Grid Page'
        }
    };

    var eventsApply = function ($pjax) {

        $pjax.on('dblclick', 'td[data-col-seq]', function (e) {
            //    $(this).css('background-color','red');
        });

        $(document).on('pjax:error', function (e) {
            e.preventDefault();
        });

        $(document).on('pjax:send', function (e) {
            purifyingUrl();

            if ($pjax.find('input[data-krajee-daterangepicker]').data('daterangepicker')) {
                $pjax.find('input[data-krajee-daterangepicker]').data('daterangepicker').remove();
            }
        });


        $(document).on('pjax:complete', function (e) {
            var pjaxID = $pjax[0].id;
            if (e.target.id == pjaxID) {
                purifyingUrl();

                $('.wk-widget-grid-custom-button').off('show.bs.dropdown').on('show.bs.dropdown', function () {
                    $(this).find('.dropdown-menu').first().stop(true, true).slideDown(200);
                });

                $('.wk-widget-grid-custom-button').off('hide.bs.dropdown').on('hide.bs.dropdown', function () {
                    $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
                });
            }
        });
    };

    var makeButtonUpdateEvent = function ($pjax) {
        $pjax.on('click', 'a.wk-btn-update', function (event) {
            var selectedRows = $pjax.gridselected2storage('selectedRows');
            if (selectedRows == 1) {
                var selectedRowID = $pjax.gridselected2storage('selectedRowID');
                if (selectedRowID === false) {
                    event.preventDefault();
                    wkwidget.alert({
                        message: 'Go to the page where you selected the row.'
                    });

                } else {
                    event.target.href += '?id=' + $pjax.gridselected2storage('selectedRowID');
                    return true;
                }
            } else {
                event.preventDefault();
                wkwidget.alert({
                    message: 'You must select one role. You selected <b>' + selectedRows + '</b>.'
                });
            }
        });
    };

    var purifyingUrl = function () {
        var UrlParams = {};

        // Convert Url to Object
        if (window.location.search.length > 1) {
            for (var aItKey, nKeyId = 0, aCouples = window.location.search.substr(1).split("&"); nKeyId < aCouples.length; nKeyId++) {
                aItKey = aCouples[nKeyId].split("=");
                UrlParams[decodeURI(aItKey[0])] = aItKey.length > 1 ? decodeURI(aItKey[1]) : "";
            }
        }

        // Delete Clean Params
        var propNames = Object.getOwnPropertyNames(UrlParams);
        for (var i = 0; i < propNames.length; i++) {
            var propName = propNames[i];
            if (UrlParams[propName] === null || UrlParams[propName] === undefined || UrlParams[propName] === '') {
                delete UrlParams[propName];
            } else {
                UrlParams[propName] = UrlParams[propName].replace(/\+/g, ' ');
                UrlParams[propName] = UrlParams[propName].replace(/%2B/g, '+');
            }
        }

        // Convert ObjectUrl to StringUrl
        var UrlCleaned = $.param(UrlParams);
        UrlCleaned = UrlCleaned == '' ? UrlCleaned : '?' + UrlCleaned;
        window.history.pushState("wk-widget", "", window.location.pathname + UrlCleaned);
    };

    var makeTooltips = function () {
        $.each($('td.wk-nowrap'), function () {
            if (parseInt($(this).children('span').css('max-width')) == $(this).children('span').outerWidth()) {
                $(this).tooltip({
                    container: $(this).children('span'),
                    title: $(this).text(),
                    delay: {show: 700, hide: 100}
                });
            }
        });
    };

    var makeButtonCreate = function ($pjax) {
        if ($pjax.find(".wk-gridview-crud-create").is("[input-name]")
            && $pjax.find(".wk-gridview-crud-create").is("[url-grid]")) {
            var $dialog = createCrudCreateDialog($pjax);
            var inputName = $pjax.find(".wk-gridview-crud-create[input-name]").attr("input-name");
            var urlGrid = $pjax.find(".wk-gridview-crud-create[url-grid]").attr("url-grid");

            $pjax.on('click', '.wk-gridview-crud-create[input-name]', function (e) {
                $dialog.modal();
                e.preventDefault();
            });

            $dialog.on('shown.bs.modal', function (e) {
                if ($('.wk-crudCreateDialog-content').html() == "") {
                    $('.wk-crudCreateDialog-content').load(urlGrid);
                }
            });

        }
    };

    var createCrudCreateDialog = function ($pjax) {
        var gridID = $pjax.data('wkgridview').grid[0].id;

        var $dialog = $('<div tabindex="-1" class="modal fade ' + gridID + '-wk-crudCreateDialog" style="display: none;" aria-hidden="true">' +
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="modal-header pmd-modal-bordered">' +
            '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>' +
            '<h3 class="pmd-card-title-text"><i class="fa fa-plus-square-o"></i> ' + $pjax.data('wkgridview').settings.messages.titleCrudCreateDialogMessage + '</h3>' +
            '</div>' +
            '<div class="modal-body" style="height: 690px;">' +
            '<div class="row">' +
            '<div class="col-xs-12 wk-crudCreateDialog-content">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="pmd-modal-action">' +
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block;">' +
            '<button class="btn pmd-ripple-effect btn-primary wk-crudCreateDialog-btn-apply" type="button">' + $pjax.data('wkgridview').settings.messages.applyButtonMessage + '</button>' +
            '<button data-dismiss="modal"  class="btn pmd-ripple-effect btn-default" type="button">' + $pjax.data('wkgridview').settings.messages.closeButtonMessage + '</button>' +
            '</div>' +
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block; float: right;">' +
            '<button class="btn pmd-ripple-effect pmd-btn-flat btn-danger wk-crudCreateDialog-btn-redirect-to-grid" type="button" data-toggle="modal">' + $pjax.data('wkgridview').settings.messages.redirectToGridButtonCrudCreateDialogMessage + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');

        $dialog.appendTo('body');

        return $dialog;
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

                $pjax.data('wkgridview', {
                    pjax: $pjax,
                    grid: $grid,
                    settings: settings
                });

                eventsApply($pjax);

                makeButtonCreate($pjax);
                makeButtonUpdateEvent($pjax);

                $(document).on('pjax:complete', function (e) {
                    makeTooltips();
                });
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