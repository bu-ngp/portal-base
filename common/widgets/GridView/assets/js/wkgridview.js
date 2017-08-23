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
            redirectToGridButtonCrudCreateDialogMessage: 'Follow to Grid Page',
            removeRecordConfirm: 'Remove record. Are you sure?'
        }
    };

    var gridSelectedToBreadcrumbs = function (opts) {
        var lastCrumb = $(".wkbc-breadcrumb").length === 1 ? $(".wkbc-breadcrumb").wkbreadcrumbs('getLast') : {};

        if (!("wk-choose" in lastCrumb)) {
            lastCrumb["wk-choose"] = {};
        }

        var wkchoose = lastCrumb["wk-choose"];

        if (opts.gridID in wkchoose) {
            if (wkchoose[opts.gridID].indexOf(opts.selected) < 0) {
                wkchoose[opts.gridID].push(opts.selected);
            }
        } else {
            wkchoose[opts.gridID] = [opts.selected];
        }

        lastCrumb["wk-choose"] = wkchoose;
        $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);
    };

    var gridSelectedFromBreadcrumb = function (opts) {
        var xhr = opts.xhr;
        var lastCrumb = $(".wkbc-breadcrumb").length === 1 ? $(".wkbc-breadcrumb").wkbreadcrumbs('getLast') : {};
        var headerName = ("headerName" in opts) ? opts.headerName.toUpperCase() : "WK-CHOOSE";

        if ("wk-choose" in lastCrumb) {
            var wkchoose = lastCrumb["wk-choose"];

            if (opts.gridID in wkchoose) {
                xhr.setRequestHeader(headerName, JSON.stringify(wkchoose[opts.gridID]));

                if ("inputName" in opts) {
                    $('input[name="' + opts.inputName + '"]').val(JSON.stringify(wkchoose[opts.gridID]));
                }
            }
        }
    };

    var gridExcludedFromBreadcrumb = function (opts) {
        var xhr = opts.xhr;
        var preLastCrumb = $(".wkbc-breadcrumb").length === 1 ? $(".wkbc-breadcrumb").wkbreadcrumbs('getPreLast') : {};
        var headerName = ("headerName" in opts) ? opts.headerName.toUpperCase() : "WK-SELECTED";

        if ("wk-choose" in preLastCrumb) {
            var wkselected = preLastCrumb["wk-choose"];

            if ("gridID" in wkselected) {
                xhr.setRequestHeader(headerName, JSON.stringify({
                    url: preLastCrumb.url,
                    exclude: wkselected[wkselected.gridID],
                    gridID: wkselected.gridID
                }));
            }
        }
    };

    var gridRejectFromBreadcrumb = function (opts) {
        var xhr = opts.xhr;
        var preLastCrumb = $(".wkbc-breadcrumb").length === 1 ? $(".wkbc-breadcrumb").wkbreadcrumbs('getPreLast') : {};
        var headerName = ("headerName" in opts) ? opts.headerName.toUpperCase() : "WK-SELECTED";

        if ("wk-id" in preLastCrumb) {
            var wkid = preLastCrumb["wk-id"];

            if ("gridID" in wkid) {
                xhr.setRequestHeader(headerName, JSON.stringify({
                    url: preLastCrumb.url,
                    reject: wkid[wkid.gridID],
                    gridID: wkid.gridID
                }));
            }
        }
    };

    var gridSaveModelMarker = function (opts) {
        var xhr = opts.xhr;
        var lastCrumb = $(".wkbc-breadcrumb").length === 1 ? $(".wkbc-breadcrumb").wkbreadcrumbs('getLast') : {};

        if ("wk-id" in lastCrumb) {
            delete(lastCrumb["wk-id"]);
            xhr.setRequestHeader("WK-GRID-OPER", "save");
            $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);
        }
    };

    var gridDeleteRecord = function (opts) {
        var inputName = opts.inputName;
        var id = opts.id;
        var lastCrumb = $(".wkbc-breadcrumb").length === 1 ? $(".wkbc-breadcrumb").wkbreadcrumbs('getLast') : {};

        if ("wk-choose" in lastCrumb) {
            var wkchoose = lastCrumb["wk-choose"];

            if (opts.gridID in wkchoose) {
                var chooseArr = wkchoose[opts.gridID];
                console.debug(chooseArr);
                chooseArr = $.grep(chooseArr, function (value) {
                    return value != id;
                });

                /*   var chooseArr33 = new Array;
                 console.debug(chooseArr33);
                 $.map(chooseArr, function (value) {
                 if (value != id) {
                 console.debug('test');
                 chooseArr33.push(value);
                 }
                 });*/

                console.debug(chooseArr);

                /*  var index = lastCrumb["wk-choose"][opts.gridID].indexOf(id);
                 if (index > -1) {
                 lastCrumb["wk-choose"][opts.gridID].splice(index, 1);
                 }*/

                //   console.debug(lastCrumb["wk-choose"]);
                //  console.debug(lastCrumb["wk-choose"][opts.gridID]);

                wkchoose[opts.gridID] = chooseArr;
                console.debug(wkchoose);
                // lastCrumb["wk-choose"] = wkchoose;
                console.debug(lastCrumb);
                $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);

                if ("inputName" in opts) {
                    //    $('input[name="' + opts.inputName + '"]').val(JSON.stringify(chooseArr2));
                }
            }
        }

        if (typeof opts.success === 'function') {
            opts.success();
        }

    };

    var eventsApply = function ($pjax) {

        $pjax.on('dblclick', 'td[data-col-seq]', function (e) {
            //    $(this).css('background-color','red');
        });

        $pjax.on('pjax:error', function (e) {
            e.preventDefault();
        });

        $pjax.on('pjax:send', function () {
            purifyingUrl();

            if ($pjax.find('input[data-krajee-daterangepicker]').data('daterangepicker')) {
                $pjax.find('input[data-krajee-daterangepicker]').data('daterangepicker').remove();
            }
        });

        $pjax.on('pjax:beforeSend', function (e, xhr) {
            var $addButtonGrid = $pjax.find(".wk-gridview-crud-create");
            var $grid = $pjax.find('.grid-view');
            if ($addButtonGrid.is("[input-name]")) {
                if ($grid.is("[wk-selected]")) {
                    gridSelectedToBreadcrumbs({
                        inputName: $addButtonGrid.attr("input-name"),
                        selected: $grid.attr("wk-selected"),
                        gridID: $pjax.find('.grid-view')[0].id
                    });
                }

                gridSelectedFromBreadcrumb({
                    xhr: xhr,
                    inputName: $addButtonGrid.attr("input-name"),
                    gridID: $pjax.find('.grid-view')[0].id
                });
            }

            if ($addButtonGrid.is("[input-name]") || $pjax.find('.grid-view').is['[wk-id]']) {
                xhr.setRequestHeader("WK-GRID-OPER", $addButtonGrid.is("[input-name]") ? 'add' : 'edit');
            }

            gridSaveModelMarker({
                xhr: xhr
            });

            gridExcludedFromBreadcrumb({
                xhr: xhr
            });
            gridRejectFromBreadcrumb({
                xhr: xhr
            });
        });

        $pjax.on('pjax:complete', function (e) {
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

        $pjax.on('click', '.wk-gridview-crud-create', function (e) {
            e.preventDefault();

            var lastCrumb = $(".wkbc-breadcrumb").wkbreadcrumbs('getLast');
            var wkchoose = "wk-choose" in lastCrumb ? lastCrumb["wk-choose"] : {};
            var wkid = {};
            var $grid = $pjax.find('.grid-view');

            if ($(this).is("[input-name]")) {
                if ("wk-choose" in lastCrumb) {
                    wkchoose.gridID = $grid[0].id;

                    if (!(wkchoose.gridID in wkchoose)) {
                        wkchoose[wkchoose.gridID] = [];
                    }

                } else {
                    wkchoose.gridID = $grid[0].id;
                    wkchoose[wkchoose.gridID] = [];
                }

                lastCrumb["wk-choose"] = wkchoose;
            }

            if ($grid.is("[wk-id]")) {
                wkid.gridID = $grid[0].id;
                wkid[wkid.gridID] = $grid.attr("wk-id");

                lastCrumb["wk-id"] = wkid;
            }

            $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);

            window.location.href = $(this).attr("href");
        });

        $pjax.on('click', '.wk-gridview-crud-delete', function (e) {
            var $button = $(this);
            var $grid = $pjax.find('.grid-view');
            e.preventDefault();

            wkwidget.confirm({
                message: $pjax.data('wkgridview').settings.messages.removeRecordConfirm,
                yes: function () {
                    if ($button.is("[wk-id]") && $button.is("[input-name]")) {
                        gridDeleteRecord({
                            inputName: $button.attr("input-name"),
                            id: $button.attr("wk-id"),
                            gridID: $pjax.find('.grid-view')[0].id,
                            success: function () {
                                $grid.yiiGridView('applyFilter');
                            }
                        });

                    } else {
                        $.ajax({
                            url: $button.attr("href"),
                            success: function (response) {
                                if (response.result == 'success') {
                                    $grid.yiiGridView('applyFilter');
                                } else if (response.result == 'error') {
                                    $pjax.find('.wk-grid-errors').append("<div>" + response.message + "</div>");
                                }
                            }
                        });
                    }
                }
            });

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

                $(document).on('pjax:complete', function (e) {
                    makeTooltips();
                });
            });
        },
        destroy: function () {
            return this.each(function () {
                var $pjax = $(this);

                $pjax.wkcustomize("destroy");
                $pjax.wkfilter("destroy");
                $pjax.wkexport("destroy");
                $(window).unbind('.wkgridview');
                $pjax.removeData('wkgridview');
            })
        }
    };

})(jQuery);