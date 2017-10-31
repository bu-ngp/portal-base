<?php
/* @var $tabs string */
/* @var $tabContent string */
?>
<div class="pmd-card pmd-z-depth wkdoc-tabs-inside">
    <div class="pmd-tabs">
        <div class="pmd-tab-active-bar"></div>
        <ul class="nav nav-tabs" role="tablist">
            <?= $tabs ?>
        </ul>
    </div>
    <div class="pmd-card-body wkdoc-content">
        <div class="tab-content">
            <?= $tabContent ?>
            <div class="wkdoc-loading"></div>
        </div>
    </div>
</div>
