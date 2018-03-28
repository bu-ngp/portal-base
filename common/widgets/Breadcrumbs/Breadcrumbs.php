<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 30.06.2017
 * Time: 12:44
 */

namespace common\widgets\Breadcrumbs;

use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\Url;
use yii\web\View;

/**
 * Виджет хлебных крошек.
 *
 * Используется в предсталении layout.
 *
 * Содержит следующие возможности:
 * * Автоматически выводит историю переходов на страницы. Нет необходимости добавлять виджет в каждое представление, достаточно добавить в главное представление `layout`;
 * * Сохраняет данные элементов форм, у которых имеется атрибут `wkkeep`. Данные не исчезнут после обновления страницы;
 * * Гибкое управление виджетом с помощью методов `show()`, `hide()`, `root()`, `removeLastCrumb()`;
 * * Возможность вывести предыдущий Url, на котором побывал пользователь с помощью метода `previousUrl()`, относительно открытой вкладки браузера. `Url::previous()` в данном случае не подходит.
 *
 * ```php
 * <?= Breadcrumbs::widget([
 *      'urlManagerName' => Yii::$app->getUser()->isGuest ? 'FrontendUrlManager' : 'urlManagerAdmin',
 * ]) ?>
 * ```
 */
class Breadcrumbs extends Widget
{
    /** @var bool Отобразить виджет Breadcrumbs, по умолчанию `true` */
    public static $show;
    /** @var bool Поведение отображения виджета Breadcrumbs по умолчанию, по умолчанию `true` */
    public $defaultShow = true;
    /** @var string Идентификатор Cookie, по умолчанию `wk_breadcrumb` */
    public static $cookieId = 'wk_breadcrumb';
    /** @var bool Устанавливает хлебную крошку в начало пути после "Главной", по умолчанию `false` */
    public static $root = false;
    /** @var string ИД виджета */
    public $id;
    /** @var string Имя компонента `UrlManager`, для определения ссылки на главную страницу, по умолчанию `Yii::$app->getHomeUrl()` */
    public $urlManagerName;

    /**
     * Инициализация виджета.
     */
    public function init()
    {
        $this->id = $this->id ?: 'wkbc_' . Yii::$app->id;
        $this->registerTranslations();
        static::$show = static::$show !== null ? static::$show : $this->defaultShow;
        parent::init();
    }

    /**
     * Выполнение виджета
     */
    public function run()
    {
        echo Html::tag('div', '', [
            'id' => $this->id,
            'home-crumb-url' => $this->urlManagerName ? Yii::$app->get($this->urlManagerName)->createUrl(['/']) : Yii::$app->getHomeUrl(),
            'current-crumb-id' => $this->getCurrentCrumbId(),
            'current-crumb-title' => $this->getView()->title,
            'remove-last-crumb' => $this->getRemoveLastCrumb() ? '1' : '0',
            'root' => self::$root ? '1' : '0',
            'cookie-id' => self::$cookieId,
            'class' => 'wkbc-breadcrumb',
        ]);
        $this->registerAssets();
    }

    /**
     * Регистрация сообщений i18n
     */
    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-widget-breadcrumbs'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        BreadcrumbsAsset::register($view);

        $options = [
            'homeCrumbMessage' => Yii::t('wk-widget-breadcrumbs', 'Home'),
            'CurrentPageMessage' => Yii::t('wk-widget-breadcrumbs', 'Current Page'),
        ];

        $options = json_encode(array_filter($options), JSON_UNESCAPED_UNICODE);

        $view->registerJs("$('#{$this->id}').wkbreadcrumbs($options);");
        array_unshift($view->js[View::POS_READY], array_pop($view->js[View::POS_READY]));
    }

    /**
     * Удалить последнюю хлебную крошку.
     *
     * **Пример:**
     *
     * ```php
     *    // Controller
     *    public function actionCreate()
     *    {
     *        $userForm = new UserForm();
     *        if ($userForm->load(Yii::$app->request->post())
     *            && $userForm->validate()
     *            && $newPersonId = $this->service->create($userForm, $profileForm)
     *        ) {
     *            Yii::$app->session->setFlash('Пользователь сохранен. Добавьте специальность.');
     *            // При создании пользователя переходим на страницу его профиля
     *            // Удаляем последнюю хлебную крошку "Создать пользователя". Она заменится на "Обновить пользователя"
     *            Breadcrumbs::removeLastCrumb();
     *            return $this->redirect(['update', 'id' => $newPersonId]);
     *        }
     *        return $this->render('create', [
     *            'modelUserForm' => $userForm,
     *        ]);
     *    }
     * ```
     */
    public static function removeLastCrumb()
    {
        Yii::$app->session->set('_wkbc_remove_last_crumb', true);
    }

    /**
     * Вернуть ссылку на предыдущую страницу из Cookie виджета.
     *
     * **Пример:**
     *
     * ```php
     *     // Controller
     *     public function actionCreate()
     *     {
     *         $form = new BuildForm();
     *
     *         if ($form->load(Yii::$app->request->post())
     *             && $form->validate()
     *             && $this->service->create($form)
     *         ) {
     *             Yii::$app->session->setFlash('Запись сохранена.');
     *             // В случае, если запись сохранилась, отправиться на предыдущую страницу.
     *             return $this->redirect(Breadcrumbs::previousUrl());
     *         }
     *
     *         return $this->render('create', [
     *             'modelForm' => $form,
     *         ]);
     *     }
     * ```
     *
     * @return string
     */
    public static function previousUrl()
    {
        if ($wkbcObject = json_decode($_COOKIE[self::$cookieId])) {
            return $wkbcObject->previousUrl;
        }

        return Url::previous();
    }

    /**
     * Устанавливает хлебную крошку в начало пути после "Главной", например `Главная / Настройки`.
     *
     * Используется в представлениях.
     */
    public static function root()
    {
        self::$root = true;
    }

    /**
     * Отображает виджет.
     *
     * Используется в представлениях.
     */
    public static function show()
    {
        self::$show = true;
    }

    /**
     * Скрывает виджет
     *
     * Используется в представлениях.
     *
     * ```php
     *    // Если открыта страница авторизации, скрыть виджет хлебных крошек.
     *    <?php $this->context->id === "site" && $this->context->action->id === "login" ? Breadcrumbs::hide() : null ?>
     * ```
     */
    public static function hide()
    {
        self::$show = false;
    }

    protected function getCurrentCrumbId()
    {
        $homeId = Yii::$app->defaultRoute . '/' . Yii::$app->controller->defaultAction . '/';
        $currentId = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id . '/';

        return $homeId === $currentId || !static::$show ?: $currentId;
    }

    protected function getRemoveLastCrumb()
    {
        if (Yii::$app->session->get('_wkbc_remove_last_crumb') === true) {
            Yii::$app->session->remove('_wkbc_remove_last_crumb');

            return true;
        }

        return false;
    }
}