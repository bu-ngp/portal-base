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

    var isMultiple = function ($widget) {
        if ($widget.next('.select2.select2-container').find(".select2-selection.select2-selection--multiple").length) {
            return true;
        }

        return false;
    };

    var selectFromUrl = function ($widget) {
        var wkchoose = $(".wkbc-breadcrumb").wkbreadcrumbs('getLastByObject', 'wk-choose');
        var select2ID = $widget.data('wkselect2').select2ID;

        if ($widget.is('[wk-selected]') && $widget.attr('wk-selected') !== "" && wkchoose.fromGridSaved !== select2ID) {
            if (isMultiple($widget)) {
                var selectedValues = $widget.val() ? $widget.val() : [];

                if ($.inArray($widget.attr('wk-selected'), selectedValues) < 0) {
                    selectedValues.push($widget.attr('wk-selected'));
                }

                $widget.val(selectedValues);
            } else {
                $widget.val($widget.attr('wk-selected'));
            }

            $widget.prop("wkSelected", true);
            $widget.trigger('change.select2');
            wkchoose.fromGridSaved = select2ID;
            $(".wkbc-breadcrumb").wkbreadcrumbs('setLastByObject', 'wk-choose', wkchoose);
        }
    };

    var eventsApply = function ($widget) {
        $widget.nextAll('.input-group-btn').on('click', '.wk-widget-select2-choose-from-grid', function (e) {
            e.preventDefault();

            var wkchoose = $(".wkbc-breadcrumb").wkbreadcrumbs('getLastByObject', 'wk-choose');
            var initValue = isMultiple($widget) ? [] : '';
            wkchoose.gridID = $widget.data('wkselect2').select2ID;
            wkchoose[wkchoose.gridID] = $widget.val() ? $widget.val() : initValue;

            if ("fromGridSaved" in wkchoose && wkchoose.fromGridSaved === wkchoose.gridID) {
                delete wkchoose.fromGridSaved;
            }

            $(".wkbc-breadcrumb").wkbreadcrumbs('setLastByObject', 'wk-choose', wkchoose);
            window.location.href = $(this).attr("href");
        });
    };

    var initSelection = function ($widget) {
        if ($widget.val() && $widget.val().toString()) {
            $widget.prop("wkSelected", true);
            $widget.trigger('change.select2');
        }
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

                initSelection($widget);
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