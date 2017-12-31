<?php

namespace domain\tests;


use domain\forms\base\EmployeeHistoryForm;
use domain\forms\base\PodrazForm;
use domain\models\base\Dolzh;
use domain\models\base\Person;
use domain\models\base\Podraz;
use domain\services\base\EmployeeHistoryService;
use domain\tests\fixtures\DolzhFixture;
use domain\tests\fixtures\PersonFixture;
use domain\tests\fixtures\PodrazFixture;
use Yii;

class EmployeeTest extends \Codeception\Test\Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

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
        ]);
        /** @var Person $person */
        $person = $this->tester->grabFixture('person', 'user1');
        /** @var Dolzh $dolzh */
        $dolzh = $this->tester->grabFixture('dolzh', 0);
        /** @var Podraz $podraz */
        $podraz = $this->tester->grabFixture('podraz', 0);
        $employeeHistoryForm = new EmployeeHistoryForm(null, false, [
            'person_id' => $person->primaryKey,
            'dolzh_id' => $dolzh->primaryKey,
            'podraz_id' => $podraz->primaryKey,
            'employee_history_begin' => date('Y-m-d'),
            'assignBuilds' => '[]',
        ]);
        $this->tester->expectException(new \DomainException, function () use ($service, $employeeHistoryForm) {
            $service->create($employeeHistoryForm);
        });

    }
}