<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 27.10.2017
 * Time: 13:10
 */

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\Documenter\Documenter;

Breadcrumbs::root();
$this->title = Yii::t('common/updates','Updates');
?>

<div class="updates-index content-container">
    <?= Documenter::widget() ?>
</div>