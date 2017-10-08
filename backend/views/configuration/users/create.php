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

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('common/employee', 'User'),
                    'content' => Panel::widget([
                        'label' => Yii::t('common/employee', 'User'),
                        'content' => $this->render('_personForm', ['modelUserForm' => $modelUserForm, 'userForm' => $userForm]),
                    ]),
                ],
                [
                    'label' => Yii::t('common/employee', 'Profile'),
                    'content' => Panel::widget([
                        'label' => Yii::t('common/employee', 'Profile'),
                        'content' => $this->render('_profileForm', ['modelProfileForm' => $modelProfileForm, 'profileForm' => $userForm]),
                    ]),
                ],
            ],
        ]) ?>

        <?php ActiveForm::end(); ?>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('common/employee', 'Employees'),
                    'content' => $this->render('_employeeGrid', [
                        'searchModel' => $searchModelEmployee,
                        'dataProvider' => $dataProviderEmployee,
                        'gridConfig' => [
                            'crudSettings' => [
                                'create' => [
                                    'urlGrid' => 'configuration/employee/create',
                                    'inputName' => 'UserForm[assignEmployees]',
                                ],
                                'delete' => [
                                    'inputName' => 'UserForm[assignEmployees]',
                                ],
                            ],
                        ],
                    ]),
                ],
                [
                    'label' => Yii::t('common/employee', 'Roles'),
                    'content' => $this->render('_roleGrid', [
                        'searchModel' => $searchModelAuthItem,
                        'dataProvider' => $dataProviderAuthItem,
                        'gridConfig' => [
                            'crudSettings' => [
                                'create' => [
                                    'urlGrid' => 'configuration/role/index-for-user',
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
            <?= Html::submitButton(Yii::t('common/podraz', 'Create'), ['class' => 'btn btn-success', 'form' => $modelUserForm->formName()]) ?>
        </div>

        <?php
        $this->registerJs(<<<EOT
            findErrors = function() {
                var tabError = $('#{$modelUserForm->formName()}').find('div.has-error').first().parents('div.tab-pane');     
                
                if (tabError.length) {
                    $('a[href="#' + tabError.attr('id') + '"]').click();
                } 
            }
        
            findErrors();
            
            $('button[type="submit"]').on('click', function() {
                setTimeout(function() {
                    findErrors();
                }, 2000);
            });
EOT
        )
        ?>

    </div>
</div>