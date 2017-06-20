/**
 * Created by VOVANCHO on 22.05.2017.
 */
$("#id-widget-pjax").gridselected2storage({
    storage: 'selectedRows',
    selectedPanelClass: 'selectedPanel'
});

wkwidget.init(wkdialogOptions);

if ($("#id-widget").length) {
    $("#id-widget").yiiGridView({"filterUrl": window.location.search});
    $("#id-widget").yiiGridView('applyFilter');
}

$("#id-widget-pjax").on('click', 'a.wk-test1', function (e) {
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