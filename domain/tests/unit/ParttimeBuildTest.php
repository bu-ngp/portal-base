<?php

namespace domain\tests;


use domain\forms\base\ParttimeBuildForm;
use domain\forms\base\ParttimeBuildUpdateForm;
use domain\models\base\Build;
use domain\models\base\ParttimeBuild;
use domain\services\base\ParttimeBuildService;
use domain\tests\fixtures\BuildFixture;
use domain\tests\fixtures\ParttimeBuildFixture;
use domain\tests\fixtures\PersonFixture;
use wartron\yii2uuid\helpers\Uuid;
use Yii;

class ParttimeBuildTest extends \Codeception\Test\Unit
{
    /**
     * @var \domain\tests\UnitTester
     */
    protected $tester;

    public function testValidateForms()
    {
        $this->tester->haveFixtures([
            'parttimeBuildFixture' => [
                'class' => ParttimeBuildFixture::className(),
            ],
        ]);
        /** @var ParttimeBuild $parttimeBuild */
        $parttimeBuild = $this->tester->grabFixture('parttimeBuildFixture', 0);

        $parttimeBuildForm = new ParttimeBuildForm([
            'build_id' => 'NFKDSNFKJDSNFDSJN1',
        ]);
        $parttimeBuildForm->validate();
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('build_id') === "Не валидная UUID строка");

        $parttimeBuildUpdateForm = new ParttimeBuildUpdateForm($parttimeBuild, [
            'build_id' => 'NFKDSNFKJDSNFDSJN1',
        ]);
        $parttimeBuildUpdateForm->validate();
        $this->tester->assertTrue($parttimeBuildUpdateForm->getFirstError('build_id') === "Не валидная UUID строка");
    }

    public function testCreateParttimeBuild()
    {
        /** @var ParttimeBuildService $service */
        $service = Yii::createObject('domain\services\base\ParttimeBuildService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'build' => [
                'class' => BuildFixture::className(),
            ],
            'parttimeBuildFixture' => [
                'class' => ParttimeBuildFixture::className(),
            ],
        ]);
        /** @var Build $build1 */
        $build1 = $this->tester->grabFixture('build', 0);
        /** @var Build $build2 */
        $build2 = $this->tester->grabFixture('build', 1);

        $parttimeBuildForm = new ParttimeBuildForm();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeBuildForm) {
            $service->create($parttimeBuildForm);
        });
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('parttime_id') === "Необходимо заполнить «Совмещение».");
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('build_id') === "Необходимо заполнить «Здание».");
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('parttime_build_deactive') === null);

        $parttimeBuildForm = new ParttimeBuildForm([
            'parttime_id' => 2,
            'build_id' => 'KLJGFGKDFKJGLFJD',
            'parttime_build_deactive' => '2017/12/01',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeBuildForm) {
            $service->create($parttimeBuildForm);
        });
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('parttime_id') === "Значение «Совмещение» неверно.");
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('build_id') === "Значение «Здание» неверно.");
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('parttime_build_deactive') === "Неверный формат значения «Дата с которой здание неактивно».");

        $parttimeBuildForm = new ParttimeBuildForm([
            'parttime_id' => 1,
            'build_id' => Uuid::uuid2str($build1->primaryKey),
        ]);
        $parttimeBuildForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeBuildForm) {
            $service->create($parttimeBuildForm);
        });
        $this->tester->assertTrue($parttimeBuildForm->getFirstError('build_id') === "У текущего совмещения уже имеется данное здание");

        $parttimeBuildForm = new ParttimeBuildForm([
            'parttime_id' => 1,
            'build_id' => Uuid::uuid2str($build2->primaryKey),
            'parttime_build_deactive' => date('Y-m-d'),
        ]);
        $parttimeBuildForm->validate();
        $service->create($parttimeBuildForm);
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_id', ['pb' => 2]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'build_id', ['pb' => 2]) === $build2->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_build_deactive', ['pb' => 2]) === date('Y-m-d'));
    }

    public function testUpdateParttimeBuild()
    {
        /** @var ParttimeBuildService $service */
        $service = Yii::createObject('domain\services\base\ParttimeBuildService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'build' => [
                'class' => BuildFixture::className(),
            ],
            'parttimeBuild' => [
                'class' => ParttimeBuildFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/parttime_build_for_update.php',
            ],
        ]);
        /** @var Build $build1 */
        $build1 = $this->tester->grabFixture('build', 0);
        /** @var Build $build2 */
        $build2 = $this->tester->grabFixture('build', 1);
        /** @var ParttimeBuild $parttimeBuild */
        $parttimeBuild = $this->tester->grabFixture('parttimeBuild', 1);

        $parttimeBuildUpdateForm = new ParttimeBuildUpdateForm($parttimeBuild, [
            'build_id' => 'KLJGFGKDFKJGLFJD',
            'parttime_build_deactive' => '2017/12/01',
        ]);
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeBuild, $parttimeBuildUpdateForm) {
            $service->update($parttimeBuild->primaryKey, $parttimeBuildUpdateForm);
        });
        $this->tester->assertTrue($parttimeBuildUpdateForm->getFirstError('build_id') === "Значение «Здание» неверно.");
        $this->tester->assertTrue($parttimeBuildUpdateForm->getFirstError('parttime_build_deactive') === "Неверный формат значения «Дата с которой здание неактивно».");

        $parttimeBuildUpdateForm = new ParttimeBuildUpdateForm($parttimeBuild, [
            'build_id' => Uuid::uuid2str($build1->primaryKey),
        ]);
        $parttimeBuildUpdateForm->validate();
        $this->tester->expectException(new \DomainException(), function () use ($service, $parttimeBuild, $parttimeBuildUpdateForm) {
            $service->update($parttimeBuild->primaryKey, $parttimeBuildUpdateForm);
        });
        $this->tester->assertTrue($parttimeBuildUpdateForm->getFirstError('build_id') === "У текущего совмещения уже имеется данное здание");

        $parttimeBuildUpdateForm = new ParttimeBuildUpdateForm($parttimeBuild, [
            'build_id' => Uuid::uuid2str($build2->primaryKey),
            'parttime_build_deactive' => date('Y-m-d', strtotime('+1 day')),
        ]);
        $parttimeBuildUpdateForm->validate();
        $service->update($parttimeBuild->primaryKey, $parttimeBuildUpdateForm);
        $this->tester->seeNumRecords(2, 'parttime_build');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_id', ['pb' => 2]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'build_id', ['pb' => 2]) === $build2->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_build_deactive', ['pb' => 2]) === date('Y-m-d', strtotime('+1 day')));
    }

    public function testDeleteParttimeBuild()
    {
        /** @var ParttimeBuildService $service */
        $service = Yii::createObject('domain\services\base\ParttimeBuildService');
        $this->tester->haveFixtures([
            'person' => [
                'class' => PersonFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/person_with_employee.php',
            ],
            'build' => [
                'class' => BuildFixture::className(),
            ],
            'parttimeBuild' => [
                'class' => ParttimeBuildFixture::className(),
                'dataFile' => '@domain/tests/fixtures/data/parttime_build_for_update.php',
            ],
        ]);
        /** @var Build $build1 */
        $build1 = $this->tester->grabFixture('build', 0);
        /** @var ParttimeBuild $parttimeBuild */
        $parttimeBuild = $this->tester->grabFixture('parttimeBuild', 1);
        $service->delete($parttimeBuild->primaryKey);

        $this->tester->seeNumRecords(1, 'parttime_build');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_id', ['pb' => 1]) === '1');
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'build_id', ['pb' => 1]) === $build1->primaryKey);
        $this->assertTrue($this->tester->grabFromDatabase('parttime_build', 'parttime_build_deactive', ['pb' => 1]) === null);
    }
}