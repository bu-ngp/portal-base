<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.10.2017
 * Time: 13:09
 */

namespace common\widgets\Select2;

use domain\helpers\BinaryHelper;
use common\widgets\PropellerAssets\PropellerAsset;
use wartron\yii2uuid\helpers\Uuid;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Виджет выбора значений из списка с использованием jquery плагина `Select2`.
 *
 * Содержит следующие возможности:
 * * Имеет возможность искать результаты, используя ajax запрос на страницу виджета;
 * * Включение ajax поиска при превышении минимального количества значений в списке, заданных в конфигурации;
 * * Возможность выбрать значение из грида [[\common\widgets\GridView\GridView]];
 * * Мультивыбор значений;
 * * Хранение значения, даже при обновлении страницы, при использовании виджета [[\common\widgets\Breadcrumbs\Breadcrumbs]];
 * * Вывод нескольких полей запроса в результат поиска, перечисленных через запятую.
 *
 * ```php
 * <?= $form->field($modelForm, 'build_id')->select2([
 *     'activeRecordClass' => Build::className(),
 *     'queryCallback' => function (ActiveQuery $query) {
 *         return $query->select(['build_id', 'build_name']);
 *     },
 *     'ajaxConfig' => [
 *         'enabled' => true,
 *         'searchAjaxCallback' => function (ActiveQuery $query, $searchString) {
 *             $query->andWhere(['like', 'build_name', $searchString]);
 *         },
 *         'minRecordsCountForUseAjax' => 50,
 *     ],
 *     'wkkeep' => true,
 *     'wkicon' => 'home',
 *     'selectionGridUrl' => ['build/index'],
 *     'multiple' => true,
 * ]); ?>
 * ```
 */
class Select2 extends \kartik\select2\Select2
{
    /** @var string Переопределенная тема на `Bootstrap` базового виджета `\kartik\select2\Select2`. [\kartik\select2\Select2](https://github.com/kartik-v/yii2-widget-select2/blob/master/Select2.php) */
    public $theme = self::THEME_BOOTSTRAP;
    /**
     * @var \Closure Анонимная функция для формирования строк результатов поиска `Select2`.
     *
     *  Используйте метод select() [\yii\db\ActiveQuery](https://www.yiiframework.com/doc/api/2.0/yii-db-activequery) для выбора полей, которые будут отображаться в результатах через запятую.
     *
     * ```php
     *  function(ActiveQuery $query) {
     *      $query->select(['id', 'code', 'description']);
     *  }
     * ```
     */
    public $queryCallback;
    /** @var string Полное имя класса `\yii\db\ActiveRecord`, используемое атрибутом формы. [\yii\db\ActiveRecord](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord) */
    public $activeRecordClass;
    /**
     * @var string Имя атрибута класса `\yii\db\ActiveRecord`, используемой атрибутом формы.
     * [\yii\db\ActiveRecord](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecord)
     *
     * Заполняется в случае, если имя утрибута формы, отличается от имени атрибута [[activeRecordClass]].
     *
     * По умолчанию `$this->attribute`
     */
    public $activeRecordAttribute;
    /** @var bool Если `true`, хранить выбранные значения в хлебных крошках, для восстановления при обновлении страницы, по умолчанию `false` */
    public $wkkeep = false;
    /** @var string Имя класса иконок FontAwesome, для добавления иконки слева относительно виджета.
     *
     * ```php
     *     <?= $form->field($modelForm, 'build_id')->select2([
     *             'activeRecordClass' => Build::className(),
     *             'queryCallback' => BuildQuery::select(),
     *             'wkicon' => 'home', // css класс иконки FontAwesome "fa fa-home"
     *     ]); ?>
     * ```
     */
    public $wkicon;
    /** @var bool Если `true`, то разрешить мультивыбор значений, по умолчанию `false` */
    public $multiple = false;
    /**
     * @var string Url страницы с гридом [[\common\widgets\GridView\GridView]].
     * В гриде добавляется кнопка выбора значения.
     * При выборе значения происходит переход на страницу с виджетом [[Select2]] с уже выбранным значением из грида.
     */
    public $selectionGridUrl;
    /** @var bool Если `true`, исключить первичные ключи в результатах поиска, по умолчанию `true` */
    public $exceptPrimaryKeyFromResult = true;
    /** @var array Массив имен атрибутов, которые следует искючить из результатов поиска */
    public $exceptAttributesFromResult = [];
    /**
     * @var array Конфигурация ajax поиска результатов.
     *
     * Содержит следующие опции ключей массива:
     *
     * Имя ключа массива           | Тип значения | Значение по умолчанию                                                   | Описание
     * --------------------------- | ------------ | ----------------------------------------------------------------------- | -----------------------------------------------------------------------
     * `enabled`                   | `bool`       | `true`, если сконфигурирована опция `searchAjaxCallback`, иначе `false` | Включить `ajax` поиск результатов
     * `searchAjaxCallback`        | `\Closure`   | `null`                                                                  | Анонимная функция для задания условия поиска. Содержит следующие параметры: <br> `$query` - Класс [\yii\db\ActiveQuery](https://www.yiiframework.com/doc/api/2.0/yii-db-activequery), используемый для поиска результатов. <br> `$searchString` - Строка поиска, введенная пользователем.
     * `minRecordsCountForUseAjax` | `int`        | `100`                                                                   | Лимит количества записей результатов, при превышении которого включается Ajax поиск
     * `onlyAjax`                  | `bool`       | `false`                                                                 | Использовать только Ajax поиск результатов. В случае `true`, опция `minRecordsCountForUseAjax` игнорируется.
     *
     * **Пример:**
     *
     * ```php
     * <?= $form->field($modelForm, 'build_id')->select2([
     *     'activeRecordClass' => Build::className(),
     *     'queryCallback' => BuildQuery::select(),
     *     'ajaxConfig' => [
     *         'enabled' => true,
     *         'searchAjaxCallback' => function(ActiveQuery $query, $searchString) {
     *             $query->andWhere(['like', 'build_name', $searchString])
     *                 ->orWhere(['like', 'build_description', $searchString]);
     *         },
     *         'onlyAjax' => true,
     *     ],
     * ]); ?>
     * ```
     */
    public $ajaxConfig;

    /**
     * Инициализация виджета.
     */
    public function init()
    {
        $this->initConfig();
        /** Если ajax запрос, вернуть json с результатами */
        $this->returnAjaxData();

        parent::init();
    }

    /**
     * Выполнение виджета
     */
    public function run()
    {
        $this->options['placeholder'] = ArrayHelper::getValue($this->options, 'placeholder', '');
        $this->pluginOptions['allowClear'] = ArrayHelper::getValue($this->pluginOptions, 'allowClear', true);

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

        if ($this->activeRecordClass) {
            $dataQuery = $this->getDataQuery();
            $resultQueryCount = $dataQuery->count();

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
                    $row[$this->activeRecordAttribute] = $this->filterBinaryToString($row[$this->activeRecordAttribute]);
                    $resultString = $this->filterPrimaryKeysAttributes($row);
                    $this->data[$row[$this->activeRecordAttribute]] = implode(', ', $resultString);
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
                $this->addon['append']['content'] = '<div class="input-group-addon wk-block-select2-choose-from-grid"><a class="btn btn-sm wk-widget-select2-choose-from-grid pmd-ripple-effect pmd-btn-fab" href="' . $url . '"><i class="fa fa-2x fa-ellipsis-h pmd-sm"></i></a></div>' . ArrayHelper::getValue($this->addon, 'append.content', '');
                $this->addon['append']['asButton'] = true;

                /** Проинициализировать выбранное значение из грида */
                $this->selectedAttribute();
            }
        }

        parent::run();
        $this->registerWKAssets();
    }

    protected function selectedAttribute()
    {
        if (Yii::$app->request->get('grid') === $this->options['id'] && Yii::$app->request->get('selected')) {
            $this->options['wk-selected'] = Yii::$app->request->get('selected');

            if ($this->options['wk-ajax']) {
                $dataQuery = $this->getDataQuery();
                $selectedID = $this->filterStringToBinary(Yii::$app->request->get('selected'));

                $resultQuery = $dataQuery->andWhere([$this->activeRecordAttribute => $selectedID])->asArray()->one();
                $resultQuery[$this->activeRecordAttribute] = $this->filterBinaryToString($resultQuery[$this->activeRecordAttribute]);
                $resultString = $this->filterPrimaryKeysAttributes($resultQuery);
                if ($this->multiple) {
                    $this->data[$resultQuery[$this->activeRecordAttribute]] = implode(', ', $resultString);
                } else {
                    $this->initValueText = implode(', ', $resultString);
                }

                if ($this->multiple) {
                    $this->value = $this->value ?: [];
                }

                $this->multiple ? array_push($this->value, $resultQuery[$this->activeRecordAttribute]) : $this->value = $resultQuery[$this->activeRecordAttribute];
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

    protected function registerWKAssets()
    {
        $view = $this->getView();

        Select2Asset::register($view);
        $view->registerJs("$('#{$this->options['id']}').wkselect2();");
        PropellerAsset::setWidget(self::className());
    }

    protected function returnAjaxData()
    {
        if (Yii::$app->request->isAjax && $q = Yii::$app->request->get('q')) {
            Yii::$app->response->clearOutputBuffers();
            $resultReturn = [];
            /** @var ActiveQuery $query */
            $query = call_user_func([$this->activeRecordClass, 'find']);

            call_user_func($this->queryCallback, $query);
            call_user_func($this->ajaxConfig['searchAjaxCallback'], $query, $q);

            $result = $query->asArray()->all();
            /** @var array $row */
            foreach ($result as $row) {
                $row[$this->activeRecordAttribute] = $this->filterBinaryToString($row[$this->activeRecordAttribute]);
                $resultString = $this->filterPrimaryKeysAttributes($row);
                $resultReturn[] = ['id' => $row[$this->activeRecordAttribute], 'text' => implode(', ', $resultString)];
            }

            exit(json_encode(['results' => $resultReturn]));
        }
    }

    protected function filterPrimaryKeysAttributes(array $resultArray)
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = new $this->activeRecordClass;
        $exceptPrimaryKeyFromResult = $this->exceptPrimaryKeyFromResult;

        return array_filter(array_map(function ($attribute, $value) use ($activeRecord, $exceptPrimaryKeyFromResult) {
            if ((!$exceptPrimaryKeyFromResult || $exceptPrimaryKeyFromResult && !$activeRecord->isPrimaryKey([$attribute])) && !in_array($attribute, $this->exceptAttributesFromResult)) {
                return $value;
            }

            return false;
        }, array_keys($resultArray), $resultArray));
    }

    protected function filterBinaryToString($value)
    {
        return BinaryHelper::isBinary($value) ? Uuid::uuid2str($value) : $value;
    }

    protected function filterStringToBinary($value)
    {
        return BinaryHelper::isBinaryValidString($value) ? Uuid::str2uuid($value) : $value;
    }

    protected function initAjaxMultiple(ActiveQuery $dataQuery)
    {
        $resultQuery = $dataQuery->andWhere([$this->activeRecordAttribute => $this->model->{$this->attribute}])->asArray()->all();

        $attribute = $this->activeRecordAttribute;
        $data = [];
        $this->value = [];
        $that = $this;
        $resultQuery = array_map(function ($value) use ($attribute, &$data, $that) {
            $value[$attribute] = $this->filterBinaryToString($value[$attribute]);
            $resultString = $this->filterPrimaryKeysAttributes($value);
            $data[$value[$attribute]] = implode(', ', $resultString);
            array_push($that->value, $value[$attribute]);
            return $value;
        }, $resultQuery);

        $this->model->{$this->attribute} = ArrayHelper::getColumn($resultQuery, $this->activeRecordAttribute);
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
        $resultQuery = $dataQuery->andWhere([$this->activeRecordAttribute => $this->model->{$this->attribute}])->asArray()->one();
        $resultQuery[$this->activeRecordAttribute] = $this->filterBinaryToString($resultQuery[$this->activeRecordAttribute]);
        $resultString = $this->filterPrimaryKeysAttributes($resultQuery);
        $this->initValueText = implode(', ', $resultString);
        $this->value = $resultQuery[$this->activeRecordAttribute];
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
        $this->activeRecordAttribute = $this->activeRecordAttribute ?: $this->attribute;
    }
}