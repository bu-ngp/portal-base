;(function ($) {
    jQuery.fn.wkselect2 = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkselect2');
        }
    };

    var defaults = {};

    var isSingle = function ($widget) {
        if ($widget.next('.select2.select2-container').find(".select2-selection.select2-selection--single").length) {
            return true;
        }

        return false;
    };

    var isMultiple = function ($widget) {
        if ($widget.next('.select2.select2-container').find(".select2-selection.select2-selection--multiple").length) {
            return true;
        }

        return false;
    };

    var selectFromUrl = function ($widget) {
        var lastCrumb = $(".wkbc-breadcrumb").wkbreadcrumbs('getLast');
        var wkchoose = "wk-choose" in lastCrumb ? lastCrumb["wk-choose"] : {};

        if ($widget.is('[wk-selected]') && $widget.attr('wk-selected') !== "" && wkchoose.fromGridSaved !== $widget[0].id) {
            if (isMultiple($widget)) {
                var selectedValues = $widget.val() ? $widget.val() : [];

                if ($.inArray($widget.attr('wk-selected'), selectedValues) < 0) {
                    selectedValues.push($widget.attr('wk-selected'));
                }

                $widget.prop("wkSelected", true);
                $widget.val(selectedValues).trigger('change.select2');
            } else {
                $widget.val($widget.attr('wk-selected')).trigger('change');
            }
            wkchoose.fromGridSaved = $widget[0].id;
            lastCrumb["wk-choose"] = wkchoose;
            $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);
        }
    };

    var eventsApply = function ($widget) {
        $widget.nextAll('.input-group-btn').on('click', '.wk-widget-select2-choose-from-grid', function (e) {
            e.preventDefault();

            var lastCrumb = $(".wkbc-breadcrumb").wkbreadcrumbs('getLast');
            var wkchoose = "wk-choose" in lastCrumb ? lastCrumb["wk-choose"] : {};
            wkchoose.gridID = $widget.data('wkselect2').select2ID;
            var initValue = isSingle($widget) ? '' : [];
            wkchoose[wkchoose.gridID] = !!$widget.val() ? $widget.val() : initValue;


            if ("fromGridSaved" in wkchoose && wkchoose.fromGridSaved === wkchoose.gridID) {
                delete wkchoose.fromGridSaved;
            }
            lastCrumb["wk-choose"] = wkchoose;
            $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);

            window.location.href = $(this).attr("href");
        });
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('wkselect2')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});

                $widget.data('wkselect2', {
                    widget: $widget,
                    settings: settings,
                    select2ID: $widget.attr('id')
                });


                eventsApply($widget);
                selectFromUrl($widget);
            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this),
                    data = $widget.data('wkselect2');

                $(window).unbind('.wkselect2');
                data.tooltip.remove();
                $widget.removeData('wkselect2');
            })
        }
    };

})(jQuery);