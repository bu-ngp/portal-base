<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.10.2017
 * Time: 13:10
 */
/* @var $this yii\web\View */
/* @var $searchModel domain\models\base\search\UsersSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\GridView\GridView;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

Breadcrumbs::root();
$this->title = Yii::t('common/contacts', 'Contacts');
?>

<div class="contacts-index content-container">

    <h1><?= FA::icon(FA::_PHONE_SQUARE) . Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'customizeDialog' => false,
        'columns' => [
            'person_fullname',
            'profile.profile_internal_phone',
            [
                'attribute' => 'profile.profile_phone',
                'value' => function ($model) {
                    return preg_match('/\d(\d{4})(\d{2})(\d{2})(\d{2})/', $model->profile->profile_phone, $matches)
                        ? "8-({$matches[1]})-{$matches[2]}-{$matches[3]}-{$matches[4]}"
                        : $model->profile->profile_phone;
                }
            ],
            'person_email',
            [
                'attribute' => 'employee.dolzh.dolzh_name',
                'label' => Yii::t('domain/employee', 'Dolzh ID'),
                'noWrap' => false,
            ],
            [
                'attribute' => 'employee.podraz.podraz_name',
                'label' => Yii::t('domain/employee', 'Podraz ID'),
                'noWrap' => false,
            ],
        ],
        'panelHeading' => [
            'title' => '',
        ],
    ]);
    ?>
</div>