/**
 * Created by VOVANCHO on 22.05.2017.
 */

$(document).ready(function () {
    var $widget = $('#RoleFormGrid-pjax');

    $widget.gridselected2input({
        storage: $('#roleform-assignroles')
    });
});