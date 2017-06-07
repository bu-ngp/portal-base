<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 05.06.2017
 * Time: 9:21
 */

namespace common\widgets\ReportLoader;


use common\widgets\ReportLoader\assets\ProgressbarAsset;
use common\widgets\ReportLoader\assets\ReportLoaderAsset;
use Yii;
use yii\bootstrap\Widget;

class ReportLoader extends Widget
{
    public function __construct(array $config = [])
    {
        $this->registerTranslations();
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget-report-loader'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

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
    }

    protected function makeDialogMessages()
    {
        $messages = [
            'titleDialogMessage' => Yii::t('wk-widget-report-loader', 'Report Loader'),
            'closeButtonMessage' => Yii::t('wk-widget-report-loader', 'Close'),
            'cancelButtonMessage' => Yii::t('wk-widget-report-loader', 'Cancel Operation'),
            'deleteButtonMessage' => Yii::t('wk-widget-report-loader', 'Remove File'),
            'downloadButtonMessage' => Yii::t('wk-widget-report-loader', 'Download File'),
        ];
        
        return $messages;
    }
}