<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.11.2017
 * Time: 16:58
 */

namespace doh\controllers;


use doh\services\classes\DoH;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

class CancelController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AjaxFilter::className(),
                'only' => ['index'],
            ],
            [
                'class' => ContentNegotiator::className(),
                'only' => ['index'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionIndex($id)
    {
        if (DoH::cancel($id)) {
            return (object)['status' => 'success'];
        }
        return (object)['status' => 'error'];
    }
}