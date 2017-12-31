<?php
namespace domain\tests;


use domain\models\base\Build;
use domain\tests\fixtures\BuildFixture;
use Yii;

class BuildTest extends \Codeception\Test\Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testCreate()
    {
        $service = Yii::createObject('domain\services\base\BuildService');
        $form = Yii::createObject('domain\forms\base\BuildForm');
        $form->build_name = 'Взрослая поликлиника №1';

        $service->create($form);
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('build', ['build_name' => mb_strtoupper($form->build_name, 'UTF-8')]);
    }

    public function testEmpty()
    {
        $service = Yii::createObject('domain\services\base\BuildService');
        $form = Yii::createObject('domain\forms\base\BuildForm');
        $form->build_name = '';
        $errorMessage = "Необходимо заполнить «{$form->getAttributeLabel('build_name')}».";

        $this->tester->expectException(new \DomainException, function () use ($service, $form) {
            $service->create($form);
        });

        $this->assertTrue($form->getErrors()['build_name'][0] === $errorMessage);
        $this->tester->seeNumRecords(0, 'build');
    }

    public function testCreateUnique()
    {
        $this->tester->haveFixtures([BuildFixture::className()]);
        $service = Yii::createObject('domain\services\base\BuildService');
        $form = Yii::createObject('domain\forms\base\BuildForm');
        $form->build_name = 'Взрослая поликлиника №1';
        $valueResult = mb_strtoupper($form->build_name, 'UTF-8');
        $errorMessage = "Значение «" . $valueResult . "» для «{$form->getAttributeLabel('build_name')}» уже занято.";

        $this->tester->expectException(new \DomainException, function () use ($service, $form) {
            $service->create($form);
        });

        $this->assertTrue($form->getErrors()['build_name'][0] === $errorMessage);
        $this->tester->seeInDatabase('build', ['build_name' => mb_strtoupper($form->build_name, 'UTF-8')]);
        $this->tester->seeNumRecords(1, 'build', ['build_name' => $form->build_name]);
    }

    public function testUpdate()
    {
        $this->tester->haveFixtures([
            'build' => [
                'class' => BuildFixture::className(),
            ],
        ]);
        $service = Yii::createObject('domain\services\base\BuildService');
        /** @var Build $build */
        $build = $this->tester->grabFixture('build', 0);
        $form = Yii::createObject('domain\forms\base\BuildForm', [$build]);
        $form->build_name = 'Взрослая поликлиника №2';

        $service->update($build->primaryKey, $form);
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('build', ['build_name' => mb_strtoupper($form->build_name, 'UTF-8')]);
    }

    public function testDelete()
    {
        $this->tester->haveFixtures([
            'build' => [
                'class' => BuildFixture::className(),
            ],
        ]);
        $service = Yii::createObject('domain\services\base\BuildService');
        /** @var Build $build */
        $build = $this->tester->grabFixture('build', 0);

        $service->delete($build->primaryKey);
        $this->tester->seeNumRecords(0, 'build');
    }
}