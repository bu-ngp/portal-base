<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 19.10.2017
 * Time: 9:58
 */

namespace domain\forms\base;


use domain\models\base\EmployeeHistoryBuild;
use domain\models\base\ParttimeBuild;
use domain\rules\base\EmployeeHistoryBuildRules;
use domain\rules\base\ParttimeBuildRules;
use Yii;
use yii\base\Model;

class ParttimeBuildForm extends Model
{
    public $parttime_id;
    public $build_id;
    public $parttime_build_deactive;

    public function __construct(ParttimeBuild $parttimeBuild = null, $config = [])
    {
        if ($parttimeBuild) {
            $this->load($parttimeBuild->attributes, '');
        } else {
            $this->parttime_id = Yii::$app->request->get('employee');
        }

        parent::__construct($config);
    }

    public function rules()
    {
        return ParttimeBuildRules::client();
    }
}