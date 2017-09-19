<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 15.09.2017
 * Time: 8:53
 */

namespace common\models\base;


use domain\models\base\ConfigLdap;
use wartron\yii2uuid\helpers\Uuid;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

class PersonLdap extends Model implements IdentityInterface
{
    private static $ldapConn;
    private static $username;

    public $person_id;
    public $person_fullname;
    public $person_username;
    public $person_auth_key;

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        $connection = self::$ldapConn;
        $id = self::idConvert($id);

        $result = ldap_search($connection, 'dc=mugp1,dc=local', "objectGUID=$id", [
            'objectguid',
            'samaccountname',
            'displayName',
            'memberof',
        ]);


        // Получаем количество результатов предыдущей проверки
        $result_ent = ldap_get_entries($connection, $result);
        $result_ent = self::getResult($result_ent);

        return new static([
            'person_id' => $result_ent[0]['objectguid'],
            'person_fullname' => $result_ent[0]['displayname'],
            'person_username' => $result_ent[0]['samaccountname'],
            'person_auth_key' => Uuid::uuid2str($result_ent[0]['objectguid']),
        ]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->person_id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->person_auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username, $password)
    {
        $connection = self::getCachedLdapUser($password, $username);

        $result = ldap_search($connection, 'dc=mugp1,dc=local', /*"memberOf:1.2.840.113556.1.4.1941:=" .*/
            "sAMAccountName=$username", [
                'objectguid',
                'samaccountname',
                'displayName',
                'memberof',
            ]);


        // Получаем количество результатов предыдущей проверки
        $result_ent = ldap_get_entries($connection, $result);
        $result_ent = self::getResult($result_ent);

        return new static([
            'person_id' => $result_ent[0]['objectguid'],
            'person_fullname' => $result_ent[0]['displayname'],
            'person_username' => $result_ent[0]['samaccountname'],
            'person_auth_key' => Uuid::uuid2str($result_ent[0]['objectguid']),
        ]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->getCachedLdapUser($password) == true;
    }

    protected static function getCachedLdapUser($password, $username = null)
    {
        if (!$username) {
            $username = self::$username;
        }

        if (!self::$ldapConn || self::$username !== $username) {
            self::$ldapConn = null;
            self::$username = null;

            $configLdap = ConfigLdap::findOne(1);
            $domainMachine = gethostbyaddr($configLdap->config_ldap_host);
            $domain = count(($domainArray = explode('.', $domainMachine))) >= 3 ? $domainArray[1] . '\\' : '';

            $connection = ldap_connect($configLdap->config_ldap_host, $configLdap->config_ldap_port);
            ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);

            if (ldap_bind($connection, $domain . $username, $password)) {
                self::$ldapConn = $connection;
                self::$username = $username;
            }
        }

        return self::$ldapConn;
    }

    protected static function getResult(array $ldapResult)
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

    protected static function idConvert($id)
    {
        $str = Uuid::uuid2str($id);
        $chunks = str_split($str, 2);
        $result = '\\' . implode('\\', $chunks);
        return strtolower($result);
    }
}