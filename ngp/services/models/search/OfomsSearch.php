<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 03.11.2017
 * Time: 10:45
 */

namespace ngp\services\models\search;


use ngp\services\models\ConfigOfoms;
use ngp\services\models\Ofoms;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\httpclient\Client;

class OfomsSearch extends Ofoms
{
    public $search_string;

    public function rules()
    {
        return [
            [['att_doct_amb', 'att_lpu_amb', 'dt_att_amb', 'att_lpu_stm', 'dt_att_stm', 'fam', 'im', 'ot', 'dr', 'w', 'enp', 'opdoc', 'polis', 'spol', 'npol', 'dbeg', 'dend', 'q', 'q_name', 'rstop', 'ter_st'], 'safe']
        ];
    }

    public function search($params)
    {
        $query = new Query();

        $dataProvider = new ArrayDataProvider([
            'key' => 'enp',
        ]);

        $configOfoms = ConfigOfoms::findOne(1);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'search_string' => $this->search_string,
        ]);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl('http://' . $configOfoms->config_ofoms_host . '/services/search-uri')
            ->setHeaders([
                'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept' => '*/*',
                'Host' => 'portal.tfoms',//$configOfoms->config_ofoms_remote_host_name,

            ])
            ->setData([
                'username' => $configOfoms->config_ofoms_login,
                'password' => Yii::$app->security->decryptByPassword($configOfoms->config_ofoms_password, Yii::$app->request->cookieValidationKey),
                'rtype' => 'json',
                's' => 'карвв86',
            ])
            ->send();
        if ($response->isOk) {
$dataProvider->allModels=$response->data['persons'];

$a2='';
        }


        return $dataProvider;
    }
}