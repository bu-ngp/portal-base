$(document).ready(function () {
    setTimeout(function listenDoH() {

        $.ajax({
            url: "doh/listen",
            success: function (response) {
                console.log(response);

                setTimeout(listenDoH, 3000);
            }
        });

    }, 3000);

    $(document).on('click','#test1',function () {
        $.ajax({
            url: "doh/default/test"
        });
    });
});