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