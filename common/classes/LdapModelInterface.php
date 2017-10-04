<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 04.10.2017
 * Time: 11:07
 */

namespace common\classes;

use yii\web\IdentityInterface;

interface LdapModelInterface extends IdentityInterface
{
    public function setLdapGroups(array $ldapGroups);
    public function getLdapGroups();
}