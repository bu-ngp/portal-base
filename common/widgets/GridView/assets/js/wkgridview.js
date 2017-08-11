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

        $(document).on('pjax:send', function () {
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