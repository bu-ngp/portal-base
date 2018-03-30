<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 09.07.2017
 * Time: 12:37
 */

namespace common\widgets\GridViewModal;

use common\widgets\GridView\GridView;
use Yii;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Виджет [[\common\widgets\GridView\GridView]] для использования в модальных окнах.
 */
class GridViewModal extends GridView
{
    public $panelPrefix = 'wkModalGrid panel panel-';
    public $minHeight   = 510;

    /**
     * Выполнение виджета
     */
    public function run()
    {
        parent::run();
        $this->registerGridViewModalAssets();
    }

    protected function registerGridViewModalAssets()
    {
        $view = $this->getView();
        GridViewModalAsset::register($view);
    }

    protected function setDefaults()
    {
        parent::setDefaults();

        $this->id = Yii::$app->controller->id . '_' . Yii::$app->controller->action->id;
        $this->pjaxSettings['options']['clientOptions']['url'] = new JsExpression('function() { return (typeof event.srcElement == "undefined") ? "' . Url::to([Yii::$app->urlManager->parseRequest(Yii::$app->request)[0]]) . '" : event.srcElement.href }');
        $this->pjaxSettings['options']['enablePushState'] = false;
    }

    protected function registerAssetsByWk()
    {
        $this->loadGridSelected2TextInputJs();
        parent::registerAssetsByWk();
    }

    protected function loadGridSelected2TextInputJs()
    {
        $options = [
            'storageElementName' => 'wk-crudCreate-input',
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $this->js[] = "$('#{$this->id}-pjax').gridselected2textinput($options);";

    }
}