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

    var selectFromUrl = function ($widget) {
        if ($widget.is('[wk-selected]') && $widget.attr('wk-selected') !== "") {
            $widget.val($widget.attr('wk-selected')).trigger('change');
        }
    };

    var eventsApply = function ($widget) {
        $widget.nextAll('.input-group-btn').on('click', '.wk-widget-select2-choose-from-grid', function (e) {
            e.preventDefault();

            var lastCrumb = $(".wkbc-breadcrumb").wkbreadcrumbs('getLast');
            var wkchoose = "wk-choose" in lastCrumb ? lastCrumb["wk-choose"] : {};
            var select2ID = $widget.data('wkselect2').select2ID;

            if ("wk-choose" in lastCrumb) {
                wkchoose.gridID = select2ID;

                if (!(wkchoose.gridID in wkchoose)) {
                    wkchoose[wkchoose.gridID] = [];
                }

                if ("isSaved" in wkchoose && wkchoose.isSaved === wkchoose.gridID) {
                    delete wkchoose.isSaved;
                }
            } else {
                wkchoose[select2ID] = [];
                wkchoose["gridID"] = select2ID;
            }

            lastCrumb["wk-choose"] = wkchoose;
            $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);
            window.location.href = $(this).attr("href");
        });

        $widget.on('change', function (e) {
            var lastCrumb = $(".wkbc-breadcrumb").wkbreadcrumbs('getLast');
            var wkchoose = "wk-choose" in lastCrumb ? lastCrumb["wk-choose"] : {};
            var select2ID = $widget.data('wkselect2').select2ID;

            if ("wk-choose" in lastCrumb) {
                wkchoose.gridID = select2ID;

                if (!(select2ID in wkchoose)) {
                    wkchoose[select2ID] = [$widget.val()];
                }

                if (!$.inArray($widget.val(), wkchoose[select2ID])) {
                    wkchoose[select2ID].push($widget.val());
                }

            } else {
                wkchoose[select2ID] = [$widget.val()];
            }

            lastCrumb["wk-choose"] = wkchoose;
            $(".wkbc-breadcrumb").wkbreadcrumbs('setLast', lastCrumb);
        });
    };
    /*
     var getAjaxSelectValue = function ($widget) {
     $.ajax({ // make the request for the selected data object
     type: 'GET',
     url: window.location.href + "&id=7741AF08ACBD11E79E9E902B3479B004",
     dataType: 'json'
     }).then(function (data) {
     console.debug(data);
     // Here we should have the data object
     var $option = $('<option selected></option>');
     $('#employeeform-dolzh_id').append($option).trigger('change');
     $option.text(data.text).val(data.id); // update the text that is displayed (and maybe even the value)
     $option.removeData(); // remove any caching data that might be associated
     $('#employeeform-dolzh_id').trigger('change'); // notify JavaScript components of possible changes
     });
     };*/

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

                selectFromUrl($widget);
                eventsApply($widget);
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