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
        customizeDialog: {
            enable: true,
            titleDialogMessage: 'Customize Dialog',
            rowsPerPageMessage: 'Rows Per Page',
            visibleColumnsMessage: 'Visible Columns',
            hiddenColumnsMessage: 'Hidden Columns',
            rowsPerPageDescriptionMessage: 'Enter the number of records on the grid from 10 to 100',
            visibleColumnsDescriptionMessage: 'Drag to the left of the column that you want to see in the grid in a specific order',
            saveChangesMessage: 'Save changes',
            cancelMessage: 'Cancel',
            resetSortMessage: 'Reset Sort',
            resetMessage: 'Reset',
            resetConfirmTitleMessage: 'Confirm',
            resetConfirmMessage: 'Reset Columns. Are you sure?',
            resetSortConfirmTitleMessage: 'Confirm',
            resetSortConfirmMessage: 'Reset Sort Grid. Are you sure?',
            confirmCloseMessage: 'No',
            confirmOKMessage: 'Yes',
            alertOKMessage: 'OK',
            validatePagerMessage: 'Rows per page must be from 10 to 100',
            validateColumnsMessage: 'Visible columns cannot empty'
        }
    };

    var Localization = function (LANG) {
        if (typeof WK_WIDGET_GRIDVIEW_I18N !== "undefined") {
            return $.extend(LANG, WK_WIDGET_GRIDVIEW_I18N);
        }
    };

    var eventsApply = function ($pjax) {
        $pjax.on('click', 'td[data-col-seq]', function (e) {
            if (!$(e.target).hasClass('kv-row-checkbox')) {
                $(e.target).parentsUntil('tbody').find('input.kv-row-checkbox').trigger('click');
            }
        });

        $pjax.on('dblclick', 'td[data-col-seq]', function (e) {
            //    $(this).css('background-color','red');
        });

        $(document).on('pjax:error', function (e) {
            e.preventDefault();
        });
    };

    var makeButtonUpdateEvent = function ($pjax) {
        var gridID = $pjax.data('wkgridview').grid[0].id;
        $pjax.on('click', 'a.wk-btn-update', function (event) {
            var selectedRows = $pjax.gridselected2storage('selectedRows');
            if (selectedRows == 1) {
                var selectedRowID = $pjax.gridselected2storage('selectedRowID');
                if (selectedRowID === false) {
                    event.preventDefault();
                    wkwidget.alert({
                        message: '<span>Go to the page where you selected the row.</span>'
                    });

                } else {
                    event.target.href += '?id=' + $pjax.gridselected2storage('selectedRowID');
                    return true;
                }
            } else {
                event.preventDefault();
                wkwidget.alert({
                    message: '<span>You must select one role. You selected <b>' + selectedRows + '</b>.</span>'
                });
            }
        });
    };

    var makeCustomizeDialog = function ($pjax) {
        var gridID = $pjax.find('.grid-view')[0].id;

        var $dialog = $('<div tabindex="-1" class="modal fade ' + gridID + '-wk-customizeDialog" style="display: none;" aria-hidden="true">' +
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="modal-header pmd-modal-bordered">' +
            '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>' +
            '<h3 class="pmd-card-title-text"><i class="fa fa-cog"></i> ' + $pjax.data('wkgridview').settings.customizeDialog.titleDialogMessage + '</h3>' +
            '</div>' +
            '<div class="modal-body wk-customizeDialog-content">' +
            '<div class="row">' +
            '<div class="col-xs-7">' +
            '<form class="form-horizontal">' +
            '<div class="form-group pmd-textfield">' +
            '<label class="control-label">' +
            $pjax.data('wkgridview').settings.customizeDialog.rowsPerPageMessage +
            '</label>' +
            '<input type="text" class="form-control wk-per-page" value="10">' +
            '<p class="help-block">' + $pjax.data('wkgridview').settings.customizeDialog.rowsPerPageDescriptionMessage + '</p>' +
            '</div>' +
            '</form>' +
            '</div>' +
            '<div class="col-xs-6">' +
            '<h4 class="wk-customize-dialog-columns-title"><i class="fa fa-eye"></i> ' + $pjax.data('wkgridview').settings.customizeDialog.visibleColumnsMessage + '</h4>' +
            '<ul class="list-group pmd-z-depth pmd-list pmd-card-list ' + gridID + '-connectedSortable wk-visible-columns">' +

            '</ul>' +
            '</div>' +
            '<i class="fa fa-chevron-left fa-2x wk-customize-dialog-columns-exchange-icon"></i>' +
            '<i class="fa fa-chevron-right fa-2x wk-customize-dialog-columns-exchange-icon2"></i>' +
            '<div class="col-xs-6">' +
            '<h4 class="wk-customize-dialog-columns-title"><i class="fa fa-eye-slash"></i> ' + $pjax.data('wkgridview').settings.customizeDialog.hiddenColumnsMessage + '</h4>' +
            '<ul class="list-group pmd-z-depth pmd-list pmd-card-list ' + gridID + '-connectedSortable wk-hidden-columns">' +

            '</ul>' +
            '</div>' +
            '<div class="wk-customize-dialog-columns-description col-xs-12"><p class="help-block">' + $pjax.data('wkgridview').settings.customizeDialog.visibleColumnsDescriptionMessage + '</p></div>' +
            '</div>' +
            '<input type="hidden" class="wk-columnsList" value="[]">' +
            '</div>' +
            '<div class="pmd-modal-action">' +
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block;">' +
            '<button class="btn pmd-ripple-effect btn-primary wk-customizeDialog-btn-save" type="button">' + $pjax.data('wkgridview').settings.customizeDialog.saveChangesMessage + '</button>' +
            '<button data-dismiss="modal"  class="btn pmd-ripple-effect btn-default" type="button">' + $pjax.data('wkgridview').settings.customizeDialog.cancelMessage + '</button>' +
            '</div>' +
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block; float: right;">' +
            '<button class="btn pmd-ripple-effect pmd-btn-flat btn-primary wk-customizeDialog-btn-reset-sort" type="button" data-toggle="modal">' + $pjax.data('wkgridview').settings.customizeDialog.resetSortMessage + '</button>' +
            '<button class="btn pmd-ripple-effect pmd-btn-flat btn-danger wk-customizeDialog-btn-reset" type="button" data-toggle="modal">' + $pjax.data('wkgridview').settings.customizeDialog.resetMessage + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');

        $dialog.appendTo('body');

        $('.' + gridID + '-connectedSortable').sortable({
            connectWith: '.' + gridID + '-connectedSortable',
            stop: function (e, ui) {
                var keys = [];
                $.each($('.' + gridID + '-connectedSortable.wk-visible-columns').children(), function () {
                    keys.push($(this).attr('wk-hash'));
                });
                $dialog.find('input.wk-columnsList').val(JSON.stringify(keys));
            }
        });

        $dialog.find(".pmd-textfield-focused").remove();
        $dialog.find(".pmd-textfield .form-control").after('<span class="pmd-textfield-focused"></span>');

        makeButtonCustomizeDialogEvent($pjax);
    };

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    var purifyingUrl = function () {
        var UrlParams = {};

        // Convert Url to Object
        if (window.location.search.length > 1) {
            for (var aItKey, nKeyId = 0, aCouples = window.location.search.substr(1).split("&"); nKeyId < aCouples.length; nKeyId++) {
                aItKey = aCouples[nKeyId].split("=");
                UrlParams[decodeURIComponent(aItKey[0])] = aItKey.length > 1 ? decodeURIComponent(aItKey[1]) : "";
            }
        }

        // Delete Clean Params
        var propNames = Object.getOwnPropertyNames(UrlParams);
        for (var i = 0; i < propNames.length; i++) {
            var propName = propNames[i];
            if (UrlParams[propName] === null || UrlParams[propName] === undefined || UrlParams[propName] === '') {
                delete UrlParams[propName];
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

    var validatePager = function ($pjax) {
        var gridID = $pjax.find('.grid-view')[0].id;
        var $dialog = $('.' + gridID + '-wk-customizeDialog');

        if (parseInt($dialog.find('.wk-per-page').val()) < 10 || parseInt($dialog.find('.wk-per-page').val()) > 100) {
            wkwidget.alert({
                message: '<span>' + $pjax.data('wkgridview').settings.customizeDialog.validatePagerMessage + '</span>'
            });

            return false;
        } else {
            return true;
        }
    };

    var validateColumns = function ($pjax) {
        var gridID = $pjax.find('.grid-view')[0].id;
        var $dialog = $('.' + gridID + '-wk-customizeDialog');
        if ($dialog.find('.wk-visible-columns').children().length === 0) {
            wkwidget.alert({
                message: '<span>' + $pjax.data('wkgridview').settings.customizeDialog.validateColumnsMessage + '</span>'
            });

            return false;
        } else {
            return true;
        }
    };

    var makeButtonCustomizeDialogEvent = function ($pjax) {
        var gridID = $pjax.find('.grid-view')[0].id;
        var $dialog = $('.' + gridID + '-wk-customizeDialog');

        $pjax.on('click', 'a.wk-btn-customizeDialog', function (event) {
            event.preventDefault();
            $dialog.modal();
        });

        $dialog.on('click', 'button.wk-customizeDialog-btn-save', function (event) {
            if (!(validatePager($pjax) && validateColumns($pjax))) {
                return false;
            }

            var $grid = $pjax.find('.grid-view');
            var inputColumns = $.parseJSON($dialog.find('input.wk-columnsList').val());

            if (inputColumns.length == 0) {
                $.each($('.' + gridID + '-connectedSortable.wk-visible-columns').children(), function () {
                    inputColumns.push($(this).attr('wk-hash'));
                });
            }

            var obj1 = {
                visible: inputColumns,
                pager: $dialog.find('input.wk-per-page').val()
            };

            $dialog.modal('hide');
            saveCookie($pjax, obj1);
            $grid.yiiGridView('applyFilter');
        });

        $dialog.on('click', 'button.wk-customizeDialog-btn-reset', function (event) {
            var $grid = $pjax.find('.grid-view');

            wkwidget.confirm({
                message: '<span>' + $pjax.data('wkgridview').settings.customizeDialog.resetConfirmMessage + '</span>',
                yes: function () {
                    $dialog.modal('hide');
                    document.cookie = gridID + "=; path=/; expires: -1";
                    $grid.yiiGridView('applyFilter');
                }
            });
        });

        $dialog.on('click', 'button.wk-customizeDialog-btn-reset-sort', function (event) {
            var $grid = $pjax.find('.grid-view');

            wkwidget.confirm({
                message: '<span>' + $pjax.data('wkgridview').settings.customizeDialog.resetSortConfirmMessage + '</span>',
                yes: function () {
                    $dialog.modal('hide');
                    removeCookie($pjax, 'sort');
                    $grid.yiiGridView('applyFilter');
                }
            });
        });

        $(document).on('pjax:send', function (e) {
            purifyingUrl();
        });

        $(document).on('pjax:complete', function (e) {
            var pjaxID = $pjax[0].id;
            if (e.target.id == pjaxID) {
                $dialog.find('.wk-visible-columns').html('');
                $dialog.find('.wk-hidden-columns').html('');

                $dialog.find('input.wk-per-page').val($pjax.find('.wk-customize-dialog-pagerValue').text());
                $pjax.find('.wk-customize-dialog-visible-columns').children().appendTo($dialog.find('.wk-visible-columns'));
                $pjax.find('.wk-customize-dialog-hidden-columns').children().appendTo($dialog.find('.wk-hidden-columns'));

                var sort = getUrlParameter('sort');
                if (typeof sort != 'undefined') {
                    saveCookie($pjax, {sort: sort});
                }

                purifyingUrl();
            }
        });
    };

    var getCookie = function (name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    };

    var saveCookie = function ($pjax, object) {
        var date = new Date(new Date().getTime() + 15552000 * 1000);
        var gridID = $pjax.find('.grid-view')[0].id;
        var objCookie = {};

        if (typeof getCookie(gridID) != 'undefined' && getCookie(gridID) != '') {
            objCookie = $.parseJSON(getCookie(gridID));
        }

        objCookie = $.extend({}, objCookie, object);
        document.cookie = gridID + "=" + JSON.stringify(objCookie) + "; path=/; expires=" + date.toUTCString();
    };

    var removeCookie = function ($pjax, name) {
        var date = new Date(new Date().getTime() + 15552000 * 1000);
        var gridID = $pjax.find('.grid-view')[0].id;
        var objCookie = {};

        if (typeof getCookie(gridID) != 'undefined') {
            objCookie = $.parseJSON(getCookie(gridID));
        }

        delete objCookie[name];
        document.cookie = gridID + "=" + JSON.stringify(objCookie) + "; path=/; expires=" + date.toUTCString();
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

                makeButtonUpdateEvent($pjax);

                makeCustomizeDialog($pjax);

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