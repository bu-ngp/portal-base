<?php
namespace common\widgets\GridView\services;

use kartik\grid\CheckboxColumn;
use Yii;
use yii\bootstrap\Html;

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 24.06.2017
 * Time: 15:18
 */
class CheckboxStorageColumn extends CheckboxColumn
{
    public $cssClass = 'kv-row-checkbox wk-widget-row-checkbox';

    public function init()
    {
        Html::addCssClass($this->headerOptions, 'wk-widget-all-select');
        parent::init();
    }

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