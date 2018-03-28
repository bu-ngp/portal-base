<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 15.05.2017
 * Time: 19:28
 */

namespace common\widgets\CardList;

/**
 * Модуль настроек для виджета [[CardList]].
 *
 * ```php
 *     'modules' => [
 *         ...
 *         'cardlist' => [
 *             'class' => '\common\widgets\CardList\Module',
 *             'cardlistTable' => '{{%cardlist}}',
 *         ],
 *         ...
 *     ],
 * ```
 */
class Module extends \yii\base\Module
{
    /**
     * @var string Имя таблицы в БД, для хранения плиток [[CardList]].
     * Для использования настраиваемых плиток в БД, необходимо применить миграцию.
     * `CardList/migrations/m171031_105453_cardlist.php`
     *
     * Таблица содержит следующие поля:
     *
     * Имя поля таблицы     | Описание
     * -------------------- | ---------------------
     * cardlist_id          | Автоинкремент записей
     * cardlist_page        | Страница, на которой содержится плитка в формате *"Имя приложения\|Controller/action"*, `wkportal-backend|site/index`
     * cardlist_title       | Наименование плитки
     * cardlist_description | Дополнительное описание плитки
     * cardlist_style       | Стиль плитки, значение одной из констант [[CardList]] (*RED_STYLE, BLUE_STYLE, GREEN_STYLE, YELLOW_STYLE, GREY_STYLE*), `wk-blue-style`
     * cardlist_link        | Ссылка в формате *"Имя UrlManager приложения[Controller/action]"*, `FrontendUrlManager[site/index]`
     * cardlist_icon        | Стиль иконки FontAwesome `fa fa-users`
     * cardlist_roles       | Имена разрешений RBAC, для которых видима плитка. Например: `ManagerPermission|UserPermission`, где `|` разделитель при вводе нескольких разрешений.
     *
     */
    public $cardlistTable = '{{%cardlist}}';

    /**
     * Инициализация модуля.
     * ```php
     * $this->id = 'cardlist';
     * ```
     */
    public function init()
    {
        $this->id = 'cardlist';
        parent::init();

    }
}