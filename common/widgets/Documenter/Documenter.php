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
use yii\helpers\Url;

class Documenter extends Widget
{
    public $directories = [];

    public function init()
    {
        if (!is_array($this->directories) || empty($this->directories)) {
            throw new InvalidConfigException(Yii::t('wkdocumenter', 'Property Directories required and must be Array of strings of paths'));
        }
        parent::init();
    }

    public function run()
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

        if (Yii::$app->request->isAjax
            && ($tab = Yii::$app->request->get('t', false))
            && ($pill = Yii::$app->request->get('p', false))
        ) {
            Yii::$app->response->clearOutputBuffers();

            /** @var DocumenterViewer[] $viewers */
            foreach ($documents as $directory => $viewers) {
                foreach ($viewers as $key => $document) {
                    $tabHash = 't_' . hash('crc32', $document->getTabName());
                    $pillHash = 'p_' . hash('crc32', $document->getPillName());

                    if ($tab === $tabHash && $pill === $pillHash) {
                        $content = strtr($document->getContent(), ['{absoluteWebRoot}' => Url::base(true)]);
                        $contentConverted = Markdown::convert($content);
                        exit("$contentConverted");
                    }
                }
            }

            exit();
        }

        $documenterContainer = new DocumenterContainer($documents);

        if ($documenterContainer->allowedTabsCount() === 0) {
            echo Html::tag('div', Yii::t('wkdocumenter', 'Documents is missed'), [
                'class' => 'wkdoc-missed',
                'style' => 'min-height:700px;line-height:700px;text-align:center;font-size:80px;font-weight:bold;color:#ccdfe8;',
            ]);
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
        $view->registerJs("$('.pmd-tabs').pmdTab();");
        PropellerAsset::setWidget(self::className());
    }
}