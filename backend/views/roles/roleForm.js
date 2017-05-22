/**
 * Created by VOVANCHO on 22.05.2017.
 */
var saveAssign = function (gridid, $checkbox) {
    var $grid = $('#' + gridid);
    var obj1 = $.parseJSON(localStorage.selectedRows);

    if (obj1[$grid[0].id].checkAll) {
        if ($checkbox.prop('checked')) {
            var ind1 = obj1[$grid[0].id].excluded.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
            if (ind1 >= 0) {
                obj1[$grid[0].id].excluded.splice(ind1, 1);
            }
        } else {
            obj1[$grid[0].id].excluded.push($checkbox.parent('td').parent('tr').attr('data-key'));
        }
    } else {
        if ($checkbox.prop('checked')) {
            obj1[$grid[0].id].included.push($checkbox.parent('td').parent('tr').attr('data-key'));
        } else {
            var ind2 = obj1[$grid[0].id].included.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
            if (ind2 >= 0) {
                obj1[$grid[0].id].included.splice(ind2, 1);
            }
        }

    }

    localStorage.selectedRows = JSON.stringify(obj1);
};

$(document).ready(function () {
    var $widget= $('#RoleFormGrid');

    $widget.find('input.kv-row-checkbox').unbind('click').click(function () {
        saveAssign($widget[0].id, $(this));
    });
});