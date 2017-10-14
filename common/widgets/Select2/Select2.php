<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:09
 */

namespace common\widgets\Select2;


use common\widgets\PropellerAssets\Select2Asset AS PropellerSelect2Asset;
use common\widgets\Select2\assets\Select2Asset;
use common\widgets\PropellerAssets\TextFieldAsset;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\InputWidget;
use yii\bootstrap\Widget;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\Response;
use yii\web\View;

class Select2 extends \kartik\select2\Select2
{
    public $theme = self::THEME_BOOTSTRAP;
    public $queryCallback;
    /** @var  ActiveQuery */
    public $modelQuery;
    public $activeRecordClass;
    public $idAttribute;
    public $wkkeep;
    public $wkicon;
    public $multiple = false;
    public $selectionGridUrl;

//    public $data;
//    public $selectClass = 'select-with-search form-control pmd-select2';

    public function init()
    {
        $this->returnAjaxData();

        parent::init();
    }

    public function run()
    {
        $this->pluginOptions['allowClear'] = true;

        $dataQuery = $this->getDataQuery();
        $resultQueryCount = $dataQuery->count();
        $this->options['placeholder'] = '';

        if ($resultQueryCount > 1 /*100*/) {
            $this->options['wk-ajax'] = true;
            $this->pluginOptions['minimumInputLength'] = 2;
            $this->pluginOptions['ajax']['url'] = Url::current();
            $this->pluginOptions['ajax']['dataType'] = 'json';
            $this->pluginOptions['ajax']['data'] = new JsExpression('function(params) { return {q:params.term}; }');
            $this->pluginOptions['ajax']['delay'] = 500;
            $this->pluginOptions['escapeMarkup'] = new JsExpression('function (markup) { return markup; }');
            $this->pluginOptions['templateResult'] = new JsExpression('function(data) { return data.text; }');
            $this->pluginOptions['templateSelection'] = new JsExpression('function (data) { return data.text; }');
        } else {
            $resultQuery = $dataQuery->asArray()->all();
            foreach ($resultQuery as $row) {
                $row[$this->attribute] = Uuid::uuid2str($row[$this->attribute]);
                $this->data[$row[$this->attribute]] = implode(', ', $row);
            }
        }

        if ($this->model->{$this->idAttribute}) {
            if ($this->multiple) {
                // Не фурычит
                $resultQuery = $dataQuery->andWhere([$this->idAttribute => $this->model->{$this->idAttribute}])->asArray()->one();
                $resultQuery[$this->attribute] = Uuid::uuid2str($resultQuery[$this->attribute]);
                $this->data = [$resultQuery[$this->attribute] => implode(', ', $resultQuery)];
                $this->value = [Uuid::uuid2str($this->model->{$this->idAttribute})];
            } else {
                $resultQuery = $dataQuery->andWhere([$this->idAttribute => $this->model->{$this->idAttribute}])->asArray()->one();
                $resultQuery[$this->attribute] = Uuid::uuid2str($resultQuery[$this->attribute]);
                $this->initValueText = implode(', ', $resultQuery);
                $this->value = Uuid::uuid2str($this->model->{$this->idAttribute});
            }
        }

        if ($this->wkkeep) {
            $this->options['wkkeep'] = true;
        }

        if ($this->wkicon) {
            $this->addon['prepend']['content'] = '<i class="fa fa-2x fa-' . $this->wkicon . ' pmd-sm"></i>';
            $this->addon['groupOptions']['class'] .= ' wk-widget-input-prepend-icon';
        }

        if ($this->multiple === true) {
            $this->options['multiple'] = true;
            $this->options['tags'] = true;
        }

        if ($this->selectionGridUrl) {
            $url = is_array($this->selectionGridUrl) ? Url::to($this->selectionGridUrl) : $this->selectionGridUrl;

            $this->addon['append']['content'] = '<div class="input-group-addon wk-block-select2-choose-from-grid"><a class="btn btn-success wk-widget-select2-choose-from-grid" href="' . $url . '"><i class="glyphicon glyphicon-option-horizontal pmd-sm"></i></a></div>';
//  $this->addon['contentAfter'] =
//                Html::button(Html::icon('option-horizontal'), [
//                'class' => 'btn btn-success',
//                'title' => Yii::t('wk-widget-select2', 'Choose from Grid'),
//                'data-toggle' => 'tooltip'
//            ]);
            //  $this->addon['groupOptions']['encode'] = false;
            $this->addon['append']['asButton'] = true;
        }

        $this->selectedAttribute();

        $this->registerWKAssets1();
        parent::run();
        $this->registerWKAssets2();
    }

    protected function selectedAttribute()
    {
        if (Yii::$app->request->get('grid') === $this->options['id'] && Yii::$app->request->get('selected')) {
            if ($this->options['wk-ajax']) {
                $dataQuery = $this->getDataQuery();
                /* ???????????????????????????????????????????????? */
                $resultQuery = $dataQuery->andWhere([$this->idAttribute => Uuid::str2uuid(Yii::$app->request->get('selected'))])->asArray()->one();
                $resultQuery[$this->attribute] = Yii::$app->request->get('selected');
                $this->initValueText = implode(', ', $resultQuery);
                if ($this->multiple) {
                    $this->value[] = Yii::$app->request->get('selected');
                } else {
                    $this->value = Yii::$app->request->get('selected');
                }
            }

            $this->options['wk-selected'] = Yii::$app->request->get('selected');
        }
    }

    /**
     * @return ActiveQuery
     */
    protected function getDataQuery()
    {
        //$this->modelQuery->asArray();

        $callBack = $this->queryCallback;

        $query = new $this->activeRecordClass;

        return $callBack($query::find());
    }

    protected function registerWKAssets1()
    {
        $view = $this->getView();

        //TextFieldAsset::register($view);

    }

    protected function registerWKAssets2()
    {
        $view = $this->getView();

//        Select2Asset::register($view);
        PropellerSelect2Asset::register($view);
        Select2Asset::register($view);
        $view->registerJs("$('#{$this->options['id']}').wkselect2();");


        $view->registerJs(<<<EOT
        
//        $.ajax({ // make the request for the selected data object
//          type: 'GET',
//          url: window.location.href + "/?id=7741AF08ACBD11E79E9E902B3479B004",
//          dataType: 'json'
//        }).then(function (data) {
//        console.debug(data);
//          // Here we should have the data object
//          var \$option = $('<option selected></option>');
//          $('#employeeform-dolzh_id').append(\$option).trigger('change');
//          \$option.text(data.text).val(data.id); // update the text that is displayed (and maybe even the value)
//          \$option.removeData(); // remove any caching data that might be associated
//          $('#employeeform-dolzh_id').trigger('change'); // notify JavaScript components of possible changes
//        });
EOT
        );

//
//
//        $view->registerJs(<<<EOT
//$('#{$this->id}').select2({theme: "bootstrap"});
//EOT
//        );
    }

    protected function returnAjaxData()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->clearOutputBuffers();

            $jsonObj = [];

            $id = Uuid::str2uuid($_GET['id']);
            $q = $_GET['q'];

            $queryCallback = $this->queryCallback;
            $query = $queryCallback((new $this->activeRecordClass)->find());
            $resultReturn = [];

            if ($id) {
                $result = $query->andWhere([$this->attribute => $id])->asArray()->one();
                $result[$this->attribute] = Uuid::uuid2str($result[$this->attribute]);
                $resultReturn = ['id' => $result[$this->attribute], 'text' => implode(', ', $result)];

                $jsonObj = $resultReturn;
            }

            if ($q) {
                $result = $query->andWhere(['like', 'dolzh_name', $q])->asArray()->all();
                foreach ($result as $row) {
                    $row[$this->attribute] = Uuid::uuid2str($row[$this->attribute]);
                    $resultReturn[] = ['id' => $row[$this->attribute], 'text' => implode(', ', $row)];
                }

                $jsonObj = ['results' => $resultReturn];
            }

            exit(json_encode($jsonObj));
        }
    }
//
//    public function init()
//    {
//
//        echo Html::dropDownList('name', '', $this->data, ['id' => $this->id, 'class' => $this->selectClass]);
//    }


}