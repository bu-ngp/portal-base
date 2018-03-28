<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 15:12
 */

namespace common\widgets\CardList;


use common\widgets\PropellerAssets\PropellerAsset;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\web\UrlManager;

/**
 * Виджет набора плиток кнопок с возможностью сортировки по популярности, поиска.
 * Один из параметров (`url` или `items`) для виджета обязателен.
 *
 * **Пример 1** (*С использованием Ajax загрузки плиток с возможностью поиска и сортировкой по популярности*)
 *
 * ```php
 * // views/site/index.php
 * // @var $modelSearch TilesSearch
 * echo CardList::widget([
 *     'url' => Url::to(['site/tiles']), // Url для получения json с конфигурацией плиток
 *     'search' => [ // Блок конфигурации поиска плиток. Добавит сверху панель для ввода строки поиска.
 *         'modelSearch' => $modelSearch, // Модель по которой ищем.
 *         'searchAttributeName' => 'search_string', // аттрибут name HTML INPUT элемента для ввода строки поиска.
 *     ],
 *     'popularity' => true, // Активировать сортировку по популярности.
 * ])
 * ```
 *
 * **Пример 2** (*С конфигурацией плиток при помощи массива свойства `items` с возможностью видимости плиток в зависимости от разрешений*)
 *
 * ```php
 * // views/site/index.php
 * echo CardList::widget([
 *     'items' => [
 *         [
 *             'icon' => 'fa fa-unlock-alt',
 *             'title' => 'Посты',
 *             'description' => 'Добавление/Редактирование/Удаление постов',
 *             'link' => Yii::$app->urlManager->createUrl(['site/posts']),
 *             'roles' => ['adminPermission'],
 *         ],
 *         [
 *             'icon' => 'fa fa-sitemap',
 *             'title' => 'Карта сайта',
 *             'link' => Yii::$app->urlManager->createUrl(['site/site-map']),
 *         ],
 *     ],
 * ])
 * ```
 */
class CardList extends Widget
{
    /** Красный стиль плитки*/
    const RED_STYLE    = 'wk-red-style';
    /** Синий стиль плитки */
    const BLUE_STYLE   = 'wk-blue-style';
    /** Зеленый стиль плитки */
    const GREEN_STYLE  = 'wk-green-style';
    /** Желтый стиль плитки */
    const YELLOW_STYLE = 'wk-yellow-style';
    /** Серый стиль плитки */
    const GREY_STYLE   = 'wk-grey-style';

    /**
     * @var string Url ajax запроса с json выводом набора плиток.
     *
     * Для формирования конфигурации плиток можно использовать метод хелпера [[CardListHelper::createAjaxCards()]]
     * Который вернет массив конфигурации по `yii\data\ActiveDataProvider`.
     *
     * Пример вывода по запрошенному url:
     *
     * ```json
     *      {
     *          "preview":"/thumbs/1513867729-363x209.jpg",
     *          "icon":"fa fa-picture",
     *          "title":"yandex",
     *          "description":"Поисковая система",
     *          "styleClass":"wk-yellow-style",
     *          "link":"http://yandex.ru/",
     *          "popularityID":"1",
     *          "linkNewWindow":true
     *      },
     *      {
     *          "preview":"/thumbs/1513867905-363x209.jpg",
     *          "icon":"fa fa-picture",
     *          "title":"Google",
     *          "description":"Поисковая система",
     *          "styleClass":"wk-blue-style",
     *          "link":"http://google.ru",
     *          "popularityID":"2",
     *          "linkNewWindow":true
     *      },
     *
     * ```
     */
    public $url;
    /**
     * @var array Массив конфигурации плиток.
     *
     * Каждая плитка конфигурируется массивом со следующими ключами свойствами:
     *
     * Имя ключа массива | Значение по умолчанию | Описание
     * ----------------- | --------------------- | ---------------------
     * `preview`         |                       | Ссылка на картинку превью
     * `icon`            |                       | Класс иконки FontAwesome, `fa fa-picture`
     * `title`           |                       | Заголовок плитки
     * `description`     |                       | Описание плитки
     * `styleClass`      | `wk-blue-style`       | Класс стиля плитки, возможные значения (`wk-blue-style`,`wk-red-style`,`wk-green-style`,`wk-yellow-style`,`wk-grey-style`)
     * `link`            |                       | Ссылка плитки
     * `linkNewWindow`   | true                  | Открывать ссылку в новом окне
     * `roles`           |                       | Массив разрешений или строка с именем разрешения, при которых видна плитка. Если пусто, то видна всем.
     *
     * **Пример:**
     *
     * ```php
     * echo CardList::widget([
     *     'items' => [
     *         [
     *             'icon' => 'fa fa-unlock-alt',
     *             'title' => 'Посты',
     *             'description' => 'Добавление/Редактирование/Удаление постов',
     *             'link' => Yii::$app->urlManager->createUrl(['site/posts']),
     *             'roles' => ['adminPermission'],
     *         ],
     *         [
     *             'icon' => 'fa fa-sitemap',
     *             'title' => 'Карта сайта',
     *             'link' => Yii::$app->urlManager->createUrl(['site/site-map']),
     *         ],
     *     ],
     * ])
     * ```
     */
    public $items;
    /**
     * @var int Количество плиток, отображаемых на странице, при использовании ajax загрузки плиток.
     */
    public $cardsPerPage;
    /**
     * @var bool Активировать панель поиска по плиткам. Добавит поисковую панель сверху, относительно плиток.
     */
    public $search     = false;
    /**
     * @var bool Активировать сортировку по популярности
     */
    public $popularity = false;

    /**
     * Инициализация виджета.
     * @throws \Exception
     */
    public function init()
    {
        $this->registerTranslations();
        if (isset($this->items) && !is_array($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'items must be Array'));
        }

        if (empty($this->url) && empty($this->items)) {
            throw new \Exception(Yii::t('wk-widget', 'url or items must be passed'));
        }

        parent::init();
    }

    /**
     * Регистрация сообщений i18n
     */
    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget'] = [
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
        $this->registerAssets();
        echo $this->initLayout();
        $view = $this->getView();
        $this->getItemsFromDB();
        $this->filterByRoles();
        $this->items = $this->items ? array_values($this->items) : $this->items;

        $options = [
            'url' => $this->url,
            'items' => $this->items,
            'popularity' => $this->popularity,
            'cardsPerPage' => 6,
            'messages' => [
                'followLinkMessage' => Yii::t('wk-widget', 'Follow the link'),
                'searchMessage' => Yii::t('wk-widget', 'Search'),
            ],
        ];

        $this->searchConfig($options);

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);
        $view->registerJs("$('#{$this->id}').wkcardlist($options);");
        PropellerAsset::setWidget(self::className());
    }

    protected function searchConfig(&$options)
    {
        if ($this->search === true) {
            $options = array_replace_recursive($options, [
                'search' => true,
            ]);
        } elseif (isset($this->search['modelSearch'])
                  && $this->search['modelSearch'] instanceof Model
                  && isset($this->search['searchAttributeName'])
        ) {
            $options = array_replace_recursive($options, [
                'search' => true,
                'ajaxSearchName' => $this->search['modelSearch']->formName() . '[' . $this->search['searchAttributeName'] . ']',
            ]);
        }
    }

    protected function registerAssets()
    {
        CardListAsset::register(self::getView());
    }

    protected function initLayout()
    {
        return Html::tag('div', '', ['id' => $this->id]);
    }

    protected function filterByRoles()
    {
        if ($this->items) {
            $this->items = array_filter($this->items, function ($item) {
                if (isset($item['roles'])) {
                    $item['roles'] = is_array($item['roles']) ? $item['roles'] : [$item['roles']];

                    foreach ($item['roles'] as $role) {
                        if (Yii::$app->user->can($role)) {
                            return true;
                        }
                    }

                    return false;
                }

                return true;
            });
        }
    }

    protected function getItemsFromDB()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('cardlist');

        if ($module) {
            $table = $module->cardlistTable;
            if (Yii::$app->db->schema->getTableSchema($table)) {
                $tiles = Yii::$app->db->createCommand('SELECT * FROM ' . $table)->queryAll();

                /**
                 * $tile
                 *      cardlist_id
                 *      cardlist_page
                 *      cardlist_title
                 *      cardlist_description
                 *      cardlist_style
                 *      cardlist_link
                 *      cardlist_icon
                 *      cardlist_roles
                 */
                foreach ($tiles as $tile) {
                    if ($this->allowTileForPage($tile)) {
                        $this->items[] = [
                            'styleClass' => $tile['cardlist_style'],
                            'icon' => $tile['cardlist_icon'],
                            'title' => $tile['cardlist_title'],
                            'description' => $tile['cardlist_description'],
                            'link' => $this->getLinkFromDBField($tile),
                            'roles' => $this->getRolesFromDBField($tile),
                        ];
                    }
                }
            }
        }
    }

    protected function allowTileForPage(array $tile)
    {
        $value = explode('|', $tile['cardlist_page']);
        if ($value[0] === Yii::$app->id && $value[1] === Yii::$app->controller->id . '/' . Yii::$app->controller->action->id) {
            return true;
        }

        return false;
    }

    protected function getLinkFromDBField(array $tile)
    {
        preg_match('/(\w+)?\[([\w\/-]+)\]/', $tile['cardlist_link'], $matches);

        $urlManagerName = $matches[1] ?: 'urlManager';
        /** @var UrlManager $urlManager */
        $urlManager = Yii::$app->get($urlManagerName);
        if ($matches[2]) {
            return $urlManager->createAbsoluteUrl([$matches[2]]);
        }

        return '#';
    }

    protected function getRolesFromDBField(array $tile)
    {
        return $tile['cardlist_roles'] ? explode('|', $tile['cardlist_roles']) : [];
    }
}