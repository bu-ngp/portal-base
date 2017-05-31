/**
 * Created by VOVANCHO on 30.05.2017.
 */
;(function ($) {
    jQuery.fn.wkfilter = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkfilter');
        }
    };

    var defaults = {
        titleDialogMessage: 'Additional Filter',
        applyButtonMessage: 'Apply',
        cancelButtonMessage: 'Cancel',
        resetButtonMessage: 'Reset Filter',
        searchMessage: 'Search',
        resetConfirmMessage: 'Reset Filter. Are you sure?'
    };

    var makeFilterDialog = function ($pjax) {
        var gridID = $pjax.data('wkfilter').gridID;

        var $dialog = $('<div tabindex="-1" class="modal fade ' + gridID + '-wk-filterDialog" style="display: none;" aria-hidden="true">' +
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="modal-header pmd-modal-bordered">' +
            '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>' +
            '<h3 class="pmd-card-title-text"><i class="fa fa-filter"></i> ' + $pjax.data('wkfilter').settings.titleDialogMessage + '</h3>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="row">' +
            '<div class="col-xs-12 wk-filter-search-panel-field">' +
            '<div class="form-group pmd-textfield pmd-textfield-floating-label form-group-lg">' +
            '<label class="control-label">' + $pjax.data('wkfilter').settings.searchMessage + '</label>' +
            '<input type="text" class="form-control input-group-lg wk-filter-search-input">' +
            '</div>' +
            '</div>' +
            '<div class="col-xs-12 wk-filterDialog-content">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="pmd-modal-action">' +
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block;">' +
            '<button class="btn pmd-ripple-effect btn-primary wk-filterDialog-btn-apply" type="button">' + $pjax.data('wkfilter').settings.applyButtonMessage + '</button>' +
            '<button data-dismiss="modal"  class="btn pmd-ripple-effect btn-default" type="button">' + $pjax.data('wkfilter').settings.cancelButtonMessage + '</button>' +
            '</div>' +
            '<div class="btn-toolbar" role="toolbar" style="display: inline-block; float: right;">' +
            '<button class="btn pmd-ripple-effect pmd-btn-flat btn-danger wk-filterDialog-btn-reset" type="button" data-toggle="modal">' + $pjax.data('wkfilter').settings.resetButtonMessage + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');

        $dialog.appendTo('body');
    };


    var eventsApply = function ($pjax) {
        var gridID = $pjax.data('wkfilter').gridID;
        var $dialog = $('.' + gridID + '-wk-filterDialog');

        $pjax.on('click', 'a.wk-btn-filterDialog', function (event) {
            event.preventDefault();
            $dialog.modal();
        });

        $dialog.on('click', 'button.wk-filterDialog-btn-apply', function (event) {
            var $grid = $pjax.find('.grid-view');

            //    console.debug($dialog.find("form").not($dialog.find('input[type="hidden"]')));
            var _filter = $dialog.find("form :input[value!='']").serializeJSON();
            //   var _filter = {};
            /* $.each(form, function () {
             if ((this.name).substr(0, 5) !== '_csrf'
             && this.value !== ''
             && !((this.name).substr((this.name).length - 6) === '_mark]' && this.value === '0')
             ) {
             _filter[this.name] = this.value;
             }
             });*/


            if ($.isEmptyObject(_filter)) {
                removeCookie($pjax, '_filter');
            } else {
                saveCookie($pjax, {_filter: _filter});
            }

            $dialog.modal('hide');
            $grid.yiiGridView('applyFilter');
        });

        $dialog.on('click', 'button.wk-filterDialog-btn-reset', function (event) {
            var $grid = $pjax.find('.grid-view');

            wkwidget.confirm({
                message: '<span>' + $pjax.data('wkfilter').settings.resetConfirmMessage + '</span>',
                yes: function () {
                    removeCookie($pjax, '_filter');
                    $dialog.modal('hide');
                    $grid.yiiGridView('applyFilter');
                }
            });
        });

        $pjax.on('click', 'button.wk-filter-output-close', function (event) {
            var $grid = $pjax.find('.grid-view');

            wkwidget.confirm({
                message: '<span>' + $pjax.data('wkfilter').settings.resetConfirmMessage + '</span>',
                yes: function () {
                    removeCookie($pjax, '_filter');
                    $pjax.find('div.wk-filter-output').fadeOut().hide(400);
                    $grid.yiiGridView('applyFilter');
                }
            });
        });

        $(document).on('pjax:complete', function (e) {
            var pjaxID = $pjax[0].id;
            if (e.target.id == pjaxID) {
                $pjax.find('.wk-filter-output').appendTo('div.kv-panel-before');
                $pjax.find('.wk-filter-output div:first-child').draggable({
                    cursor: "pointer",
                    containment: "wk-filter-output",
                    revert: true,
                    drag: function (event, ui) {
                        var widthChild = $('div.wk-filter-output div:first-child').outerWidth();
                        var containerWidth = $('div.wk-filter-output').outerWidth();

                        ui.position.top = 0;
                        if (widthChild < containerWidth) {
                            ui.position.left = 0;
                        } else {
                            if (ui.position.left < (containerWidth - widthChild - 40)) {
                                ui.position.left = containerWidth - widthChild - 40;
                            } else if (ui.position.left > 0) {
                                ui.position.left = 0;
                            }
                        }
                    }
                });

                $dialog.find('.wk-filterDialog-content').html($pjax.find('.wk-filter-dialog-content').html());
                $pjax.find('.wk-filter-dialog-content').html('');
                $dialog.find('.pmd-tabs').pmdTab();

                $dialog.find(".pmd-textfield-focused").remove();
                $dialog.find(".pmd-textfield .form-control").after('<span class="pmd-textfield-focused"></span>');

                $dialog.find('.pmd-textfield input.form-control').each(function () {
                    if ($(this).val() !== "") {
                        $(this).closest('.pmd-textfield').addClass("pmd-textfield-floating-label-completed");
                    }
                });

                $dialog.find('.pmd-checkbox input').after('<span class="pmd-checkbox-label">&nbsp;</span>');
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
                if ($pjax.data('wkfilter')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                $pjax.data('wkfilter', {
                    pjax: $pjax,
                    grid: $grid,
                    gridID: $grid[0].id,
                    settings: settings
                });

                makeFilterDialog($pjax);
                eventsApply($pjax);
            });
        },
        destroy: function () {
            return this.each(function () {
                var $pjax = $(this),
                    data = $pjax.data('wkfilter');

                $(window).unbind('.wkfilter');
                data.tooltip.remove();
                $pjax.removeData('wkfilter');
            })
        }
    };

})(jQuery);