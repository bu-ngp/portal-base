<?php

use yii\db\Migration;

class m170913_050424_configLdap extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%config_ldap}}', [
            'config_ldap_id' => $this->primaryKey()->unsigned(),
            'config_ldap_host' => $this->string(),
            'config_ldap_port' => $this->integer()->notNull()->unsigned()->defaultValue(389),
            'config_ldap_admin_login' => $this->string()->notNull()->defaultValue(''),
            'config_ldap_admin_password' => $this->string()->notNull()->defaultValue(''),
            'config_ldap_active' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->insert('{{%config_ldap}}', [
            'config_ldap_host' => '',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%config_ldap}}');
    }
}
