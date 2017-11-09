<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 03.11.2017
 * Time: 10:45
 */

namespace ngp\services\models\search;

use domain\services\ProxyService;
use ngp\services\models\Ofoms;
use ngp\services\repositories\OfomsRepository;
use ngp\services\services\OfomsService;
use yii\data\ArrayDataProvider;

class OfomsSearch extends Ofoms
{
    public $search_string;
    /** @var OfomsService */
    public $service;

    public function __construct($config = [])
    {
        $this->service = new OfomsService(new OfomsRepository());
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['search_string'], 'safe']
        ];
    }

    public function search($params)
    {
        $dataProvider = new ArrayDataProvider([
            'key' => 'enp',
        ]);

        $this->load($params);
        $dataProvider->allModels = $this->service->search($this->search_string);
        return $dataProvider;
    }
}