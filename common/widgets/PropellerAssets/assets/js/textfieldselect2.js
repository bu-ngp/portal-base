/*!
 * Propeller v1.1.0 (http://propeller.in)
 * Copyright 2016-2017 Digicorp, Inc.
 * Licensed under MIT (http://propeller.in/LICENSE)
 */

// Propeller form ------------------------------------------------------//
$(document).ready(function () {
    //var $select = $('.pmd-textfield-select2 select.form-control');
    // paper input
    $(".pmd-textfield-select2-focused").remove();
    $(".pmd-textfield-select2 .form-control").after('<span class="pmd-textfield-select2-focused"></span>');
    // floating label
    $('.pmd-textfield-select2 select.form-control').each(function () {
        if ($(this).val() && $(this).val().toString()) {
            $(this).closest('.pmd-textfield-select2').addClass("pmd-textfield-select2-floating-label-completed");
        }
    });
    // floating change label
    $(".pmd-textfield-select2 select.form-control").on('change.select2', function () {
        if ($(this).val() && $(this).val().toString()) {
            $(this).closest('.pmd-textfield-select2').addClass("pmd-textfield-select2-floating-label-completed");
        } else {
            $(this).closest('.pmd-textfield-select2').removeClass("pmd-textfield-select2-floating-label-completed");
        }
    });
    // floating label animation
    $("body").on("focus", ".pmd-textfield-select2 .select2-selection", function () {
        var $select = $(this).closest('.select2.select2-container').prev('select.form-control.select2-hidden-accessible');

        if ($select.val() && $select.val().toString()) {
            $select.closest('.pmd-textfield-select2').addClass("pmd-textfield-select2-floating-label-completed");
        } else {
            $select.closest('.pmd-textfield-select2').removeClass("pmd-textfield-select2-floating-label-completed");
        }
    });
    // remove floating label animation
    $("body").on("focusout", ".pmd-textfield-select2 .select2-selection", function () {
        var $select = $(this).closest('.select2.select2-container').prev('select.form-control.select2-hidden-accessible');

        if (!($select.val() && $select.val().toString())) {
            $select.closest('.pmd-textfield-select2').removeClass("pmd-textfield-select2-floating-label-completed");
        }
        $select.closest('.pmd-textfield-select2').removeClass("pmd-textfield-select2-floating-label-active");
    });
});