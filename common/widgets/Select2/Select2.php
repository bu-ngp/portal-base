<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:09
 */

namespace common\widgets\Select2;


use domain\helpers\GridViewHelper;
use common\widgets\PropellerAssets\PropellerAsset;
use common\widgets\PropellerAssets\Select2Asset AS PropellerSelect2Asset;
use common\widgets\Select2\assets\Select2Asset;
use common\widgets\PropellerAssets\TextFieldAsset;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\InputWidget;
use yii\bootstrap\Widget;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\Response;
use yii\web\View;

class Select2 extends \kartik\select2\Select2
{
    public $theme = self::THEME_BOOTSTRAP;
    /** @var \Closure анонимная функция с ActiveQuery поиска результатов
     *
     *  Используйте метод select() для выбора полей, которые будут отображаться в результатах через запятую
     *
     *  function(ActiveQuery $query) {
     *      $query->select(['id', 'code', 'description']);
     *  }
     */
    public $queryCallback;
    /** @var string Полное имя класса ActiveRecord используемое для создания ActiveQuery */
    public $activeRecordClass;
    /** @var  bool Если true, хранить выбранные значения в хлебных крошках, для восстановления при обновлении страницы, по умолчанию false */
    public $wkkeep = false;
    /** @var  string Имя класса иконок FontAwesome, для добавления иконки слева от select2
     *  [
     *      ...
     *      'wkicon' => FA::_ADDRESS_BOOK,
     *      ...
     *  ]
     */
    public $wkicon;
    /** @var bool Если true, то разрешить мультивыбор значений, по умолчанию false */
    public $multiple = false;
    /** @var  string Url грида common\widgets\GridView\GridView для выбора значений */
    public $selectionGridUrl;
    /** @var bool Если true, исключить первичные ключи в результатах поиска, по умолчанию true */
    public $exceptPrimaryKeyFromResult = true;
    /** @var array Конфигурация ajax поиска результатов
     * 'enabled' (bool) Включить ajax поиск результатов, по умолчанию true, если сконфигурирован Callback 'searchAjaxCallback'
     * 'searchAjaxCallback' => (\Closure) Анонимная функция ActiveQuery для задания условия поиска
     *
     *      function(ActiveQuery $query, $searchString) {
     *          $query->andWhere(['like', 'description', $searchString])
     *                ->orWhere(['like', 'code', $searchString]);
     *      }
     *
     * 'minRecordsCountForUseAjax' => 100 (int) Лимит количества записей результатов, при превышении которого включается Ajax поиск, по умолчанию 100
     * 'onlyAjax' => false (bool) Использовать только Ajax поиск результатов
     */
    public $ajaxConfig;

    public function init()
    {
        /** Если ajax запрос, вернуть json с результатами */
        $this->returnAjaxData();
        $this->initConfig();

        parent::init();
    }

    public function run()
    {
        $this->options['placeholder'] = ArrayHelper::getValue($this->options, 'placeholder', '');
        $this->pluginOptions['allowClear'] = ArrayHelper::getValue($this->pluginOptions, 'allowClear', true);
        $dataQuery = $this->getDataQuery();
        $resultQueryCount = $dataQuery->count();

        /** Восстановление значений при обновлении страницы */
        if ($this->wkkeep) {
            $this->options['wkkeep'] = true;
        }

        /** Иконка перед select2 */
        if ($this->wkicon) {
            $this->addon['prepend']['content'] .= '<i class="fa fa-2x fa-' . $this->wkicon . ' pmd-sm"></i>';
            $this->addon['groupOptions']['class'] .= ' wk-widget-input-prepend-icon';
        }

        /** Мультивыбор значений */
        if ($this->multiple) {
            $this->options['multiple'] = true;
        }

        /** Включить ajax загрузку результатов, если превышен лимит количества результатов */
        if ($this->ajaxConfig['enabled'] && ($resultQueryCount > $this->ajaxConfig['minRecordsCountForUseAjax'] || $this->ajaxConfig['onlyAjax'])) {
            $this->options['wk-ajax'] = true;
            $this->pluginOptions['minimumInputLength'] = ArrayHelper::getValue($this->pluginOptions, 'minimumInputLength', 3);
            $this->pluginOptions['ajax']['url'] = Url::current();
            $this->pluginOptions['ajax']['dataType'] = 'json';
            $this->pluginOptions['ajax']['data'] = new JsExpression('function(params) { return {q:params.term}; }');
            $this->pluginOptions['ajax']['delay'] = 500;
            $this->pluginOptions['escapeMarkup'] = new JsExpression('function (markup) { return markup; }');
            $this->pluginOptions['templateResult'] = new JsExpression('function(data) { return data.text; }');
            $this->pluginOptions['templateSelection'] = new JsExpression('function (data) { return data.text; }');
        } else {
            $resultQuery = $dataQuery->asArray()->all();
            /** @var array $row */
            foreach ($resultQuery as $row) {
                $row[$this->attribute] = $this->filterBinaryToString($row[$this->attribute]);
                $resultString = $this->filterPrimaryKeysAttributes($row);
                $this->data[$row[$this->attribute]] = implode(', ', $resultString);
            }
        }

        /** Инициализировать значения, если они есть в модели */
        if ($this->model->{$this->attribute}) {
            if ($this->multiple) {
                $this->options['wk-ajax'] ? $this->initAjaxMultiple($dataQuery) : $this->initAjaxSingle();
            } else {
                if ($this->options['wk-ajax']) {
                    $this->initDataMultiple($dataQuery);
                }

                $this->initSingle();
            }
        }

        /** Добавить кнопку выбора из грида */
        if ($this->selectionGridUrl) {
            $url = is_array($this->selectionGridUrl) ? Url::to($this->selectionGridUrl) : $this->selectionGridUrl;
            $this->addon['append']['content'] = '<div class="input-group-addon wk-block-select2-choose-from-grid"><a class="btn btn-sm btn-success wk-widget-select2-choose-from-grid pmd-ripple-effect pmd-btn-fab" href="' . $url . '"><i class="fa fa-2x fa-ellipsis-h pmd-sm"></i></a></div>' . ArrayHelper::getValue($this->addon, 'append.content', '');
            $this->addon['append']['asButton'] = true;

            /** Проинициализировать выбранное значение из грида */
            $this->selectedAttribute();
        }

        $this->registerWKAssets1();
        parent::run();
        $this->registerWKAssets2();
    }

    protected function selectedAttribute()
    {
        if (Yii::$app->request->get('grid') === $this->options['id'] && Yii::$app->request->get('selected')) {
            $this->options['wk-selected'] = Yii::$app->request->get('selected');

            if ($this->options['wk-ajax']) {
                $dataQuery = $this->getDataQuery();
                $selectedID = $this->filterStringToBinary(Yii::$app->request->get('selected'));

                $resultQuery = $dataQuery->andWhere([$this->attribute => $selectedID])->asArray()->one();
                $resultQuery[$this->attribute] = $this->filterBinaryToString($resultQuery[$this->attribute]);
                $resultString = $this->filterPrimaryKeysAttributes($resultQuery);
                if ($this->multiple) {
                    $this->data[$resultQuery[$this->attribute]] = implode(', ', $resultString);
                } else {
                    $this->initValueText = implode(', ', $resultString);
                }

                if ($this->multiple) {
                    $this->value = $this->value ?: [];
                }

                $this->multiple ? array_push($this->value, $resultQuery[$this->attribute]) : $this->value = $resultQuery[$this->attribute];
            }
        }
    }

    /**
     * @return ActiveQuery
     */
    protected function getDataQuery()
    {
        $query = call_user_func([$this->activeRecordClass, 'find']);
        call_user_func($this->queryCallback, $query);

        return $query;
    }

    protected function registerWKAssets1()
    {
        $view = $this->getView();

        //TextFieldAsset::register($view);

    }

    protected function registerWKAssets2()
    {
        $view = $this->getView();

        //PropellerSelect2Asset::register($view);
        Select2Asset::register($view);
        $view->registerJs("$('#{$this->options['id']}').wkselect2();");
        PropellerAsset::setWidget(self::className());
        //TextFieldAsset::assetDepend(self::className());
    }

    protected function returnAjaxData()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->get('q')) {
            Yii::$app->response->clearOutputBuffers();
            $jsonObj = [];

            $id = $this->filterStringToBinary($_GET['id']);
            $q = $_GET['q'];

            /** @var ActiveQuery $query */
            $query = call_user_func([$this->activeRecordClass, 'find']);
            call_user_func($this->queryCallback, $query);
            $resultReturn = [];

            if ($id) {
                $result = $query->andWhere([$this->attribute => $id])->asArray()->one();

                $result[$this->attribute] = $this->filterBinaryToString($result[$this->attribute]);
                $resultString = $this->filterPrimaryKeysAttributes($result);
                $resultReturn = ['id' => $result[$this->attribute], 'text' => implode(', ', $resultString)];

                $jsonObj = $resultReturn;
            } elseif ($q) {
                call_user_func($this->ajaxConfig['searchAjaxCallback'], $query, $q);
                $result = $query->asArray()->all();
                /** @var array $row */
                foreach ($result as $row) {
                    $row[$this->attribute] = $this->filterBinaryToString($row[$this->attribute]);
                    $resultString = $this->filterPrimaryKeysAttributes($row);
                    $resultReturn[] = ['id' => $row[$this->attribute], 'text' => implode(', ', $resultString)];
                }

                $jsonObj = ['results' => $resultReturn];
            }

            exit(json_encode($jsonObj));
        }
    }

    protected function filterPrimaryKeysAttributes(array $resultArray)
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = new $this->activeRecordClass;
        $exceptPrimaryKeyFromResult = $this->exceptPrimaryKeyFromResult;

        return array_filter(array_map(function ($attribute, $value) use ($activeRecord, $exceptPrimaryKeyFromResult) {
            if (!$exceptPrimaryKeyFromResult || $exceptPrimaryKeyFromResult && !$activeRecord->isPrimaryKey([$attribute])) {
                return $value;
            }

            return false;
        }, array_keys($resultArray), $resultArray));
    }

    protected function filterBinaryToString($value)
    {
        return GridViewHelper::isBinary($value) ? Uuid::uuid2str($value) : $value;
    }

    protected function filterStringToBinary($value)
    {
        return GridViewHelper::isBinaryValidString($value) ? Uuid::str2uuid($value) : $value;
    }

    protected function initAjaxMultiple(ActiveQuery $dataQuery)
    {
        $resultQuery = $dataQuery->andWhere([$this->attribute => $this->model->{$this->attribute}])->asArray()->all();

        $attribute = $this->attribute;
        $data = [];
        $this->value = [];
        $resultQuery = array_map(function ($value) use ($attribute, &$data) {
            $value[$attribute] = $this->filterBinaryToString($value[$attribute]);
            $resultString = $this->filterPrimaryKeysAttributes($value);
            $data[$value[$attribute]] = implode(', ', $resultString);
            array_push($this->value, $value[$attribute]);
            return $value;
        }, $resultQuery);

        $this->model->{$this->attribute} = ArrayHelper::getColumn($resultQuery, $this->attribute);
        $this->data = $data;
    }

    protected function initAjaxSingle()
    {
        $this->model->{$this->attribute} = array_map(function ($value) {
            return $this->filterBinaryToString($value);
        }, $this->model->{$this->attribute});
    }

    protected function initDataMultiple(ActiveQuery $dataQuery)
    {
        $resultQuery = $dataQuery->andWhere([$this->attribute => $this->model->{$this->attribute}])->asArray()->one();
        $resultQuery[$this->attribute] = $this->filterBinaryToString($resultQuery[$this->attribute]);
        $resultString = $this->filterPrimaryKeysAttributes($resultQuery);
        $this->initValueText = implode(', ', $resultString);
        $this->value = $resultQuery[$this->attribute];
    }

    protected function initSingle()
    {
        $this->model->{$this->attribute} = $this->filterBinaryToString($this->model->{$this->attribute});
    }

    protected function initConfig()
    {
        $this->ajaxConfig['enabled'] = ArrayHelper::getValue($this->ajaxConfig, 'enabled', $this->ajaxConfig['searchAjaxCallback'] instanceof \Closure);
        $this->ajaxConfig['minRecordsCountForUseAjax'] = ArrayHelper::getValue($this->ajaxConfig, 'minRecordsCountForUseAjax', 100);
        $this->ajaxConfig['onlyAjax'] = ArrayHelper::getValue($this->ajaxConfig, 'onlyAjax', false);
    }
}