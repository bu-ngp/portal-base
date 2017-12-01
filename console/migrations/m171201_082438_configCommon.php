<?php

use console\classes\mysql\Migration;

/**
 * Class m171201_082438_configCommon
 */
class m171201_082438_configCommon extends Migration
{
    public function up()
    {
        $this->createTable('{{%config_common}}', [
            'config_common_id' => $this->primaryKey()->unsigned(),
            'config_common_portal_mail' => $this->string(),
            'config_common_mail_administrators' => $this->string(),
        ]);

        $this->insert('{{%config_common}}', []);
    }

    public function down()
    {
        $this->dropTable('{{%config_common}}');
    }
}
