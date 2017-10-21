<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\Panel\Panel;
use common\widgets\Tabs\Tabs;
use wartron\yii2uuid\helpers\Uuid;
use yii\bootstrap\Html;
use yii\helpers\Url;

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
                            'customActionButtons' => [
                                'customUpdate' => function ($url, $model) {
                                    $customurl = Url::to([$model['employee_type'] == 1 ? 'configuration/employee/update' : 'configuration/parttime/update', 'id' => $model['primary_key']]);
                                    return Html::a('<i class="fa fa-2x fa-pencil-square-o"></i>', $customurl, ['title' => Yii::t('wk-widget-gridview', 'Update'), 'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-primary', 'data-pjax' => '0']);
                                },
                                'customDelete' => function ($url, $model) {
                                    $urlArr = Url::to([$model['employee_type'] == 1 ? 'configuration/employee/delete' : 'configuration/parttime/delete', 'id' => $model['primary_key']]);

                                    return Html::a('<i class="fa fa-2x fa-trash-o"></i>', $urlArr, [
                                        'title' => Yii::t('wk-widget-gridview', 'Delete'),
                                        'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-danger wk-gridview-crud-delete',
                                        'data-pjax' => '0'
                                    ]);
                                }
                            ],
                            'toolbar' => [
                                'content' => \yii\helpers\Html::a('Добавить основную специальность', ['configuration/employee/create', 'person' => Yii::$app->request->get('id')], ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0']) .
                                    \yii\helpers\Html::a('Добавить совмещение', ['configuration/parttime/create', 'person' => Yii::$app->request->get('id')], ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0'])
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