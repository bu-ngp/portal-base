<?php
/** @var $result array */
?>
<style>
    td {
        border: 1px solid black;
        padding: 5px 10px;
    }

    tr > td:first-child {
        text-align: right;
    }

    table.wk-table {
        border-collapse: collapse;
    }
</style>
<h3>Краткий отчет импорта сотрудников</h3>
<table class="wk-table">
    <tbody>
    <tr>
        <td>Количество записей</td>
        <td><?= $result['rows'] ?></td>
    </tr>
    <tr>
        <td>Добавлено записей</td>
        <td><?= $result['added'] . " ({$result['addedPercent']}%)" ?></td>
    </tr>
    <tr>
        <td>Изменено записей</td>
        <td><?= $result['changed'] . " ({$result['changedPercent']}%)" ?></td>
    </tr>
    <tr>
        <td>Ошибок</td>
        <td><?= $result['error'] . " ({$result['errorPercent']}%)" ?></td>
    </tr>
    </tbody>
</table>
<p style="color: #929292; font-size: 12px">* Файл ошибок прикреплен к письму.</p>