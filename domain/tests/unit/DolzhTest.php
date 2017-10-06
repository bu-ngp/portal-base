<?php


use domain\repositories\base\DolzhRepository;
use domain\services\base\DolzhService;

class DolzhTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testCreateDolzh($a)
    {
        $service = new DolzhService(new DolzhRepository());

        $service->create('Программист');

        $this->tester->seeInDatabase('dolzh', array('dolzh_name' => 'Программист'));
    }
}