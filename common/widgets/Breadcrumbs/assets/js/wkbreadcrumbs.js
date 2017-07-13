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

            if ("crumbs" in bc && bc.crumbs.length) {
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

            if ("crumbs" in bc && bc.crumbs.length) {
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
            url: $widget.attr("home-crumb-url"),
            visible: true,
            forms: {}
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
        if (!$widget.data('wkbreadcrumbs').crumbs.length) {
            return;
        }

        var items = '';
        $.each($widget.data('wkbreadcrumbs').crumbs, function (index) {
            if (this.visible) {
                var item = index === $widget.data('wkbreadcrumbs').crumbs.length - 1 ? '<li class="active">' + this.title + '</li>' : '<li><a href="' + this.url + '">' + this.title + '</a></li>';

                items = items + item;
            }
        });

        return $('<ul class="breadcrumb">' + items + '</ul>');
    };

    var addCurrentBreadcrumb = function ($widget) {
        var bc = [];
        var addCurrent = true;

        $.each($widget.data('wkbreadcrumbs').crumbs, function (ind) {
            if (!$widget.attr("current-crumb-id")) {
                return false;
            }

            bc.push(this);

            if (this.id === $widget.attr("current-crumb-id")) {
                addCurrent = false;
                return false;
            }
        });

        if (bc.length && addCurrent) {
            bc.push({
                id: $widget.attr("current-crumb-id"),
                title: $widget.attr("current-crumb-title"),
                url: window.location.pathname + window.location.search,
                visible: true,
                forms: {}
            });
        }

        if ($widget.attr("remove-last-crumb") === '1' && bc[bc.length - 2]) {
            bc[bc.length - 2].visible = false;
        }

        $widget.data('wkbreadcrumbs').crumbs = bc;
    };

    var fillForms = function ($widget) {
        if ($widget.data('wkbreadcrumbs').crumbs.length) {
            var lastBC = $widget.data('wkbreadcrumbs').crumbs[$widget.data('wkbreadcrumbs').crumbs.length - 1];

            $.each(lastBC.forms, function (name, value) {
                var input = $('[name="' + name + '"]');

                if (input.length && !input.val()) {
                    input.val(value);
                }
            });
        }
    };

    var eventsApply = function ($widget) {

        $(document).on('change', 'input[wkkeep]', function () {
            getFromSessionStorage($widget, {
                fail: function () {
                    getFromLocalStorage($widget);
                }
            });

            if ($widget.data('wkbreadcrumbs').crumbs.length && $(this).is('[name]')) {
                var lastBC = $widget.data('wkbreadcrumbs').crumbs[$widget.data('wkbreadcrumbs').crumbs.length - 1];

                lastBC.forms[$(this).attr("name")] = $(this).val();
                $widget.data('wkbreadcrumbs').crumbs[$widget.data('wkbreadcrumbs').crumbs.length - 1] = lastBC;

                saveCrumbs($widget);
            }
        });

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
                    id: $widget[0].id,
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

                $widget.append(constructBreadcrumbs($widget));

                fillForms($widget);

                eventsApply($widget);

            });
        },
        getLast: function () {
            var $widget = $(this);

            if ($widget.data('wkbreadcrumbs').crumbs.length > 0) {
                return $widget.data('wkbreadcrumbs').crumbs[$widget.data('wkbreadcrumbs').crumbs.length - 1];
            }

            return false;
        },
        setLast: function (bcObj) {
            var $widget = $(this);

            if ($widget.data('wkbreadcrumbs').crumbs.length > 0) {
                $widget.data('wkbreadcrumbs').crumbs[$widget.data('wkbreadcrumbs').crumbs.length - 1] = bcObj;
                saveCrumbs($widget);
                return true;
            }

            return false;
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