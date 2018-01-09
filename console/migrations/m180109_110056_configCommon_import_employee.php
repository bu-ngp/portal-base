<?php

use yii\db\Migration;

/**
 * Class m180109_110056_configCommon_import_employee
 */
class m180109_110056_configCommon_import_employee extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{%config_common}}', 'config_common_import_employee', $this->boolean());
        $this->update('{{%config_common}}', ['config_common_import_employee' => 0], ['config_common_id' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{%config_common}}', 'config_common_import_employee');
    }
}
