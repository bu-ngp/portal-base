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
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\FileHelper;

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

        $documenterContainer = new DocumenterContainer($documents);

        echo Html::tag('div', $this->render('_container', [
            'pillLinks' => $documenterContainer->getPillsContent(),
            'tabs' => $documenterContainer->getTabsContent(),
            'tabContent' => $documenterContainer->getCurrentDocument(),
        ]), ['id' => $this->id]);

        $this->registerAssets();
    }

    protected function registerAssets()
    {
        $view = $this->getView();
        DocumenterAsset::register($view);
        PropellerAsset::setWidget(self::className());
        $view->registerJs(<<<EOT
            $('.pmd-tabs').pmdTab();
EOT
        );
    }
}