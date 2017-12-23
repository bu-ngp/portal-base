;(function ($) {
    jQuery.fn.wkFixButtonBackward = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkFixButtonBackward');
        }
    };

    var defaults = {};

    var eventsApply = function ($widget) {
        $widget.click(function () {
            window.location.href = $widget.attr("wk-fix-button-backward-url");
            return false;
        });
    };


    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('wkFixButtonBackward')) {
                    return;
                }

                if (!jQuery().wkbreadcrumbs) {
                    throw "Plugin wkbreadcrumbs not initialized";
                }

                var settings = $.extend({}, defaults, options || {});

                $widget.data('wkFixButtonBackward', {
                    widget: $widget,
                    settings: settings
                });

                $widget.append('<i class="fa fa-5x fa-angle-left"></i>');

                eventsApply($widget);

                var backwardObj = $(".wkbc-breadcrumb").wkbreadcrumbs("getPreLast");
                if (backwardObj) {
                    $widget.attr("wk-fix-button-backward-url", backwardObj.url);
                    $widget.animate({opacity: 1}, 500);
                }
            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this);

                $(window).unbind('.wkFixButtonBackward');
                $widget.removeData('wkFixButtonBackward');
            })
        }
    };

})(jQuery);