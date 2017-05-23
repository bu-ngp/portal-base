/**
 * Created by VOVANCHO on 22.05.2017.
 */
$(document).ready(function () {
    $("#id-widget").wkgridview(object);
    $("#id-widget").parent().gridselected2storage({
        storage: 'selectedRows',
        selectedPanelClass: 'selectedPanel'
    });

    console.debug($("#id-widget"));

   // setTimeout(function () {
        $("#id-widget").yiiGridView('applyFilter');
   // }, 3000);

});