<?php

use console\classes\mysql\Migration;

class m171102_111209_ofoms extends Migration
{
    public function up()
    {
        $this->createTable('{{%config_ofoms}}', [
            'config_ofoms_id' => $this->primaryKey()->unsigned(),
            'config_ofoms_url' => $this->string(),
            'config_ofoms_url_prik' => $this->string(),
            'config_ofoms_login' => $this->string()->notNull()->defaultValue(''),
            'config_ofoms_password' => $this->blob()->notNull(),
            'config_ofoms_remote_host_name' => $this->string(),
            'config_ofoms_active' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->insert('{{%config_ofoms}}', [
            'config_ofoms_url' => '',
            'config_ofoms_password' => '',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%config_ofoms}}');
    }
}