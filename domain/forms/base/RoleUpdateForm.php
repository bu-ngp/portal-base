<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use domain\models\base\AuthItem;
use domain\rules\base\RoleRules;
use Yii;
use yii\base\Model;

class RoleUpdateForm extends Model
{
    public $description;
    public $ldap_group;

    private $authItem;

    public function __construct(AuthItem $authItem = null, $config = [])
    {
        $this->authItem = $authItem;
        $this->description = $authItem->description;
        $this->ldap_group = $authItem->ldap_group;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return RoleRules::client();
    }

    public function attributeLabels()
    {
        return (new AuthItem())->attributeLabels();
    }

    public function getPrimaryKey()
    {
        return $this->authItem->primaryKey;
    }
}