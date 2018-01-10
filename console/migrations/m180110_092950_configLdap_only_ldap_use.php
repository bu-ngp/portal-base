<?php

use yii\db\Migration;

/**
 * Class m180110_092950_configLdap_only_ldap_use
 */
class m180110_092950_configLdap_only_ldap_use extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{%config_ldap}}', 'config_ldap_only_ldap_use', $this->boolean());
        $this->update('{{%config_ldap}}', ['config_ldap_only_ldap_use' => 0], ['config_ldap_id' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{%config_ldap}}', 'config_ldap_only_ldap_use');
    }
}
