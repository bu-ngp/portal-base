<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 12.09.2017
 * Time: 15:10
 */

namespace common\classes;


use yii\web\User;

class WKUser extends User
{
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        return parent::can($permissionName, $params, $allowCaching) ?: $this->ldapAccess();
    }

    protected function ldapAccess()
    {
        $ds=ldap_connect("172.19.17.100");

        if ($ds) {
            $r=ldap_bind($ds,'sysadmin','test');

            $sr=ldap_search($ds,"ou=Administrators,dc=mugp1,dc=local", "cn=sysadmin");

            $result_ent = ldap_get_entries($ds,$sr);

          //  $a=print_r($result_ent,true);

        } else {
            echo "<h4>Невозможно подключиться к серверу LDAP</h4>";
        }

        return true;
    }
}