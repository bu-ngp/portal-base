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

    var getFromSessionStorage = function ($widget) {

    };

    var getFromLocalStorage = function ($widget, params) {

    };

    var generateBreadcrumbs = function ($widget) {

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
                    crumbs: []
                });

                getFromSessionStorage($widget, {
                    fail: function () {
                        getFromLocalStorage($widget);
                    }
                });

                generateBreadcrumbs($widget);
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