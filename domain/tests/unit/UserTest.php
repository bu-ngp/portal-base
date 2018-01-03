<?php

namespace domain\tests;


use domain\forms\base\ChangeUserPasswordForm;
use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\forms\base\UserFormUpdate;
use domain\models\base\Person;
use domain\models\base\Profile;
use domain\services\base\PersonService;
use domain\tests\fixtures\PersonFixture;
use domain\tests\fixtures\ProfileFixture;
use wartron\yii2uuid\helpers\Uuid;
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
        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_password') === "Необходимо заполнить «Пароль».");
        $this->assertTrue($userForm->getFirstError('person_password_repeat') === "Необходимо заполнить «Повторите ввод пароля».");

        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII',
            'person_password' => '1',
            'person_password_repeat' => '2',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_password') === "Значение «Пароль» должно содержать минимум 6 символов.");
        $this->assertTrue($userForm->getFirstError('person_password_repeat') === "Значение «Повторите ввод пароля» должно быть равно «Пароль».");

        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII',
            'person_password' => '111111',
            'person_password_repeat' => '111111',
        ]);
        $userForm->validate();
        $this->assertTrue($userForm->getFirstError('person_password') === null);
        $this->assertTrue($userForm->getFirstError('person_password_repeat') === null);
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
    }

    public function testPersonServiceByPersonRequired()
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
        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_fullname', ['person_code' => 2]) === 'ИВАНОВ ИВАН');
        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_username', ['person_code' => 2]) === 'username-_');
        $this->assertTrue(Yii::$app->security->validatePassword('111111', $this->tester->grabFromDatabase('person', 'person_password_hash', ['person_code' => 2])));
        $this->assertTrue(strlen($this->tester->grabFromDatabase('person', 'person_auth_key', ['person_code' => 2])) === 32);
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('person', 'created_at', ['person_code' => 2])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('person', 'updated_at', ['person_code' => 2])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('person', 'created_by', ['person_code' => 2]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('person', 'updated_by', ['person_code' => 2]) === 'Гость');

        $userForm = new UserForm([
            'person_fullname' => 'Сидоров Иван',
            'person_username' => 'UserName-_',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_username') === "Значение «username-_» для «Логин» уже занято.");
    }

    public function testPersonServiceByPersonNonRequired()
    {
        $service = Yii::createObject('domain\services\base\PersonService');
        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII',
            'person_password' => '111111',
            'person_email' => 'mail',
            'assignRoles' => '[]',
        ]);
        $profileForm = new ProfileForm();

        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($userForm->getFirstError('person_email') === "Значение «Электронная почта» не является правильным email адресом.");

        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII',
            'person_password' => '111111',
            'person_email' => 'mail@mail.ru',
            'assignRoles' => '["baseDolzhEdit","notExistRole"]',
        ]);
        $person_id = $service->create($userForm, $profileForm);
        $this->assertTrue(strlen($person_id) === 32);
        $this->assertTrue($userForm->getFirstError('person_email') === null);
        $this->tester->seeNumRecords(2, 'person');
        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_email', ['person_code' => 2]) === 'mail@mail.ru');
        $this->tester->seeNumRecords(2, 'auth_assignment');
        $this->assertTrue($this->tester->grabFromDatabase('auth_assignment', 'item_name', ['user_id' => Uuid::str2uuid($person_id)]) === 'baseDolzhEdit');
        $this->assertFalse($this->tester->grabFromDatabase('auth_assignment', 'item_name', ['user_id' => Uuid::str2uuid($person_id)]) === 'notExistRole');
    }

    public function testPersonServiceByProfile()
    {
        $service = Yii::createObject('domain\services\base\PersonService');
        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);

        $profileForm = new ProfileForm(null, [
            'profile_inn' => '123456',
            'profile_dr' => '15611561',
            'profile_pol' => '3',
            'profile_snils' => '123',
            'profile_address' => 'Address',
            'profile_phone' => '132467494654132',
            'profile_internal_phone' => '132467494654132',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
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
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
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
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
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
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($profileForm->getFirstError('profile_snils') === "Неверная контрольная сумма СНИЛС");
        $this->assertTrue($profileForm->getFirstError('profile_phone') === null);

        $profileForm = new ProfileForm(null, [
            'profile_snils' => '12312312384',
            'profile_inn' => '123456789102',
        ]);
        $this->assertTrue(strlen($service->create($userForm, $profileForm)) === 32);
        $this->assertTrue($profileForm->getFirstError('profile_snils') === null);

        $userForm = new UserForm([
            'person_fullname' => 'Петров Петр Петрович',
            'person_username' => 'PetrovPP',
            'person_password' => '111111',
            'assignRoles' => '[]',
        ]);
        $profileForm = new ProfileForm(null, [
            'profile_snils' => '12312312384',
            'profile_inn' => '123456789102',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $userForm, $profileForm) {
            $service->create($userForm, $profileForm);
        });
        $this->assertTrue($profileForm->getFirstError('profile_snils') === "Значение «12312312384» для «СНИЛС» уже занято.");
        $this->assertTrue($profileForm->getFirstError('profile_inn') === "Значение «123456789102» для «ИНН» уже занято.");
    }

    public function testPersonServiceUpdate()
    {
        /** @var PersonService $service */
        $service = Yii::createObject('domain\services\base\PersonService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
            ],
            'profile' => [
                'class' => ProfileFixture::className(),
            ],
        ]);
        /** @var Person $person */
        $person = $this->tester->grabFixture('person', 'user1');
        /** @var Profile $profile */
        $profile = $this->tester->grabFixture('profile', 'user1');
        $userFormUpdate = new UserFormUpdate($person, [
            'person_fullname' => 'Петров Петр Петрович',
            'person_username' => 'PetrovPP',
            'person_email' => 'PetrovPP@mail.ru',
            'person_fired' => '2017-01-01',
        ]);
        $profileForm = new ProfileForm($profile, [
            'profile_inn' => '123456789103',
            'profile_dr' => '1981-12-01',
            'profile_pol' => '2',
            'profile_snils' => '14230395728',
            'profile_address' => 'Address 2',
            'profile_phone' => '89225555555',
            'profile_internal_phone' => '200',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $person, $userFormUpdate, $profileForm) {
            $service->update($person->primaryKey, $userFormUpdate, $profileForm);
        });
        $this->assertTrue($userFormUpdate->getFirstError('person_fired') === "\"Дата увольнения\": Отсутствуют специальности");

        $userFormUpdate = new UserFormUpdate($person, [
            'person_fullname' => 'Петров Петр Петрович',
            'person_username' => 'PetrovPP',
            'person_email' => 'PetrovPP@mail.ru',
            'person_fired' => null,
        ]);
        $service->update($person->primaryKey, $userFormUpdate, $profileForm);

        $this->tester->seeNumRecords(2, 'person');
        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_fullname', ['person_code' => 2]) === 'ПЕТРОВ ПЕТР ПЕТРОВИЧ');
        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_username', ['person_code' => 2]) === 'petrovpp');
        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_email', ['person_code' => 2]) === 'PetrovPP@mail.ru');
        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_fired', ['person_code' => 2]) === null);

        $this->tester->seeNumRecords(1, 'profile');
        $this->assertTrue($this->tester->grabFromDatabase('profile', 'profile_inn', ['profile_id' => $profile->primaryKey]) === '123456789103');
        $this->assertTrue($this->tester->grabFromDatabase('profile', 'profile_dr', ['profile_id' => $profile->primaryKey]) === '1981-12-01');
        $this->assertTrue($this->tester->grabFromDatabase('profile', 'profile_pol', ['profile_id' => $profile->primaryKey]) === '2');
        $this->assertTrue($this->tester->grabFromDatabase('profile', 'profile_snils', ['profile_id' => $profile->primaryKey]) === '14230395728');
        $this->assertTrue($this->tester->grabFromDatabase('profile', 'profile_address', ['profile_id' => $profile->primaryKey]) === 'Address 2');
        $this->assertTrue($this->tester->grabFromDatabase('profile', 'profile_phone', ['profile_id' => $profile->primaryKey]) === '89225555555');
        $this->assertTrue($this->tester->grabFromDatabase('profile', 'profile_internal_phone', ['profile_id' => $profile->primaryKey]) === '200');
    }

    public function testChangePassword()
    {
        /** @var PersonService $service */
        $service = Yii::createObject('domain\services\base\PersonService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
            ],
            'profile' => [
                'class' => ProfileFixture::className(),
            ],
        ]);
        /** @var Person $person */
        $person = $this->tester->grabFixture('person', 'user1');

        $changeUserPasswordForm = new ChangeUserPasswordForm($person, [
            'person_password' => '',
            'person_password_repeat' => '',
        ]);
        $changeUserPasswordForm->validate();
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password') === 'Необходимо заполнить «Пароль».');
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password_repeat') === 'Необходимо заполнить «Повторите ввод пароля».');

        $changeUserPasswordForm = new ChangeUserPasswordForm($person, [
            'person_password' => '123',
            'person_password_repeat' => '123',
        ]);
        $changeUserPasswordForm->validate();
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password') === 'Значение «Пароль» должно содержать минимум 6 символов.');
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password_repeat') === null);

        $changeUserPasswordForm = new ChangeUserPasswordForm($person, [
            'person_password' => '123456',
            'person_password_repeat' => '654321',
        ]);
        $changeUserPasswordForm->validate();
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password') === null);
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password_repeat') === 'Значение «Повторите ввод пароля» должно быть равно «Пароль».');

        $changeUserPasswordForm = new ChangeUserPasswordForm($person, [
            'person_password' => '222222',
            'person_password_repeat' => '222222',
        ]);
        $changeUserPasswordForm->validate();
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password') === null);
        $this->assertTrue($changeUserPasswordForm->getFirstError('person_password_repeat') === null);

        $service->changePassword($person->primaryKey, $changeUserPasswordForm);

        $this->assertTrue(Yii::$app->security->validatePassword('222222', $this->tester->grabFromDatabase('person', 'person_password_hash', ['person_code' => 2])));
    }
}