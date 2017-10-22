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
            $this->parttime_id = $parttimeBuild->parttime_id;
            $this->build_id = $parttimeBuild->build_id;
            $this->parttime_build_deactive = $parttimeBuild->parttime_build_deactive;
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