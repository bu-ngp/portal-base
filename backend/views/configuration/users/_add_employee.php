<?php use yii\bootstrap\Html; ?>
<span class="dropdown pmd-dropdown">
    <button class="btn pmd-ripple-effect btn-success dropdown-toggle" type="button" data-toggle="dropdown"
            aria-expanded="true"> <?= Yii::t('common', 'Create') ?>&nbsp
        <span class="caret"></span>
    </button>
    <ul role="menu" class="dropdown-menu">
        <li role="presentation">
            <?= Html::a(Yii::t('common/employee', 'Add main speciality'), ['configuration/employee/create', 'person' => Yii::$app->request->get('id')], [
                'class' => 'btn btn-sm pmd-btn-flat pmd-ripple-effect btn-default',
                'data-pjax' => '0',
                'tabindex' => '-1',
                'role' => 'menuitem',
            ]) ?>
        </li>
        <li role="presentation">
            <?= Html::a(Yii::t('common/employee', 'Add parttime'), ['configuration/parttime/create', 'person' => Yii::$app->request->get('id')], [
                'class' => 'btn btn-sm pmd-btn-flat pmd-ripple-effect btn-default',
                'data-pjax' => '0',
                'tabindex' => '-1',
                'role' => 'menuitem',
            ]) ?>
        </li>
    </ul>
</span>