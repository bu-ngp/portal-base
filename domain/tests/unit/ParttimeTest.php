<?php

namespace domain\tests;


use domain\forms\base\ParttimeForm;
use domain\forms\base\ParttimeUpdateForm;
use domain\models\base\Build;
use domain\models\base\Dolzh;
use domain\models\base\Parttime;
use domain\models\base\Person;
use domain\models\base\Podraz;
use domain\services\base\ParttimeService;
use domain\tests\fixtures\BuildFixture;
use domain\tests\fixtures\DolzhFixture;
use domain\tests\fixtures\EmployeeFixture;
use domain\tests\fixtures\EmployeeHistoryFixture;
use domain\tests\fixtures\ParttimeFixture;
use domain\tests\fixtures\PersonFixture;
use domain\tests\fixtures\PodrazFixture;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class ParttimeTest extends \Codeception\Test\Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testValidateForms()
    {
        $this->tester->haveFixtures([
            'parttime' => [
                'class' => ParttimeFixture::className(),
            ],
        ]);
        /** @var Parttime $parttime */
        $parttime = $this->tester->grabFixture('parttime', 0);

        $employeeHistoryForm = new ParttimeForm([
            'person_id' => 'NFKDSNFKJDSNFDSJN1',
            'dolzh_id' => 'NFKDSNFKJDSNFDSJN2',
            'podraz_id' => 'NFKDSNFKJDSNFDSJN3',
        ]);
        $employeeHistoryForm->validate();
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('person_id') === "Не валидная UUID строка");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('dolzh_id') === "Не валидная UUID строка");
        $this->tester->assertTrue($employeeHistoryForm->getFirstError('podraz_id') === "Не валидная UUID строка");

        $employeeHistoryUpdateForm = new ParttimeUpdateForm($parttime, [
            'dolzh_id' => 'NFKDSNFKJDSNFDSJN1',
            'podraz_id' => 'NFKDSNFKJDSNFDSJN2',
        ]);
        $employeeHistoryUpdateForm->validate();
        $this->tester->assertTrue($employeeHistoryUpdateForm->getFirstError('dolzh_id') === "Не валидная UUID строка");
        $this->tester->assertTrue($employeeHistoryUpdateForm->getFirstError('podraz_id') === "Не валидная UUID строка");
    }

    public function testCreateFirstParttime()
    {
        /** @var ParttimeService $service */
        $service = Yii::createObject('domain\services\base\ParttimeService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_for_create_parttime.php',
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
            'employee' => [
                'class' => EmployeeFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_for_create_parttime.php',
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_for_create_parttime.php',
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

        $parttimeForm = new ParttimeForm();
        $this->tester->expectException(new \DomainException("Ошибка при распознавании выбранных элементов"), function () use ($service, $parttimeForm) {
            $service->create($parttimeForm);
        });

        $parttimeForm = new ParttimeForm([
            'assignBuilds' => '[]',
        ]);

        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeForm) {
            $service->create($parttimeForm);
        });
        $this->tester->assertTrue($parttimeForm->getFirstError('person_id') === "Необходимо заполнить «Пользователь».");
        $this->tester->assertTrue($parttimeForm->getFirstError('dolzh_id') === "Необходимо заполнить «Должность».");
        $this->tester->assertTrue($parttimeForm->getFirstError('podraz_id') === "Необходимо заполнить «Подразделение».");
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_begin') === null);
        $this->tester->assertTrue($parttimeForm->parttime_begin === date('Y-m-d'));
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_end') === null);
        $this->tester->assertTrue($parttimeForm->parttime_end === null);

        $parttimeForm = new ParttimeForm([
            'person_id' => 'NFKDSNFKJDSNFDSJN1',
            'dolzh_id' => 'NFKDSNFKJDSNFDSJN2',
            'podraz_id' => 'NFKDSNFKJDSNFDSJN3',
            'parttime_begin' => '2017/01/01',
            'parttime_end' => '2017/01/02',
            'assignBuilds' => '[]',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeForm) {
            $service->create($parttimeForm);
        });
        $this->tester->assertTrue($parttimeForm->getFirstError('person_id') === "Значение «Пользователь» неверно.");
        $this->tester->assertTrue($parttimeForm->getFirstError('dolzh_id') === "Значение «Должность» неверно.");
        $this->tester->assertTrue($parttimeForm->getFirstError('podraz_id') === "Значение «Подразделение» неверно.");
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_begin') === "Неверный формат значения «Дата начала».");
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_end') === "Неверный формат значения «Дата окончания».");

        $parttimeForm = new ParttimeForm([
            'person_id' => Uuid::uuid2str($person->primaryKey),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($podraz->primaryKey),
            'parttime_begin' => '2018-01-15',
            'parttime_end' => '2018-01-10',
            'assignBuilds' => '[]',
        ]);
        $parttimeForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeForm) {
            $service->create($parttimeForm);
        });
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_end') === "Значение «Дата окончания» должно быть больше или равно значения «Дата начала».");

        $parttimeForm = new ParttimeForm([
            'person_id' => Uuid::uuid2str($person->primaryKey),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($podraz->primaryKey),
            'parttime_begin' => '2015-01-01',
            'parttime_end' => '2018-01-10',
            'assignBuilds' => '[]',
        ]);
        $parttimeForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeForm) {
            $service->create($parttimeForm);
        });
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_begin') === "\"Дата начала\" не может быть менее даты приема на работу 01.01.2016");
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_end') === null);

        $parttimeForm = new ParttimeForm([
            'person_id' => Uuid::uuid2str($person->primaryKey),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($podraz->primaryKey),
            'parttime_begin' => date('Y-m-d'),
            'parttime_end' => date('Y-m-d', strtotime('+61 day')),
            'assignBuilds' => '[]',
        ]);
        $parttimeForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeForm) {
            $service->create($parttimeForm);
        });
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_begin') === null);
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_end') === "\"Дата окончания\" не может быть более даты увольнения " . Yii::$app->formatter->asDate(date('Y-m-d', strtotime('+60 day'))));

        $parttimeForm = new ParttimeForm([
            'person_id' => Uuid::uuid2str($person->primaryKey),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($podraz->primaryKey),
            'parttime_begin' => date('Y-m-d'),
            'parttime_end' => date('Y-m-d', strtotime('+1 day')),
            'assignBuilds' => '["invalidIdBuild", "' . Uuid::uuid2str($build1->primaryKey) . '","' . Uuid::uuid2str($build2->primaryKey) . '"]',
        ]);
        $parttimeForm->validate();
        $service->create($parttimeForm);

        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'person_id', ['parttime_id' => 1]) === $person->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'dolzh_id', ['parttime_id' => 1]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'podraz_id', ['parttime_id' => 1]) === $podraz->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_begin', ['parttime_id' => 1]) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_end', ['parttime_id' => 1]) === date('Y-m-d', strtotime('+1 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'created_at', ['parttime_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'updated_at', ['parttime_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'created_by', ['parttime_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'updated_by', ['parttime_id' => 1]) === 'Гость');

        $this->tester->seeNumRecords(2, 'parttime_build');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_id', ['pb' => 1]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'build_id', ['pb' => 1]) === $build1->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_build_deactive', ['pb' => 1]) === null);
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_id', ['pb' => 2]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'build_id', ['pb' => 2]) === $build2->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_build_deactive', ['pb' => 2]) === null);
    }

    public function testCreateSecondParttime()
    {
        /** @var ParttimeService $service */
        $service = Yii::createObject('domain\services\base\ParttimeService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_for_create_parttime.php',
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
            'employee' => [
                'class' => EmployeeFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_for_create_parttime.php',
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_for_create_parttime.php',
            ],
            'parttime' => [
                'class' => ParttimeFixture::className(),
            ],
        ]);
        /** @var Parttime $parttime */
        $parttime = $this->tester->grabFixture('parttime', 0);
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 1);

        $parttimeForm = new ParttimeForm([
            'person_id' => Uuid::uuid2str($parttime->person_id),
            'dolzh_id' => Uuid::uuid2str($parttime->dolzh_id),
            'podraz_id' => Uuid::uuid2str($parttime->podraz_id),
            'parttime_begin' => date('Y-m-d'),
            'parttime_end' => date('Y-m-d', strtotime('+10 day')),
            'assignBuilds' => '[]',
        ]);
        $parttimeForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeForm) {
            $service->create($parttimeForm);
        });
        $this->tester->assertTrue($parttimeForm->getFirstError('parttime_begin') === "На данный период уже существует совмещение с такими должностью и подразделением");

        $parttimeForm = new ParttimeForm([
            'person_id' => Uuid::uuid2str($parttime->person_id),
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($parttime->podraz_id),
            'parttime_begin' => date('Y-m-d'),
            'parttime_end' => date('Y-m-d', strtotime('+11 day')),
            'assignBuilds' => '[]',
        ]);
        $parttimeForm->validate();
        $service->create($parttimeForm);

        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'person_id', ['parttime_id' => 2]) === $parttime->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'dolzh_id', ['parttime_id' => 2]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'podraz_id', ['parttime_id' => 2]) === $parttime->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_begin', ['parttime_id' => 2]) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_end', ['parttime_id' => 2]) === date('Y-m-d', strtotime('+11 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'created_at', ['parttime_id' => 2])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'updated_at', ['parttime_id' => 2])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'created_by', ['parttime_id' => 2]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'updated_by', ['parttime_id' => 2]) === 'Гость');
    }

    public function testUpdateParttime()
    {
        /** @var ParttimeService $service */
        $service = Yii::createObject('domain\services\base\ParttimeService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_for_create_parttime.php',
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
            'employee' => [
                'class' => EmployeeFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_for_create_parttime.php',
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_for_create_parttime.php',
            ],
            'parttime' => [
                'class' => ParttimeFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/parttime_for_update.php',
            ],
        ]);
        /** @var Parttime $parttime1 */
        $parttime1 = $this->tester->grabFixture('parttime', 0);
        /** @var Parttime $parttime2 */
        $parttime2 = $this->tester->grabFixture('parttime', 1);
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 2);

        $parttimeUpdateForm = new ParttimeUpdateForm($parttime2, [
            'dolzh_id' => Uuid::uuid2str($parttime1->dolzh_id),
            'podraz_id' => Uuid::uuid2str($parttime1->podraz_id),
            'parttime_begin' => date('Y-m-d'),
            'parttime_end' => date('Y-m-d', strtotime('+10 day')),
        ]);
        $parttimeUpdateForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttime2, $parttimeUpdateForm) {
            $service->update($parttime2->primaryKey, $parttimeUpdateForm);
        });
        $this->tester->assertTrue($parttimeUpdateForm->getFirstError('parttime_begin') === "На данный период уже существует совмещение с такими должностью и подразделением");

        $parttimeUpdateForm = new ParttimeUpdateForm($parttime2, [
            'dolzh_id' => Uuid::uuid2str($dolzh->primaryKey),
            'podraz_id' => Uuid::uuid2str($parttime2->podraz_id),
            'parttime_begin' => date('Y-m-d'),
            'parttime_end' => date('Y-m-d', strtotime('+10 day')),
        ]);
        $parttimeUpdateForm->validate();
        $service->update($parttime2->primaryKey, $parttimeUpdateForm);

        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'person_id', ['parttime_id' => 2]) === $parttime2->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'dolzh_id', ['parttime_id' => 2]) === $dolzh->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'podraz_id', ['parttime_id' => 2]) === $parttime2->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_begin', ['parttime_id' => 2]) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_end', ['parttime_id' => 2]) === date('Y-m-d', strtotime('+10 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'created_at', ['parttime_id' => 2])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'updated_at', ['parttime_id' => 2])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'created_by', ['parttime_id' => 2]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'updated_by', ['parttime_id' => 2]) === 'Гость');
    }

    public function testDeleteParttime()
    {
        /** @var ParttimeService $service */
        $service = Yii::createObject('domain\services\base\ParttimeService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_for_create_parttime.php',
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
            'employee' => [
                'class' => EmployeeFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_for_create_parttime.php',
            ],
            'employeeHistory' => [
                'class' => EmployeeHistoryFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_for_create_parttime.php',
            ],
            'parttime' => [
                'class' => ParttimeFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/parttime_for_update.php',
            ],
        ]);
        /** @var Parttime $parttime1 */
        $parttime1 = $this->tester->grabFixture('parttime', 0);
        /** @var Parttime $parttime2 */
        $parttime2 = $this->tester->grabFixture('parttime', 1);
        $service->delete($parttime2->primaryKey);

        $this->tester->seeNumRecords(1, 'parttime');
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'person_id', ['parttime_id' => 1]) === $parttime1->person_id);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'dolzh_id', ['parttime_id' => 1]) === $parttime1->dolzh_id);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'podraz_id', ['parttime_id' => 1]) === $parttime1->podraz_id);
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_begin', ['parttime_id' => 1]) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'parttime_end', ['parttime_id' => 1]) === date('Y-m-d', strtotime('+10 day')));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'created_at', ['parttime_id' => 1])) === date('Y-m-d'));
        $this->assertTrue(date('Y-m-d', $this->tester->grabFromDatabase('parttime', 'updated_at', ['parttime_id' => 1])) === date('Y-m-d'));
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'created_by', ['parttime_id' => 1]) === 'Гость');
        $this->assertTrue($this->tester->grabFromDatabase('parttime', 'updated_by', ['parttime_id' => 1]) === 'Гость');
    }
}