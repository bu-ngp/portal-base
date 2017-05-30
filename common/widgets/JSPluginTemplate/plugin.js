;(function ($) {
    jQuery.fn.jsplugin = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.jsplugin');
        }
    };

    var LANG = {};

    var defaults = {};

    var Localization = function (LANG) {
        if (typeof WK_WIDGET_JSPLUGIN_I18N !== "undefined") {
            return $.extend(LANG, WK_WIDGET_JSPLUGIN_I18N);
        }
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('jsplugin')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                LANG = Localization(LANG);

                $widget.data('jsplugin', {
                    widget: $widget,
                    settings: settings
                });
                

            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this),
                    data = $widget.data('jsplugin');

                $(window).unbind('.jsplugin');
                data.tooltip.remove();
                $widget.removeData('jsplugin');
            })
        }
    };

})(jQuery);