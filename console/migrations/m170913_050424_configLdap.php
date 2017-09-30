<?php

use common\classes\mysql\Migration;

class m170913_050424_configLdap extends Migration
{
    public function up()
    {
        $this->createTable('{{%config_ldap}}', [
            'config_ldap_id' => $this->primaryKey()->unsigned(),
            'config_ldap_host' => $this->string(),
            'config_ldap_port' => $this->integer()->notNull()->unsigned()->defaultValue(389),
            'config_ldap_admin_login' => $this->string()->notNull()->defaultValue(''),
            'config_ldap_admin_password' => $this->blob()->notNull(),
            'config_ldap_active' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->insert('{{%config_ldap}}', [
            'config_ldap_host' => '',
            'config_ldap_admin_password' => '',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%config_ldap}}');
    }
}
