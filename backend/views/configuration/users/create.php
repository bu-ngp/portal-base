<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\HeaderPanel\HeaderPanel;
use common\widgets\Html\Html;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;

/* @var $this yii\web\View */
/* @var $modelUserForm domain\forms\base\UserForm */
/* @var $modelProfileForm domain\forms\base\ProfileForm */
/* @var $searchModelEmployee domain\models\base\search\EmployeeSearch */
/* @var $searchModelAuthItem domain\models\base\search\AuthItemSearch */
/* @var $dataProviderEmployee yii\data\ActiveDataProvider */
/* @var $dataProviderAuthItem yii\data\ActiveDataProvider */

$this->title = Yii::t('common/person', 'Create User');
?>
<div class="user-create content-container">
    <?= HeaderPanel::widget(['title' => Html::encode($this->title)]) ?>

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

        <div class="form-group toolbox-form-group">
            <?= Html::nextButton(['form' => $modelUserForm->formName()]) ?>
        </div>

    </div>
</div>