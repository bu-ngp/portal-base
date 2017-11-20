<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 03.11.2017
 * Time: 10:45
 */

namespace ngp\services\models\search;

use domain\models\base\Person;
use ngp\services\models\Ofoms;
use ngp\services\repositories\OfomsRepository;
use ngp\services\services\OfomsService;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class OfomsSearch extends Ofoms
{
    public $search_string;
    /** @var OfomsService */
    public $service;

    public function __construct($config = [])
    {
        $this->service = new OfomsService(new OfomsRepository());
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['search_string'], 'safe'],
            [['search_string'], 'filter', 'filter' => 'strtoupper'],
            [['search_string'], 'match', 'pattern' => '/[а-я\d\.\s]/ui', 'message' => 'Строка поиска "{value}" не соответствует правилам поиска.'],
        ];
    }

    public function search($params)
    {
        $dataProvider = new ArrayDataProvider([
            'key' => 'enp',
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if (Yii::$app->request->isAjax) {
            $result = $this->service->search($this->search_string);
            if (isset($result['error'])) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                throw new \DomainException($result['error']);
            } else {
                $dataProvider->allModels = $result;
                $dataProvider->allModels = $this->appendOfomsStatus($dataProvider->allModels);
                $dataProvider->allModels = $this->appendOfomsVrach($dataProvider->allModels);
            }
        }

        return $dataProvider;
    }

    protected function appendOfomsStatus(array $rows)
    {
        if (count($rows) === 0) {
            return $rows;
        }

        return array_map(function ($row) {
            $row['ofomsStatus'] = null;

            if ($row['dend']) {
                $row['ofomsStatus'] = '<span class="label label-danger" title="' . $row['rstop'] . '">' . Yii::t('ngp/ofoms', 'Removed') . '</span>';
            } else {
                if ($row['att_lpu_amb'] == '14099') {
                    $row['ofomsStatus'] = '<span class="label label-success">' . Yii::t('ngp/ofoms', 'Attached') . '</span>';
                } else {
                    $row['ofomsStatus'] = '<span class="label label-primary" title="' . ($row['att_lpu_amb'] ?: Yii::t('ngp/ofoms', 'Don\'t attached to LPU')) . '">' . Yii::t('ngp/ofoms', 'Non Attached') . '</span>';
                }
            }

            return $row;
        }, $rows);
    }

    protected function appendOfomsVrach(array $rows)
    {
        if (count($rows) === 0) {
            return $rows;
        }

        return array_map(function ($row) {
            $row['ofomsVrach'] = null;

            if ($row['att_doct_amb']) {

                /** @var Person $vrach */
                $vrach = Person::find()
                    ->joinWith([
                        'profile',
                        'employee.dolzh',
                        'employee.podraz',
                        'employee.employeeHistory.employeeHistoryBuilds.build',
                    ])
                    ->andWhere(['profile.profile_inn' => $row['att_doct_amb']])
                    ->one();

                if ($vrach) {
                    $builds = $vrach->employee->employeeHistory->employeeHistoryBuilds ? ' (' . implode(', ', ArrayHelper::getColumn($vrach->employee->employeeHistory->employeeHistoryBuilds, 'build.build_name')) . ')' : '';
                    $row['ofomsVrach'] = $vrach->person_fullname . ', ' . $vrach->employee->dolzh->dolzh_name . $builds;
                } else {
                    $row['ofomsVrach'] = $row['att_doct_amb'];
                }
            }

            return $row;
        }, $rows);
    }
}