<?php
use common\widgets\ActiveForm\ActiveFilterForm;

/**
 * @var \yii\base\Model $filterModel
 */
?>
<div class="pmd-card pmd-z-depth">
    <div class="pmd-tabs">
        <div class="pmd-tab-active-bar"></div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#main" aria-controls="main" role="tab"
                   data-toggle="tab"><?= Yii::t('domain/authitem', 'Main') ?></a>
            </li>
            <li role="presentation">
                <a href="#additional" aria-controls="additional" role="tab"
                   data-toggle="tab"><?= Yii::t('domain/authitem', 'Additional') ?></a>
            </li>
        </ul>
    </div>
    <div class="pmd-card-body">
        <?php $form = ActiveFilterForm::begin(); ?>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="main">
                <div class="panel panel-default pmd-z-depth">
                    <div class="panel-heading">
                        <h3 class="panel-title">Системные</h3>
                    </div>
                    <div class="panel-body">

                        <?= $form->field($filterModel, 'authitem_system_roles_mark')->checkbox() ?>

                    </div>
                </div>
                <div class="panel panel-default pmd-z-depth">
                    <div class="panel-heading">
                        <h3 class="panel-title">Пользовательские</h3>
                    </div>
                    <div class="panel-body">

                        <?= $form->field($filterModel, 'authitem_users_roles_mark')->checkbox() ?>

                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="additional">
                <?= $form->field($filterModel, 'authitem_name')->textInput() ?>
            </div>

        </div>
        <?php ActiveFilterForm::end(); ?>
    </div>
</div>