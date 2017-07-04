;(function ($) {
    jQuery.fn.wkbreadcrumbs = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkbreadcrumbs');
        }
    };

    var defaults = {
        homeCrumbMessage: 'Home'
    };

    var getFromSessionStorage = function ($widget, params) {
        var id = $widget.data('wkbreadcrumbs').id;
        var bcJson = sessionStorage.getItem(id);

        if (bcJson) {
            var bc = $.parseJSON(bcJson);

            if ("crumbs" in bc) {
                $widget.data('wkbreadcrumbs').crumbs = bc.crumbs;
                return;
            }
        }

        if (typeof params.fail == "function") {
            params.fail();
        }
    };

    var getFromLocalStorage = function ($widget, params) {
        var id = $widget.data('wkbreadcrumbs').id;
        var bcJson = localStorage.getItem(id);
        if (bcJson) {
            var bc = $.parseJSON(bcJson);

            if ("crumbs" in bc) {
                $widget.data('wkbreadcrumbs').crumbs = bc.crumbs;
                return;
            }
        }

        if (typeof params.fail == "function") {
            params.fail();
        }
    };

    var generateBreadcrumbs = function ($widget) {
        $widget.data('wkbreadcrumbs').crumbs.push({
            id: $widget.attr("home-crumb-url"),
            title: $widget.data('wkbreadcrumbs').settings.homeCrumbMessage,
            url: $widget.attr("home-crumb-url")
        });
    };

    var saveToSessionStorage = function ($widget) {
        var id = $widget.data('wkbreadcrumbs').id;
        sessionStorage.setItem(id, JSON.stringify({crumbs: $widget.data('wkbreadcrumbs').crumbs}));
    };

    var saveToLocalStorage = function ($widget) {
        var id = $widget.data('wkbreadcrumbs').id;
        localStorage.setItem(id, JSON.stringify({crumbs: $widget.data('wkbreadcrumbs').crumbs}));
    };

    var saveCrumbs = function ($widget) {
        saveToSessionStorage($widget);
        saveToLocalStorage($widget);
    };

    var constructBreadcrumbs = function ($widget) {
        var items = '';
        $.each($widget.data('wkbreadcrumbs').crumbs, function (index) {
            var item = index === $widget.data('wkbreadcrumbs').crumbs.length - 1 ? '<li class="active">' + this.title + '</li>' : '<li><a href="' + this.url + '">' + this.title + '</a></li>';

            items = items + item;
        });

        return $('<ul class="breadcrumb">' + items + '</ul>');
    };

    var addCurrentBreadcrumb = function ($widget) {
        var bc = [];
        $.each($widget.data('wkbreadcrumbs').crumbs, function () {
            if (this.id === $widget.attr("current-crumb-id")) {
                return false;
            } else {
                bc.push(this);
            }
        });

        bc.push({
            id: $widget.attr("current-crumb-id"),
            title: $widget.attr("current-crumb-title"),
            url: window.location.pathname + window.location.search
        });

        $widget.data('wkbreadcrumbs').crumbs = bc;
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('wkbreadcrumbs')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                $widget.data('wkbreadcrumbs', {
                    widget: $widget,
                    settings: settings,
                    id: 'bc_' + $widget[0].id,
                    crumbs: []
                });

                getFromSessionStorage($widget, {
                    fail: function () {
                        getFromLocalStorage($widget, {
                                fail: function () {
                                    generateBreadcrumbs($widget);
                                }
                            }
                        )
                    }
                });

                addCurrentBreadcrumb($widget);

                saveCrumbs($widget);

                constructBreadcrumbs($widget).appendTo($widget);

            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this),
                    data = $widget.data('wkbreadcrumbs');

                $(window).unbind('.wkbreadcrumbs');
                data.tooltip.remove();
                $widget.removeData('wkbreadcrumbs');
            })
        }
    };

})(jQuery);