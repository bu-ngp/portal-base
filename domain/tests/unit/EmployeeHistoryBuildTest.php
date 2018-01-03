<?php

namespace domain\tests;


use domain\forms\base\EmployeeBuildForm;
use domain\forms\base\EmployeeBuildUpdateForm;
use domain\models\base\Build;
use domain\models\base\EmployeeHistoryBuild;
use domain\services\base\EmployeeBuildService;
use domain\tests\fixtures\BuildFixture;
use domain\tests\fixtures\EmployeeHistoryBuildFixture;
use domain\tests\fixtures\PersonFixture;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class EmployeeHistoryBuildTest extends \Codeception\Test\Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testValidateForms()
    {
        $this->tester->haveFixtures([
            'employeeHistoryBuild' => [
                'class' => EmployeeHistoryBuildFixture::className(),
            ],
        ]);
        /** @var EmployeeHistoryBuild $employeeHistoryBuild */
        $employeeHistoryBuild = $this->tester->grabFixture('employeeHistoryBuild', 0);

        $employeeBuildForm = new EmployeeBuildForm([
            'build_id' => 'NFKDSNFKJDSNFDSJN1',
        ]);
        $employeeBuildForm->validate();
        $this->tester->assertTrue($employeeBuildForm->getFirstError('build_id') === "Не валидная UUID строка");

        $employeeBuildUpdateForm = new EmployeeBuildUpdateForm($employeeHistoryBuild, [
            'build_id' => 'NFKDSNFKJDSNFDSJN1',
        ]);
        $employeeBuildUpdateForm->validate();
        $this->tester->assertTrue($employeeBuildUpdateForm->getFirstError('build_id') === "Не валидная UUID строка");
    }

    public function testCreateEmployeeHistoryBuild()
    {
        /** @var EmployeeBuildService $service */
        $service = Yii::createObject('domain\services\base\EmployeeBuildService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'build' => [
                'class' => BuildFixture::className(),
            ],
            'employeeHistoryBuild' => [
                'class' => EmployeeHistoryBuildFixture::className(),
            ],
        ]);
        /** @var Build $build1 */
        $build1 = $this->tester->grabFixture('build', 0);
        /** @var Build $build2 */
        $build2 = $this->tester->grabFixture('build', 1);

        $employeeBuildForm = new EmployeeBuildForm();
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeBuildForm) {
            $service->create($employeeBuildForm);
        });
        $this->tester->assertTrue($employeeBuildForm->getFirstError('employee_history_id') === "Необходимо заполнить «Специальность».");
        $this->tester->assertTrue($employeeBuildForm->getFirstError('build_id') === "Необходимо заполнить «Здание».");
        $this->tester->assertTrue($employeeBuildForm->getFirstError('employee_history_build_deactive') === null);

        $employeeBuildForm = new EmployeeBuildForm([
            'employee_history_id' => 2,
            'build_id' => 'KLJGFGKDFKJGLFJD',
            'employee_history_build_deactive' => '2017/12/01',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeBuildForm) {
            $service->create($employeeBuildForm);
        });
        $this->tester->assertTrue($employeeBuildForm->getFirstError('employee_history_id') === "Значение «Специальность» неверно.");
        $this->tester->assertTrue($employeeBuildForm->getFirstError('build_id') === "Значение «Здание» неверно.");
        $this->tester->assertTrue($employeeBuildForm->getFirstError('employee_history_build_deactive') === "Неверный формат значения «Дата с которой здание неактивно».");

        $employeeBuildForm = new EmployeeBuildForm([
            'employee_history_id' => 1,
            'build_id' => Uuid::uuid2str($build1->primaryKey),
        ]);
        $employeeBuildForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeBuildForm) {
            $service->create($employeeBuildForm);
        });
        $this->tester->assertTrue($employeeBuildForm->getFirstError('build_id') === "У текущей специальности уже имеется данное здание");

        $employeeBuildForm = new EmployeeBuildForm([
            'employee_history_id' => 1,
            'build_id' => Uuid::uuid2str($build2->primaryKey),
            'employee_history_build_deactive' => date('Y-m-d'),
        ]);
        $employeeBuildForm->validate();
        $service->create($employeeBuildForm);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_id', ['ehb_id' => 2]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'build_id', ['ehb_id' => 2]) === $build2->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_build_deactive', ['ehb_id' => 2]) === date('Y-m-d'));
    }

    public function testUpdateEmployeeHistoryBuild()
    {
        /** @var EmployeeBuildService $service */
        $service = Yii::createObject('domain\services\base\EmployeeBuildService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'build' => [
                'class' => BuildFixture::className(),
            ],
            'employeeHistoryBuild' => [
                'class' => EmployeeHistoryBuildFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_build_for_update.php',
            ],
        ]);
        /** @var Build $build1 */
        $build1 = $this->tester->grabFixture('build', 0);
        /** @var Build $build2 */
        $build2 = $this->tester->grabFixture('build', 1);
        /** @var EmployeeHistoryBuild $employeeHistoryBuild */
        $employeeHistoryBuild = $this->tester->grabFixture('employeeHistoryBuild', 1);

        $employeeBuildUpdateForm = new EmployeeBuildUpdateForm($employeeHistoryBuild, [
            'build_id' => 'KLJGFGKDFKJGLFJD',
            'employee_history_build_deactive' => '2017/12/01',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeHistoryBuild, $employeeBuildUpdateForm) {
            $service->update($employeeHistoryBuild->primaryKey, $employeeBuildUpdateForm);
        });
        $this->tester->assertTrue($employeeBuildUpdateForm->getFirstError('build_id') === "Значение «Здание» неверно.");
        $this->tester->assertTrue($employeeBuildUpdateForm->getFirstError('employee_history_build_deactive') === "Неверный формат значения «Дата с которой здание неактивно».");

        $employeeBuildUpdateForm = new EmployeeBuildUpdateForm($employeeHistoryBuild, [
            'build_id' => Uuid::uuid2str($build1->primaryKey),
        ]);
        $employeeBuildUpdateForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $employeeHistoryBuild, $employeeBuildUpdateForm) {
            $service->update($employeeHistoryBuild->primaryKey, $employeeBuildUpdateForm);
        });
        $this->tester->assertTrue($employeeBuildUpdateForm->getFirstError('build_id') === "У текущей специальности уже имеется данное здание");

        $employeeBuildUpdateForm = new EmployeeBuildUpdateForm($employeeHistoryBuild, [
            'build_id' => Uuid::uuid2str($build2->primaryKey),
            'employee_history_build_deactive' => date('Y-m-d', strtotime('+1 day')),
        ]);
        $employeeBuildUpdateForm->validate();
        $service->update($employeeHistoryBuild->primaryKey, $employeeBuildUpdateForm);
        $this->tester->seeNumRecords(2, 'employee_history_build');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_id', ['ehb_id' => 2]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'build_id', ['ehb_id' => 2]) === $build2->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_build_deactive', ['ehb_id' => 2]) === date('Y-m-d', strtotime('+1 day')));
    }

    public function testDeleteEmployeeHistoryBuild()
    {
        /** @var EmployeeBuildService $service */
        $service = Yii::createObject('domain\services\base\EmployeeBuildService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'build' => [
                'class' => BuildFixture::className(),
            ],
            'employeeHistoryBuild' => [
                'class' => EmployeeHistoryBuildFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/employee_history_build_for_update.php',
            ],
        ]);
        /** @var Build $build1 */
        $build1 = $this->tester->grabFixture('build', 0);
        /** @var EmployeeHistoryBuild $employeeHistoryBuild */
        $employeeHistoryBuild = $this->tester->grabFixture('employeeHistoryBuild', 1);
        $service->delete($employeeHistoryBuild->primaryKey);

        $this->tester->seeNumRecords(1, 'employee_history_build');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_id', ['ehb_id' => 1]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'build_id', ['ehb_id' => 1]) === $build1->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('employee_history_build', 'employee_history_build_deactive', ['ehb_id' => 1]) === null);
    }
}