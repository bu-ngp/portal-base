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
            throw new \RuntimeException('Model not found.');
        }

        return $authAssignment;
    }

    /**
     * @param AuthAssignment $authAssignment
     */
    public function add(AuthAssignment $authAssignment)
    {
        if (!$authItem = Yii::$app->authManager->getRole($authAssignment->item_name)) {
            throw new \DomainException(Yii::t('domain/auth-assignment', "AuthItem {item_name} not exist.", [
                'item_name' => $authAssignment->item_name,
            ]));
        }

        if (!$userID = Person::findOne($authAssignment->user_id)->primaryKey) {
            throw new \DomainException(Yii::t('domain/auth-assignment', "User with ID '{userUUID}' not exist.", [
                'userUUID' => Uuid::uuid2str($authAssignment->user_id),
            ]));
        }

        if (!Yii::$app->authManager->assign($authItem, $userID)) {
            throw new \DomainException(Yii::t('domain/auth-assignment', "Can't assign User with ID '{userUUID}' to '{$authAssignment->item_name}'", [
                'userUUID' => Uuid::uuid2str($authAssignment->user_id),
            ]));
        }
    }
}