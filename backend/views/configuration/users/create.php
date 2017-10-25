<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelUserForm domain\forms\base\UserForm */
/* @var $modelProfileForm domain\forms\base\ProfileForm */
/* @var $searchModelEmployee domain\models\base\search\EmployeeSearch */
/* @var $searchModelAuthItem domain\models\base\search\AuthItemSearch */
/* @var $dataProviderEmployee yii\data\ActiveDataProvider */
/* @var $dataProviderAuthItem yii\data\ActiveDataProvider */

$this->title = Yii::t('common/person', 'Create User');
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">
        <?php $userForm = ActiveForm::begin(['id' => $modelUserForm->formName()]); ?>

        <?= Panel::widget([
            'label' => Yii::t('common/person', 'User'),
            'content' => $this->render('_personForm', ['modelUserForm' => $modelUserForm, 'userForm' => $userForm]),
        ]) ?>

        <?= Panel::widget([
            'label' => Yii::t('common/person', 'Profile'),
            'content' => $this->render('_profileForm', ['modelProfileForm' => $modelProfileForm, 'profileForm' => $userForm]),
        ]) ?>

        <?php ActiveForm::end(); ?>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('common/person', 'Roles'),
                    'content' => $this->render('_roleGrid', [
                        'searchModel' => $searchModelAuthItem,
                        'dataProvider' => $dataProviderAuthItem,
                        'gridConfig' => [
                            'crudSettings' => [
                                'create' => [
                                    'urlGrid' => 'configuration/roles/index-for-users',
                                    'inputName' => 'UserForm[assignRoles]',
                                ],
                                'delete' => [
                                    'inputName' => 'UserForm[assignRoles]',
                                ],
                            ],
                        ],
                    ]),
                ],
            ],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Next'), ['class' => 'btn btn-primary', 'form' => $modelUserForm->formName()]) ?>
        </div>

    </div>
</div>