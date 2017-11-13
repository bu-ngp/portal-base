<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.11.2017
 * Time: 13:57
 */

namespace ngp\services\repositories;


use ngp\services\models\ConfigOfoms;
use Yii;
use yii\httpclient\Client;

class OfomsRepository
{
    public $configOfoms;

    public function __construct()
    {
        if (!$this->configOfoms) {
            $this->configOfoms = ConfigOfoms::findOne(1);
        }
    }

    public function search($searchString)
    {
        if (!empty($searchString)) {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('post')
                ->setUrl($this->configOfoms->config_ofoms_url)
                ->setHeaders([
                    'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept' => '*/*',
                    'Host' => $this->configOfoms->config_ofoms_remote_host_name,

                ])
                ->setData([
                    'username' => $this->configOfoms->config_ofoms_login,
                    'password' => Yii::$app->security->decryptByPassword($this->configOfoms->config_ofoms_password, Yii::$app->request->cookieValidationKey),
                    'rtype' => 'json',
                    's' => $searchString,
                ])
                ->send();

            if ($response->isOk) {
                return $response->data['persons'];
            }
        }

        return [];
    }

    public function attach($ffio, $policy, $doctor) {
        if (empty($ffio) || empty($policy) || empty($doctor)) {
            throw new \RuntimeException('attach error');
        }

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl('http://172.19.17.16/services/attach-person' /*$this->configOfoms->config_ofoms_url_prik*/)
            ->setHeaders([
                'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept' => '*/*',
                'Host' => $this->configOfoms->config_ofoms_remote_host_name,

            ])
            ->setData([
                'typemp' => 1,
                'ffio' => $ffio,
                'policy' => $policy,
                'doctor' => $doctor,
                'rtype' => 'json',
                'username' => $this->configOfoms->config_ofoms_login,
                'password' => Yii::$app->security->decryptByPassword($this->configOfoms->config_ofoms_password, Yii::$app->request->cookieValidationKey),
            ])
            ->send();

        if ($response->isOk) {
            return $response->data;
        }

        throw new \DomainException('Request error');
    }
}