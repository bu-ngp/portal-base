<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 19.09.2017
 * Time: 8:53
 */

namespace common\classes;


use common\models\base\Person;
use domain\models\base\ConfigLdap;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class Ldap
{
    const ADMIN = 'admin';
    const USER = 'user';

    private $_ldapConn;

    public function __construct($type, $username = null, $password = null)
    {
        $this->_ldapConn = $this->getConnection($type, $username, $password);
    }

    public static function adminConnect()
    {
        return new self(Ldap::ADMIN);
    }

    public static function userConnect($username, $password)
    {
        return new self(Ldap::USER, $username, $password);
    }

    public static function getDomain($host)
    {
        $domainMachine = filter_var($host, FILTER_VALIDATE_IP) ? gethostbyaddr($host) : $host;
        $domainArray = explode('.', $domainMachine);

        unset($domainArray[count($domainArray) - 1]);
        unset($domainArray[0]);

        if ($domainArray) {
            return implode('.', $domainArray) . '\\';
        }

        return '';
    }

    public function find($id)
    {
        $id = $this->idConvert($id);

        $result = ldap_search($this->_ldapConn, $this->getBaseDN(), "objectGUID=$id", [
            'objectguid',
            'samaccountname',
            'displayName',
            'mail',
            'memberof',
        ]);

        // Получаем количество результатов предыдущей проверки
        $result_ent = ldap_get_entries($this->_ldapConn, $result);
        $result_ent = $this->getResult($result_ent);

        if ($result_ent) {
            return new Person([
                'person_id' => $result_ent[0]['objectguid'],
                'person_fullname' => $result_ent[0]['displayname'],
                'person_username' => $result_ent[0]['samaccountname'],
                'person_email' => $result_ent[0]['mail'],
                'person_auth_key' => Uuid::uuid2str($result_ent[0]['objectguid']),
                'person_ldap_groups' => $this->getGroups($result_ent[0]['memberof']),
            ]);
        }

        return null;
    }

    public function findByUser($username)
    {
        $result = ldap_search($this->_ldapConn, $this->getBaseDN(), "sAMAccountName=$username", [
            'objectguid',
            'samaccountname',
            'displayName',
            'mail',
            'memberof',
        ]);

        // Получаем количество результатов предыдущей проверки
        $result_ent = ldap_get_entries($this->_ldapConn, $result);
        $result_ent = $this->getResult($result_ent);

        if ($result_ent) {
            return new Person([
                'person_id' => $result_ent[0]['objectguid'],
                'person_fullname' => $result_ent[0]['displayname'],
                'person_username' => $result_ent[0]['samaccountname'],
                'person_email' => $result_ent[0]['mail'],
                'person_auth_key' => Uuid::uuid2str($result_ent[0]['objectguid']),
                'person_ldap_groups' => $this->getGroups($result_ent[0]['memberof']),
            ]);
        }

        return null;
    }

    protected function getConnection($type, $username = null, $password = null)
    {
        $configLdap = ConfigLdap::findOne(1);
        $domain = self::getDomain($configLdap->config_ldap_host);

        $connection = ldap_connect($configLdap->config_ldap_host, $configLdap->config_ldap_port);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);


        switch ($type) {
            case Ldap::ADMIN:
                if (ldap_bind($connection, $domain . $configLdap->config_ldap_admin_login, Yii::$app->security->decryptByPassword($configLdap->config_ldap_admin_password, Yii::$app->request->cookieValidationKey))) {
                    return $connection;
                }
                break;
            case Ldap::USER:
                if (ldap_bind($connection, $domain . $username, $password)) {
                    return $connection;
                }
                break;
            default:
                throw new \Exception('Error Connection');
        }
    }

    protected function idConvert($id)
    {
        return strtolower(preg_replace('/(\w{2})/', '\\\\$1', Uuid::uuid2str($id)));
    }

    protected function getResult(array $ldapResult)
    {
        unset($ldapResult['count']);

        array_walk($ldapResult, function (&$item) {
            foreach ($item as $key => $value) {
                if (is_int($key)) {
                    unset($item[$key]);
                } else {
                    unset($item['count']);
                    unset($item['dn']);

                    if ($value['count'] === 1) {
                        $item[$key] = $value[0];
                    } else {
                        unset($item[$key]['count']);
                    }
                }
            }
        });

        return $ldapResult;
    }

    protected function getGroups(array $memberOf)
    {
        return array_map(function ($group) {
            return preg_replace('/CN=(.*?),.*/', '$1', $group);
        }, $memberOf);
    }

    protected function getBaseDN()
    {
        $configLdap = ConfigLdap::findOne(1);
        $domainMachine = filter_var($configLdap->config_ldap_host, FILTER_VALIDATE_IP) ? gethostbyaddr($configLdap->config_ldap_host) : $configLdap->config_ldap_host;
        $domainArray = explode('.', $domainMachine);

        unset($domainArray[0]);

        if ($domainArray) {
            return 'dc=' . implode(',dc=', $domainArray);
        }

        return '';
    }
}