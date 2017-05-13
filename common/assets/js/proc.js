var checkScrollBars = function () {
    var b = $('body');
    var normalw = 0;
    var scrollw = 0;
    if (b.prop('scrollHeight') > b.height()) {
        normalw = window.innerWidth;
        scrollw = normalw - b.width();
        $('div.wrapper').css({paddingLeft: scrollw + 'px'});
        $('nav.navbar.navbar-fixed-top').css({paddingLeft: scrollw + 'px'});
    } else {
        $('div.wrapper').css({paddingLeft: scrollw + 'px'});
        $('nav.navbar.navbar-fixed-top').css({paddingLeft: scrollw + 'px'});
    }
};

$(function () {
    /*----------- Костыль убирающий сдвиги контента при изменении
     размера страницы, появлении скролинга, открытии модального окна ----------------*/
    checkScrollBars();
    $(window).resize(function () {
        checkScrollBars();
    });

    var modalElem = $('div.modal');
    var navElem = $('nav.navbar.navbar-fixed-top');
    var padding = 0;
    var scrollw = 0;

    modalElem.on('show.bs.modal', function (e) {
        scrollw = window.innerWidth - $('body').width();
        padding = parseInt(navElem.css('paddingLeft'));
        navElem.css({paddingLeft: (padding - scrollw) + 'px'});
    }).on('hidden.bs.modal', function (e) {
        scrollw = window.innerWidth - $('body').width();
        padding = parseInt(navElem.css('paddingLeft'));
        navElem.css({paddingLeft: (padding + scrollw) + 'px'});
    });

    modalElem.on('show.bs.modal', function (e) {
        t1 = $('#simple-dialog > div > div > div > p').text();
        $('#simple-dialog > div > div > div > p').text(t1 + " my Nigga!");
    });

    $('div.wrapper').css({opacity: 1});
    /*-----------------------------------------------------------------*/
});
