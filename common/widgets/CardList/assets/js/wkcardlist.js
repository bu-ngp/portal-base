/**
 * Created by VOVANCHO on 16.05.2017.
 */
;(function ($) {
    var LANG = {
        followLink: 'Follow the link',
        search: 'Search'
    };

    var options = {
        'url': '',
        'linkName': LANG.followLink,
        'items': [],
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

            function initMasonry($thisRow, pagerid) {
                $thisRow.imagesLoaded(function () {
                    $thisRow.on('layoutComplete', function () {
                        if (typeof(options.url) == "string" && options.url != '') {
                            triggerNextPage({
                                pagerid: pagerid,
                                appendTo: $thisRow
                            });
                            $thisRow.unbind('layoutComplete');
                        }
                    });

                    $thisRow.masonry({
                        itemSelector: '.wk-widget-card',
                        isAnimated: true,
                        horizontalOrder: true,
                        percentPosition: true
                    });
                });
            }

            function initScrollPager(props) {
                if (typeof props === 'object') {
                    $(window).scroll(function () {
                        /* console.debug('$(window).scrollTop(): ' + $(window).scrollTop());
                         console.debug('$(document).height(): ' + $(document).height());
                         console.debug('$(window).height(): ' + $(window).height());
                         console.debug('$(document).height() - $(window).height(): ' + ($(document).height() - $(window).height()));
                         */
                        if ($(window).scrollTop() + 5 >= $(document).height() - $(window).height()) {
                            triggerNextPage({
                                pagerid: props.pagerid,
                                appendTo: props.appendTo
                            });
                        }
                    });
                } else {
                    console.error('initScrollPager(props) - props is not Object, is ' + (typeof props));
                }
            }

            function triggerNextPage(props) {
                if (typeof props === 'object') {
                    $('div#' + props.pagerid).show();
                    $.ajax({
                        url: options.url,
                        success: function (items) {
                            if (typeof items == 'object') {
                                fillCards({
                                    items: items,
                                    appendToElem: props.appendTo,
                                    scroll: true,
                                    pagerid: props.pagerid,
                                    fillComplete: function ($items) {
                                        this.appendToElem.masonry('appended', $items);
                                        $($items).animate({opacity: 1}, 500);
                                        $('div#' + this.pagerid).hide();
                                    },
                                    fillIncomplete: function () {
                                        $(window).unbind('scroll');
                                        $('div#' + this.pagerid).hide();
                                    }
                                });
                            }
                        }
                    });
                } else {
                    console.error('triggerNextPage(props) - props is not Object, is ' + (typeof props));
                }
            }

            function fillCards(props) {
                if (typeof props === 'object') {
                    if (props.items != null && props.items.length > 0) {
                        var $cards = $();
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

                        props.appendToElem.append($cards);

                        if (typeof props.fillComplete === 'function') {
                            props.fillComplete($cards);
                        }
                    } else {
                        if (typeof props.fillComplete === 'function') {
                            props.fillIncomplete();
                        }
                    }
                } else {
                    console.error('fillCards(props) - props is not Object, is ' + (typeof props));
                }
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

                    $this.addClass('wk-widget-container');

                    var idWidget = makeID($this);
                    var pagerid = idWidget + '-scroll-pager';

                    makeSearchPanel(idWidget).appendTo($this);

                    var $thisRow = $('<div class="row pmd-z-depth"></div>').appendTo($this);

                    fillCards({
                        items: options.items,
                        appendToElem: $thisRow
                    });

                    if (typeof(options.url) == "string" && options.url != '') {
                        makeScrollPager(pagerid).insertAfter($thisRow);
                    }

                    initMasonry($thisRow, pagerid);

                    if (typeof(options.url) == "string" && options.url != '') {
                        initScrollPager({
                            pagerid: pagerid,
                            appendTo: $thisRow
                        });
                    }

                    if (options.items != null && options.items.length > 0) {
                        $(".wk-widget-card.wk-widget-show").each(function (i) {
                            var stallFor = 100 * parseInt(i);
                            $(this).delay(stallFor).animate({opacity: 1}, 500);
                        });
                    }

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
            return options.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' not exists in jQuery.wkcardlist');
        }
    };

})(jQuery);