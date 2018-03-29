<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 01.09.2017
 * Time: 13:58
 */

namespace common\widgets\GridView\services;

use common\widgets\GridView\GridView;
use domain\helpers\BinaryHelper;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\bootstrap\Html;

/**
 * Класс кнопки действия грида "Выбрать"
 */
class ActionButtonChoose
{
    protected $actionButtons;
    protected $grid;

    /**
     * Создать экземпляр класса
     *
     * @param array $actionButtons Конфигурационный массив кнопок `CRUD`
     * @param GridView $grid Грид [[\common\widgets\GridView\GridView]]
     * @return $this
     */
    static public function init(array &$actionButtons, GridView $grid)
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
                $selected = BinaryHelper::isBinary($model->primaryKey) ? Uuid::uuid2str($model->primaryKey) : $model->primaryKey;
                $url = $_selected->url . (preg_match('/\?/', $_selected->url) ? '&' : '?') . 'grid=' . urlencode($_selected->gridID) . '&selected=' . urlencode($selected);

                return Html::a('<i class="fa fa-2x fa-check-square-o"></i>', $url, ['title' => Yii::t('wk-widget-gridview', 'Choose'), 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0']);
            };

            $func = $this->grid->gridExcludeIdsFunc;

            if (property_exists($_selected, 'exclude')) {
                $_selected->exclude = is_array($_selected->exclude) ? $_selected->exclude : [$_selected->exclude];
                $_selected->exclude = array_map(function ($id) {
                    return BinaryHelper::isBinaryValidString($id) ? Uuid::str2uuid($id) : $id;
                }, $_selected->exclude);

                $func($this->grid->dataProvider->query, $_selected->exclude, GridView::ADD);
            } else {
                $_selected->reject = BinaryHelper::isBinaryValidString($_selected->reject) ? Uuid::str2uuid($_selected->reject) : $_selected->reject;
                $func($this->grid->dataProvider->query, [$_selected->reject], GridView::EDIT);
            }
        }

        return $this->actionButtons;
    }
}