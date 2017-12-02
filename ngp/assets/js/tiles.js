$(document).ready(function () {
    $(".wk-tiles-upload-image-button").click(function () {
        $(".wk-tiles-upload-input").click();
    });

    $(".wk-tiles-upload-input").change(function () {
        readURL(this);
    });

    $("#tilesform-tiles_link").focusout(function () {
        if ($(this).val() !== "") {
            var matches = $(this).val().match(/^((https:\/\/)|(http:\/\/))?(.*)$/i);
            if (matches[1] === undefined) {
                $(this).val('http://' + matches[4]).trigger('change');
            }
        }
    });

    $('#tilesform-tiles_icon_color').on('change', function () {
        $('div.wk-tiles-preview-icon').find('div').attr('class', $(this).val());
    });

    if ($('#tilesform-tiles_icon_color').val() !== "") {
        $('div.wk-tiles-preview-icon').find('div').attr('class', $('#tilesform-tiles_icon_color').val());
    }

    if ($('#tilesform-tiles_thumbnail').val() !== "") {
        $('.wk-tiles-preview').css("background-image", 'url("' + $('#tilesform-tiles_thumbnail').val() + '")');
    }

    if ($('#tilesform-tiles_icon').val() !== "") {

        if ($('.wk-tiles-preview').css("background-image") === "none") {
            $('ul.nav-tabs').find('a[href="#w1-tab1"]').tab('show');
        }

        $('div.wk-tiles-preview-icon').find('i').attr('class', 'fa fa-' + $('#tilesform-tiles_icon').val());
    }

    // floating label textarea
    $('.pmd-textfield textarea.form-control').each(function () {
        if ($(this).val() !== "") {
            $(this).closest('.pmd-textfield').addClass("pmd-textfield-floating-label-completed");
        }
    });
    // floating change label textarea
    $(".pmd-textfield textarea.form-control").on('change', function () {
        if ($(this).val() !== "") {
            $(this).closest('.pmd-textfield').addClass("pmd-textfield-floating-label-completed");
        }
    });
});

var jcrop_init;

function showCoords(c) {
    var original = $('.wk-tiles-crop');
    var preview = $(".wk-tiles-preview");
    var oH = original.height();
    var oW = original.width();
    var pH = preview.height();
    var pW = preview.width();
    var rW = pW / c.w;
    var rH = pH / c.h;

    preview.css("background-size", (oW * rW) + "px" + " " + (oH * rH) + "px");
    preview.css("background-position", rW * Math.round(c.x) * -1 + "px" + " " + rH * Math.round(c.y) * -1 + "px");

    $('#tilesform-tiles_thumbnail_x').val(c.x);
    $('#tilesform-tiles_thumbnail_x2').val(c.x2);
    $('#tilesform-tiles_thumbnail_y').val(c.y);
    $('#tilesform-tiles_thumbnail_y2').val(c.y2);
    $('#tilesform-tiles_thumbnail_w').val(c.w);
    $('#tilesform-tiles_thumbnail_h').val(c.h);
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.wk-tiles-crop').attr('src', e.target.result);
            $('.wk-tiles-preview').css("background-image", 'url("' + e.target.result + '")');

            if (jcrop_init !== undefined) {
                jcrop_init.destroy();
            }

            jcrop_init = $.Jcrop('.wk-tiles-crop', {
                onChange: showCoords,
                onSelect: showCoords,
                aspectRatio: 330 / 190,
                boxWidth: 866
                //minSize: [310,190]
            });

            $('.wk-tiles-crop-button').prop("disabled", false);
        };

        reader.readAsDataURL(input.files[0]);
    }
}