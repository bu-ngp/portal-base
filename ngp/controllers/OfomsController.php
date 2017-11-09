<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 14:37
 */

namespace ngp\controllers;

use common\widgets\GridView\services\AjaxResponse;
use domain\services\ProxyService;
use ngp\helpers\RbacHelper;
use ngp\services\models\search\OfomsSearch;
use ngp\services\services\OfomsService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

class OfomsController extends Controller
{
    /**
     * @var OfomsService
     */
    private $service;

    public function __construct($id, $module, OfomsService $service, $config = [])
    {
        $this->service = new ProxyService($service);
        parent::__construct($id, $module, $config = []);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'search'],
                        'roles' => [RbacHelper::OFOMS_VIEW],
                    ],
                ],
            ],
            [
                'class' => AjaxFilter::className(),
                'only' => ['search'],
            ],
            [
                'class' => ContentNegotiator::className(),
                'only' => ['search'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new OfomsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}