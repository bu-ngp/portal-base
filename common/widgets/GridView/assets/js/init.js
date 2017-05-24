/**
 * Created by VOVANCHO on 22.05.2017.
 */
$(document).ready(function () {
    $("#id-widget-pjax").wkgridview();
    $("#id-widget-pjax").gridselected2storage({
        storage: 'selectedRows',
        selectedPanelClass: 'selectedPanel'
    });

    $("#id-widget").yiiGridView('applyFilter');
});