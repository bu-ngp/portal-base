<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 28.10.2017
 * Time: 11:03
 */

namespace common\widgets\Documenter\services;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Url;

class DocumenterViewer
{
    private $_filePath;
    private $_name;
    private $_id;
    private $_permissions;
    private $_order;
    private $_tabHash;
    private $_pillHash;

    public function __construct($path, $filePath)
    {
        $this->_filePath = $filePath;
        $path = DIRECTORY_SEPARATOR === '/' ? $path : mb_convert_encoding($path, 'UTF-8', 'Windows-1251');

        if (!preg_match('/^\\\\((\d+)_)?(\w+)(\[([\w\d_-|]+)?\])?\\\\(\d+|[\w\d-_\(\)]+)\.md$/u', $path, $matches)) {
            throw new InvalidConfigException("Invalid parse path '$path'");
        }

        $this->_name = $matches[3];
        $this->_id = $matches[6];
        $this->_permissions = empty($matches[5]) ? [] : explode('|', $matches[5]);
        $this->_order = $matches[2];
        $this->_tabHash = 't_' . hash('crc32', $this->getTabName());
        $this->_pillHash = 'p_' . hash('crc32', $this->getPillName());
    }

    public function getTabName()
    {
        return $this->_name;
    }

    public function getPillName()
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->_id)) {
            return date('d.m.Y', strtotime($this->_id));
        }

        return $this->_id;
    }

    public function getOrigPillName()
    {
        return $this->_id;
    }

    public function isAllowed()
    {
        if (count($this->_permissions) === 0) {
            return true;
        }

        foreach ($this->_permissions as $permission) {
            return Yii::$app->user->can($permission);
        }

        return false;
    }

    public function getOrder()
    {
        return $this->_order ?: false;
    }

    public function getContent()
    {
        return strtr(file_get_contents($this->_filePath), ['{absoluteWebRoot}' => Url::base(true)]);
    }

    public function getTabHash()
    {
        return $this->_tabHash;
    }

    public function getPillHash()
    {
        return $this->_pillHash;
    }
}