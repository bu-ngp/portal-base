<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 08.10.2017
 * Time: 13:53
 */

namespace domain\tests\fixtures;


use yii\test\ActiveFixture;

class ProfileFixture extends ActiveFixture
{
    public $modelClass = 'domain\models\base\Profile';
    public $depends = ['domain\tests\fixtures\PersonFixture'];
}