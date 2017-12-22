<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.10.2017
 * Time: 13:24
 */

namespace common\widgets\Documenter;


use common\widgets\Documenter\assets\DocumenterAsset;
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
 * Class Documenter
 * @package common\widgets\Documenter
 *
 * update_docs\
 *      1_Общие\
 *          2017-10-25.md
 *              <img src="{absoluteWebRoot}/static/pic.png" style="display: block; margin: auto;" width="250">
 *      Дополнительные\
 *          2017-10-26.md
 */
class Documenter extends Widget
{
    public $directories = [];

    public function init()
    {
        if (!is_array($this->directories) || empty($this->directories)) {
            throw new InvalidConfigException(Yii::t('wkdocumenter', 'Property Directories required and must be Array of strings of paths'));
        }
        $this->registerTranslations();
        parent::init();
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wkdocumenter'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

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