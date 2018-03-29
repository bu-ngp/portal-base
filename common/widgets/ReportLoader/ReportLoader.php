<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 9:21
 */

namespace common\widgets\ReportLoader;

use common\widgets\PropellerAssets\PropellerAsset;
use Yii;
use yii\bootstrap\Widget;

/**
 * Виджет обработки и формирания отчетов.
 *
 * Представляет из себя модальное окно со списом формирующихся и сформированных отчетов с возможностью скачивания для каждого пользователя.
 *
 * Возможности виджета:
 * * Паралельное формирование отчетов в независимости от обновления страницы в браузере;
 * * Отображение процента выполнения отчета;
 * * Формирование отчетов в `PDF` и `Excel` форматах;
 * * Возможность подключить к [[\common\widgets\GridView\GridView]] для выгрузки данных грида;
 * * Возможность формировать отчеты по шаблонам составленных в формате `Excel`;
 * * Возможность формировать отчеты по провайдерам данных [\yii\data\ActiveDataProvider](https://www.yiiframework.com/doc/api/2.0/yii-data-activedataprovider);
 * * Возможность очищать, удалять, отменять выполнение отчетов.
 *
 * Для работы виджета необходимо применить миграцию `ReportLoader/migrations/m170604_042824_reportLoader.php`
 *
 * Таблица содержит следующие поля:
 *
 * Имя поля таблицы        | Описание
 * ----------------------- | ---------------------
 * `rl_id`                 | Автоинкрементный идентификатор отчетов
 * `rl_process_id`         | Идентификатор пользователя или идентификатор сессии
 * `rl_report_id`          | Идентификатор вида отчета
 * `rl_report_filename`    | Путь файла отчета в файловой системе
 * `rl_report_displayname` | Имя отчета (имя файла отчета)
 * `rl_report_type`        | Тип отчета (`Excel2007` или `PDF`)
 * `rl_status`             | Статус выполнения отчета (1 - `В процессе`, 2 - `Выполнен`, 3 - `Отменен`)
 * `rl_percent`            | Процент выполнения отчета, от 0 до 100
 * `rl_start`              | Дата и время формирования отчета
 *
 * **Примеры использования:**
 *
 * Виджет необходимо разместить в главном представлении `layout.php`
 *
 * ```php
 *    ...
 *    <?= ReportLoader::widget(['id' => 'wk-Report-Loader']) ?>
 *    ...
 * ```
 *
 * Пример формирования отчета с помощью шаблона: [[ReportByTemplate]].
 *
 * Пример формирования отчета с помощью модели и провайдера данных: [[ReportByModel]]
 *
 * Пример использования виджета в [[\common\widgets\GridView\GridView]]
 *
 */
class ReportLoader extends Widget
{
    /**
     * Инициализация виджета.
     */
    public function init()
    {
        $this->registerTranslations();
        parent::init();
    }

    /**
     * Регистрация сообщений i18n
     */
    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget-report-loader'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    /**
     * Выполнение виджета
     */
    public function run()
    {
        echo '<div id="' . $this->id . '"></div>';
        $this->registerAssets();
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        ReportLoaderAsset::register($view);
        $options = (object)array_filter($this->makeDialogMessages());

        $view->registerJs('$("#' . $this->id . '").wkreportloader(' . json_encode($options, JSON_UNESCAPED_UNICODE) . ');');
        PropellerAsset::setWidget(self::className());
    }

    protected function makeDialogMessages()
    {
        $messages = [
            'titleDialogMessage' => Yii::t('wk-widget-report-loader', 'Report Loader'),
            'closeButtonMessage' => Yii::t('wk-widget-report-loader', 'Close'),
            'cancelButtonMessage' => Yii::t('wk-widget-report-loader', 'Cancel Operation'),
            'deleteButtonMessage' => Yii::t('wk-widget-report-loader', 'Remove File'),
            'downloadButtonMessage' => Yii::t('wk-widget-report-loader', 'Download File'),
            'clearButtonMessage' => Yii::t('wk-widget-report-loader', 'Clear'),
            'deleteConfirmMessage' => Yii::t('wk-widget-report-loader', 'Delete Report. Are you sure?'),
            'cancelConfirmMessage' => Yii::t('wk-widget-report-loader', 'Cancel Report. Are you sure?'),
            'clearConfirmMessage' => Yii::t('wk-widget-report-loader', 'Delete All Reports. Are you sure?'),
            'errorAlertMessage' => Yii::t('wk-widget-report-loader', 'Error'),
            'emptyMessage' => Yii::t('wk-widget-report-loader', 'Empty'),
        ];

        return $messages;
    }
}