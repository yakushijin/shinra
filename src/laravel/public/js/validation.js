//カテゴリ操作時の所有ユーザかチェック※管理者権限は所有者関係なくOK
function checkCategoryMyUserAdmin(domCategoryAreaId) {
    if ($("#authority").val() == 0) {
        var check = $("#" + domCategoryAreaId).attr('data-myUserflg');
    } else {
        var check = 1;
    }
    return Number(check);
};

//カテゴリ操作時の所有ユーザかチェック※管理者権限は考慮しない
function checkCategoryMyUserNotAdmin(domCategoryAreaId) {
    var check = $("#" + domCategoryAreaId).attr('data-myUserflg');
    return Number(check);
};

//管理者権限チェック
function checkAdmin() {
    if ($("#authority").val() == 1) {
        var check = 1;
    } else {
        var check = 0;
    }
    return check;
};

//システム管理者権限チェック
function checkSystem() {
    if ($("#systemUser").val() == 1) {
        var check = 1;
    } else {
        var check = 0;
    }
    return check;
};

//管理者権限チェック
function checkAdminMessageDisp(message) {
    if ($("#authority").val() == 0) {
        var resultMessage = message;
    } else {
        var resultMessage = "";
    }
    return Number(resultMessage);
};

function notAdminAlert() {
    MessageDisp(WARNING, "管理者ユーザ以外は操作できません", 3000);
};


//半角英数チェック
function checkAlphabetNumber(str) {
    str = (str == null) ? "" : str;
    if (str.match(/^[A-Za-z0-9]*$/)) {
        return true;
    } else {
        return false;
    }
}

//半角英数記号チェック
function checkAlphabetNumberSymbol(str) {
    str = (str == null) ? "" : str;
    if (str.match(/^[A-Za-z0-9!#$%*+,.:;=?@^_-]*$/)) {
        return true;
    } else {
        return false;
    }
}

//使用不可能文字チェック
function checkDoNotUseSymbol(str) {
    if (str.match(/[<>{}[\]()&"']/)) {
        MessageDisp(WARNING, "使用禁止文字列「<>{}[\]()&\"'」が含まれています", 5000);
        return true;
    } else {
        return false;
    }
}

//XSS対策エスケープエンコード処理
function xssEscapeEncode(str) {
    str = str.replace(/&/g, '&amp;');
    str = str.replace(/</g, '&lt;');
    str = str.replace(/>/g, '&gt;');
    str = str.replace(/"/g, '&quot;');
    str = str.replace(/'/g, '&#39;');
    return str;
  }

//XSS対策エスケープでコード処理
function xssEscapeDecode(str) {
    str = str.replace(/&amp;/g, '&');
    str = str.replace(/&lt;/g, '<');
    str = str.replace(/gt;/g, '>');
    str = str.replace(/&quot;/g, '"');
    str = str.replace(/&#39;/g, '\'');
    return str;
  }

//カテゴリ操作時の所有ユーザで無かった時のアラート
function categoryNotMyUserAlert() {
    MessageDisp(WARNING, "ほかのユーザのカテゴリは操作できません", 3000);
};

//テキスト長さ
function textLengthCheck(text, minlength, maxlength) {
    if (text.length <= maxlength && text.length >= minlength) {
        return true;
    } else {
        return false;
    }
};

//日付形式チェック
function dayCheck(day) {
    if (!day.match(/^\d{4}\/\d{2}\/\d{2}$/)) {
        return false;
    }
    var y = day.split("/")[0];
    var m = day.split("/")[1] - 1;
    var d = day.split("/")[2];
    var date = new Date(y, m, d);
    if (date.getFullYear() != y || date.getMonth() != m || date.getDate() != d) {
        return false;
    }
    return true;
};

//指定した日付と日付を比較
function dayComparisonCheck(leftDate, rightDate) {
    var leftDate = stringDateChange(leftDate);
    var rightDate = stringDateChange(rightDate);
    if (leftDate < rightDate) {
        return "rightDateWin";
    } else if(leftDate > rightDate){
        return "leftDateWin";
    }
    return "same";
};

//日付範囲指定チェック
function dayRangeCheck(fromDay, toDay, range) {
    var fromDay = stringDateChange(fromDay);
    var toDay = stringDateChange(toDay);
    var difference = msDay(toDay.getTime() - fromDay.getTime());
    var check;
    if (fromDay > toDay) {
        check = false;
    } else {
        if (difference > range) {
            check = false;
        } else {
            check = true;
        }
    }
    return check;
};

function monthRangeCheck(fromDay, toDay, range) {
    var fromDay = stringDateChange(fromDay);
    var toDay = stringDateChange(toDay);
    var difference = msMonth(toDay.getTime() - fromDay.getTime());
    var check;
    if (fromDay > toDay) {
        check = false;
    } else {
        if (difference > range) {
            check = false;
        } else {
            check = true;
        }
    }
    return check;
};

//入力可能数値チェック
function numberRangeCheck(number, min, max) {
    if (number >= min && number <= max) {
        check = 0;
    } else {
        check = 1;
    }
    return check;
};


//必須項目チェック
function requiredCheck(text) {
    if (text == "") {
        check = 0;
    } else {
        check = 1;
    }
    return check;
};

//テキストボックス色変更
function textBoxColorChange(id, check) {
    if (check) {
        $('#' + id).css('background', 'radial-gradient(#e44b2e,#c64b2e)');
    } else {
        $('#' + id).css('background', '#e9edf7');
    }
};


//日付範囲チェックラッパー
function dateCheckWrap(validationMessageId, DayToId, DayFromId, range, interval) {
    var DayTo = $('#' + DayToId).val();
    var DayFrom = $('#' + DayFromId).val();

    var check = 1;
    $('#' + validationMessageId).empty();
    if (!requiredCheck(DayTo)) {
        var performanceDayToCheck = 1;
        check = 0;
        $('#' + validationMessageId).append("<div>日付（to）に値を入れてください</div>");
    } else if (!dayCheck(DayTo)) {
        var performanceDayToCheck = 1;
        check = 0;
        $('#' + validationMessageId).append("<div>日付（to）の値をyyyy/mm/dd形式にしてください</div>");
    }

    if (!requiredCheck(DayFrom)) {
        var performanceDayFromCheck = 1;
        check = 0;
        $('#' + validationMessageId).append("<div>日付（from）に値を入れてください</div>");
    } else if (!dayCheck(DayFrom)) {
        var performanceDayFromCheck = 1;
        $('#' + validationMessageId).append("<div>日付（from）の値をyyyy/mm/dd形式にしてください</div>");
        check = 0;
    }

    switch (interval) {
        case "day":
            if (!dayRangeCheck(DayFrom, DayTo, range)) {
                $('#' + validationMessageId).append("<div>日付（from）～日付（to）の期間を" + range + "日以内にしてください</div>");
                var performanceDayToCheck = 1;
                var performanceDayFromCheck = 1;
                check = 0;
            }
            break;

        case "month":
            if (!monthRangeCheck(DayFrom, DayTo, range)) {
                $('#' + validationMessageId).append("<div>日付（from）～日付（to）の期間を" + range + "か月以内にしてください</div>");
                var performanceDayToCheck = 1;
                var performanceDayFromCheck = 1;
                check = 0;
            }
            break;
    }


    textBoxColorChange(DayToId, performanceDayToCheck);
    textBoxColorChange(DayFromId, performanceDayFromCheck);
    return check;
};

//カテゴリ、タスク削除前確認
function deleteCheck() {
    if ($("#deleteMessageFlg").val() == 1) {
        var check = 1;
    } else {
        var check = 0;
    }
    return check;
}

//期日自動挿入フラグ確認
function autoDeadlineCheck() {
    if ($("#defaultDeadlineFlg").val() == 1) {
        var check = 1;
    } else {
        var check = 0;
    }
    return check;
}

//完了ステータス自動非表示フラグ確認
function autoActiveCheck() {
    if ($("#doneAutoActiveFlg").val() == 1) {
        var check = 1;
    } else {
        var check = 0;
    }
    return check;
}

function deleteCheckDialog(title, content, target, dispDataSet, position, exeButtonColor) {

    var $dialog = $('<div id="dialog"></div>').append();
    var exeFlg = 0;

    $dialog.dialog({
        modal: true,
        title: title,
        dialogClass: "dialogCustom",
        resizable: false,
        position: position,
        closeOnEscape: true,
        height: 150,
        width: 250,
        buttons: [
            {
                text: '実行',
                class: 'modalSubButton ' + exeButtonColor,
                click: function () {
                    exeFlg = 1; $(this).dialog('close');
                }
            },
            {
                text: 'キャンセル',
                class: 'modalSubButton subButtonSelect',
                click: function () {
                    $(this).dialog('close');
                }
            }
        ],

        close: function () {
            exeFunction(exeFlg, target, dispDataSet);
        },
    });

    $('#dialog').append(content);

    //タブ新規作成テキストエリアエンター押下の発火処理追加
    tabTextEnterPress();
    $('#tabAddName').focus();
}


function exeFunction(exeFlg, target, dispDataSet) {
    if (exeFlg) {
        switch (target) {
            case "category":
                categoryDeleteRun(dispDataSet);
                break;
            case "task":
                taskDeleteRun(dispDataSet);
                break;
            case "tab":
                tabDeleteRun(dispDataSet);
                break;
            case "tabAdd":
                tabAddRun();
                break;

        }
    }
    $("#dialog").remove();

}