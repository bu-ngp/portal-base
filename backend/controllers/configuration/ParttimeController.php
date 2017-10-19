<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 11:06
 */

namespace backend\controllers\configuration;


use common\widgets\Breadcrumbs\Breadcrumbs;
use domain\forms\base\ParttimeForm;
use domain\models\base\search\ParttimeBuildSearch;
use domain\services\base\ParttimeService;
use domain\services\proxyService;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class ParttimeController extends Controller
{
    /**
     * @var ParttimeService
     */
    private $parttimeService;

    public function __construct($id, $module, ParttimeService $parttimeService, $config = [])
    {
        $this->parttimeService = new proxyService($parttimeService);
        parent::__construct($id, $module, $config = []);
    }

    public function actionCreate()
    {
        $form = new ParttimeForm();

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $parttimeId = $this->parttimeService->create($form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved. Add Builds.'));
            Breadcrumbs::removeLastCrumb();

            return $this->redirect(['update', 'id' => $parttimeId]);
        }

        return $this->render('create', [
            'modelForm' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $employee = $this->parttimeService->get($id);
        $form = new ParttimeForm($employee);

        $searchModelParttimeBuild = new ParttimeBuildSearch();
        $dataProviderParttimeBuild = $searchModelParttimeBuild->search(Yii::$app->request->queryParams);

        if ($form->load(Yii::$app->request->post())
            && $form->validate()
            && $this->parttimeService->update($id, $form)
        ) {
            Yii::$app->session->setFlash('success', Yii::t('common', 'Record is saved.'));

            return $this->redirect(Url::previous());
        }

        return $this->render('update', [
            'modelForm' => $form,
            'searchModelParttimeBuild' => $searchModelParttimeBuild,
            'dataProviderParttimeBuild' => $dataProviderParttimeBuild,
        ]);
    }
}