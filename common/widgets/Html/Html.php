<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 05.12.2017
 * Time: 15:11
 */

namespace common\widgets\Html;

use Yii;

/**
 * Расширение стандартного класса Yii2 `\yii\bootstrap\Html`.
 * [\yii\bootstrap\Html](https://www.yiiframework.com/doc-2.0/yii-bootstrap-html.html)
 */
class Html extends \yii\bootstrap\Html
{
    /**
     * Кнопка обновления записи на базе `Html::submitButton()`.
     *
     * @param array $options Опции виджета.
     * @param string $title Имя кнопки, по умолчанию `Обновить`.
     * @return string
     */
    public static function updateButton($options = [], $title = '')
    {
        $title = $title ?: Yii::t('common', 'Update');
        $options = array_merge(['class' => 'btn pmd-btn-raised pmd-ripple-effect btn-primary'], $options);
        return parent::submitButton($title, $options);
    }

    /**
     * Кнопка создания записи на базе `Html::submitButton()`.
     *
     * @param array $options Опции виджета.
     * @param string $title Имя кнопки, по умолчанию `Добавить`.
     * @return string
     */
    public static function createButton($options = [], $title = '')
    {
        $title = $title ?: Yii::t('common', 'Create');
        $options = array_merge(['class' => 'btn pmd-btn-raised pmd-ripple-effect btn-success'], $options);
        return parent::submitButton($title, $options);
    }

    /**
     * Кнопка создания записи с последующим переходом на страницу редактирования, на базе `Html::submitButton()`.
     *
     * @param array $options Опции виджета.
     * @param string $title Имя кнопки, по умолчанию `Далее`.
     * @return string
     */
    public static function nextButton($options = [], $title = '')
    {
        $title = $title ?: Yii::t('common', 'Next');
        $options = array_merge(['class' => 'btn pmd-btn-raised pmd-ripple-effect btn-primary'], $options);
        return parent::submitButton($title, $options);
    }
}