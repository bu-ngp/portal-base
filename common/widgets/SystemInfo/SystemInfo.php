<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 10.01.2018
 * Time: 15:50
 */

namespace common\widgets\SystemInfo;

use Yii;
use yii\bootstrap\Modal;
use yii\bootstrap\Widget;

/**
 * Виджет с кнопкой-версией приложения, открывающей модальное окно с системной информацией.
 *
 * Виджет выводит следующую информцию:
 * * Имя текущего пользователя. Если гость, то выводится `Гость`;
 * * Тип авторизации текущего пользователя. (`Гость`, `Доменная`, `Локальная`);
 * * Последняя дата обновления пакетов проекта;
 * * Версия PHP;
 * * Версия Yii;
 * * Дисковое пространство на сервере в формате `Всего 20Гб, Free 15Гб (75%)`;
 * * Активация LDAP авторизации;
 * * Включение только LDAP авторизации;
 * * Активация Автоматического импорт сотрудников.
 *
 * ```php
 * <?= SystemInfo::widget() ?>
 * ```
 */
class SystemInfo extends Widget
{
    /**
     * @var string Таблица БД с данными о версии приложения.
     */
    public $tableVersion = '{{%version}}';
    /**
     * @var string Таблица БД с данными конфигурации приложения.
     */
    public $tableConfigCommon = '{{%config_common}}';
    /**
     * @var string Таблица БД с данными конфигурации `LDAP` приложения.
     */
    public $tableConfigLdap = '{{%config_ldap}}';

    /**
     * Регистрация сообщений i18n
     */
    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['wk-system-info'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    /**
     * Инициализация виджета.
     */
    public function init()
    {
        $this->registerTranslations();
        parent::init();
    }

    /**
     * Выполнение виджета
     */
    public function run()
    {
        $this->registerAssets();

        $version = Yii::$app->db->createCommand("select version from {$this->tableVersion}")->queryScalar();

        Modal::begin([
            'options' => [
                'class' => 'wk-system-info-modal',
            ],
            'toggleButton' => ['label' => $version, 'class' => 'wk-system-info-button'],
        ]);

        echo <<<EOT
            <table class="table table-bordered wk-system-info-table">
                <tbody>
                {$this->currentUser()}
                {$this->authentication()}
                {$this->composerDateUpdate()}
                {$this->phpVersion()}
                {$this->yiiVersion()}                
                {$this->diskSpace()}
                {$this->isLdapActive()}
                {$this->onlyLdapUse()}
                {$this->onlyImportEmployeeActive()}
                </tbody>
            </table>
EOT;

        Modal::end();
    }

    protected function registerAssets()
    {
        SystemInfoAsset::register($this->getView());
    }

    protected function currentUser()
    {
        $user = Yii::$app->user->isGuest ? Yii::t('wk-system-info', 'Guest') : Yii::$app->user->identity->person_fullname;
        return "<tr><td>" . Yii::t('wk-system-info', 'Current User') . "</td><td>$user</td></tr>";
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

    protected function yiiVersion()
    {
        return "<tr><td>" . Yii::t('wk-system-info', 'Yii Version') . "</td><td>" . Yii::getVersion() . "</td></tr>";
    }

    protected function diskSpace()
    {
        $totalSpace = DIRECTORY_SEPARATOR === '/' ? disk_total_space("/") : disk_total_space("C:");
        $freeSpace = DIRECTORY_SEPARATOR === '/' ? disk_free_space("/") : disk_free_space("C:");
        $freeSpacePercent = round($freeSpace / $totalSpace * 100);

        $data = Yii::t('wk-system-info', 'Total {total}, Free {free} ({precent}%)', [
            'total' => Yii::$app->formatter->asShortSize($totalSpace, 2),
            'free' => Yii::$app->formatter->asShortSize($freeSpace, 2),
            'precent' => $freeSpacePercent,
        ]);

        return "<tr><td>" . Yii::t('wk-system-info', 'Disk Space') . "</td><td>" . $data . "</td></tr>";
    }

    protected function isLdapActive()
    {
        $ldapActive = Yii::$app->db->createCommand("select IF(config_ldap_active = 1, 'Активно', 'Неактивно') from {$this->tableConfigLdap}")->queryScalar();
        return "<tr><td>" . Yii::t('wk-system-info', 'Ldap Authentication is Active') . "</td><td>" . $ldapActive . "</td></tr>";
    }

    protected function onlyLdapUse()
    {
        $ldapUse = Yii::$app->db->createCommand("select IF(config_ldap_only_ldap_use = 1, 'Активно', 'Неактивно') from {$this->tableConfigLdap}")->queryScalar();
        return "<tr><td>" . Yii::t('wk-system-info', 'Use Only Ldap Authentication') . "</td><td>" . $ldapUse . "</td></tr>";
    }

    protected function onlyImportEmployeeActive()
    {
        $importEmployeeActive = Yii::$app->db->createCommand("select IF(config_common_import_employee = 1, 'Активно', 'Неактивно') from {$this->tableConfigCommon}")->queryScalar();
        return "<tr><td>" . Yii::t('wk-system-info', 'Import Employee is Active') . "</td><td>" . $importEmployeeActive . "</td></tr>";
    }

    protected function phpVersion()
    {
        return "<tr><td>" . Yii::t('wk-system-info', 'PHP Version') . "</td><td>" . phpversion() . "</td></tr>";
    }
}