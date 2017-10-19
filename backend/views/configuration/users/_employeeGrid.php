<?php
use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

?>

<?=
/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $gridConfig array */

GridView::widget(array_replace([
    'id' => 'EmployeesUserGrid',
//    'dataProvider' => $dataProvider,
//    'filterModel' => $searchModel,
    'dataProvider' => (new searchModel)->getDataProvider(),
    'filterModel' => new searchModel,
    'columns' => [
//        'dolzh.dolzh_name',
//        'podraz.podraz_name',
        'dolzh_name',
        'podraz_name',
        'employee_history_begin',
        'employee_history_end',
        'employee_type'
    ],
    'panelHeading' => array(
        'icon' => FA::icon(FA::_LIST_ALT),
        'title' => Yii::t('common/employee', 'Employees'),
    ),
    'toolbar' => [
        'content' => \yii\helpers\Html::a('Добавить совмещение', ['configuration/parttime/create', 'person' => Yii::$app->request->get('id')], ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0'])
    ],
    //'pjaxSettings' => ['options' => ['clientOptions' => ['async' => false]]],
], $gridConfig));
?>

<?php

class searchModel extends \yii\base\Model
{
    public $id;
    public $person_id;
    public $dolzh_name;
    public $podraz_name;
    public $employee_history_begin;
    public $employee_history_end;
    public $employee_type;

    public function rules()
    {
        return [
            [[
                'id',
                'person_id',
                'dolzh_name',
                'podraz_name',
                'employee_history_begin',
                'employee_history_end',
                'employee_type'
            ], 'safe'],
        ];
    }

    public function getDataProvider()
    {
        return new \yii\data\SqlDataProvider([
            'sql' => <<<EOT
                SELECT @curRow := @curRow + 1 AS id,
                    person_id,
                    wk_dolzh.dolzh_name,
                    wk_podraz.podraz_name,
                    employee_history_begin,
                    employee_history_end,
                    employee_type
                FROM (
                    SELECT wk1.person_id,
                        wk1.dolzh_id,
                        wk1.podraz_id,
                        wk1.employee_history_begin,
                        (
                            SELECT wk2.employee_history_begin - INTERVAL 1 DAY
                            FROM wk_employee_history wk2
                            WHERE wk2.employee_history_begin > wk1.employee_history_begin limit 1
                            ) AS employee_history_end,
                        1 AS employee_type
                    FROM wk_employee_history wk1
                    
                    UNION
                    
                    SELECT person_id,
                        dolzh_id,
                        podraz_id,
                        parttime_begin,
                        parttime_end,
                        2
                    FROM wk_parttime
                    ) emp
                LEFT JOIN wk_dolzh ON wk_dolzh.dolzh_id = emp.dolzh_id
                LEFT JOIN wk_podraz ON wk_podraz.podraz_id = emp.podraz_id
                JOIN (
                    SELECT @curRow := 0
                    ) r
                ORDER BY emp.employee_history_begin DESC
EOT
        ]);
    }
}

?>
