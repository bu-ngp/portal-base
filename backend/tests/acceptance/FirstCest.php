<?php
namespace backend\tests;
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

class FirstCest
{
    public function login(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('site/acceptance-test'));
        $I->see('Приемочные тесты');
    }
}
