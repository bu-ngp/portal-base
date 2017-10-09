<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 9:58
 */

namespace domain\repositories\base;


use common\models\base\Person;
use domain\models\base\AuthAssignment;
use RuntimeException;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class AuthAssignmentRepository
{
    /**
     * @param $id
     * @return AuthAssignment
     */
    public function find($id)
    {
        if (!$authAssignment = AuthAssignment::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $authAssignment;
    }

    /**
     * @param AuthAssignment $authAssignment
     */
    public function add(AuthAssignment $authAssignment)
    {
        $userIDStr = Uuid::uuid2str($authAssignment->user_id);
        
        if (!$authItem = Yii::$app->authManager->getRole($authAssignment->item_name)) {
            throw new RuntimeException("AuthItem {$authAssignment->item_name} not exist.");
        }

        if (!$userID = Person::findOne($authAssignment->user_id)->primaryKey) {
            throw new RuntimeException("User with ID '$userIDStr' not exist.");
        }

        if (!Yii::$app->authManager->assign($authItem, $userID)) {
            throw new RuntimeException("Can't assign User with ID '$userIDStr' to '{$authAssignment->item_name}'");
        }
    }
}