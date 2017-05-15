/**
 * Created by VOVANCHO on 15.05.2017.
 */
$(document).ready(function () {
    $('#id-widget').imagesLoaded(function () {
        $('#id-widget').masonry({
            itemSelector: '.wk-widget-card',
            isAnimated: true,
            horizontalOrder: true,
            percentPosition: true
        });
    });
    $(window).scroll(function () {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('div#id-widget-scroll-pager').show();
            $.ajax({
                url: "wkcardlist/wk-widget/scroll",
                success: function (html) {
                    if (html) {
                        var $items = $(html);
                        $('#id-widget').append($items);
                        $('#id-widget').masonry('appended', $items);
                        $($items).animate({opacity: 1}, 500);
                        $('div#id-widget-scroll-pager').hide();
                    } else {
                        $('div#id-widget-scroll-pager').html('<span>No more cards to show.</span>');
                    }
                }
            });
        }
    });

    $(".wk-widget-card.wk-widget-show").each(function (i, elem) {
        var stallFor = 100 * parseInt(i);
        $(this).delay(stallFor).animate({opacity: 1}, 500);
    });

});