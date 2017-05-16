/**
 * Created by VOVANCHO on 16.05.2017.
 */
(function ($) {
    var options = {
        'url': '',
        'linkName': 'Follow the link',
        'items': [],
        init: function (options) {
            return this.each(function () {

                var $this = $(this),
                    data = $this.data('wkcardlist');

                // Если плагин ещё не проинициализирован
                if (!data) {

                    /*
                     * Тут выполняем инициализацию
                     */

                    if ((typeof(options.url) == "undefined" || options.url == '')
                        && (typeof(options.items) == "undefined" || options.items.length == 0)
                    )
                        $.error('Options url or items must be passed');


                    console.debug(options);

                    $this.addClass('row wk-widget-container');

                    if (options.items.length > 0) {
                        $.each(options.items, function (index, card) {
                            $this.append(
                                makeCard(
                                    makeMedia({
                                        content: card
                                    }) + (
                                        makeTitle({
                                            title: card.title,
                                            description: card.description
                                        })
                                    ) + (
                                        makeActions({
                                            link: card.link,
                                            linkName: options.linkName
                                        })
                                    )
                                )
                            );
                        });
                    }

                    $pagerid = $this.attr('id') + '-scroll-pager';

                    $('<div id="' + $pagerid + '" class="wk-widget-pager" style="display: none;"><i class="fa fa-refresh fa-4x fa-spin"></i></div>').insertAfter($this);

                    $this.imagesLoaded(function () {
                        $this.on('layoutComplete',
                            function (event, laidOutItems) {

                            }
                        );

                        $this.masonry({
                            itemSelector: '.wk-widget-card',
                            isAnimated: true,
                            horizontalOrder: true,
                            percentPosition: true
                        });


                    });

                    $(window).scroll(function () {
                        if (($(window).scrollTop() == $(document).height() - $(window).height())
                            || $(window).height() >= $(document).height()
                        ) {
                            $('div#' + $pagerid).show();
                            $.ajax({
                                url: options.url, // "wkcardlist/wk-widget/scroll",
                                success: function (items) {

                                    /*  items = $.parseJSON(items);*/
                                    if (typeof items == 'object') {
                                        if (items.length > 0) {
                                            var $items = $();

                                            $.each(items, function (index, card) {
                                                $item = makeCard(
                                                    makeMedia({
                                                        content: card
                                                    }) + (
                                                        makeTitle({
                                                            title: card.title,
                                                            description: card.description
                                                        })
                                                    ) + (
                                                        makeActions({
                                                            link: card.link,
                                                            linkName: options.linkName
                                                        })
                                                    ), true
                                                );

                                                $items = $items.add($item);
                                            });

                                            $this.append($items);
                                            $this.masonry('appended', $items);
                                            $($items).animate({opacity: 1}, 500);
                                            $('div#' + $pagerid).hide();
                                        } else {
                                            $(window).unbind('scroll');
                                            $('div#' + $pagerid).html('<span>No more cards to show.</span>');

                                        }
                                    }
                                }
                            });
                        }
                    });

                    $(".wk-widget-card.wk-widget-show").each(function (i, elem) {
                        var stallFor = 100 * parseInt(i);
                        $(this).delay(stallFor).animate({opacity: 1}, 500);
                    });


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

    function makeCard(elem, scroll) {
        scroll = (typeof scroll === "undefined") ? '' : 'wk-widget-scroll';

        return $('<div class="col-xs-12 col-sm-6 col-md-4 wk-widget-card wk-widget-show ' + scroll + '">' +
            '<div class="pmd-card pmd-card-default pmd-z-depth">' +
            elem +
            '</div>' +
            '</div>');
    }

    function makeMedia(contentObj) {
        preview = '';
        styleWidgetMedia = '';
        if (typeof contentObj.content.preview === 'string') {
            preview = '<img class="img-responsive" src="' + contentObj.content.preview + '">';
        } else if (typeof contentObj.content.preview === 'object') {
            preview = '<i class="fa fa-' + contentObj.content.preview.FAIcon + ' wk-style"></i>';
            styleWidgetMedia = contentObj.content.styleClass;
        }

        return '<div class="pmd-card-media ' + styleWidgetMedia + '">' +
            preview +
            '</div>';
    }

    function makeTitle(contentObj) {
        return '<div class="pmd-card-title">' +
            '<h2 class="pmd-card-title-text">' +
            contentObj.title +
            '</h2>' +
            '<span class="pmd-card-subtitle-text">' +
            contentObj.description +
            '</span>' +
            '</div>';
    }

    function makeActions(contentObj) {
        return '<div class="pmd-card-actions">' +
            '<a href="' + contentObj.link + '" class="btn pmd-btn-flat pmd-ripple-effect btn-primary">' +
            contentObj.linkName +
            '</a>' +
            '</div>';
    }

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