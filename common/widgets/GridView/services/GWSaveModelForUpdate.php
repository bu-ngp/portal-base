<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 21.08.2017
 * Time: 8:31
 */

namespace common\widgets\GridView\services;


use Yii;
use yii\db\ActiveRecord;

class GWSaveModelForUpdate
{
    private $mainField;
    private $mainIdParameterName;
    private $foreignField;
    private $saveFunc;
    private $model;
    private $mainId;
    private $foreignId;

    public function __construct($config = []/*, , $mainField, $foreignField, $saveFunc, $mainIdParameterName = 'id'*/)
    {
        if (!class_exists($config['modelClassName'])) {
            throw new \Exception("class '{$config['modelClassName']}' not exists");
        }

        if (!is_string($config['mainField']) || $config['mainField'] === '') {
            throw new \Exception('mainField must be string');
        }

        if (!is_string($config['foreignField']) || $config['foreignField'] === '') {
            throw new \Exception('foreignField must be string');
        }

        if (!is_string($config['mainIdParameterName']) || $config['mainIdParameterName'] === '') {
            throw new \Exception('mainIdParameterName must be string');
        }

        if (!($config['saveFunc'] instanceof \Closure)) {
            throw new \Exception('saveFunc must be Closure');
        }

        if (!$this->mainId = Yii::$app->request->get($config['mainIdParameterName'])) {
            throw new \Exception("url parameter '{$config['mainIdParameterName']}' is missing or empty");
        }

        if (!$this->foreignId = Yii::$app->request->get('selected')) {
            throw new \Exception("url parameter 'selected' is missing or empty");
        }

        $this->model = new $config['modelClassName'];
        $this->mainField = $config['mainField'];
        $this->foreignField = $config['foreignField'];
        $this->saveFunc = $config['saveFunc'];
        $this->mainIdParameterName = $config['mainIdParameterName'];
    }

    public function save()
    {
        $func = $this->saveFunc;
        $func($this->model, $this->mainId, $this->mainField, $this->foreignField, $this->foreignId);

        return $this->model;
    }
}