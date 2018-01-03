<?php

namespace domain\tests;


use domain\forms\base\RoleForm;
use domain\forms\base\RoleUpdateForm;
use domain\models\base\AuthItem;
use domain\services\base\RoleService;
use Yii;

class RoleTest extends \Codeception\Test\Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testCreateRole()
    {
        /** @var RoleService $service */
        $service = Yii::createObject('domain\services\base\RoleService');

        $roleForm = new RoleForm();
        $this->tester->expectException(new \DomainException("Ошибка при распознавании выбранных элементов"), function () use ($service, $roleForm) {
            $service->create($roleForm);
        });

        $roleForm = new RoleForm([
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException("Необходимо добавить роли"), function () use ($service, $roleForm) {
            $service->create($roleForm);
        });

        $roleForm = new RoleForm([
            'assignRoles' => '["NotExistRole"]',
        ]);
        $this->tester->expectException(new \DomainException("Необходимо добавить роли"), function () use ($service, $roleForm) {
            $service->create($roleForm);
        });

        $roleForm = new RoleForm([
            'assignRoles' => '["Administrator"]',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $roleForm) {
            $service->create($roleForm);
        });
        $this->tester->assertTrue($roleForm->getFirstError('description') === "Необходимо заполнить «Наименование».");
        $this->tester->assertTrue($roleForm->getFirstError('ldap_group') === null);

        $roleForm = new RoleForm([
            'description' => 'Администратор базовой конфигурации',
            'assignRoles' => '["Administrator"]',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $roleForm) {
            $service->create($roleForm);
        });
        $this->tester->assertTrue($roleForm->getFirstError('description') === "Значение «Администратор базовой конфигурации» для «Наименование» уже занято.");

        $roleForm = new RoleForm([
            'description' => 'Новая роль',
            'ldap_group' => 'ldapGroup',
            'assignRoles' => '["Administrator"]',
        ]);
        $service->create($roleForm);
        $this->assertTrue(preg_match('/^UserRole\d+$/', $this->tester->grabFromDatabase('auth_item', 'name', ['description' => 'Новая роль'])) === 1);
        $this->assertTrue($this->tester->grabFromDatabase('auth_item', 'type', ['description' => 'Новая роль']) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('auth_item', 'view', ['description' => 'Новая роль']) === '0');
        $this->assertTrue($this->tester->grabFromDatabase('auth_item', 'ldap_group', ['description' => 'Новая роль']) === 'ldapGroup');
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('auth_item', 'created_at', ['description' => 'Новая роль'])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('auth_item', 'updated_at', ['description' => 'Новая роль'])) === date('Y-m-d'));

        $this->assertTrue($this->tester->grabFromDatabase('auth_item_child', 'child', ['parent like' => 'UserRole%']) === 'Administrator');
    }

    public function testUpdateRole()
    {
        /** @var RoleService $service */
        $service = Yii::createObject('domain\services\base\RoleService');
        $roleForm = new RoleForm([
            'description' => 'Новая роль',
            'assignRoles' => '["Administrator"]',
        ]);
        $service->create($roleForm);
        $authItem = AuthItem::findOne(['description' => 'Новая роль']);

        $roleUpdateForm = new RoleUpdateForm($authItem, [
            'description' => '',
        ]);

        $this->tester->expectException(new \DomainException(), function () use ($service, $authItem, $roleUpdateForm) {
            $service->update($authItem->primaryKey, $roleUpdateForm);
        });
        $this->tester->assertTrue($roleUpdateForm->getFirstError('description') === "Необходимо заполнить «Наименование».");

        $roleUpdateForm = new RoleUpdateForm($authItem, [
            'description' => 'Переименованная роль',
            'ldap_group' => 'LdapGroup',
        ]);
        $service->update($authItem->primaryKey, $roleUpdateForm);
        $this->assertTrue(preg_match('/^UserRole\d+$/', $this->tester->grabFromDatabase('auth_item', 'name', ['description' => 'Переименованная роль'])) === 1);
        $this->assertTrue($this->tester->grabFromDatabase('auth_item', 'type', ['description' => 'Переименованная роль']) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('auth_item', 'view', ['description' => 'Переименованная роль']) === '0');
        $this->assertTrue($this->tester->grabFromDatabase('auth_item', 'ldap_group', ['description' => 'Переименованная роль']) === 'LdapGroup');
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('auth_item', 'created_at', ['description' => 'Переименованная роль'])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('auth_item', 'updated_at', ['description' => 'Переименованная роль'])) === date('Y-m-d'));

        $this->assertTrue($this->tester->grabFromDatabase('auth_item_child', 'child', ['parent like' => 'UserRole%']) === 'Administrator');
    }

    public function testDeleteAssignedRole()
    {
        /** @var RoleService $service */
        $service = Yii::createObject('domain\services\base\RoleService');
        $roleForm = new RoleForm([
            'description' => 'Новая роль',
            'assignRoles' => '["Administrator"]',
        ]);
        $service->create($roleForm);
        $authItem = AuthItem::findOne(['description' => 'Новая роль']);

        $service->removeRoleForUpdate($authItem->name, "Administrator");
        $this->assertFalse($this->tester->grabFromDatabase('auth_item_child', 'child', ['parent like' => 'UserRole%']) === 'Administrator');
    }

    public function testDeleteRole()
    {
        /** @var RoleService $service */
        $service = Yii::createObject('domain\services\base\RoleService');
        $roleForm = new RoleForm([
            'description' => 'Новая роль',
            'assignRoles' => '["Administrator"]',
        ]);
        $service->create($roleForm);
        $authItem = AuthItem::findOne(['description' => 'Новая роль']);

        $service->removeRole($authItem->name);
        $this->assertFalse(preg_match('/^UserRole\d+$/', $this->tester->grabFromDatabase('auth_item', 'name', ['description' => 'Новая роль'])) === 1);
    }
}