/**
 * Created by VOVANCHO on 22.05.2017.
 */
$(document).ready(function () {
    $("#id-widget").wkgridview(object);
    $("#id-widget").parent().gridselected2storage({
        storage: 'selectedRows',
        selectedPanelClass: 'selectedPanel'
    });
});