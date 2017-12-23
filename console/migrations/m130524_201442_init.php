<?php

use wartron\yii2uuid\helpers\Uuid;
use yii\base\InvalidConfigException;
use console\classes\mysql\Migration;
use yii\rbac\DbManager;

class m130524_201442_init extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function safeUp()
    {
        $authManager = $this->getAuthManager();

        $this->createTable('{{%profile}}', [
            'profile_id' => $this->baseBinary()->notNull(),
            'profile_inn' => $this->char(12)->unique(),
            'profile_dr' => $this->date(),
            'profile_pol' => $this->boolean(),
            'profile_snils' => $this->char(11)->unique(),
            'profile_address' => $this->string(400),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->addPrimaryKey('profile_id', '{{%profile}}', 'profile_id');

        $this->createTable('{{%person}}', [
            'person_id' => $this->baseBinary()->notNull(),
            'person_fullname' => $this->string()->notNull(),
            'person_username' => $this->string()->notNull()->unique(),
            'person_auth_key' => $this->char(32)->notNull(),
            'person_password_hash' => $this->string()->notNull(),
            'person_email' => $this->string(),
            'person_hired' => $this->date(),
            'person_fired' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->string()->notNull(),
            'updated_by' => $this->string()->notNull(),
        ]);

        $this->addPrimaryKey('person_id', '{{%person}}', 'person_id');
        $this->addForeignKey('person_profile', '{{%profile}}', 'profile_id', '{{%person}}', 'person_id', 'CASCADE', 'CASCADE');

        $this->createOnlyAutoIncrement('person_code', '{{%person}}', 'person_id');

        $this->createTable($authManager->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->blob(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'PRIMARY KEY (name)',
        ]);

        $this->createTable($authManager->itemTable, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'view' => $this->boolean()->notNull()->defaultValue(0),
            'ldap_group' => $this->string(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('name', $authManager->itemTable, 'name');
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');
        $this->addForeignKey('rule_name', $authManager->itemTable, 'rule_name', $authManager->ruleTable, 'name', 'SET NULL', 'CASCADE');

        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ]);

        $this->addPrimaryKey('parent_child', $authManager->itemChildTable, ['parent', 'child']);
        $this->addForeignKey('parent', $authManager->itemChildTable, 'parent', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('child', $authManager->itemChildTable, 'child', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');

        $this->createTable($authManager->assignmentTable, [
            'user_id' => $this->baseBinary()->notNull(),
            'item_name' => $this->string(64)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('item_name_user_id', $authManager->assignmentTable, ['item_name', 'user_id']);
        $this->addForeignKey('item_name', $authManager->assignmentTable, 'item_name', $authManager->itemTable, 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('user_id', $authManager->assignmentTable, 'user_id', '{{%person}}', 'person_id', 'CASCADE');

        $this->insert('{{%person}}', [
            'person_id' => Uuid::uuid(),
            'person_code' => null,
            'person_fullname' => 'Администратор',
            'person_username' => 'admin',
            'person_auth_key' => Yii::$app->security->generateRandomString(),
            'person_password_hash' => Yii::$app->security->generatePasswordHash('administrator'),
            'person_email' => 'admin@mm.ru',
            'person_hired' => null,
            'person_fired' => null,
            'created_at' => time(),
            'updated_at' => time(),
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);
    }

    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);

        $this->dropTable('{{%profile}}');
        $this->dropTable('{{%person}}');
    }
}
