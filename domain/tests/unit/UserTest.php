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


    public function testValidateUserform()
    {
        $userForm = new UserForm();
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_fullname') === "Необходимо заполнить «Фамилия Имя Отчество».");
        $this->assertTrue($userForm->getFirstError('person_username') === "Необходимо заполнить «Логин».");
        $this->assertTrue($userForm->getFirstError('person_password') === "Необходимо заполнить «Пароль».");
        $this->assertTrue($userForm->getFirstError('person_password_repeat') === "Необходимо заполнить «Повторите ввод пароля».");

        $userForm = new UserForm([
            'person_fullname' => 'ab',
            'person_username' => 'ab',
            'person_password' => '1',
            'person_password_repeat' => '2',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_fullname') === "Значение «Фамилия Имя Отчество» должно содержать минимум 3 символа.");
        $this->assertTrue($userForm->getFirstError('person_username') === "Значение «Логин» должно содержать минимум 3 символа.");
        $this->assertTrue($userForm->getFirstError('person_password') === "Значение «Пароль» должно содержать минимум 6 символов.");
        $this->assertTrue($userForm->getFirstError('person_password_repeat') === "Значение «Повторите ввод пароля» должно быть равно «Пароль».");

        $userForm = new UserForm([
            'person_fullname' => 'abcd',
            'person_username' => 'логин',
            'person_password' => '111111',
            'person_password_repeat' => '111111',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" должны состоять минимум из двух слов только на кирилице");
        $this->assertTrue($userForm->getFirstError('person_username') === "\"Логин\" может содержать только буквы на латинице, '-' и '_' и цифры. Первый символ должен быть буквой.");
        $this->assertTrue($userForm->getFirstError('person_password') === null);
        $this->assertTrue($userForm->getFirstError('person_password_repeat') === null);

        $userForm = new UserForm([
            'person_fullname' => 'Иванов',
            'person_username' => 'UserName-_',
            'person_password' => '111111',
            'person_password_repeat' => '111111',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" должны состоять минимум из двух слов только на кирилице");
        $this->assertTrue($userForm->getFirstError('person_username') === null);

        $userForm = new UserForm([
            'person_fullname' => 'Иванов--ван Иван',
            'person_username' => 'UserName-_',
            'person_password' => '111111',
            'person_password_repeat' => '111111',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" не может содержать два дифиса подряд");

        $userForm = new UserForm([
            'person_fullname' => ' Иванов Иван',
            'person_username' => 'UserName-_',
            'person_password' => '111111',
            'person_password_repeat' => '111111',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_fullname') === null);
    }

    public function testValidateProfileform()
    {
        $profileForm = new ProfileForm();
        $profileForm->validate();
        $this->assertTrue($profileForm->getFirstError('profile_inn') === null);
        $this->assertTrue($profileForm->getFirstError('profile_dr') === null);
        $this->assertTrue($profileForm->getFirstError('profile_pol') === null);
        $this->assertTrue($profileForm->getFirstError('profile_snils') === null);
        $this->assertTrue($profileForm->getFirstError('profile_address') === null);
        $this->assertTrue($profileForm->getFirstError('profile_phone') === null);
        $this->assertTrue($profileForm->getFirstError('profile_internal_phone') === null);

        $profileForm = new ProfileForm(null, [
            'profile_inn' => '123456',
            'profile_dr' => '15611561',
            'profile_pol' => '3',
            'profile_snils' => '123',
            'profile_address' => 'Address',
            'profile_phone' => '132467494654132',
            'profile_internal_phone' => '132467494654132',
        ]);
        $profileForm->validate();
        $this->assertTrue($profileForm->getFirstError('profile_inn') === "ИНН должен содержать 12 знаков");
        $this->assertTrue($profileForm->getFirstError('profile_dr') === "Неверный формат значения «Дата рождения».");
        $this->assertTrue($profileForm->getFirstError('profile_pol') === "Значение «Пол» неверно.");
        $this->assertTrue($profileForm->getFirstError('profile_snils') === "СНИЛС должен быть больше 1001998");
        $this->assertTrue($profileForm->getFirstError('profile_address') === null);
        $this->assertTrue($profileForm->getFirstError('profile_phone') === "Значение «Телефон» должно содержать максимум 11 символов.");
        $this->assertTrue($profileForm->getFirstError('profile_internal_phone') === "Значение «Внутренний телефон» должно содержать максимум 10 символов.");

        $profileForm = new ProfileForm(null, [
            'profile_inn' => 'abcdabcdabcdabcd',
            'profile_dr' => '1980-12-01',
            'profile_pol' => 1,
            'profile_snils' => '123123123845',
            'profile_phone' => '1324',
            'profile_internal_phone' => 'a12',
        ]);
        $profileForm->validate();
        $this->assertTrue($profileForm->getFirstError('profile_inn') === "ИНН должен содержать 12 знаков");
        $this->assertTrue($profileForm->getFirstError('profile_dr') === null);
        $this->assertTrue($profileForm->getFirstError('profile_pol') === null);
        $this->assertTrue($profileForm->getFirstError('profile_snils') === "СНИЛС должен быть длинной 11 символов");
        $this->assertTrue($profileForm->getFirstError('profile_phone') === "Значение «Телефон» должно содержать минимум 11 символов.");
        $this->assertTrue($profileForm->getFirstError('profile_internal_phone') === "Значение «Внутренний телефон» должно быть целым числом.");

        $profileForm = new ProfileForm(null, [
            'profile_inn' => '123456789102',
            'profile_dr' => '01.12.1980',
            'profile_pol' => 2,
            'profile_snils' => '12333312384',
            'profile_phone' => 'abcdeabcdea',
            'profile_internal_phone' => '128',
        ]);
        $profileForm->validate();
        $this->assertTrue($profileForm->getFirstError('profile_inn') === null);
        $this->assertTrue($profileForm->getFirstError('profile_dr') === null);
        $this->assertTrue($profileForm->getFirstError('profile_pol') === null);
        $this->assertTrue($profileForm->getFirstError('profile_snils') === "Цифры в СНИЛС повторяются три и более раз");
        $this->assertTrue($profileForm->getFirstError('profile_phone') === "Значение «Телефон» неверно.");
        $this->assertTrue($profileForm->getFirstError('profile_internal_phone') === null);

        $profileForm = new ProfileForm(null, [
            'profile_snils' => '12312312385',
            'profile_phone' => '89224444444',
        ]);
        $profileForm->validate();
        $this->assertTrue($profileForm->getFirstError('profile_snils') === "Неверная контрольная сумма СНИЛС");
        $this->assertTrue($profileForm->getFirstError('profile_phone') === null);

        $profileForm = new ProfileForm(null, [
            'profile_snils' => '12312312384',
        ]);
        $profileForm->validate();
        $this->assertTrue($profileForm->getFirstError('profile_snils') === null);
    }

    public function testPersonServiceByPerson()
    {
        $service = Yii::createObject('domain\services\base\PersonService');
        $userForm = new UserForm();
        $profileForm = new ProfileForm();

        $this->tester->expectException(new \DomainException("Пароль должен содержать не менее 6 символов."), function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });

        $userForm = new UserForm([
            'person_password' => '123456',
        ]);

        $this->tester->expectException(new \DomainException("Ошибка при распознавании выбранных элементов"), function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });

        /* Person */

        $userForm = new UserForm([
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "Необходимо заполнить «Фамилия Имя Отчество».");
        $this->assertTrue($userForm->getFirstError('person_username') === "Необходимо заполнить «Логин».");
        $this->assertTrue($userForm->getFirstError('person_password') === null);

        $userForm = new UserForm([
            'person_fullname' => 'ab',
            'person_username' => 'ab',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "Значение «Фамилия Имя Отчество» должно содержать минимум 3 символа.");
        $this->assertTrue($userForm->getFirstError('person_username') === "Значение «Логин» должно содержать минимум 3 символа.");

        $userForm = new UserForm([
            'person_fullname' => 'abcd',
            'person_username' => 'логин',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" должны состоять минимум из двух слов только на кирилице");
        $this->assertTrue($userForm->getFirstError('person_username') === "\"Логин\" может содержать только буквы на латинице, '-' и '_' и цифры. Первый символ должен быть буквой.");

        $userForm = new UserForm([
            'person_fullname' => 'Иванов',
            'person_username' => 'UserName-_',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" должны состоять минимум из двух слов только на кирилице");
        $this->assertTrue($userForm->getFirstError('person_username') === null);

        $userForm = new UserForm([
            'person_fullname' => 'Иванов--ван Иван',
            'person_username' => 'UserName-_',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_fullname') === "\"Фамилия Имя Отчество\" не может содержать два дифиса подряд");

        $userForm = new UserForm([
            'person_fullname' => ' Иванов Иван',
            'person_username' => 'UserName-_',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $this->tester->seeNumRecords(1, 'person');
        $this->assertTrue(strlen($service->create($userForm, $profileForm)) === 32);
        $this->assertTrue($userForm->getFirstError('person_fullname') === null);

        $this->tester->seeNumRecords(2, 'person');
    }

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
}