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

    $("#ofomsGrid-pjax").on('pjax:error', function (e, xhr) {
        var response = $.parseJSON(xhr.responseText);
        toastr.error(response.message, 'Ошибка!');
    });

    $("#ofomsGrid-pjax").on('pjax:beforeSend', function () {
        $(this)[0].busy = true;
    });

    $(".wk-ofoms-attach-list-button").click(function () {
        $(".wk-ofoms-attach-list-input").click();
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
            "filterSelector": '#ofoms_search'
        });
    }
};