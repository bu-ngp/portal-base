<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 15.05.2017
 * Time: 19:28
 */

namespace common\widgets\CardList;


class Module extends \yii\base\Module
{
    public $cardlistTable = '{{%cardlist}}';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->id = 'cardlist';
        parent::init();

    }
}