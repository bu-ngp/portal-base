<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 12.09.2017
 * Time: 15:10
 */

namespace common\classes;


use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\base\InvalidValueException;
use yii\db\Query;
use yii\rbac\DbManager;
use yii\web\Cookie;
use yii\web\IdentityInterface;
use yii\web\User;

class WKUser extends User
{
    private $_accessLdap;

    public function can($permissionName, $params = [], $allowCaching = true)
    {
        return parent::can($permissionName, $params, $allowCaching) ?: $this->canLdap($permissionName, $allowCaching);
    }

    protected function canLdap($permissionName, $allowCaching = true)
    {
        if ($allowCaching && isset($this->_accessLdap[$permissionName])) {
            return $this->_accessLdap[$permissionName];
        }

        if (($accessChecker = $this->getAccessChecker()) === null && !$accessChecker instanceof DbManager) {
            return false;
        }

        $access = $this->checkAccessLdap($accessChecker, $permissionName);

        if ($allowCaching && empty($params)) {
            $this->_accessLdap[$permissionName] = $access;
        }

        return $access;
    }

    protected function checkAccessLdap(DbManager $accessChecker, $permissionName)
    {
        if (!Yii::$app->user->identity instanceof LdapModelInterface) {
            return false;
        }

        $AuthItem = (new Query)
            ->select(['ldap_group'])
            ->from($accessChecker->itemTable)
            ->where(['name' => $permissionName])
            ->one($accessChecker->db);

        return $this->checkAccessLdapRecursive($accessChecker, $permissionName, $AuthItem['ldap_group']);
    }

    protected function checkAccessLdapRecursive(DbManager $accessChecker, $itemName, $ldap_group)
    {
        /** @var array $groups */
        /** @var LdapModelInterface Yii::$app->user->identity */
        $groups = Yii::$app->user->identity->getLdapGroups();

        if ($groups && in_array($ldap_group, $groups)) {
            return true;
        }

        $parents = (new Query)
            ->select(['parent', 'ldap_group'])
            ->from($accessChecker->itemChildTable)
            ->innerJoin($accessChecker->itemTable, "{$accessChecker->itemTable}.name = {$accessChecker->itemChildTable}.parent")
            ->where(['child' => $itemName])
            ->all($accessChecker->db);

        foreach ($parents as $parent) {
            if ($this->checkAccessLdapRecursive($accessChecker, $parent['parent'], $parent['ldap_group'])) {
                return true;
            }
        }

        return false;
    }

    protected function sendIdentityCookie($identity, $duration)
    {
        $cookie = new Cookie($this->identityCookie);
        $cookie->value = json_encode([
            Uuid::uuid2str($identity->getId()), // Binary to String
            $identity->getAuthKey(),
            $duration,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $cookie->expire = time() + $duration;
        Yii::$app->getResponse()->getCookies()->add($cookie);
    }

    protected function getIdentityAndDurationFromCookie()
    {
        $value = Yii::$app->getRequest()->getCookies()->getValue($this->identityCookie['name']);
        if ($value === null) {
            return null;
        }
        $data = json_decode($value, true);
        if (count($data) == 3) {
            list ($id, $authKey, $duration) = $data;
            /* @var $class IdentityInterface */
            $class = $this->identityClass;
            $identity = $class::findIdentity(Uuid::str2uuid($id)); // String to Binary
            if ($identity !== null) {
                if (!$identity instanceof IdentityInterface) {
                    throw new InvalidValueException("$class::findIdentity() must return an object implementing IdentityInterface.");
                } elseif (!$identity->validateAuthKey($authKey)) {
                    Yii::warning("Invalid auth key attempted for user '$id': $authKey", __METHOD__);
                } else {
                    return ['identity' => $identity, 'duration' => $duration];
                }
            }
        }
        $this->removeIdentityCookie();
        return null;
    }
}