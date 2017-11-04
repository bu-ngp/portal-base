$(document).ready(function () {

    setTimeout(function listenDoH() {

        var keys = [];
        $(".wk-progress").each(function () {
            if ($(this).attr('percent') < 1) {
                keys.push($(this).attr('key'));
            }
        });

        $.ajax({
            url: "doh/listen",
            data: {keys: JSON.stringify(keys)},
            success: function (response) {
                console.log(response);

                $.each(response, function() {
                    console.debug( $('.wk-progress[key="' + this[0] + '"]'))

console.debug($('.wk-progress[key="' + this[0] + '"]').line())

                });

                setTimeout(listenDoH, 5000);
            }
        });

    }, 3000);

    $(document).on('click', '#test1', function () {
        $.ajax({
            url: "doh/default/test"
        });
    });

    $("#handlerSearchGrid-pjax").on('pjax:success', function () {
        initProgressBars();
    });
});

var initProgressBars = function () {
    $('.wk-progress').each(function () {
        var bar = new ProgressBar.Line('div.wk-progress[key="' + $(this).attr('key') + '"]', {
            strokeWidth: 4,
            easing: 'easeInOut',
            duration: 1400,
            color: '#FFEA82',
            trailColor: '#eee',
            trailWidth: 4,
            svgStyle: {width: '100%', height: '100%'},
            text: {
                style: {
                    // Text color.
                    // Default: same as stroke color (options.color)
                    color: '#999',
                    position: 'relative',
                    left: '40%',
                    padding: 0,
                    margin: 0,
                    transform: null
                },
                autoStyleContainer: false
            },
            from: {color: '#FFEA82'},
            to: {color: '#ED6A5A'},
            step: function (state, bar) {
                bar.setText(Math.round(bar.value() * 100) + ' %');
            }
        });
console.debug("bar", bar);
        bar.set($(this).attr('percent'));
    });
};