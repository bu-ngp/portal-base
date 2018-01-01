<?php

namespace domain\tests;


use domain\forms\base\EmployeeHistoryForm;
use domain\forms\base\EmployeeHistoryUpdateForm;
use domain\models\base\Build;
use domain\models\base\Dolzh;
use domain\models\base\EmployeeHistory;
use domain\models\base\Person;
use domain\models\base\Podraz;
use domain\services\base\EmployeeHistoryService;
use domain\tests\fixtures\BuildFixture;
use domain\tests\fixtures\DolzhFixture;
use domain\tests\fixtures\EmployeeHistoryFixture;
use domain\tests\fixtures\PersonFixture;
use domain\tests\fixtures\PodrazFixture;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class EmployeeTest extends \Codeception\Test\Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testValidateForms()
    {
        $this->tester->haveFixtures([
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
            ],
        ]);
        /** @var EmployeeHistory $employeeHistory */
        $employeeHistory = $this->tester->grabFixture('employeeHistory', 0);

        $employeeHistoryForm = new EmployeeHistoryForm([
            'person_id' => 'NFKDSNFKJDSNFDSJN1',
            'dolzh_id' => 'NFKDSNFKJDSNFDSJN2',
            'podraz_id' => 'NFKDSNFKJDSNFDSJN3',
        ]);
        $employeeHistoryForm->validate();
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('person_id') === "Не валидная UUID строка");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('dolzh_id') === "Не валидная UUID строка");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('podraz_id') === "Не валидная UUID строка");

        $employeeHistoryUpdateForm = new EmployeeHistoryUpdateForm($employeeHistory, [
            'dolzh_id' => 'NFKDSNFKJDSNFDSJN1',
            'podraz_id' => 'NFKDSNFKJDSNFDSJN2',
        ]);
        $employeeHistoryUpdateForm->validate();
        $this->tester->assertTrue($employeeHistoryUpdateForm->getFirstError('dolzh_id') === "Не валидная UUID строка");
        $this->tester->assertTrue($employeeHistoryUpdateForm->getFirstError('podraz_id') === "Не валидная UUID строка");
    }

    public function testCreateFirstEmployee()
    {
        /** @var EmployeeHistoryService $service */
        $service = Yii::createObject('domain\services\base\EmployeeHistoryService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
            ],
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
            'build' => [
                'class' => BuildFixture::className(),
            ],
        ]);
        /** @var Person $person */
        $person = $this->tester->grabFixture('person', 'user1');
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 0);
        /** @var Podraz $podraz */
        $podraz = $this->tester->grabFixture('podraz', 0);
        /** @var Build $build1 */
        $build1 = $this->tester->grabFixture('build', 0);
        /** @var Build $build2 */
        $build2 = $this->tester->grabFixture('build', 1);

        $employeeHistoryForm = new EmployeeHistoryForm();
        $this->tester->expectException(new \DomainException("Ошибка при распознавании выбранных элементов"), function () use ($service, $employeeHistoryForm) {
            $service->create($employeeHistoryForm);
        });

        $employeeHistoryForm = new EmployeeHistoryForm([
            'assignBuilds' => '[]',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeHistoryForm) {
            $service->create($employeeHistoryForm);
        });
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('person_id') === "Необходимо заполнить «Пользователь».");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('dolzh_id') === "Необходимо заполнить «Должность».");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('podraz_id') === "Необходимо заполнить «Подразделение».");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('employee_history_begin') === null);
        $this->tester->assertTrue($employeeHistoryForm->employee_history_begin === date('Y-m-d'));

        $employeeHistoryForm = new EmployeeHistoryForm([
            'person_id' => 'NFKDSNFKJDSNFDSJN1',
            'dolzh_id' => 'NFKDSNFKJDSNFDSJN2',
            'podraz_id' => 'NFKDSNFKJDSNFDSJN3',
            'employee_history_begin' => '2017/01/01',
            'assignBuilds' => '[]',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeHistoryForm) {
            $service->create($employeeHistoryForm);
        });
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('person_id') === "Значение «Пользователь» неверно.");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('dolzh_id') === "Значение «Должность» неверно.");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('podraz_id') === "Значение «Подразделение» неверно.");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('employee_history_begin') === "Неверный формат значения «Дата начала».");

        $employeeHistoryForm = new EmployeeHistoryForm([
            'person_id' => Uuid::uuid2str($person->primaryKey),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($podraz->primaryKey),
            'employee_history_begin' => date('Y-m-d'),
            'assignBuilds' => '["invalidIdBuild", "' . Uuid::uuid2str($build1->primaryKey) . '","' . Uuid::uuid2str($build2->primaryKey) . '"]',
        ]);
        $employeeHistoryForm->validate();
        $service->create($employeeHistoryForm);

        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_hired', ['person_id' => $person->primaryKey]) === date('Y-m-d'));

        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'person_id', ['employee_history_id' => 1]) === $person->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'dolzh_id', ['employee_history_id' => 1]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'podraz_id', ['employee_history_id' => 1]) === $podraz->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'employee_history_begin', ['employee_history_id' => 1]) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'created_at', ['employee_history_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'updated_at', ['employee_history_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'created_by', ['employee_history_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'updated_by', ['employee_history_id' => 1]) === 'Гость');

        $this->assertTrue($this->tester->grabFromDatabase('employee', 'person_id', ['employee_id' => 1]) === $person->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'dolzh_id', ['employee_id' => 1]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'podraz_id', ['employee_id' => 1]) === $podraz->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'employee_begin', ['employee_id' => 1]) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'created_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'updated_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'created_by', ['employee_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'updated_by', ['employee_id' => 1]) === 'Гость');

        $this->tester->seeNumRecords(2, 'employee_history_build');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_id', ['ehb_id' => 1]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'build_id', ['ehb_id' => 1]) === $build1->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_build_deactive', ['ehb_id' => 1]) === null);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_id', ['ehb_id' => 2]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'build_id', ['ehb_id' => 2]) === $build2->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_build_deactive', ['ehb_id' => 2]) === null);
    }

    public function testCreateSecondEmployeeIfDateLess()
    {
        /** @var EmployeeHistoryService $service */
        $service = Yii::createObject('domain\services\base\EmployeeHistoryService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
            ],
        ]);
        /** @var EmployeeHistory $employeeHistory */
        $employeeHistory = $this->tester->grabFixture('employeeHistory', 0);
        /** @var Dolzh $dolzhOrig */
        $dolzhOrig = $this->tester->grabFixture('dolzh', 0);
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 1);

        $employeeHistoryForm = new EmployeeHistoryForm([
            'person_id' => Uuid::uuid2str($employeeHistory->person_id),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($employeeHistory->podraz_id),
            'employee_history_begin' => date('Y-m-d'),
            'assignBuilds' => '[]',
        ]);
        $employeeHistoryForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeHistoryForm) {
            $service->create($employeeHistoryForm);
        });
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('employee_history_begin') === "Специальность у пользователя на эту дату уже существует.");

        $employeeHistoryForm = new EmployeeHistoryForm([
            'person_id' => Uuid::uuid2str($employeeHistory->person_id),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($employeeHistory->podraz_id),
            'employee_history_begin' => '2017-12-31',
            'assignBuilds' => '[]',
        ]);
        $employeeHistoryForm->validate();
        $service->create($employeeHistoryForm);

        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_hired', ['person_id' => $employeeHistory->person_id]) === '2017-12-31');

        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'person_id', ['employee_history_id' => 2]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'dolzh_id', ['employee_history_id' => 2]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'podraz_id', ['employee_history_id' => 2]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'employee_history_begin', ['employee_history_id' => 2]) === '2017-12-31');
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'created_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'updated_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'created_by', ['employee_history_id' => 2]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'updated_by', ['employee_history_id' => 2]) === 'Гость');

        $this->assertTrue($this->tester->grabFromDatabase('employee', 'person_id', ['employee_id' => 1]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'dolzh_id', ['employee_id' => 1]) === $dolzhOrig->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'podraz_id', ['employee_id' => 1]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'employee_begin', ['employee_id' => 1]) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'created_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'updated_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'created_by', ['employee_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'updated_by', ['employee_id' => 1]) === 'Гость');
    }

    public function testCreateSecondEmployeeIfDateMore()
    {
        /** @var EmployeeHistoryService $service */
        $service = Yii::createObject('domain\services\base\EmployeeHistoryService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
            ],
        ]);
        /** @var EmployeeHistory $employeeHistory */
        $employeeHistory = $this->tester->grabFixture('employeeHistory', 0);
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 1);

        $employeeHistoryForm = new EmployeeHistoryForm([
            'person_id' => Uuid::uuid2str($employeeHistory->person_id),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($employeeHistory->podraz_id),
            'employee_history_begin' => date('Y-m-d', strtotime('+1 day')),
            'assignBuilds' => '[]',
        ]);
        $employeeHistoryForm->validate();
        $service->create($employeeHistoryForm);

        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_hired', ['person_id' => $employeeHistory->person_id]) === date('Y-m-d'));

        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'person_id', ['employee_history_id' => 2]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'dolzh_id', ['employee_history_id' => 2]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'podraz_id', ['employee_history_id' => 2]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'employee_history_begin', ['employee_history_id' => 2]) === date('Y-m-d', strtotime('+1 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'created_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'updated_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'created_by', ['employee_history_id' => 2]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'updated_by', ['employee_history_id' => 2]) === 'Гость');

        $this->assertTrue($this->tester->grabFromDatabase('employee', 'person_id', ['employee_id' => 1]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'dolzh_id', ['employee_id' => 1]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'podraz_id', ['employee_id' => 1]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'employee_begin', ['employee_id' => 1]) === date('Y-m-d', strtotime('+1 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'created_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'updated_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'created_by', ['employee_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'updated_by', ['employee_id' => 1]) === 'Гость');
    }

    public function testUpdateSecondEmployeeifDateLess()
    {
        /** @var EmployeeHistoryService $service */
        $service = Yii::createObject('domain\services\base\EmployeeHistoryService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee_update.php',
            ],
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_two.php',
            ],
        ]);
        /** @var EmployeeHistory $employeeHistory */
        $employeeHistory = $this->tester->grabFixture('employeeHistory', 1);
        /** @var Dolzh $dolzhOrig */
        $dolzhOrig = $this->tester->grabFixture('dolzh', 0);
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 2);

        $employeeHistoryUpdateForm = new EmployeeHistoryUpdateForm($employeeHistory, [
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($employeeHistory->podraz_id),
            'employee_history_begin' => '2017-12-31',
        ]);
        $employeeHistoryUpdateForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeHistory, $employeeHistoryUpdateForm) {
            $service->update($employeeHistory->primaryKey, $employeeHistoryUpdateForm);
        });
        $this->tester->assertTrue($employeeHistoryUpdateForm->getFirstError('employee_history_begin') === "Специальность у пользователя на эту дату уже существует.");

        $employeeHistoryUpdateForm = new EmployeeHistoryUpdateForm($employeeHistory, [
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($employeeHistory->podraz_id),
            'employee_history_begin' => '2017-11-30',
        ]);
        $employeeHistoryUpdateForm->validate();
        $service->update($employeeHistory->primaryKey, $employeeHistoryUpdateForm);

        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_hired', ['person_id' => $employeeHistory->person_id]) === '2017-11-30');

        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'person_id', ['employee_history_id' => 2]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'dolzh_id', ['employee_history_id' => 2]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'podraz_id', ['employee_history_id' => 2]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'employee_history_begin', ['employee_history_id' => 2]) === '2017-11-30');
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'created_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'updated_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'created_by', ['employee_history_id' => 2]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'updated_by', ['employee_history_id' => 2]) === 'Гость');

        $this->assertTrue($this->tester->grabFromDatabase('employee', 'person_id', ['employee_id' => 1]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'dolzh_id', ['employee_id' => 1]) === $dolzhOrig->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'podraz_id', ['employee_id' => 1]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'employee_begin', ['employee_id' => 1]) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'created_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'updated_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'created_by', ['employee_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'updated_by', ['employee_id' => 1]) === 'Гость');
    }


    public function testUpdateSecondEmployeeifDateMore()
    {
        /** @var EmployeeHistoryService $service */
        $service = Yii::createObject('domain\services\base\EmployeeHistoryService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee_update.php',
            ],
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_two.php',
            ],
        ]);
        /** @var EmployeeHistory $employeeHistory */
        $employeeHistory = $this->tester->grabFixture('employeeHistory', 1);
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 2);

        $employeeHistoryUpdateForm = new EmployeeHistoryUpdateForm($employeeHistory, [
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($employeeHistory->podraz_id),
            'employee_history_begin' => '2017-12-31',
        ]);
        $employeeHistoryUpdateForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeHistory, $employeeHistoryUpdateForm) {
            $service->update($employeeHistory->primaryKey, $employeeHistoryUpdateForm);
        });
        $this->tester->assertTrue($employeeHistoryUpdateForm->getFirstError('employee_history_begin') === "Специальность у пользователя на эту дату уже существует.");

        $employeeHistoryUpdateForm = new EmployeeHistoryUpdateForm($employeeHistory, [
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($employeeHistory->podraz_id),
            'employee_history_begin' => date('Y-m-d', strtotime('+1 day')),
        ]);
        $employeeHistoryUpdateForm->validate();
        $service->update($employeeHistory->primaryKey, $employeeHistoryUpdateForm);

        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_hired', ['person_id' => $employeeHistory->person_id]) === '2017-12-31');

        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'person_id', ['employee_history_id' => 2]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'dolzh_id', ['employee_history_id' => 2]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'podraz_id', ['employee_history_id' => 2]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'employee_history_begin', ['employee_history_id' => 2]) === date('Y-m-d', strtotime('+1 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'created_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee_history', 'updated_at', ['employee_history_id' => 2])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'created_by', ['employee_history_id' => 2]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history', 'updated_by', ['employee_history_id' => 2]) === 'Гость');

        $this->assertTrue($this->tester->grabFromDatabase('employee', 'person_id', ['employee_id' => 1]) === $employeeHistory->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'dolzh_id', ['employee_id' => 1]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'podraz_id', ['employee_id' => 1]) === $employeeHistory->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'employee_begin', ['employee_id' => 1]) === date('Y-m-d', strtotime('+1 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'created_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'updated_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'created_by', ['employee_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'updated_by', ['employee_id' => 1]) === 'Гость');
    }

    public function testDeleteFirstEmployee()
    {
        /** @var EmployeeHistoryService $service */
        $service = Yii::createObject('domain\services\base\EmployeeHistoryService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee_update.php',
            ],
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_two.php',
            ],
        ]);
        /** @var EmployeeHistory $employeeHistory1 */
        $employeeHistory1 = $this->tester->grabFixture('employeeHistory', 0);
        /** @var EmployeeHistory $employeeHistory2 */
        $employeeHistory2 = $this->tester->grabFixture('employeeHistory', 1);
        $service->delete($employeeHistory1->primaryKey);

        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_hired', ['person_id' => $employeeHistory2->person_id]) === $employeeHistory2->employee_history_begin);

        $this->tester->seeNumRecords(1, 'employee_history');

        $this->assertTrue($this->tester->grabFromDatabase('employee', 'person_id', ['employee_id' => 1]) === $employeeHistory2->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'dolzh_id', ['employee_id' => 1]) === $employeeHistory2->dolzh_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'podraz_id', ['employee_id' => 1]) === $employeeHistory2->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'employee_begin', ['employee_id' => 1]) === $employeeHistory2->employee_history_begin);
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'created_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'updated_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'created_by', ['employee_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'updated_by', ['employee_id' => 1]) === 'Гость');
    }

    public function testDeleteSecondEmployee()
    {
        /** @var EmployeeHistoryService $service */
        $service = Yii::createObject('domain\services\base\EmployeeHistoryService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee_update.php',
            ],
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_two.php',
            ],
        ]);
        /** @var EmployeeHistory $employeeHistory1 */
        $employeeHistory1 = $this->tester->grabFixture('employeeHistory', 0);
        /** @var EmployeeHistory $employeeHistory2 */
        $employeeHistory2 = $this->tester->grabFixture('employeeHistory', 1);
        $service->delete($employeeHistory2->primaryKey);

        $this->assertTrue($this->tester->grabFromDatabase('person', 'person_hired', ['person_id' => $employeeHistory1->person_id]) === $employeeHistory1->employee_history_begin);

        $this->tester->seeNumRecords(1, 'employee_history');

        $this->assertTrue($this->tester->grabFromDatabase('employee', 'person_id', ['employee_id' => 1]) === $employeeHistory1->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'dolzh_id', ['employee_id' => 1]) === $employeeHistory1->dolzh_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'podraz_id', ['employee_id' => 1]) === $employeeHistory1->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'employee_begin', ['employee_id' => 1]) === $employeeHistory1->employee_history_begin);
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'created_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('employee', 'updated_at', ['employee_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'created_by', ['employee_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('employee', 'updated_by', ['employee_id' => 1]) === 'Гость');
    }
}