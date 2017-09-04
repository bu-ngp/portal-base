<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.09.2017
 * Time: 13:58
 */

namespace common\widgets\GridView\services;

use common\widgets\GridView\GridView;
use Yii;
use yii\bootstrap\Html;

class ActionButtonChoose
{
    protected $actionButtons;
    protected $grid;

    static public function init(&$actionButtons, GridView $grid)
    {
        return new self($actionButtons, $grid);
    }

    protected function __construct(&$actionButtons, GridView $grid)
    {
        $this->actionButtons = $actionButtons;
        $this->grid = $grid;

        $actionButtons = $this->buttonInit();
    }

    protected function buttonInit()
    {
        if ($this->grid->gridExcludeIdsFunc instanceof \Closure
            && Yii::$app->request->headers['wk-selected']
            && ($_selected = json_decode(Yii::$app->request->headers['wk-selected']))
            && (property_exists($_selected, 'exclude') || property_exists($_selected, 'reject'))
        ) {
            $this->actionButtons['choose'] = function ($url, $model) use ($_selected) {
                $url = $_selected->url . (preg_match('/\?/', $_selected->url) ? '&' : '?') . 'grid=' . urlencode($_selected->gridID) . '&selected=' . urlencode($model->primaryKey);

                return Html::a('<i class="fa fa-2x fa-check-square-o"></i>', $url, ['title' => Yii::t('wk-widget-gridview', 'Choose'), 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0']);
            };

            $func = $this->grid->gridExcludeIdsFunc;

            if (property_exists($_selected, 'exclude')) {
                $func($this->grid->dataProvider->query, $_selected->exclude, GridView::ADD);
            } else {
                $func($this->grid->dataProvider->query, [$_selected->reject], GridView::EDIT);
            }
        }

        return $this->actionButtons;
    }
}