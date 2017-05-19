/**
 * Created by VOVANCHO on 16.05.2017.
 */
;(function ($) {
    var LANG = {
        followLink: 'Follow the link',
        search: 'Search'
    };

    var options = {
        url: '',
        cardsPerPage: 6,
        items: [],
        search: false,
        ajaxSearchName: 'search',
        //ajaxSearchName: 'AuthItemSearch[description]',
        init: function (options) {

            function makeCard(props) {
                if (typeof props === 'object') {
                    props.scroll = (typeof props.scroll === "undefined" || props.scroll == false) ? '' : 'wk-widget-scroll';

                    var $card = $('<div class="col-xs-12 col-sm-6 col-md-4 wk-widget-card wk-widget-show ' + props.scroll + '">' +
                        '<div class="pmd-card pmd-card-default pmd-z-depth">' +
                        '</div>' +
                        '</div>');

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
            }

            function makeMedia(contentObj) {
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
            }

            function makeTitle(contentObj) {
                return $('<div class="pmd-card-title">' +
                    '<h2 class="pmd-card-title-text">' +
                    contentObj.title +
                    '</h2>' +
                    '<span class="pmd-card-subtitle-text">' +
                    contentObj.description +
                    '</span>' +
                    '</div>');
            }

            function makeActions(contentObj) {
                return $('<div class="pmd-card-actions">' +
                    '<a href="' + contentObj.link + '" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">' +
                    LANG.followLink +
                    '</a>' +
                    '</div>');
            }

            function makeSearchPanel(id) {
                return $('<div class="row pmd-z-depth wk-widget-search-panel">' +
                    '<div class="col-xs-1 wk-widget-search-panel-icon">' +
                    '<i class="fa fa-map-marker fa-4x"></i>' +
                    '</div>' +
                    '<div class="col-xs-11 wk-widget-search-panel-field">' +
                    '<div class="form-group pmd-textfield pmd-textfield-floating-label form-group-lg">' +
                    '<label for="search_cards" class="control-label">' + LANG.search + '</label>' +
                    '<input type="text" id="' + id + '-wk-widget-search-input" class="form-control input-group-lg">' +
                    '</div>' +
                    '</div>' +
                    '</div>');
            }

            function Localization(LANG) {
                if (typeof WK_WIDGET_CARDLIST_I18N !== "undefined") {
                    return $.extend(LANG, WK_WIDGET_CARDLIST_I18N);
                }
            }

            function makeID(container) {
                var attr = $(container).attr('id');

                if (typeof attr !== typeof undefined && attr !== false) {
                    return attr;
                } else {
                    return 'wk-card-list-' + Math.floor((Math.random() * 1000000) + 1);
                }
            }

            function makeScrollPager(pagerid) {
                return $('<div id="' + pagerid + '" class="wk-widget-pager" style="display: none;"><i class="fa fa-refresh fa-4x fa-spin"></i></div>');
            }

            function initMasonry($thisRow) {
                $thisRow.masonry({
                    itemSelector: '.wk-widget-card',
                    isAnimated: true,
                    horizontalOrder: true,
                    percentPosition: true,
                    transitionDuration: '0.4s'
                });
            }

            function initScrollPager(props) {
                if (typeof props === 'object') {
                    $(window).unbind().bind('scroll', function () {
                        if ($(window).scrollTop() + 5 >= $(document).height() - $(window).height()) {
                            triggerNextPage({
                                pagerid: props.pagerid,
                                container: props.container
                            });
                        }
                    });
                } else {
                    console.error('initScrollPager(props) - props is not Object, is ' + (typeof props));
                }
            }

            function triggerNextPage(props) {
                if (typeof props === 'object') {
                    if (!props.container.ajaxSended || typeof props.container.ajaxSended == 'undefined') {
                        /*  if (typeof props.searchValue == 'undefined') {
                         props.searchValue = '';
                         }*/

                        if (typeof props.removeBefore == 'undefined') {
                            props.removeBefore = false;
                        }

                        $('div#' + props.pagerid).show();

                        var data = {
                            page: props.container.currentPage,
                            'per-page': options.cardsPerPage
                        };

                        if (props.container.searchString != '') {
                            data[options.ajaxSearchName] = props.container.searchString;
                        }

                        $.ajax({
                            url: options.url,
                            data: data,
                            beforeSend: function () {
                                props.container.ajaxSended = true;
                            },
                            success: function (items) {
                                if (typeof items == 'object') {
                                    addItemsMasonry({
                                        itemsElements: getCards({
                                            items: items,
                                            scroll: true
                                        }),
                                        container: props.container,
                                        removeBefore: props.removeBefore
                                    });

                                    $('div#' + props.pagerid).hide();

                                    if (items.length == 0) {
                                        $(window).unbind('scroll');
                                    } else {
                                        props.container.currentPage = ++props.container.currentPage;
                                    }
                                }
                                props.container.ajaxSended = false;
                            }
                        });
                    }
                } else {
                    console.error('triggerNextPage(props) - props is not Object, is ' + (typeof props));
                }
            }

            function getCards(props) {
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
                                scroll: props.scroll
                            });

                            $cards = $cards.add($card);
                        });
                    }

                    return $cards;
                } else {
                    console.error('getCards(props) - props is not Object, is ' + (typeof props));
                }
            }

            function itemsExists() {
                return options.items != null && options.items.length > 0;
            }

            function itemsUrlExists() {
                return typeof(options.url) == "string" && options.url != ''
            }

            function addItemsMasonry(props) {
                if (typeof props === 'object') {
                    if (typeof props.removeBefore != 'undefined' && props.removeBefore) {
                        props.container.one('removeComplete', function () {
                            var isEmptyContainer = props.container.masonry('getItemElements').length == 0;
                            props.container.append(props.itemsElements);
                            if (isEmptyContainer) {
                                props.container.masonry('addItems', props.itemsElements);
                                props.container.masonry();
                            } else {
                                props.container.masonry('appended', props.itemsElements);
                            }
                        });

                        props.container.masonry('remove', props.container.children('.wk-widget-scroll'));
                    } else {
                        var isEmptyContainer = props.container.masonry('getItemElements').length == 0;
                        props.container.append(props.itemsElements);
                        if (isEmptyContainer) {
                            props.container.masonry('addItems', props.itemsElements);
                            props.container.masonry();
                        } else {
                            props.container.masonry('appended', props.itemsElements);
                        }
                    }
                } else {
                    console.error('addItemsMasonry(props) - props is not Object, is ' + (typeof props));
                }
            }

            function addButtons() {
                return $('<button id="b1" class="btn pmd-btn-flat pmd-ripple-effect btn-success">Layout</button>' +
                    '<button id="b2" class="btn pmd-btn-flat pmd-ripple-effect btn-success">ReloadItems</button>'
                );
            }

            return this.each(function () {
                var $this = $(this),
                    data = $this.data('wkcardlist');

                if (!data) {
                    if ((typeof(options.url) == "undefined" || options.url == '')
                        && (typeof(options.items) == "undefined" || options.items.length == 0)
                    )
                        $.error('Options url or items must be passed');

                    LANG = Localization(LANG);

                    console.debug(options);

                    $this.addClass('wk-widget-container container-fluid');

                    var idWidget = makeID($this);
                    var pagerid = idWidget + '-scroll-pager';

                    if (options.search === true) {
                        makeSearchPanel(idWidget).appendTo($this);
                        var $searchInput = $('#' + idWidget + '-wk-widget-search-input');
                        $searchInput.busy = false;
                    }

                    var $thisRow = $('<div class="pmd-z-depth"></div>').appendTo($this);
                    $thisRow.searchString = '';

                    if (itemsUrlExists()) {
                        makeScrollPager(pagerid).insertAfter($thisRow);
                        $thisRow.currentPage = 1;
                    }

                    initMasonry($thisRow);

                    if (itemsExists()) {
                        var $cardTmp = getCards({
                            items: options.items
                        });
                        var $LocalElemsStore = $cardTmp.clone(true);

                        addItemsMasonry({
                            itemsElements: $cardTmp,
                            container: $thisRow
                        });

                    }

                    if (itemsUrlExists()) {
                        initScrollPager({
                            pagerid: pagerid,
                            container: $thisRow
                        });

                        triggerNextPage({
                            pagerid: pagerid,
                            container: $thisRow
                        });
                    }

                    //--------

                    function keyCtrlPress(event) {
                        return event.keyCode == 17 || event.ctrlKey
                    }

                    function textSearchChange($searchInput) {
                        return $searchInput.oldText !== $searchInput.val();
                    }

                    function localSearch($searchInput, $container, afterSearch) {
                        $searchInput.busy = true;
                        var searchString = $searchInput.val().toLowerCase();

                        if (!itemsExists()) {
                            $searchInput.busy = false;
                            if (typeof afterSearch == 'function') {
                                afterSearch();
                            }
                            return;
                        }

                        var $foundCards = $();

                        if (searchString == "") {
                            $foundCards = $LocalElemsStore.clone(true);
                        } else {
                            $.each($LocalElemsStore, function () {
                                var title = $(this).find(".pmd-card-title-text").text().toLowerCase();
                                var description = $(this).find(".pmd-card-subtitle-text").text().toLowerCase();

                                if ((title + description).indexOf(searchString) >= 0) {
                                    $foundCards = $foundCards.add($(this).clone(true));
                                }
                            });
                        }

                        $container.one('removeComplete', function () {
                            $container.one('layoutComplete', function () {
                                $container.masonry('reloadItems');
                                $container.masonry();
                                $searchInput.busy = false;
                                if (typeof afterSearch == 'function') {
                                    afterSearch();
                                }
                            });

                            addItemsMasonry({
                                itemsElements: $foundCards,
                                container: $container
                            });
                        });

                        $container.masonry('remove', $container.children());
                    }

                    function ajaxSearch($searchInput, $container, afterSearch) {
                        $searchInput.busy = true;
                        var searchString = $searchInput.val().toLowerCase();
                        $thisRow.currentPage = 1;
                        if (itemsUrlExists()) {
                            initScrollPager({
                                pagerid: pagerid,
                                container: $thisRow
                            });
                        }

                        if (!itemsUrlExists()) {
                            $searchInput.busy = false;
                            if (typeof afterSearch == 'function') {
                                afterSearch();
                            }
                            return;
                        }

                        $thisRow.searchString = searchString;
                        triggerNextPage({
                            pagerid: pagerid,
                            container: $thisRow,
                            removeBefore: true
                            //   searchValue: searchString
                        });
                    }

                    if (options.search === true) {
                        $searchInput.keypress(function (e) {
                            if (!keyCtrlPress(e)) {
                                $(this).oldText = $(this).val();
                            }
                        });

                        $searchInput.keyup(function (e) {
                            if (!$(this).busy) {
                                if (!keyCtrlPress(e) && textSearchChange($(this))) {
                                    if ($(this).val().length >= 3 || $(this).val().length == 0) {
                                        localSearch($(this), $thisRow, function () {
                                            ajaxSearch($searchInput, $thisRow);
                                        });
                                    }
                                }
                            }
                        });
                    }
                    //---------

                    $(this).data('wkcardlist', {
                        target: $this
                    });
                }
            });
        },
        destroy: function () {
            return this.each(function () {
                var $this = $(this),
                    data = $this.data('wkcardlist');

                $(window).unbind('.wkcardlist');
                data.tooltip.remove();
                $this.removeData('wkcardlist');
            })
        }
    };

    jQuery.fn.wkcardlist = function (method) {
        if (options[method]) {
            return options[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            method = $.extend(options, method);
            return options.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkcardlist');
        }
    };

})(jQuery);