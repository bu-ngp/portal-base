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
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

class ActionButtonCreate
{
    protected $createButton;
    protected $grid;
    protected $key;
    protected $crudProp;

    static public function init(&$createButton, GridView $grid, $key, $crudProp)
    {
        if ($key !== 'create') {
            return false;
        }

        return new self($createButton, $grid, $key, $crudProp);
    }

    protected function __construct(&$createButton, GridView $grid, $key, $crudProp)
    {
        $this->createButton = $createButton;
        $this->grid = $grid;
        $this->key = $key;
        $this->crudProp = $crudProp;

        $createButton = $this->buttonInit();
    }

    protected function buttonInit()
    {
        $options = [
            'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success wk-gridview-crud-create',
            'data-pjax' => '0'
        ];

        $isTypeObject = isset($this->crudProp['class']);

        if ($isTypeObject) {
            $GWCreateCrud = Yii::createObject($this->crudProp['class'], [[
                'urlGrid' => $this->crudProp['urlGrid'],
                'inputName' => $this->crudProp['inputName'],
            ]]);

            $crudUrl = is_array($GWCreateCrud->getUrlGrid()) ? Url::to($GWCreateCrud->getUrlGrid()) : $GWCreateCrud->getUrlGrid();

            if ($GWCreateCrud instanceof GWAddCrudConfigForCreate) {
                $options = array_merge($options, [
                    'input-name' => $GWCreateCrud->getInputName(),
                ]);
            }

            $this->addCrudCreateSelectedToQuery();
        } else {
            $crudUrl = is_array($this->crudProp) ? Url::to($this->crudProp) : $this->crudProp;
        }

        return Html::a(Yii::t('wk-widget-gridview', 'Create'), $crudUrl, $options);
    }

    protected function addCrudCreateSelectedToQuery()
    {
        if ($this->grid->dataProvider instanceof ActiveDataProvider) {
            $condition = '';

            if (Yii::$app->request->headers['wk-choose']) {
                $condition = '1=2';

                if ($_choose = json_decode(Yii::$app->request->headers['wk-choose'])) {
                    if (is_array($_choose)) {
                        $condition = ['in', $this->grid->filterModel->primaryKey()[0], $_choose];
                    }
                }
            }

            if (!empty($condition)) {
                $this->grid->dataProvider->query->andWhere($condition);
            }

        }
    }
}