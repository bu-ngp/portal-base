<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 12.09.2017
 * Time: 15:10
 */

namespace common\classes;


use yii\db\Query;
use yii\rbac\DbManager;
use yii\web\IdentityInterface;
use yii\web\User;

class WKUser extends User
{
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        return parent::can($permissionName, $params, $allowCaching) ?: $this->canLdap($permissionName);
    }

    protected function canLdap($permissionName)
    {
        /*   if ($allowCaching && empty($params) && isset($this->_access[$permissionName])) {
               return $this->_access[$permissionName];
           }*/
        if (($accessChecker = $this->getAccessChecker()) === null && !$accessChecker instanceof DbManager) {
            return false;
        }

        $access = $this->checkAccessLdap($accessChecker, $this->getId(), $permissionName);

        /* if ($allowCaching && empty($params)) {
             $this->_access[$permissionName] = $access;
         }*/

        return $access;
    }

    protected function checkAccessLdap(DbManager $accessChecker, $id, $permissionName)
    {
      
    }
    
    protected function checkAccessRecursive($user, $itemName, $params, $assignments) {
        if (($item = $this->getItem($itemName)) === null) {
            return false;
        }

        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }
        
        $query = new Query;
        $parents = $query->select(['parent'])
            ->from($this->itemChildTable)
            ->where(['child' => $itemName])
            ->column($this->db);
        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($user, $parent, $params, $assignments)) {
                return true;
            }
        }
    }
}