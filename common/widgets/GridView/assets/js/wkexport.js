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
                    var $button = $(this);

                    if (typeof $(this).data('buttonHTML') == 'undefined') {
                        $(this).data('buttonHTML', $(this).html());
                    }

                    $(this).html($(this).data('buttonHTML') + '<div class="wk-widget-loading-block"><div></div></div>');

                    $.ajax({
                        url: 'roles/report',
                        data: {},
                        method: 'post',
                        success: function (response) {
                            $button.find('.wk-widget-loading-block').remove();
                            if (!$("#wk-Report-Loader").data('bs.modal').isShown) {
                                window.location.href = response;
                            }
                        }
                    });
                });

                $pjax.on('click', 'a.wk-btn-exportGrid div.wk-widget-loading-block', function (event) {
                    $('#wk-Report-Loader').modal();
                    event.stopPropagation();
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