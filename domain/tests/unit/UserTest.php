<?php

namespace domain\tests;


use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\repositories\base\PersonRepository;
use domain\repositories\base\ProfileRepository;
use domain\services\base\PersonService;
use domain\services\TransactionManager;
use Yii;
use yii\codeception\DbTestCase;

class UserTest extends DbTestCase
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testCreateEmpty()
    {
        $service = Yii::createObject('domain\services\base\PersonService');
        $userForm = new UserForm();
        $profileForm = new ProfileForm();

        $this->tester->expectException(new \DomainException("Пароль должен содержать не менее 6 символов."), function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });

        $this->tester->seeNumRecords(1, 'person');
    }

    public function testCreateGuardAssignedRoles()
    {
        $service = Yii::createObject('domain\services\base\PersonService');
        $userForm = new UserForm([
            'person_password' => '123456',
        ]);
        $profileForm = new ProfileForm();

        $this->tester->expectException(new \DomainException("Ошибка при распознавании выбранных элементов"), function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });

        $this->tester->seeNumRecords(1, 'person');
    }

    public function testCreateFormsValidate()
    {
        $service = Yii::createObject('domain\services\base\PersonService');
        $userForm = new UserForm([
            'person_password' => '123456',
            'assignRoles' => '[]',
        ]);
        $profileForm = new ProfileForm();

        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });

        $this->assertTrue($userForm->getFirstError('person_fullname') === "Необходимо заполнить «Фамилия Имя Отчество».");
        $this->assertTrue($userForm->getFirstError('person_username') === "Необходимо заполнить «Логин».");
        $this->assertTrue($userForm->getFirstError('person_password') === null);
        $this->assertTrue($userForm->getFirstError('person_password_repeat') === null);

        $this->tester->seeNumRecords(1, 'person');
    }

    public function testPersonFullname()
    {
        $service = Yii::createObject('domain\services\base\PersonService');
        $profileForm = new ProfileForm();

        $userForm = new UserForm([
            'person_fullname' => 'ab',
            'person_username' => 'user1',
            'person_password' => '123456',
            'person_password_repeat' => '123456',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $userForm->validate();
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "Значение «Фамилия Имя Отчество» должно содержать минимум 3 символа.");

        $userForm = new UserForm([
            'person_fullname' => ' abcd',
            'person_username' => 'user1',
            'person_password' => '123456',
            'person_password_repeat' => '123456',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $userForm->validate();
            $service->create($userForm, $profileForm);
        });

        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" должны состоять минимум из двух слов только на кирилице");

        $userForm = new UserForm([
            'person_fullname' => 'Иванов',
            'person_password' => '123456',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" должны состоять минимум из двух слов только на кирилице");

        $userForm = new UserForm([
            'person_fullname' => 'Иванов--Петров Иванович',
            'person_password' => '123456',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" не может содержать два дифиса подряд");

        $userForm = new UserForm([
            'person_fullname' => 'Иванов Петр Иванович',
            'person_password' => '123456',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === null);
        $this->tester->seeNumRecords(1, 'person');
    }

    public function testPersonUsername() {

    }
}