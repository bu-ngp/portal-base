$(document).ready(function () {
    $(".pmd-card-list a:visible").first().addClass("active");

    $('.wkdoc-tab-link').click(function () {
        var tabClass = $(this).attr("href").substr(1);
        $(".pmd-card-list a").removeClass("wkdoc-pill-show");
        $(".pmd-card-list a").addClass("wkdoc-pill-hide");
        $('a[hash-tab="' + tabClass + '"]').removeClass("wkdoc-pill-hide");
        $('a[hash-tab="' + tabClass + '"]').addClass("wkdoc-pill-show");
        if ($(".pmd-card-list a.active:visible").length === 0) {
            $(".pmd-card-list a:visible").first().addClass("active");
        }
    });

    $(".wkdoc-pill-link").click(function (e) {
        var $that = $(this);
        $.ajax({
            url: $(this).attr("href"),
            beforeSend: function () {
                var hash = $(".wkdoc-pill-link").attr("hash-tab");
                $(".wkdoc-loading").show();
                $('.pmd-card-list a[hash-tab="' + hash + '"]').removeClass("active");
                $that.addClass("active");
            },
            success: function (response) {
                var hash = $(".wkdoc-pill-link").attr("hash-tab");
                $("#" + hash).html(response);

                $(".wkdoc-loading").hide();
            }
        });

        e.preventDefault();
    });
});