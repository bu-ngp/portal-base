<?php

namespace domain\tests;


use Codeception\Test\Unit;
use domain\models\base\Podraz;
use domain\tests\fixtures\PodrazFixture;
use Yii;

class PodrazTest extends Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testCreate()
    {
        $service = Yii::createObject('domain\services\base\PodrazService');
        $form = Yii::createObject('domain\forms\base\PodrazForm');
        $form->podraz_name = 'Терапевтическое отделение №1';

        $service->create($form);
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('{{%podraz}}', ['podraz_name' => mb_strtoupper($form->podraz_name, 'UTF-8')]);
    }

    public function testEmpty()
    {
        $service = Yii::createObject('domain\services\base\PodrazService');
        $form = Yii::createObject('domain\forms\base\PodrazForm');
        $form->podraz_name = '';
        $errorMessage = "Необходимо заполнить «{$form->getAttributeLabel('podraz_name')}».";

        $this->tester->expectException(new \DomainException, function () use ($service, $form) {
            $service->create($form);
        });

        $this->assertTrue($form->getErrors()['podraz_name'][0] === $errorMessage);
        $this->tester->seeNumRecords(0, '{{%podraz}}');
    }

    public function testCreateUnique()
    {
        $this->tester->haveFixtures([PodrazFixture::className()]);
        $service = Yii::createObject('domain\services\base\PodrazService');
        $form = Yii::createObject('domain\forms\base\PodrazForm');
        $form->podraz_name = 'Терапевтическое отделение №1';
        $valueResult = mb_strtoupper($form->podraz_name, 'UTF-8');
        $errorMessage = "Значение «" . $valueResult . "» для «{$form->getAttributeLabel('podraz_name')}» уже занято.";

        $this->tester->expectException(new \DomainException, function () use ($service, $form) {
            $service->create($form);
        });

        $this->assertTrue($form->getErrors()['podraz_name'][0] === $errorMessage);
        $this->tester->seeInDatabase('{{%podraz}}', ['podraz_name' => mb_strtoupper($form->podraz_name, 'UTF-8')]);
        $this->tester->seeNumRecords(1, '{{%podraz}}', ['podraz_name' => $form->podraz_name]);
    }

    public function testUpdate()
    {
        $this->tester->haveFixtures([
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
        ]);
        $service = Yii::createObject('domain\services\base\PodrazService');
        $podraz_id = $this->tester->grabFixture('podraz', 0);
        $podraz = Podraz::findOne($podraz_id);
        $form = Yii::createObject('domain\forms\base\PodrazForm', [$podraz]);
        $form->podraz_name = 'Терапевтическое отделение №2';

        $service->update($podraz_id, $form);
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('{{%podraz}}', ['podraz_name' => mb_strtoupper($form->podraz_name, 'UTF-8')]);
    }

    public function testDelete()
    {
        $this->tester->haveFixtures([
            'podraz' => [
                'class' => PodrazFixture::className(),
            ],
        ]);
        $service = Yii::createObject('domain\services\base\PodrazService');
        $podraz_id = $this->tester->grabFixture('podraz', 0);

        $service->delete($podraz_id);
        $this->tester->seeNumRecords(0, '{{%podraz}}');
    }
}