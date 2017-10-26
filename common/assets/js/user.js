transliterate = (function () {
    var rus = "щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь".split(/ +/g),
        eng = "shh sh ch cz yu ya yo zh `` y' e` a b v g d e z i j k l m n o p r s t u f x `".split(/ +/g);
    return function (text, engToRus) {
        var x;
        for (x = 0; x < rus.length; x++) {
            text = text.split(engToRus ? eng[x] : rus[x]).join(engToRus ? rus[x] : eng[x]);
            text = text.split(engToRus ? eng[x].toUpperCase() : rus[x].toUpperCase()).join(engToRus ? rus[x].toUpperCase() : eng[x].toUpperCase());
        }
        return text;
    }
})();

$(document).ready(function () {
    $("#userform-person_fullname").on('change', function () {
        if ($("#userform-person_fullname").val() !== "" && $("#userform-person_username").val() === "") {
            var translit = transliterate($("#userform-person_fullname").val());
            var matches = translit.match(/^((\w)(.*?))\s(\w)((.*?\s)(\w))?/);
            if (matches !== null) {
                matches[1] = (matches[1] || '').toLowerCase().charAt(0).toUpperCase() + (matches[1] || '').slice(1).toLowerCase();
                matches[4] = (matches[4] || '').toUpperCase();
                matches[7] = (matches[7] || '').toUpperCase();

                if (matches[1] && matches[4]) {
                    $("#userform-person_username").closest(".form-group.pmd-textfield.pmd-textfield-floating-label").addClass("pmd-textfield-floating-label-completed");
                    $("#userform-person_username").val(matches[1] + matches[4] + (matches[7] || ''));
                }
            }
        }
    });

    $("#userformupdate-person_fullname").on('change', function () {
        if ($("#userformupdate-person_fullname").val() !== "" && $("#userformupdate-person_username").val() === "") {
            var translit = transliterate($("#userformupdate-person_fullname").val());
            var matches = translit.match(/^((\w)(.*?))\s(\w)((.*?\s)(\w))?/u);
            if (matches !== null) {
                matches[1] = (matches[1] || '').toLowerCase().charAt(0).toUpperCase() + (matches[1] || '').slice(1).toLowerCase();
                matches[4] = (matches[4] || '').toUpperCase();
                matches[7] = (matches[7] || '').toUpperCase();

                if (matches[1] && matches[4]) {
                    $("#userformupdate-person_username").closest(".form-group.pmd-textfield.pmd-textfield-floating-label").addClass("pmd-textfield-floating-label-completed");
                    $("#userformupdate-person_username").val(matches[1] + matches[4] + (matches[7] || ''));
                }
            }
        }
    });
});