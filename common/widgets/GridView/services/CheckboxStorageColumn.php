<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 24.06.2017
 * Time: 15:18
 */
namespace common\widgets\GridView\services;

use kartik\grid\CheckboxColumn;
use Yii;
use yii\bootstrap\Html;

/**
 * Класс колонки `\yii\grid\GridView`.
 * Колонка выбора записей с возможность хранить выбранные записи в локальном хранилище браузера.
 *
 * [\yii\grid\GridView](https://www.yiiframework.com/doc/api/2.0/yii-grid-gridview)
 */
class CheckboxStorageColumn extends CheckboxColumn
{
    /** @var string Переопределение css класса колонки */
    public $cssClass = 'kv-row-checkbox wk-widget-row-checkbox';

    /** Инициализация колонки */
    public function init()
    {
        Html::addCssClass($this->headerOptions, 'wk-widget-all-select');
        parent::init();
    }

    /**
     * Переопределение клиентских скриптов. Добавление jquery плагина `gridselected2storage.js` к контейнеру грида.
     */
    public function registerClientScript()
    {
        parent::registerClientScript();

        $options = [
            'storage' => 'selectedRows',
            'selectedPanelClass' => 'selectedPanel',
            'recordsSelectedMessage' => Yii::t('wk-widget-gridview', 'Records selected <b>{from}</b> from <b>{all}</b>'),
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $this->grid->getView()->registerJs("$('#{$this->grid->id}-pjax').gridselected2storage($options);");
    }
}