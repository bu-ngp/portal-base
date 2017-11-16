<?php

use dosamigos\fileinput\FileInput;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

/* @var $modelForm \ngp\services\forms\OfomsAttachListForm */
/* @var $form \common\widgets\ActiveForm\ActiveForm */
?>
    <div class="col-md-12">
        <h4>Файл со списком пациентов для прикрепления должен быть в формате <span style="color: #179c0e">Excel</span> с
            расширение .xls или .xlsx <i style="color: #179c0e" class="fa fa-2x fa-file-excel-o"></i></h4>
        <strong><p> Файл должен содержать следующие колонки: </p></strong>

        <ol>
            <li>ИНН врача</li>
            <li>Номер полиса пациента</li>
            <li>Фамилию Имя Отчество пациента</li>
            <li>Дату рождения пациента</li>
        </ol>

        <strong><p>или:</p></strong>
        <ol>
            <li>ИНН врача</li>
            <li>Номер полиса пациента</li>
            <li>Фамилию пациента</li>
            <li>Имя пациента</li>
            <li>Отчество пациента</li>
            <li>Дату рождения пациента</li>
        </ol>
        <p></p>
        <blockquote><em><p>Дополнительные правила:</p></em>
            <footer>Отчество не обязательно</footer>
            <footer>Дата рождения должна быть в формате строки ДД.ММ.ГГГГ или ГГГГ-ММ-ДД или в формате даты
                Excel
            </footer>
        </blockquote>
    </div>
<?= $form->field($modelForm, 'listFile')->widget(FileInput::className(), [
    'style' => FileInput::STYLE_BUTTON
])->label(false) ?>