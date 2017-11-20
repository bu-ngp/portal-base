<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 11.11.2017
 * Time: 11:19
 */
/* @var $modelForm ngp\services\forms\TilesForm */

/* @var $form \common\widgets\ActiveForm\ActiveForm */

use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

?>
<div class="row wk-tiles-icon-container">
    <div class="col-md-4">
        <div class="wk-tiles-preview-icon">
            <div><i class="fa fa-picture-o"></i></div>
        </div>
    </div>
    <div class="col-md-3 btn-group-vertical">
        <?= $form->field($modelForm, 'tiles_icon')->hiddenInput()->label(false) ?>
        <?= Html::a(FA::icon(FA::_WINDOW_MAXIMIZE) . Yii::t('ngp/tiles', 'Choose Icon'), ['tiles/icons', 'redirectTo' => 'create'], [
            'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-primary wk-tiles-choose-icon-button',
        ]) ?>
    </div>
</div>