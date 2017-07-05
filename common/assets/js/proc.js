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

    var navElem = $('nav.navbar.navbar-fixed-top');
    var padding = 0;
    var scrollw = 0;
    $(document).on('show.bs.modal', 'div.modal', function (e) {
        console.debug("Dialogs shows: " + $('.modal:visible').length);

        if ($('.modal:visible').length == 0) {
            scrollw = window.innerWidth - $('body').width();
            padding = parseInt(navElem.css('paddingLeft'));
            navElem.css({paddingLeft: (padding - scrollw) + 'px'});
        }

        var zIndex = Math.max.apply(null, Array.prototype.map.call(document.querySelectorAll('*'), function (el) {
                return +el.style.zIndex;
            })) + 10;

        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);

    }).on('hidden.bs.modal', 'div.modal', function (e) {
        if ($('.modal:visible').length == 0) {
            scrollw = window.innerWidth - $('body').width();
            padding = parseInt(navElem.css('paddingLeft'));
            navElem.css({paddingLeft: (padding + scrollw) + 'px'});
        }

        $('.modal:visible').length && $(document.body).addClass('modal-open');
    });

    $('div.wrapper').css({opacity: 1});
    /*-----------------------------------------------------------------*/

    $(document).on('click', '.wk-report', function (e) {
        $.ajax({
            url: $(this).attr('href'),
            success: function (response) {
                if (typeof $("#wk-Report-Loader").data('bs.modal') == 'undefined' || !$("#wk-Report-Loader").data('bs.modal').isShown) {
                    window.open(response);
                }
            }
        });
        e.preventDefault();
    });

    $(document).on('click', 'a.btn-success', function (e) {
        $('#large-dialog').modal();

        e.preventDefault();
    });

    $('#large-dialog').on('shown.bs.modal', function (e) {

        $('.grid-content').load('/wk-portal/manager/roles/index-for-roles');
        /*$.ajax({
            url: '/wk-portal/manager/roles/index-for-roles',
            success: function(response) {
                $('.grid-content').append(response);
            }
        });*/
    })
});
