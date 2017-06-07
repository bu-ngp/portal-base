/**
 * Created by VOVANCHO on 07.06.2017.
 */
;(function ($) {
    jQuery.fn.wkexport = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkexport');
        }
    };

    var defaults = {};

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $pjax = $(this);
                if ($pjax.data('wkexport')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                $pjax.data('wkexport', {
                    pjax: $pjax,
                    settings: settings
                });

                $pjax.on('click', 'a.wk-btn-exportGrid', function () {
                    $.ajax({
                        url: 'roles/report',
                        data: {},
                        method: 'post',
                        success: function (response) {

                        }
                    });
                });
            });
        },
        destroy: function () {
            return this.each(function () {
                var $pjax = $(this),
                    data = $pjax.data('wkexport');

                $(window).unbind('.wkexport');
                data.tooltip.remove();
                $pjax.removeData('wkexport');
            })
        }
    };

})(jQuery);