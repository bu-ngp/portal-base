<?php
/* @var $this yii\web\View */
/* @var $tabs string */
/* @var $tabContent string */
?>
<div class="pmd-card pmd-z-depth">
    <div class="pmd-tabs pmd-tabs-scroll">
        <div class="pmd-tabs-scroll-left"><i class="material-icons pmd-sm">chevron_left</i></div>
        <div class="pmd-tabs-scroll-container">
            <div class="pmd-tab-active-bar"></div>
            <ul class="nav nav-tabs" role="tablist">
                <?= $tabs ?>
            </ul>
        </div>
        <div class="pmd-tabs-scroll-right"><i class="material-icons pmd-sm">chevron_right</i></div>
    </div>
    <div class="pmd-card-body wkdoc-content">
        <div class="tab-content">
            <?= $tabContent ?>
        </div>
    </div>
</div>
