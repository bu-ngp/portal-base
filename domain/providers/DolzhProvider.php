<?php
namespace domain\providers;

use domain\models\base\Dolzh;
use Yii;

/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 11.10.2017
 * Time: 16:27
 */
class DolzhProvider extends \yii\data\ActiveDataProvider
{
    public function __construct(array $config = [])
    {
        $this->query = (new Dolzh())->find();

        $model = new $this->query->modelClass;

        $model->load(Yii::$app->request->queryParams);

        $this->query->andFilterWhere(['like', 'dolzh_id', $model->dolzh_id])
            ->andFilterWhere(['like', 'dolzh_name',  $model->dolzh_name]);

        return parent::__construct($config);
    }


}