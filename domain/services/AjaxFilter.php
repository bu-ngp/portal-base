<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 11.09.2017
 * Time: 11:56
 */

namespace domain\services;


use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\HttpException;

class AjaxFilter extends Behavior
{

    /**
     * @var array Actions of controller which will be apply this filter.
     */
    public $actions = [];

    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    public function beforeAction($event)
    {
        if (in_array($event->action->id, $this->actions)) {
            if (!Yii::$app->request->isAjax) {
                throw new HttpException(400, 'This URL can call only via Ajax.');
            }
        }
    }
}