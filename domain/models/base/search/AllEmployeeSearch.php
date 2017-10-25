<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 20.10.2017
 * Time: 8:13
 */

namespace domain\models\base\search;


use common\classes\validators\WKDateValidator;
use common\widgets\GridView\services\GWItemsTrait;
use domain\services\DateTimeCondition;
use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

class AllEmployeeSearch extends Model
{
    const EMPLOYEE = 1;
    const PARTTIME = 2;

    public $id;
    public $primary_key;
    public $person_id;
    public $dolzh_name;
    public $podraz_name;
    public $employee_history_begin;
    public $employee_history_end;
    public $employee_type;

    use GWItemsTrait;

    public function rules()
    {
        return [
            [[
                'id',
                'primary_key',
                'person_id',
                'dolzh_name',
                'podraz_name',
                'employee_type',
            ], 'safe'],
            [['employee_history_begin', 'employee_history_end'], WKDateValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dolzh_name' => Yii::t('domain/employee','Dolzh Name'),
            'podraz_name' => Yii::t('domain/employee','Podraz Name'),
            'employee_type' => Yii::t('domain/employee','Type'),
            'employee_history_begin' => Yii::t('domain/employee','Employee Begin'),
            'employee_history_end' => Yii::t('domain/employee','Employee End'),
        ];
    }

    public function search($params)
    {
        $count = Yii::$app->db->createCommand($this->sqlCount($params), array_merge([
            ':person_id' => $params['id'],
        ], $this->params($params)))->queryScalar();

        $this->load($params);

        return new SqlDataProvider([
            'sql' => $this->sql($params),
            'totalCount' => $count,
            'params' => array_merge([
                ':person_id' => $params['id'],
            ], $this->params($params)),
            'sort' => [
                'attributes' => [
                    'dolzh_name' => [
                        'asc' => ['dolzh_name' => SORT_ASC],
                        'desc' => ['dolzh_name' => SORT_DESC],
                    ],
                    'podraz_name' => [
                        'asc' => ['podraz_name' => SORT_ASC],
                        'desc' => ['podraz_name' => SORT_DESC],
                    ],
                    'employee_history_begin' => [
                        'asc' => ['employee_history_begin' => SORT_ASC],
                        'desc' => ['employee_history_begin' => SORT_DESC],
                    ],
                    'employee_history_end' => [
                        'asc' => ['employee_history_end' => SORT_ASC],
                        'desc' => ['employee_history_end' => SORT_DESC],
                    ],
                    'employee_type' => [
                        'asc' => ['employee_type' => SORT_ASC],
                        'desc' => ['employee_type' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => ['employee_history_begin' => SORT_DESC, 'employee_type' => SORT_ASC]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
    }

    protected function sqlCount($params)
    {
        return $this->selectCount() . $this->sqlBody($params);
    }

    protected function sql($params)
    {
        return $this->selectBody() . $this->sqlBody($params);
    }

    protected function sqlBody($params)
    {
        return <<<EOT
            FROM (
                SELECT wk1.employee_history_id as primary_key,
                    wk1.person_id,
                    wk1.dolzh_id,
                    wk1.podraz_id,
                    wk1.employee_history_begin,
                    IFNULL((
                            SELECT wk2.employee_history_begin - INTERVAL 1 DAY
                            FROM wk_employee_history wk2
                            WHERE wk2.employee_history_begin > wk1.employee_history_begin 
                             AND wk2.person_id = UNHEX(:person_id)
                             LIMIT 1
                        ), (
                            SELECT person_fired 
                            FROM {{%person}} 
                            WHERE person_id = UNHEX(':person_id')
                        )) AS employee_history_end,
                    1 AS employee_type
                FROM {{%employee_history}} wk1                
            UNION                
                SELECT parttime_id,
                    person_id,
                    dolzh_id,
                    podraz_id,
                    parttime_begin,
                    parttime_end,
                    2
                FROM {{%parttime}}
                ) emp
            LEFT JOIN {{%dolzh}} ON {{%dolzh}}.dolzh_id = emp.dolzh_id
            LEFT JOIN {{%podraz}} ON {{%podraz}}.podraz_id = emp.podraz_id
            JOIN (
                SELECT @curRow := 0
                ) r
            WHERE person_id = UNHEX(:person_id)
EOT
            . $this->whereStatement($params);
    }

    protected function selectBody()
    {
        return <<<EOT
            SELECT @curRow := @curRow + 1 AS id,
            primary_key,
            person_id,
            {{%dolzh}}.dolzh_name,
            {{%podraz}}.podraz_name,
            employee_history_begin,
            employee_history_end,
            employee_type
EOT;
    }

    protected function selectCount()
    {
        return 'SELECT COUNT(*) ';
    }

    protected function params($params)
    {
        return array_merge($this->columnsLike($params), $this->columnsStrict($params));
    }

    protected function whereStatement($params)
    {
        $filterColumns = $this->columnsLike($params);
        $resultLike = array_map(function ($key) {
            $ltrimKey = ltrim($key, ':');
            return " and $ltrimKey like $key";
        }, array_keys($filterColumns));

        $filterColumns = $this->columnsStrict($params);
        $resultStrict = array_map(function ($key) {
            $ltrimKey = ltrim($key, ':');
            return " and $ltrimKey = " . $key;
        }, array_keys($filterColumns));

        $filterColumns = $this->columnDate($params);
        $resultDate = array_map(function ($key, $value) {
            $ltrimKey = ltrim($key, ':');
            return " and " . (new DateTimeCondition($ltrimKey, $value, DateTimeCondition::DATE))->convertAsSql();
        }, array_keys($filterColumns), $filterColumns);

        return implode($resultLike) . implode($resultStrict) . implode($resultDate);
    }

    protected function columnsLike($params)
    {
        return array_filter([
            ':dolzh_name' => $params[$this->formName()]['dolzh_name'] ? '%' . $params[$this->formName()]['dolzh_name'] . '%' : '',
            ':podraz_name' => $params[$this->formName()]['podraz_name'] ? '%' . $params[$this->formName()]['podraz_name'] . '%' : '',
        ], function ($value) {
            return !empty($value);
        });
    }

    protected function columnsStrict($params)
    {
        return array_filter([
            ':employee_type' => $params[$this->formName()]['employee_type'],
        ], function ($value) {
            return !empty($value);
        });
    }

    protected function columnDate($params)
    {
        return array_filter([
            ':employee_history_begin' => $params[$this->formName()]['employee_history_begin'],
            ':employee_history_end' => $params[$this->formName()]['employee_history_end'],
        ], function ($value) {
            return !empty($value);
        });
    }


    public static function items()
    {
        return [
            'employee_type' => [
                AllEmployeeSearch::EMPLOYEE => 'Основная специальность',
                AllEmployeeSearch::PARTTIME => 'Совмещение',
            ],
        ];
    }
}