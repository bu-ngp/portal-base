<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $modelUserFormUpdate domain\forms\base\UserFormUpdate */
/* @var $modelProfileForm domain\forms\base\ProfileForm */
/* @var $searchModelEmployee domain\models\base\search\EmployeeSearch */
/* @var $searchModelAuthItem domain\models\base\search\AuthItemSearch */
/* @var $dataProviderEmployee yii\data\ActiveDataProvider */
/* @var $dataProviderAuthItem yii\data\ActiveDataProvider */

$this->title = Yii::t('common/person', $modelUserFormUpdate->person_fullname);
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">
        <?php $userForm = ActiveForm::begin(['id' => $modelUserFormUpdate->formName()]); ?>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('common/employee', 'User'),
                    'content' => Panel::widget([
                        'label' => Yii::t('common/employee', 'User'),
                        'content' => $this->render('_personFormUpdate', ['modelUserFormUpdate' => $modelUserFormUpdate, 'userForm' => $userForm]),
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
                                'create' => ['configuration/employee/create', 'person_id' => Yii::$app->request->get('id')],
                                'update' => 'configuration/employee/update',
                                'delete' => 'configuration/employee/delete',
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
                                'create' => 'configuration/roles/index-for-users',
                                'delete' => 'configuration/roles/delete',
                            ],
                        ],
                    ]),
                ],
            ],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary', 'form' => $modelUserFormUpdate->formName()]) ?>
        </div>

        <?php
        $this->registerJs(<<<EOT
                findErrors = function() {
                    var tabError = $('#{$modelUserFormUpdate->formName()}').find('div.has-error').first().parents('div.tab-pane');     
                    
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