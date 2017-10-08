<?php
namespace domain\tests;


use domain\forms\base\DolzhForm;
use domain\models\base\Dolzh;
use domain\repositories\base\DolzhRepository;
use domain\services\base\DolzhService;
use domain\tests\fixtures\DolzhFixture;
use yii\codeception\DbTestCase;

class DolzhTest extends DbTestCase
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
        $service = new DolzhService(new DolzhRepository());
        $form = new DolzhForm();
        $form->dolzh_name = 'Программист';

        $this->assertTrue($service->create($form));
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('dolzh', ['dolzh_name' => mb_strtoupper($form->dolzh_name, 'UTF-8')]);
    }

    public function testEmpty()
    {
        $service = new DolzhService(new DolzhRepository());
        $form = new DolzhForm();
        $form->dolzh_name = '';

        $this->assertFalse($service->create($form));
        $this->assertTrue($form->getErrors()['dolzh_name'][0] === "Необходимо заполнить «{$form->getAttributeLabel('dolzh_name')}».");
        $this->tester->seeNumRecords(0, 'dolzh');
    }

    public function testCreateUnique()
    {
        $this->tester->haveFixtures([DolzhFixture::className()]);
        $service = new DolzhService(new DolzhRepository());
        $form = new DolzhForm();
        $form->dolzh_name = 'Программист';

        $this->assertFalse($service->create($form));
        $valueResult = mb_strtoupper($form->dolzh_name, 'UTF-8');
        $this->assertTrue($form->getErrors()['dolzh_name'][0] === "Значение «" . $valueResult . "» для «{$form->getAttributeLabel('dolzh_name')}» уже занято.");
        $this->tester->seeInDatabase('dolzh', ['dolzh_name' => mb_strtoupper($form->dolzh_name, 'UTF-8')]);
        $this->tester->seeNumRecords(1, 'dolzh', ['dolzh_name' => $form->dolzh_name]);
    }

    public function testUpdate()
    {
        $this->tester->haveFixtures([
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
        ]);
        $service = new DolzhService(new DolzhRepository());
        $dolzh_id = $this->tester->grabFixture('dolzh', 0);
        $dolzh = Dolzh::findOne($dolzh_id);
        $form = new DolzhForm($dolzh);
        $form->dolzh_name = 'Системный администратор';

        $this->assertTrue($service->update($dolzh_id, $form));
        $this->assertEmpty($form->getErrors());
        $this->tester->seeInDatabase('dolzh', ['dolzh_name' => mb_strtoupper($form->dolzh_name, 'UTF-8')]);
    }

    public function testDelete()
    {
        $this->tester->haveFixtures([
            'dolzh' => [
                'class' => DolzhFixture::className(),
            ],
        ]);
        $service = new DolzhService(new DolzhRepository());
        $dolzh_id = $this->tester->grabFixture('dolzh', 0);

        $service->delete($dolzh_id);
        $this->tester->seeNumRecords(0, 'dolzh');
    }
}