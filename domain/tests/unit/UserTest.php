<?php
namespace domain\tests;


use domain\forms\base\ProfileForm;
use domain\forms\base\UserForm;
use domain\repositories\base\PersonRepository;
use domain\repositories\base\ProfileRepository;
use domain\services\base\PersonService;
use domain\services\TransactionManager;
use yii\codeception\DbTestCase;

class UserTest extends DbTestCase
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testCreate()
    {
        $service = new PersonService(new TransactionManager, new PersonRepository(), new ProfileRepository());
        $userForm = new UserForm([
            'person_fullname' => 'Иванов Иван Иванович',
            'person_username' => 'IvanovII',
            'person_password' => '123456',
            'person_password_repeat' => '123456',
            'person_email' => 'ivanovii@mail.ru',
            'person_fired' => null,
            'assignEmployees' => '[]',
            'assignRoles' => '[]',
        ]);
        $profileForm = new ProfileForm(null, [
            'profile_inn' => '123456789101',
            'profile_dr' => '05.04.1950',
            'profile_pol' => '0',
            'profile_snils' => '123-123-123-84',
            'profile_address' => 'ул. Ленина, д. 5, кв. 17',
        ]);

        $this->assertTrue($service->create($userForm, $profileForm, '[]', '[]'));
        $this->assertEmpty($userForm->getErrors());
        $this->assertEmpty($profileForm->getErrors());
        $this->tester->seeInDatabase('person', [
            'person_fullname' => mb_strtoupper($userForm->person_fullname, 'UTF-8')
        ]);
        $this->tester->seeInDatabase('profile', [
            'profile_inn' => $profileForm->profile_inn,
        ]);
    }
}