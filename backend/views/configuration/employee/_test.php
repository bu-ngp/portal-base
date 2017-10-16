<?php
use common\widgets\PropellerAssets\Select2Asset;
use common\widgets\PropellerAssets\TextFieldAsset;
use kartik\select2\Select2;


?>
<div class="form-group">
    <button id="button1" class="btn btn-primary">Test</button>
</div>
<div class="form-group pmd-textfield pmd-textfield-floating-label">
    <label>Select Multiple Tags</label>

    <?= Select2::widget([
        'id' => 'testSelect2',
        'name' => 'testSelect2',
        'data' => [1 => 'test1', 2 => 'test2', 3 => 'test3', 4 => 'test4'],
        'theme' => Select2::THEME_BOOTSTRAP,
        'options' => [
            'class' => 'form-control select-with-search pmd-select2',
            //'class' => 'select-add-tags form-control select-with-search',
            'placeholder' => '',
            'multiple' => true,
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
    <?php \common\widgets\PropellerAssets\PropellerAsset::setWidget(Select2::className()) ?>
</div>

<?php
$this->registerJs(<<<EOT
    $("body").on("change",".pmd-textfield .form-control", function() {
        console.debug('$("#testSelect2").length');
        console.debug($("#testSelect2").length);
        console.debug('$("#testSelect2").val()');
        console.debug($("#testSelect2").val());
        if (!$('#testSelect2').val()) {
            $('div.form-group.pmd-textfield').removeClass("pmd-textfield-floating-label-completed");
            $('div.form-group.pmd-textfield').removeClass("pmd-textfield-floating-label-active");
        }
    });
    
    $("body").on("focus",".pmd-textfield .form-control",function(){
        console.debug('focused');
    	$(this).closest('.pmd-textfield').addClass("pmd-textfield-floating-label-active pmd-textfield-floating-label-completed");
    });
    
    $('#button1').click(function() {
        var option = $('<option selected></option>');
        option.text("test5").val(5);
        $("#testSelect2").append(option).trigger('change');
        console.debug($('#testSelect2').val());
    });
EOT
);

Select2Asset::register($this);
//TextFieldAsset::register($this);
?>
<style>
    .form-group.pmd-textfield {
        margin-top: 400px;
    }
</style>
