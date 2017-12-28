<?php

namespace domain\tests;


use Codeception\Test\Unit;
use domain\forms\base\DolzhForm;
use domain\models\base\Dolzh;
use domain\repositories\base\DolzhRepository;
use domain\services\base\DolzhService;
use domain\tests\fixtures\DolzhFixture;
use Yii;
use yii\codeception\DbTestCase;

class DolzhTest extends Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;
//
//    public function fixtures() {
//        return [
//            'dolzhs' => DolzhFixture::className(),
//        ];
//    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }

//    public static function tearDownAfterClass()
//    {
//
//    }

    public function testCreate()
    {
        $service = Yii::createObject('domain\services\base\DolzhService');
        $form = Yii::createObject('domain\forms\base\DolzhForm');
        $form->dolzh_name = 'Программист';

        $service->create($form);
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('{{%dolzh}}', ['dolzh_name' => mb_strtoupper($form->dolzh_name, 'UTF-8')]);
    }

    public function testEmpty()
    {
        $service = Yii::createObject('domain\services\base\DolzhService');
        $form = Yii::createObject('domain\forms\base\DolzhForm');
        $form->dolzh_name = '';
        $errorMessage = "Необходимо заполнить «{$form->getAttributeLabel('dolzh_name')}».";

        $this->tester->expectException(new \DomainException, function () use ($service, $form) {
            $service->create($form);
        });

        $this->assertTrue($form->getErrors()['dolzh_name'][0] === $errorMessage);
        $this->tester->seeNumRecords(0, '{{%dolzh}}');
    }

    public function testCreateUnique()
    {
        $this->tester->haveFixtures([DolzhFixture::className()]);
        $service = Yii::createObject('domain\services\base\DolzhService');
        $form = Yii::createObject('domain\forms\base\DolzhForm');
        $form->dolzh_name = 'Программист';
        $valueResult = mb_strtoupper($form->dolzh_name, 'UTF-8');
        $errorMessage = "Значение «" . $valueResult . "» для «{$form->getAttributeLabel('dolzh_name')}» уже занято.";

        $this->tester->expectException(new \DomainException, function () use ($service, $form) {
            $service->create($form);
        });

        $this->assertTrue($form->getErrors()['dolzh_name'][0] === $errorMessage);
        $this->tester->seeInDatabase('{{%dolzh}}', ['dolzh_name' => mb_strtoupper($form->dolzh_name, 'UTF-8')]);
        $this->tester->seeNumRecords(1, '{{%dolzh}}', ['dolzh_name' => $form->dolzh_name]);
    }

    public function testUpdate()
    {
        $this->tester->haveFixtures([
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
        ]);
        $service = Yii::createObject('domain\services\base\DolzhService');
        $dolzh_id = $this->tester->grabFixture('dolzh', 0);
        $dolzh = Dolzh::findOne($dolzh_id);
        $form = Yii::createObject('domain\forms\base\DolzhForm', [$dolzh]);
        $form->dolzh_name = 'Системный администратор';

        $service->update($dolzh_id, $form);
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('{{%dolzh}}', ['dolzh_name' => mb_strtoupper($form->dolzh_name, 'UTF-8')]);
    }

    public function testDelete()
    {
        $this->tester->haveFixtures([
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
        ]);
        $service = Yii::createObject('domain\services\base\DolzhService');
        $dolzh_id = $this->tester->grabFixture('dolzh', 0);

        $service->delete($dolzh_id);
        $this->tester->seeNumRecords(0, '{{%dolzh}}');
    }
}