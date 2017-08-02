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

        // Open Filter Dialog
        $pjax.on('click', 'a.wk-btn-filterDialog', function (event) {
            event.preventDefault();
            $dialog.modal();
        });

        // Apply Button
        $dialog.on('click', 'button.wk-filterDialog-btn-apply', function (event) {
            var $grid = $pjax.find('.grid-view');
            var form = $dialog.find("form");
            var _filter = form.find(":input").filter(function () {
                return $.trim(this.value).length > 0
                    && !(this.name.substr(0, 5) === '_csrf' || this.type === 'hidden')
            }).serialize();

            _filter === '' ? removeCookie($pjax, '_filter') : saveCookie($pjax, {_filter: _filter});

            $dialog.modal('hide');
            $pjax.gridselected2storage('clearSelected');
            $grid.yiiGridView('applyFilter');
        });

        // Reset Button By Dialog
        $dialog.on('click', 'button.wk-filterDialog-btn-reset', function (event) {
            var $grid = $pjax.find('.grid-view');

            wkwidget.confirm({
                message: '<span>' + $pjax.data('wkfilter').settings.resetConfirmMessage + '</span>',
                yes: function () {
                    removeCookie($pjax, '_filter');
                    $dialog.modal('hide');
                    $pjax.gridselected2storage('clearSelected');
                    $grid.yiiGridView('applyFilter');
                }
            });
        });

        // Reset Button By Grid
        $pjax.on('click', 'button.wk-filterDialog-btn-close', function (event) {
            var $grid = $pjax.find('.grid-view');

            wkwidget.confirm({
                message: '<span>' + $pjax.data('wkfilter').settings.resetConfirmMessage + '</span>',
                yes: function () {
                    removeCookie($pjax, '_filter');
                    $pjax.gridselected2storage('clearSelected');
                    $grid.yiiGridView('applyFilter');
                }
            });
        });

        $(document).on('pjax:complete', function (e) {
            var pjaxID = $pjax[0].id;
            if (e.target.id == pjaxID) {
                $pjax.find('.wk-filter-output div:first-child').draggable({
                    cursor: "pointer",
                    containment: "wk-filter-output",
                    revert: true,
                    drag: dragOutputFilter
                });

                $dialog.find('.wk-filterDialog-content').html($pjax.find('.wk-filter-dialog-content').html());
                $pjax.find('.wk-filter-dialog-content').html('');

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

        $dialog.on('shown.bs.modal', function (e) {
            $dialog.find('.pmd-tabs').pmdTab();
        });

        $dialog.on("keyup", "input.wk-filter-search-input", function () {
            var searchInput = $(this).val();
            var tabs = [];
            var labels = [];

            $dialog.find('div.wk-filterDialog-content').find("label.control-label, span.control-label, .panel-title").each(function (key, value) {
                var labelInput = $(this).text();

                if (labelInput.toLowerCase().indexOf(searchInput) != -1) {
                    labels.push($(this));
                    tabs.push($(this).parentsUntil(".wk-filterDialog-content", ".tab-pane")[0].id);
                } else {
                    $(this).parentsUntil(".wk-filterDialog-content").not(".tab-content").not(".tab-pane").hide();
                }
            });

            $.each(labels, function () {
                $(this).parentsUntil(".wk-filterDialog-content").not(".tab-content").not(".tab-pane").show();
            });

            tabs = uniqueArray(tabs);

            if (tabs.length > 0) {
                $dialog.find('.wk-filterDialog-content').find('ul.nav.nav-tabs').children('li').show();
                $('a[href="#' + tabs[0] + '"]').tab('show');

                $.each($dialog.find('.wk-filterDialog-content').find('ul.nav.nav-tabs').children('li'), function () {
                    if (tabs.indexOf($(this).children('a').attr('aria-controls')) == -1) {
                        $(this).hide();
                    }
                });

                $dialog.find('.pmd-tabs').pmdTab();
            }
        });

        $dialog.on('change', 'input:not(.wk-filter-search-input)', function () {
            var empty;

            if ($(this).attr('type') === 'checkbox') {
                empty = !$(this).prop('checked');
            } else {
                empty = !$(this).val();
            }

            if (empty) {
                $(this).parents('.form-group').removeClass('filter-marked');
            } else {
                $(this).parents('.form-group').addClass('filter-marked');
            }
        });
    };

    var dragOutputFilter = function (event, ui) {
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
    };

    var uniqueArray = function (xs) {
        var seen = {};
        return xs.filter(function (x) {
            if (seen[x])
                return;
            seen[x] = true;
            return x
        })
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
                    gridID = $pjax.find('.grid-view')[0].id;

                $('div.' + gridID + '-wk-filterDialog').remove();
                $(window).unbind('.wkfilter');
                $pjax.removeData('wkfilter');
            })
        }
    };

})(jQuery);