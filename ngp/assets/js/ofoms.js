$(document).ready(function () {
    $('#ofoms_search').on('keypress', function (e) {
        if (e.which === 13 && $(this).val() !== '') {
            reloadGrid();
        }
    });

    $("#ofomsGrid-pjax").on('pjax:success', function () {
        $(this)[0].busy = false;
        $('.wkbc-breadcrumb').wkbreadcrumbs('changeCurrentUrl', window.location.href);
    });

    $("#ofomsGrid-pjax").on('pjax:beforeSend', function () {
        $(this)[0].busy = true;
    });

    $(".wk-ofoms-attach-list-button").click(function () {
        $(".wk-ofoms-attach-list-input").click();
    });

    $(".wk-ofoms-attach-list-input").change(function() {
        $('.wk-ofoms-attach-list-button').append('<span>'+$(this).val().replace(/C:\\fakepath\\/i, '')+'</span>');

    });
});

var reloadGrid = function () {
    var busy = false;

    if ($("#ofomsGrid-pjax")[0].busy) {
        busy = $(this)[0].busy;
    }

    if (busy === false) {
        $('#ofomsGrid').yiiGridView({
            "filterUrl": window.location.search,
            "filterSelector": '#ofoms_search' //'field-ofomssearch-search_string input'
        });
    }
};