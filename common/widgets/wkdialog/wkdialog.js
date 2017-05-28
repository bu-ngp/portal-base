/**
 * Created by VOVANCHO on 28.05.2017.
 */
wkwidget = {
    settings: {
        messages: {
            dialogConfirmTitle: 'Confirm',
            dialogAlertTitle: 'Information',
            dialogConfirmButtonClose: 'Close',
            dialogConfirmButtonOK: 'OK',
            dialogAlertButtonClose: 'Close'
        }
    },
    init: function (options) {
        var makeDialog = function (settings) {
            var $dialog = $('<div tabindex="-1" class="modal fade wk-dialog-alert" style="display: none;" aria-hidden="true">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h3 class="pmd-card-title-text wk-dialog-title">' + settings.messages.dialogAlertTitle + '</h3>' +
                '</div>' +
                '<div class="modal-body">' +
                '<p class="wk-dialog-text"></p>' +
                '</div>' +
                '<div class="pmd-modal-action pmd-modal-bordered text-right">' +
                '<button data-dismiss="modal" type="button" class="btn pmd-btn-flat pmd-ripple-effect btn-default">' + settings.messages.dialogAlertButtonClose + '</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');

            if ($('body').children('div.modal.wk-dialog-alert').length === 0) {
                $dialog.appendTo('body');
            }
        };

        var makeConfirm = function (settings) {
            var $dialog = $('<div tabindex="-1" class="modal fade wk-dialog-confirm" style="display: none;" aria-hidden="true">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h3 class="pmd-card-title-text wk-confirm-title">' + settings.messages.dialogConfirmTitle + '</h3>' +
                '</div>' +
                '<div class="modal-body">' +
                '<p class="wk-confirm-text"></p>' +
                '</div>' +
                '<div class="pmd-modal-action pmd-modal-bordered text-right">' +
                '<button data-dismiss="modal" type="button" class="btn pmd-btn-flat pmd-ripple-effect btn-default">' + settings.messages.dialogConfirmButtonClose + '</button>' +
                '<button type="button" class="btn pmd-btn-flat pmd-ripple-effect btn-primary wk-btn-confirm-ok">' + settings.messages.dialogConfirmButtonOK + '</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');

            if ($('body').children('div.modal.wk-dialog-confirm').length === 0) {
                $dialog.appendTo('body');
            }
        };

        wkwidget.settings = $.extend({}, wkwidget.settings, options || {});

        makeDialog(wkwidget.settings);
        makeConfirm(wkwidget.settings);
    },
    alert: function (options) {
        var $alertDialog = $('body').children('div.wk-dialog-alert');

        if ('message' in options) {
            $alertDialog.find('.wk-dialog-text').html(options.message);
        }

        if ('title' in options) {
            $alertDialog.find('.wk-dialog-title').text(options.title);
        } else {
            $alertDialog.find('.wk-dialog-title').text(wkwidget.settings.messages.dialogAlertTitle);
        }

        $alertDialog.modal();
    },
    confirm: function (options) {
        var $confirmDialog = $('body').children('div.wk-dialog-confirm');

        if ('message' in options) {
            $confirmDialog.find('.wk-confirm-text').html(options.message);
        }

        if ('title' in options) {
            $confirmDialog.find('.wk-confirm-title').text(options.title);
        } else {
            $confirmDialog.find('.wk-confirm-title').text(wkwidget.settings.messages.dialogConfirmTitle);
        }

        var $yesButton = $confirmDialog.find('button.wk-btn-confirm-ok').off('click');

        if ('yes' in options && typeof options.yes == 'function') {
            $yesButton.on('click', function () {
                $confirmDialog.modal('hide');
                options.yes();
            });
        }

        $confirmDialog.modal();
    }
};