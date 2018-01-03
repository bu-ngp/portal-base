<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 19.10.2017
 * Time: 9:58
 */

namespace domain\forms\base;


use domain\models\base\ParttimeBuild;
use domain\rules\base\ParttimeBuildRules;
use domain\validators\Str2UUIDValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ParttimeBuildUpdateForm extends Model
{
    private $parttime_id;
    public $build_id;
    public $parttime_build_deactive;

    public function __construct(ParttimeBuild $parttimeBuild, $config = [])
    {
        $this->parttime_id = $parttimeBuild->parttime_id;
        $this->build_id = $parttimeBuild->build_id;
        $this->parttime_build_deactive = $parttimeBuild->parttime_build_deactive;

        parent::__construct($config);
    }

    public function rules()
    {
        return ArrayHelper::merge(ParttimeBuildRules::client(), [
            [['!parttime_id'], 'required'],
            [['build_id'], Str2UUIDValidator::className()],
        ]);
    }

    public function attributeLabels()
    {
        return (new ParttimeBuild())->attributeLabels();
    }

    public function getParttime_id()
    {
        return $this->parttime_id;
    }
}