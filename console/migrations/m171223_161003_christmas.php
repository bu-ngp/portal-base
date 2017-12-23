<?php

use yii\db\Migration;

/**
 * Class m171223_161003_cristmas
 */
class m171223_161003_christmas extends Migration
{
    public function up()
    {
        $this->addColumn('{{%config_common}}', 'config_common_christmas', $this->boolean());
        $this->update('{{%config_common}}', ['config_common_christmas' => 0], ['config_common_id' => 1]);
    }

    public function down()
    {
        $this->dropColumn('{{%config_common}}', 'config_common_christmas');
    }
}
