$(document).ready(function () {
    $(".pmd-card-list a:visible").first().addClass("active");

    $('.wkdoc-tab-link').click(function () {
        var tabClass = $(this).attr("href").substr(1);
        $(".pmd-card-list a").removeClass("wkdoc-pill-show").addClass("wkdoc-pill-hide");
        $('a[hash-tab="' + tabClass + '"]').removeClass("wkdoc-pill-hide").addClass("wkdoc-pill-show");
        if ($(".pmd-card-list a.active:visible").length === 0) {
            $(".pmd-card-list a:visible").first().addClass("active");
        }
    });

    $(".wkdoc-pill-link").click(function (e) {
        if (!$(this).hasClass(("active"))) {
            var $that = $(this);
            var hash = $(this).attr("hash-tab");
            $.ajax({
                url: $(this).attr("href"),
                beforeSend: function () {
                    $('.pmd-card-list a[hash-tab="' + hash + '"]').removeClass("active");
                    $that.addClass("active");
                    $(".wkdoc-loading").show();
                },
                success: function (response) {
                    $("#" + hash).html(response);
                    $(".wkdoc-loading").hide();
                }
            });
        }

        e.preventDefault();
    });
});