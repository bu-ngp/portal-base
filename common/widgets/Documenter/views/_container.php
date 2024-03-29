<?php
/* @var $this yii\web\View */
/* @var $pillLinks string */
/* @var $tabs string */
/* @var $tabContent string */
?>
<div class="row wkdoc-container">
    <div class="col-md-2 wkdoc-pills">
        <?= $this->render('_pills', [
            'pillLinks' => $pillLinks,
        ]) ?>
    </div>
    <div class="col-md-10 wkdoc-tabs">
        <?= $this->render('_tabs', [
            'tabs' => $tabs,
            'tabContent' => $tabContent,
        ]) ?>
    </div>
</div>