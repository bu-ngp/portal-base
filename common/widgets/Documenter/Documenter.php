<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.10.2017
 * Time: 13:24
 */

namespace common\widgets\Documenter;

use common\widgets\Documenter\services\DocumenterContainer;
use common\widgets\Documenter\services\DocumenterViewer;
use common\widgets\PropellerAssets\PropellerAsset;
use kartik\markdown\Markdown;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\FileHelper;

/**
 * Виджет для отображения обновлений приложения, или документации.
 *
 * Виджет представляет из себя страницу с контентом документов, с группировкой по плитках (слева) и вкладкам (сверху) для удобной навигации.
 *
 * Имеет следующие возможности:
 * * Группировка документов с помощью плиток и вкладок (На подобии матрицы);
 * * Возможность подменить Url веб директории в контенте документа, для отображения ссылок на картинки;
 * * Упорядочивание вкладок документов;
 * * Именование плиток документов с помощью дат;
 * * Ограничевать видимость документов с помощью разрешений `RBAC`;
 * * Конфигурировать несколько директорий для парсинга документов. Возможность использовать алиасы Yii2 в именах директорий;
 * * Использует Ajax подзагрузку контента при переходе по плиткам документов.
 *
 * **Пример:**
 *
 * ```php
 *      <?= Documenter::widget([
 *          // Набор директорий, из которых будут парситься .md файлы документов.
 *          'directories' => [
 *              '@app/documentation_v1',
 *              '@app/documentation_v2',
 *          ],
 *      ]) ?>
 * ```
 *
 * Правила именования `.md` файлов и директорий при парсинге:
 * * Каждый `.md` документ (плитка документа) должен быть расположен в директории (вкладке документа);
 * * Вкладки можно упорядочить, добавив в начало имени порядковый номер. `1_`, `2_`, `3_` и т.д.;
 * * Каждый `.md` файл в директории это плитка в виджете. Имя файла - это имя плитки;
 * * В имя файла можно вписывать дату в формате `YYYY-MM-DD.md`. При этом дата будет отображаться в имени плитки в формате `DD.MM.YYYY`;
 * * В контенте документа для отображения ссылок на картинки можно использовать маску `{absoluteWebRoot}` для подстановки абсолютного пути Url веб директории приложения;
 * * Для ограничения доступа к `.md` файлам для пользователей, можно вписывать наименования разрешений `RBAC` в имена файлов в формате `Имя плитки[Имя разрешения]`. Имена файлов без имен разрешений доступны всем пользователям.
 *
 * *Пример расположения директорий и файлов документов:*
 *
 * ```
 * update_docs\ // Директория для парсинга
 *      1_Общие\ // Имя вкладки с порядковым номером 1, т.е. вкладка будет отображена первой.
 *          2017-10-25.md // Файл с контентом, где имя файла - имя плитки. Имя содержит дату, которая будет преобразована в имя "25.10.2017"
 *      Дополнительные\ // Имя вкладки
 *          Служебные[UserPermission].md // Файл с контентом, где имя плитки "Служебные", доступно только пользователям с разрешением "UserPermission".
 * ```
 *
 * *Пример содержимого `.md` файла:*
 *
 * ```html
 * // {absoluteWebRoot} заменится на "http://localhost/myapp"
 * <img src="{absoluteWebRoot}/static/pic.png" style="display: block; margin: auto;" width="250">
 * ```
 */
class Documenter extends Widget
{
    /**
     * @var array Массив директорий, в которых хранятся `.md` файлы документов.
     *
     * Для имен путей возможно использование алиасов Yii2.
     */
    public $directories = [];

    /**
     * Инициализация виджета.
     *
     * Проверка на обязательную опцию `directories`.
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!is_array($this->directories) || empty($this->directories)) {
            throw new InvalidConfigException(Yii::t('wkdocumenter', 'Property Directories required and must be Array of strings of paths'));
        }
        $this->registerTranslations();
        parent::init();
    }

    /**
     * Регистрация сообщений i18n
     */
    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wkdocumenter'] = [
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
        $documents = $this->getDocumenterViewers();
        $this->returnContentIfAjax($documents);
        $documenterContainer = new DocumenterContainer($documents);

        if ($this->returnContentIfEmpty($documenterContainer)) {
            return;
        }

        echo Html::tag('div', $this->render('_container', [
            'pillLinks' => $documenterContainer->getPillsContent(),
            'tabs' => $documenterContainer->getTabsLinks(),
            'tabContent' => $documenterContainer->getTabsContent(),
        ]), ['id' => $this->id]);

        $this->registerAssets();
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        DocumenterAsset::register($view);
        PropellerAsset::setWidget(self::className());
        $view->registerJs("$('.wkdoc-tabs-inside>.pmd-tabs').pmdTab();");
    }

    /**
     * @param DocumenterViewer[] $documents
     */
    protected function returnContentIfAjax(array $documents)
    {
        if (Yii::$app->request->isAjax
            && ($tab = Yii::$app->request->get('t', false))
            && ($pill = Yii::$app->request->get('p', false))
        ) {
            Yii::$app->response->clearOutputBuffers();

            /** @var DocumenterViewer[] $viewers */
            foreach ($documents as $directory => $viewers) {
                foreach ($viewers as $key => $document) {
                    if ($tab === $document->getTabHash() && $pill === $document->getPillHash()) {
                        exit(Markdown::convert($document->getContent()));
                    }
                }
            }

            exit();
        }
    }

    protected function getDocumenterViewers()
    {
        $documents = [];
        foreach ($this->directories as $directory) {
            if (!is_dir($dirPath = Yii::getAlias($directory))) {
                throw new InvalidConfigException(Yii::t('wkdocumenter', 'Directory "{dirPath}" not exists', ['dirPath' => $dirPath]));
            }
            $preg = preg_quote($dirPath, '/');

            $documents[$directory] = array_map(function ($filePath) use ($preg) {
                $path = preg_replace("/$preg/", '', $filePath);

                return new DocumenterViewer($path, $filePath);
            }, FileHelper::findFiles($dirPath));
        }

        return $documents;
    }

    protected function returnContentIfEmpty(DocumenterContainer $documenterContainer)
    {
        if ($documenterContainer->allowedTabsCount() === 0) {
            echo Html::tag('div', Yii::t('wkdocumenter', 'Documents is missed'), [
                'class' => 'wkdoc-missed',
                'style' => 'min-height:700px;line-height:700px;text-align:center;font-size:80px;font-weight:bold;color:#ccdfe8;',
            ]);
            return true;
        }

        return false;
    }
}