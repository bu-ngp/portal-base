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

/**
 * Класс для вывода контента документа для виджета [[\common\widgets\Documenter\Documenter]]
 */
class DocumenterViewer
{
    private $_filePath;
    private $_name;
    private $_id;
    private $_permissions;
    private $_order;
    private $_tabHash;
    private $_pillHash;

    /**
     * Конструктор класса.
     *
     * @param string $path Часть пути к файлу содержащая `Имя вкладки/Имя плитки (файла с контентом для документа)`
     * @param string $filePath Полный путь к файлу документу.
     * @throws InvalidConfigException
     */
    public function __construct($path, $filePath)
    {
        $this->_filePath = $filePath;
        $path = DIRECTORY_SEPARATOR === '/' ? $path : mb_convert_encoding($path, 'UTF-8', 'Windows-1251');

        $pattern = DIRECTORY_SEPARATOR === '/'
            ? '/^\/((\d+)_)?(\w+)(\[([\w\d_-|]+)?\])?\/(\d+|[\w\d-_\(\)]+)\.md$/u'
            : '/^\\\\((\d+)_)?(\w+)(\[([\w\d_-|]+)?\])?\\\\(\d+|[\w\d-_\(\)]+)\.md$/u';

        if (!preg_match($pattern, $path, $matches)) {
            throw new InvalidConfigException("Invalid parse path '$path'");
        }

        $this->_name = $matches[3];
        $this->_id = $matches[6];
        $this->_permissions = empty($matches[5]) ? [] : explode('|', $matches[5]);
        $this->_order = $matches[2];
        $this->_tabHash = 't_' . hash('crc32', $this->getTabName());
        $this->_pillHash = 'p_' . hash('crc32', $this->getPillName());
    }

    /**
     * Возвращает имя вкладки документа
     *
     * @return string
     */
    public function getTabName()
    {
        return $this->_name;
    }

    /**
     * Возвращает имя вкладки документа, с преобразованием даты, если она содержится в имени файла.
     *
     * @return string
     */
    public function getPillName()
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->_id)) {
            return date('d.m.Y', strtotime($this->_id));
        }

        return $this->_id;
    }

    /**
     * Возвращает оригинальное имя вкладки документа.
     *
     * @return string
     */
    public function getOrigPillName()
    {
        return $this->_id;
    }

    /**
     * Провверяет права доступа для отображения документа, если сконфигурировны разрешения.
     *
     * @return bool
     */
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

    /**
     * Возвращает порядковый номер вкладки, если есть. Иначе `false`.
     *
     * @return bool|int
     */
    public function getOrder()
    {
        return $this->_order ?: false;
    }

    /**
     * Получаем содержимое файла с контентом документа.
     *
     * Для подстановки абсолютного пути Url веб директории в контенте можно использовать маску `{absoluteWebRoot}`, которая автоматически заменится на базовый url приложения.
     *
     * @return string
     */
    public function getContent()
    {
        return strtr(file_get_contents($this->_filePath), ['{absoluteWebRoot}' => Url::base(true)]);
    }

    /**
     * Возвращает хэш вкладки документа.
     *
     * @return string
     */
    public function getTabHash()
    {
        return $this->_tabHash;
    }

    /**
     * Возвращает хэш плитки документа.
     *
     * @return string
     */
    public function getPillHash()
    {
        return $this->_pillHash;
    }
}