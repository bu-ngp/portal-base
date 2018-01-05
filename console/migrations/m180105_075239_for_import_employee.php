<?php

use yii\db\Migration;

/**
 * Class m180105_075239_for_import_employee
 */
class m180105_075239_for_import_employee extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{%person}}', 'for_import', $this->boolean());
        $this->addColumn('{{%parttime}}', 'for_import', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{%person}}', 'for_import');
        $this->dropColumn('{{%parttime}}', 'for_import');
    }
}
