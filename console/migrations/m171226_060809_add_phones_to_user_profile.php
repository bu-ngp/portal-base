<?php

use yii\db\Migration;

/**
 * Class m171226_060809_add_phones_to_user_profile
 */
class m171226_060809_add_phones_to_user_profile extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%profile}}', 'profile_phone', $this->string());
        $this->addColumn('{{%profile}}', 'profile_internal_phone', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profile}}', 'profile_phone');
        $this->dropColumn('{{%profile}}', 'profile_internal_phone');
    }
}
