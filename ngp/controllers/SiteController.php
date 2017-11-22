<?php
namespace ngp\controllers;

use common\widgets\CardList\CardListHelper;
use domain\models\base\search\AuthItemSearch;
use domain\services\base\PersonService;
use domain\services\ProxyService;
use ngp\services\models\search\TilesMainPageSearch;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @var PersonService
     */
    private $personService;

    public function __construct($id, $module, PersonService $personService, $config = [])
    {
        $this->personService = new ProxyService($personService);
        parent::__construct($id, $module, $config = []);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $modelSearch = new TilesMainPageSearch();
        return $this->render('index', ['modelSearch' => $modelSearch]);
    }

    public function actionTiles() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $searchModel = new TilesMainPageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return CardListHelper::createAjaxCards($dataProvider, 'tiles_name', 'tiles_link', 'tiles_thumbnail', 'tiles_icon','tiles_description', 'tiles_icon_color', 'tiles_id');
    }
//
//    public function actionTest()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $searchModel = new AuthItemSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return CardListHelper::createAjaxCards($dataProvider, 'name', '', '', 'description', '', 'name');
//    }
}
