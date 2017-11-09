const HANDLER_QUEUE = 1,
    HANDLER_DURING = 2,
    HANDLER_FINISHED = 3,
    HANDLER_CANCELED = 4,
    HANDLER_ERROR = 5;

var progressBarOpts = {
    strokeWidth: 4,
    easing: 'easeInOut',
    duration: 1400,
    color: '#a0b6ff',
    trailColor: '#eee',
    trailWidth: 4,
    svgStyle: {width: '100%', height: '100%'},
    text: {
        style: {
            color: '#999',
            position: 'relative',
            left: '40%',
            padding: 0,
            margin: 0,
            transform: null
        },
        autoStyleContainer: false
    },
    step: function (state, bar) {
        bar.setText(Math.round(bar.value() * 100) + ' %');
    }
};

var bars_cache = {};

$(document).ready(function () {
    setTimeout(listenDoH(), 3000);

    $(document).on('click', '#test1', function () {
        $.ajax({
            url: "doh/default/test"
        });
    });

    $(document).on('click', '#test_error', function () {
        $.ajax({
            url: "doh/default/test-error"
        });
    });

    $(document).on('click', '#test_with_files', function () {
        $.ajax({
            url: "doh/default/test-with-files"
        });
    });

    $("#handlerSearchGrid-pjax").on('pjax:success', function () {
        $(this)[0].busy = false;
        initProgressBars();
    });

    $("#handlerSearchGrid-pjax").on('pjax:beforeSend', function () {
        $(this)[0].busy = true;
    });

    $("#handlerSearchGrid-pjax").on('click', '.wk-doh-cancel', function (e) {
        var $button = $(this);
        e.preventDefault();

        wkwidget.confirm({
            message: 'Вы уверены, что хотите отменить задание?',
            yes: function () {
                $.ajax({
                    url: $button.attr("href"),
                    success: function (response) {
                        if (response.result === 'success') {
                            $("#handlerSearchGrid").yiiGridView('applyFilter');
                        } else if (response.result === 'error') {
                            $("#handlerSearchGrid-pjax").find('.wk-grid-errors').html("<div>" + response.message + "</div>");
                        }
                    }
                });
            }
        });
    });

    $("#handlerSearchGrid-pjax").on('click', '.wk-doh-delete', function (e) {
        var $button = $(this);
        e.preventDefault();

        wkwidget.confirm({
            message: 'Вы уверены, что хотите удалить задание?',
            yes: function () {
                $.ajax({
                    url: $button.attr("href"),
                    success: function (response) {
                        if (response.result === 'success') {
                            $("#handlerSearchGrid").yiiGridView('applyFilter');
                        } else if (response.result === 'error') {
                            $("#handlerSearchGrid-pjax").find('.wk-grid-errors').html("<div>" + response.message + "</div>");
                        }
                    }
                });
            }
        });
    });

    $("#handlerSearchGrid-pjax").on('click', '.wk-doh-clear', function (e) {
        var $button = $(this);
        e.preventDefault();

        wkwidget.confirm({
            message: 'Вы уверены, что хотите очистить все задания?',
            yes: function () {
                $.ajax({
                    url: $button.attr("href"),
                    success: function (response) {
                        if (response.result === 'success') {
                            $("#handlerSearchGrid").yiiGridView('applyFilter');
                        } else if (response.result === 'error') {
                            $("#handlerSearchGrid-pjax").find('.wk-grid-errors').html("<div>" + response.message + "</div>");
                        }
                    }
                });
            }
        });
    });
});

var initProgressBars = function () {
    bars_cache = {};
    $('.wk-progress').each(function () {
        var id = $(this).attr('key');
        var barSelector = 'div.wk-progress[key="' + id + '"]';
        var percent = $(this).attr('percent');

        bars_cache[id] = new ProgressBar.Line(barSelector, progressBarOpts);
        bars_cache[id].set(percent);
    });
};

var listenDoH = function () {
    var keys = [];
    var dataColSeq = $('th[attribute="handler_status"]').attr('data-col-seq');

    $("#handlerSearchGrid tbody > tr[data-key]").each(function () {
        var id = $(this).find('.wk-progress').attr('key');
        var percent = $(this).find('.wk-progress').attr('percent');

        var status = $(this).find('td[data-col-seq="' + dataColSeq + '"]').children('span').attr('key');
        if ([HANDLER_QUEUE, HANDLER_DURING].indexOf(parseInt(status)) >= 0) {
            keys.push(id);
        }
    });

    if (keys.length > 0) {
        $.ajax({
            url: "doh/listen",
            data: {keys: JSON.stringify(keys)},
            success: function (response) {
                var dataColSeq = $('th[attribute="handler_status"]').attr('data-col-seq');

                $.each(response, function () {
                    var id = this[0];
                    var percent = this[1];
                    var status = this[2];
                    var barSelector = 'div.wk-progress[key="' + id + '"]';
                    var barPercent = $(barSelector).attr('percent');
                    var barStatus = $('tr[data-key="' + id + '"]').children('td[data-col-seq="' + dataColSeq + '"]').children('span').attr('key');

                    if (parseInt(status) !== parseInt(barStatus)) {
                        reloadGrid();
                    } else if (parseFloat(barPercent) !== parseFloat(percent) || (parseInt(status) === HANDLER_ERROR && parseInt(status) !== parseInt(barStatus))) {
                        $(barSelector).attr('percent', percent);
                        bars_cache[id].animate(percent, {}, afterAnimateBar(percent, status));
                    }
                });

                setTimeout(listenDoH, 5000);
            }
        });
    } else {
        setTimeout(listenDoH, 5000);
    }
};

var afterAnimateBar = function (percent, status) {
    if (parseFloat(percent) === 1 || parseInt(status) === HANDLER_ERROR) {
        reloadGrid();
    }
};

var reloadGrid = function () {
    var busy = false;
    if ($("#handlerSearchGrid-pjax")[0].busy) {
        busy = $(this)[0].busy;
    }

    if (busy === false) {
        $("#handlerSearchGrid").yiiGridView("applyFilter");
    } else {
        setTimeout(function () {
            reloadGrid();
        }, 300);
    }
};