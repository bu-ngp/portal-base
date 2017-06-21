<?php
namespace common\reports;

use common\widgets\ReportLoader\ReportByTemplate;
use domain\models\base\AuthItem;

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 20.06.2017
 * Time: 8:53
 */
class RolesReport extends ReportByTemplate
{
    public $title = 'Роли';

    public function body()
    {
        $PHPExcel = $this->PHPExcel;
        $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Дата: ' . date('d.m.Y'));

        $roles = AuthItem::find()->andWhere(['view' => $this->params['view']])->all();

        $row = 5;
        /** @var AuthItem $ar */
        foreach ($roles as $ar) {
            $PHPExcel->getActiveSheet()->insertNewRowBefore($row);
            $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $ar->description);
            $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $ar->name);
            $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $ar->itemsValues('type')[$ar->type]);
            $row++;
        }

        $PHPExcel->getActiveSheet()->removeRow($row);

        $PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row + 1, 'Всего: ' . ($row - 5));
    }
}