<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 11:06
 */

namespace backend\controllers\configuration;


use domain\forms\base\EmployeeForm;
use domain\models\base\Dolzh;
use domain\queries\DolzhQuery;
use domain\services\base\EmployeeService;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class EmployeeController extends Controller
{
    /**
     * @var EmployeeService
     */
    private $employeeService;

    public function __construct($id, $module, EmployeeService $employeeService, $config = [])
    {
        $this->employeeService = $employeeService;
        parent::__construct($id, $module, $config = []);
    }

    public function actionCreate()
    {
        $form = new EmployeeForm();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $id = Uuid::str2uuid($_GET['id']);
            $q = $_GET['q'];

            $dolzhQuery = DolzhQuery::getCallbackAllDolzhs();
            $query = $dolzhQuery((new Dolzh())->find());
            $resultReturn = [];

            if ($id) {
                $result = $query->andWhere(['dolzh_id' => $id])->asArray()->one();
                $result['dolzh_id'] = Uuid::uuid2str($result['dolzh_id']);
                $resultReturn = ['id' => $result['dolzh_id'], 'text' => implode(', ', $result)];

                return $resultReturn;
            }

            if ($q) {

                $result = $query->andWhere(['like', 'dolzh_name', $q])->asArray()->all();
                foreach ($result as $row) {
                    $row['dolzh_id'] = Uuid::uuid2str($row['dolzh_id']);
                    $resultReturn[] = ['id' => $row['dolzh_id'], 'text' => implode(', ', $row)];
                }

                return ['results' => $resultReturn];
            }

            return [];
        }

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->employeeService->create($form)
        ) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        //       $form->dolzh_id = Dolzh::find()->andWhere(['like','dolzh_name','сист'])->one()->dolzh_id;

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }
}