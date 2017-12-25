;(function ($) {
    jQuery.fn.wkFixButtonOnTop = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkFixButtonOnTop');
        }
    };

    var defaults = {};
    var doAnim = true;

    var hideButton = function ($widget) {
        $widget.animate({opacity: 0}, 500, "swing", function () {
            setTimeout(function () {
                $widget.hide();
            }, 500);
            doAnim = true;
        });
    };

    var showButton = function ($widget) {
        $widget.show();
        $widget.animate({opacity: 1}, 500, "swing", function () {
            doAnim = true;
        });
    };

    var eventsApply = function ($widget) {
        $(document).scroll(function () {
            if (doAnim) {
                if ($(this).scrollTop() > 100) {
                    if ($widget.css("opacity") === "0") {
                        doAnim = false;
                        showButton($widget);
                    }
                } else {
                    if ($widget.css("opacity") === "1") {
                        doAnim = false;
                        hideButton($widget);
                    }
                }
            }
        });

        //Click event to scroll to top
        $widget.click(function () {
            $('html, body').animate({scrollTop: 0}, 500);
            return false;
        });
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('wkFixButtonOnTop')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                $widget.data('wkFixButtonOnTop', {
                    widget: $widget,
                    settings: settings
                });

                $widget.append('<i class="fa fa-5x fa-angle-up"></i>');

                eventsApply($widget);
            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this);

                $(window).unbind('.wkFixButtonOnTop');
                $widget.removeData('wkFixButtonOnTop');
            })
        }
    };

})(jQuery);