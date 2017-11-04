<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 04.11.2017
 * Time: 13:21
 */

namespace doh\controllers;


use doh\services\models\Handler;
use yii\db\Expression;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class ListenController extends Controller
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

    public function actionIndex($keys)
    {
        $keys = json_decode($keys);

        if ($keys === null) {
            throw new \Exception('keys JSON decode error');
        }

        if ($keys) {
            $handlers = Handler::find()->select(['handler_id', new Expression('round(handler_percent / 100, 2) as handler_percent')])->andWhere(['handler_id' => $keys])->asArray()->all();

            return $handlers ? array_map(function ($handler) {
                return [$handler['handler_id'], $handler['handler_percent']];
            }, $handlers) : [];
        }

        return [];
    }
}