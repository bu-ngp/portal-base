<?php
use yii\bootstrap\ActiveForm;

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
                   data-toggle="tab"><?= Yii::t('domain/authitem', 'Additional') ?></a></li>
        </ul>
    </div>
    <div class="pmd-card-body">
        <?php $form = ActiveForm::begin(); ?>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main">

                <?= $form->field($filterModel, 'authitem_system_roles_mark')->checkbox(['label' => "<span>{$filterModel->getAttributeLabel('authitem_system_roles_mark')}</span>", 'labelOptions' => ['class' => 'pmd-checkbox'], 'template' => "<div class=\"checkbox pmd-default-theme\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>"]) ?>

                <?= $form->field($filterModel, 'authitem_users_roles_mark')->checkbox(['label' => "<span>{$filterModel->getAttributeLabel('authitem_users_roles_mark')}</span>", 'labelOptions' => ['class' => 'pmd-checkbox'], 'template' => "<div class=\"checkbox pmd-default-theme\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>"]) ?>

            </div>
            <div role="tabpanel" class="tab-pane" id="additional">
                <?= $form->field($filterModel, 'authitem_name', ['options' => ['class' => 'form-group pmd-textfield pmd-textfield-floating-label']])->textInput() ?>
            </div>

        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>