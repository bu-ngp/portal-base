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

        return [];
    }
}