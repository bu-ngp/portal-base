/**
 * Created by VOVANCHO on 16.05.2017.
 */
;(function ($) {
    jQuery.fn.wkcardlist = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkcardlist');
        }
    };

    var LANG = {
        followLink: 'Follow the link',
        search: 'Search'
    };

    var defaults = {
        url: '',
        cardsPerPage: 6,
        items: [],
        search: false,
        popularity: false,
        ajaxSearchName: 'search'
    };

    var cardlistEvents = {};
    var cardlistEventHandlers = {};

    var makeCard = function (props) {

        if (typeof props === 'object') {
            props.scroll = (typeof props.scroll === "undefined" || props.scroll == false) ? '' : 'wk-widget-scroll';
            var popularityID = typeof props.popularityID != undefined ? ' popularity-id="' + props.popularityID + '"' : '';

            var $card = $('<div' + popularityID + ' class="col-xs-12 col-sm-6 col-md-4 wk-widget-card wk-widget-show ' + props.scroll + '">' +
                '<div class="pmd-card pmd-card-default pmd-z-depth">' +
                '</div>' +
                '</div>');

            if (typeof props.popularityID != 'undefined') {
                $card.not($card.children()).bind('click', function () {
                    var popularity = $.parseJSON(localStorage.popularity);
                    var popularity2 = popularity[props.widgetID];
                    var popularitylocal = popularity2.local;
                    var popularityajax = popularity2.ajax;
                    var tmpObj = {};
                    var tmpObj2 = {};

                    var local = {};
                    var ajax = {};
                    if (props.scroll) {
                        ajax[props.popularityID] = (typeof popularityajax[props.popularityID] == 'undefined') ? 1 : ++popularityajax[props.popularityID];
                        tmpObj[props.widgetID] = {ajax: ajax};
                    } else {
                        local[props.popularityID] = (typeof popularitylocal[props.popularityID] == 'undefined') ? 1 : ++popularitylocal[props.popularityID];
                        tmpObj2[props.widgetID] = {local: local};
                    }

                    localStorage.popularity = JSON.stringify($.extend(true, popularity, tmpObj, tmpObj2));
                });
            }

            var $media = makeMedia({
                preview: props.preview,
                styleClass: props.styleClass
            });

            var $title = makeTitle({
                title: props.title,
                description: props.description
            });

            var $actions = makeActions({
                link: props.link
            });

            var $cardInside = $media.add($title).add($actions);

            $cardInside.appendTo($card.children('div'));

            return $card;
        } else {
            console.error('makeCard(props) - props is not Object, is ' + (typeof props));
        }
    };

    var makeMedia = function (contentObj) {
        var preview = '';
        var styleWidgetMedia = '';
        if (typeof contentObj.preview === 'string') {
            preview = '<img class="img-responsive" src="' + contentObj.preview + '">';
        } else if (typeof contentObj.preview === 'object') {
            preview = '<i class="fa fa-' + contentObj.preview.FAIcon + ' wk-style"></i>';
            styleWidgetMedia = contentObj.styleClass;
        }

        return $('<div class="pmd-card-media ' + styleWidgetMedia + '">' +
            preview +
            '</div>');
    };

    var makeTitle = function (contentObj) {
        return $('<div class="pmd-card-title">' +
            '<h2 class="pmd-card-title-text">' +
            contentObj.title +
            '</h2>' +
            '<span class="pmd-card-subtitle-text">' +
            contentObj.description +
            '</span>' +
            '</div>');
    };

    var makeActions = function (contentObj) {
        return $('<div class="pmd-card-actions">' +
            '<a href="' + contentObj.link + '" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">' +
            LANG.followLink +
            '</a>' +
            '</div>');
    };

    var accessKeys = function (e) {
        return e.keyCode == 8 // Backspace
            || e.keyCode == 13 // Enter
            || e.keyCode == 46; // Delete
    };

    var makeSearchPanel = function ($widget) {
        $('<div class="row pmd-z-depth wk-widget-search-panel">' +
            '<div class="col-xs-1 wk-widget-search-panel-icon">' +
            '<i class="fa fa-map-marker fa-4x"></i>' +
            '</div>' +
            '<div class="col-xs-11 wk-widget-search-panel-field">' +
            '<div class="form-group pmd-textfield pmd-textfield-floating-label form-group-lg">' +
            '<label for="search_cards" class="control-label">' + LANG.search + '</label>' +
            '<input type="text" id="' + $widget[0].id + '-wk-widget-search-input" class="form-control input-group-lg">' +
            '</div>' +
            '</div>' +
            '</div>').prependTo($widget);

        $widget.data('wkcardlist').$searchInput = $('#' + $widget[0].id + '-wk-widget-search-input');
        $widget.data('wkcardlist').$searchInput.busy = false;
        $widget.data('wkcardlist').searchString = '';

        $widget.data('wkcardlist').$searchInput.keypress(function (e) {
            if (!e.ctrlKey && !e.altKey) {
                if (!e.shiftKey) {
                    $widget.data('wkcardlist').$searchInput.oldText = $(this).val();
                }

                $widget.data('wkcardlist').keypress = true;
            }
        });

        $widget.data('wkcardlist').$searchInput.keyup(function (e) {
            delay(function () {
                if (!$widget.data('wkcardlist').$searchInput.busy
                    && ($widget.data('wkcardlist').keypress || accessKeys(e))
                ) {
                    $widget.data('wkcardlist').keypress = false;
                    if (textSearchChange($widget) || accessKeys(e)) {
                        if ($widget.data('wkcardlist').$searchInput.val().length >= 3 || $widget.data('wkcardlist').$searchInput.val().length == 0) {
                            localSearch($widget, function () {
                                ajaxSearch($widget, function () {
                                    removeAllandAddItemsMasonry($widget, function () {
                                        console.debug('searchComplete');
                                    });
                                });
                            });
                        }
                    }
                }
            }, 500);
        });
    };

    var Localization = function (LANG) {
        if (typeof WK_WIDGET_CARDLIST_I18N !== "undefined") {
            return $.extend(LANG, WK_WIDGET_CARDLIST_I18N);
        }
    };

    var makeID = function ($container) {
        var attr = $container.attr('id');
        if (typeof attr == undefined) {
            $container.attr('id', 'wk-card-list-' + Math.floor((Math.random() * 1000000) + 1));
        }
    };

    var makeScrollPager = function ($widget, scrollHandler) {
        $widget.data('wkcardlist').pager = $('<div id="' + $widget[0].id + '-scroll-pager" class="wk-widget-pager" style="display: none;">' +
            '<i class="fa fa-refresh fa-4x fa-spin"></i>' +
            '</div>').insertAfter($widget.data('wkcardlist').$masonryContainer);

        $widget.data('wkcardlist').currentPage = 1;
        $widget.data('wkcardlist').scrollHandler = scrollHandler;

        initScrollPager($widget);
        triggerNextPage($widget, scrollHandler);
    };

    var initScrollPager = function ($widget) {
        if (typeof $widget != undefined) {
            $(window).unbind().bind('scroll', function () {
                if ($(window).scrollTop() + 5 >= $(document).height() - $(window).height()) {
                    triggerNextPage($widget, $widget.data('wkcardlist').scrollHandler);
                }
            });
        } else {
            console.error('initScrollPager($widget) - $widget undefined');
        }
    };

    var initMasonry = function ($widget) {
        $widget.data('wkcardlist').$masonryContainer.masonry({
            itemSelector: '.wk-widget-card',
            isAnimated: true,
            horizontalOrder: true,
            percentPosition: true,
            transitionDuration: '0.4s'
        });
    };

    var triggerNextPage = function ($widget, afterComplete) {
        if (typeof $widget != undefined) {
            if (!$widget.data('wkcardlist').$masonryContainer.ajaxSended || typeof $widget.data('wkcardlist').$masonryContainer.ajaxSended == undefined) {

                $widget.data('wkcardlist').pager.show();
                var data = {
                    page: $widget.data('wkcardlist').currentPage,
                    'per-page': $widget.data('wkcardlist').settings.cardsPerPage
                };

                if ($widget.data('wkcardlist').searchString != '') {
                    data[$widget.data('wkcardlist').settings.ajaxSearchName] = $widget.data('wkcardlist').searchString;
                }

                if ($widget.data('wkcardlist').popularity) {
                    var obj1 = $.parseJSON(localStorage.popularity);
                    var obj2 = obj1[$widget[0].id].ajax;
                    var arr1 = [];
                    var arr2 = [];
                    var str1 = '';

                    $.each(obj2, function (key, val) {
                        arr1.push({key: key, val: val});
                    });

                    arr1.sort(function (a, b) {
                        return a.val - b.val;
                    });

                    $.each(arr1, function () {
                      //  str1 =  str1.concat("'"+this.key+"'" + ',') ;
                        arr2.push(this.key);
                    });
                    str1 = str1.slice(0, -1);

                    // var p1 = ($widget.data('wkcardlist').currentPage - 1) * $widget.data('wkcardlist').settings.cardsPerPage;
                    // var p2 = $widget.data('wkcardlist').currentPage * $widget.data('wkcardlist').settings.cardsPerPage;
                    //
                    // arr1 = arr1.slice(p1, p2);
                    data['popularity'] = JSON.stringify(arr2);
                }

                $.ajax({
                    url: $widget.data('wkcardlist').settings.url,
                    data: data,
                    beforeSend: function () {
                        $widget.data('wkcardlist').$masonryContainer.ajaxSended = true;
                    },
                    success: function (items) {
                        if (typeof items == 'object') {
                            $widget.data('wkcardlist').pager.hide();

                            if (items.length == 0) {
                                $(window).unbind('scroll');
                            } else {
                                $widget.data('wkcardlist').currentPage = ++$widget.data('wkcardlist').currentPage;
                            }

                            $widget.data('wkcardlist').$cards = $widget.data('wkcardlist').$cards.add(getCards({
                                items: items,
                                scroll: true,
                                widgetID: $widget[0].id
                            }));

                            if (typeof afterComplete == 'function') {
                                afterComplete();
                            }
                        }
                        $widget.data('wkcardlist').$masonryContainer.ajaxSended = false;
                    }
                });
            }
        } else {
            console.error('triggerNextPage($widget) - $widget undefined');
        }
    };

    var getCards = function (props) {
        if (typeof props === 'object') {
            if (typeof props.scroll == 'undefined') {
                props.scroll = false;
            }

            var $cards = $();
            if (props.items != null && props.items.length > 0) {
                var $card;

                $.each(props.items, function () {
                    $card = makeCard({
                        preview: this.preview,
                        styleClass: this.styleClass,
                        title: this.title,
                        description: this.description,
                        link: this.link,
                        popularityID: this.popularityID,
                        scroll: props.scroll,
                        widgetID: props.widgetID
                    });

                    $cards = $cards.add($card);
                });
            }

            return $cards;
        } else {
            console.error('getCards(props) - props is not Object, is ' + (typeof props));
        }
    };

    var itemsExists = function (settings) {
        return settings.items != null && settings.items.length > 0;
    };

    var itemsUrlExists = function (settings) {
        return typeof(settings.url) == "string" && settings.url != ''
    };

    var MasonryExecute = function ($widget, afterComplete) {
        if (typeof $widget != undefined) {

            var isEmptyContainer = $widget.data('wkcardlist').$masonryContainer.masonry('getItemElements').length == 0;
            $widget.data('wkcardlist').$masonryContainer.append($widget.data('wkcardlist').$cards);

            $widget.data('wkcardlist').$masonryContainer.one('layoutComplete', function () {
                $widget.data('wkcardlist').$cards = $();
                $widget.data('wkcardlist').$searchInput.busy = false;

                if (typeof afterComplete == 'function') {
                    afterComplete();
                }
            });

            if (isEmptyContainer) {
                $widget.data('wkcardlist').$masonryContainer.masonry('addItems', $widget.data('wkcardlist').$cards);
                $widget.data('wkcardlist').$masonryContainer.masonry();
            } else {
                $widget.data('wkcardlist').$masonryContainer.masonry('appended', $widget.data('wkcardlist').$cards);
            }

        } else {
            console.error('MasonryExecute($widget) - $widget undefined');
        }
    };

    var removeAllandAddItemsMasonry = function ($widget, afterComplete) {
        if (typeof $widget != undefined) {

            $widget.data('wkcardlist').$masonryContainer.one('removeComplete', function () {
                MasonryExecute($widget, afterComplete);
            });

            $widget.data('wkcardlist').$masonryContainer.masonry('remove', $widget.data('wkcardlist').$masonryContainer.children());

        } else {
            console.error('removeAllandAddItemsMasonry($widget) - $widget undefined');
        }
    };

    var addItemsMasonry = function ($widget, afterComplete) {
        if (typeof $widget != undefined) {
            MasonryExecute($widget, afterComplete);
        } else {
            console.error('addItemsMasonry($widget) - $widget undefined');
        }
    };

    var makeLocalCards = function ($widget, afterComplete) {
        if (typeof $widget != undefined) {
            if (itemsExists($widget.data('wkcardlist').settings)) {
                $widget.data('wkcardlist').$cards = $widget.data('wkcardlist').$cards.add(getCards({
                    items: $widget.data('wkcardlist').settings.items,
                    widgetID: $widget[0].id
                }));

                $widget.data('wkcardlist').$localCards = $widget.data('wkcardlist').$cards.clone();
            }

            if (typeof afterComplete == 'function') {
                afterComplete();
            }
        } else {
            console.error('initScrollPager($widget) - $widget undefined');
        }
    };

    var textSearchChange = function ($widget) {
        return $widget.data('wkcardlist').$searchInput.oldText !== $widget.data('wkcardlist').$searchInput.val();
    };

    var localSearch = function ($widget, afterComplete) {
        if (typeof $widget != undefined) {

            $widget.data('wkcardlist').$searchInput.busy = true;
            var searchString = $widget.data('wkcardlist').$searchInput.val().toLowerCase();

            if (!itemsExists($widget.data('wkcardlist').settings)) {
                $widget.data('wkcardlist').$searchInput.busy = false;
                if (typeof afterComplete == 'function') {
                    afterComplete();
                }
                return;
            }

            var $foundCards = $();

            if (searchString == "") {
                $foundCards = $widget.data('wkcardlist').$localCards.clone();
            } else {
                $.each($widget.data('wkcardlist').$localCards, function () {
                    var title = $(this).find(".pmd-card-title-text").text().toLowerCase();
                    var description = $(this).find(".pmd-card-subtitle-text").text().toLowerCase();

                    if ((title + description).indexOf(searchString) >= 0) {
                        $foundCards = $foundCards.add($(this).clone(true));
                    }
                });
            }

            $widget.data('wkcardlist').$cards = $foundCards;

            if (typeof afterComplete == 'function') {
                afterComplete();
            }

        } else {
            console.error('localSearch($widget) - $widget undefined');
        }
    };

    var ajaxSearch = function ($widget, afterComplete) {
        if (typeof $widget != undefined) {
            if (!itemsUrlExists($widget.data('wkcardlist').settings)) {
                if (typeof afterComplete == 'function') {
                    afterComplete();
                }
                return;
            }

            $widget.data('wkcardlist').$searchInput.busy = true;
            $widget.data('wkcardlist').searchString = $widget.data('wkcardlist').$searchInput.val().toLowerCase();
            $widget.data('wkcardlist').currentPage = 1;
            initScrollPager($widget);
            triggerNextPage($widget, afterComplete);
        } else {
            console.error('localSearch($widget) - $widget undefined');
        }
    };

    var makeAjaxCards = function ($widget, afterComplete) {
        if (itemsUrlExists($widget.data('wkcardlist').settings)) {
            makeScrollPager($widget, afterComplete);
        } else {
            if (typeof afterComplete == 'function') {
                afterComplete();
            }
        }
    };

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $widget = $(this);
                if ($widget.data('wkcardlist')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});
                if ((typeof(settings.url) == "undefined" || settings.url == '')
                    && (typeof(settings.items) == "undefined" || settings.items.length == 0)
                ) {
                    $.error('Settings url or items must be passed');
                }

                LANG = Localization(LANG);

                $widget.data('wkcardlist', {
                    widget: $widget,
                    settings: settings
                });

                console.debug(settings);

                $widget.addClass('wk-widget-container container-fluid');
                makeID($widget);

                $widget.data('wkcardlist').$masonryContainer = $('<div class="pmd-z-depth wk-widget-masonry-container"></div>').appendTo($widget);

                initMasonry($widget);

                $widget.data('wkcardlist').$localCards = $();
                $widget.data('wkcardlist').$cards = $();

                $widget.data('wkcardlist').popularity = settings.popularity;
                if (settings.popularity && typeof localStorage.popularity == 'undefined') {
                    var popularity = {};
                    popularity[$widget[0].id] = {local: {}, ajax: {}};
                    localStorage.popularity = JSON.stringify(popularity);
                }

                makeLocalCards($widget, function () {
                    makeAjaxCards($widget, function () {
                        addItemsMasonry($widget, function () {
                            console.debug('initComplete');

                        });
                    });
                });

                if (settings.search === true) {
                    makeSearchPanel($widget);
                }
            });
        },
        destroy: function () {
            return this.each(function () {
                var $widget = $(this),
                    data = $widget.data('wkcardlist');

                $(window).unbind('.wkcardlist');
                data.tooltip.remove();
                $widget.removeData('wkcardlist');
            })
        }
    };

    /**
     * Used for attaching event handler and prevent of duplicating them. With each call previously attached handler of
     * the same type is removed even selector was changed.
     * @param {jQuery} $cardList According jQuery cardlist element
     * @param {string} type Type of the event which acts like a key
     * @param {string} event Event name, for example 'change.yiiGridView'
     * @param {string} selector jQuery selector
     * @param {function} callback The actual function to be executed with this event
     */
    function initEventHandler($cardList, type, event, selector, callback) {
        var id = $cardList.attr('id');
        var prevHandler = cardlistEventHandlers[id];
        if (prevHandler !== undefined && prevHandler[type] !== undefined) {
            var data = prevHandler[type];
            $(document).off(data.event, data.selector);
        }
        if (prevHandler === undefined) {
            cardlistEventHandlers[id] = {};
        }
        $(document).on(event, selector, callback);
        cardlistEventHandlers[id][type] = {event: event, selector: selector};
    }

})(jQuery);