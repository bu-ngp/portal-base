<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.01.2018
 * Time: 15:50
 */

namespace common\widgets\SystemInfo;


use common\widgets\SystemInfo\assets\SystemInfoAsset;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;

class SystemInfo extends Widget
{
    public $tableVersion = '{{%version}}';

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-system-info'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    public function init()
    {
        $this->registerTranslations();
        parent::init();
    }

    public function run()
    {
        $this->registerAssets();

        $version = Yii::$app->db->createCommand("select version from {$this->tableVersion}")->queryScalar();

        Modal::begin([
            'toggleButton' => ['label' => $version, 'class' => 'wk-system-info-button'],
        ]);

        echo <<<EOT
            <table class="table table-bordered wk-system-info-table">
                <tbody>
                {$this->authentication()}
                {$this->composerDateUpdate()}
                </tbody>
            </table>
EOT;

        Modal::end();
    }

    protected function registerAssets()
    {
        SystemInfoAsset::register($this->getView());
    }

    protected function authentication()
    {
        $type = Yii::t('wk-system-info', 'Unknown');

        if (Yii::$app->user->isGuest) {
            $type = Yii::t('wk-system-info', 'Guest');
        } elseif (Yii::$app->user->identity->isLocal()) {
            $type = Yii::t('wk-system-info', 'Local');
        } elseif (Yii::$app->user->identity->isLdap()) {
            $type = Yii::t('wk-system-info', 'LDAP');
        }

        return "<tr><td>" . Yii::t('wk-system-info', 'Authentication Type') . "</td><td>$type</td></tr>";
    }

    protected function composerDateUpdate()
    {
        $date = file_exists(Yii::getAlias('@common/../composer.lock')) ? date('d.m.Y', filemtime((Yii::getAlias('@common/../composer.lock')))) : Yii::t('wk-system-info', 'Unknown');
        return "<tr><td>" . Yii::t('wk-system-info', 'Composer Update Date') . "</td><td>$date</td></tr>";
    }
}