<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 11:17
 */

namespace domain\forms\base;

use domain\models\base\AuthItem;
use Yii;
use yii\base\Model;

class RoleUpdateForm extends Model
{
    public $description;

    private $authItem;

    public function __construct(AuthItem $authItem = null, $config = [])
    {
        $this->authItem = $authItem;
        $this->load($authItem->attributes, '');
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => Yii::t('common/authitem', 'Description'),
        ];
    }

    public function getPrimaryKey()
    {
        return $this->authItem->primaryKey;
    }
}