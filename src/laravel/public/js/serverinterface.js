
/*=================================
ajaxPOST処理
=================================*/
//送信共通
function ajaxPost(url, data) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: data
    })
        // 成功
        .done((data) => {
            ajaxPostResult(url, data);
        })
        // 失敗
        .fail(function (jqXHR, textStatus, errorThrown) {
            ajaxPostErrorResult(jqXHR);
            CloseModal();
            LoadOff();
        })
        // 成功・失敗どちらでも発動
        .always((data) => {
        });
};

//各受信処理
function ajaxPostResult(url, data) {
    //APIごとに各後処理を実行する
    switch (url) {
        //システム
        case DATABASECREATE:
            systemCreateResultDisp(data['reqdata'], data['resdata']);
            break;

        case COMPANYGET:
            LoadOff();
            companyDisp(data['resdata']);
            break;

        case COMPANYUPDATE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        // case SYSTEMUSERCHANGE:システムユーザ変更関連API保留
        //     LoadOff();
        //     MessageDisp(INFO, "システム情報更新", 3000);
        //     break;

        case LOGINIDUPDATE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        case LOGINPASSWORDUPDATE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        case LOGININFOGET:
            LoadOff();
            loginInfoDisp(data['reqdata'], data['resdata']);
            break;

        case SYSTEMLOGINNAMEGET:
            LoadOff();
            userCreateDisp(data['reqdata'], data['resdata']);
            break;

        //ユーザ、グループ
        // case ADMINUSERGET:システムユーザ変更関連API保留
        //     LoadOff();
        //     systemUserEditDisp(data['reqdata'], data['resdata']);
        //     MessageDisp(INFO, "システム情報更新", 3000);
        //     break;

        case GROUPUSERGET:
            MiniLoadOff();
            groupUserDisp(data['reqdata'], data['resdata']);
            break;

        case GROUPNOTUSERGET:
            MiniLoadOff();
            groupUserChangDisp(data['reqdata'], data['resdata']);
            break;

        case USERGROUPGET:
            MiniLoadOff();
            tabOwnChangDisp(data['reqdata'], data['resdata']);
            break;

        case GROUPALLGET:
            MiniLoadOff();
            tabOwnChangDisp(data['reqdata'], data['resdata']);
            break;

        case USERSEARCH:
            LoadOff();
            managementDisp('ユーザリスト', 'ユーザ名', 'user', data['resdata']);
            break;

        case GROUPSEARCH:
            LoadOff();
            managementDisp('グループリスト', 'グループ名', 'group', data['resdata']);
            break;

        case USERINFOGET:
            CloseModal();
            userInfoDisp(data['reqdata'], data['resdata']);
            break;

        case GROUPINFOGET:
            CloseModal();
            groupInfoDisp(data['reqdata'], data['resdata']);
            break;

        case GROUPADD:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        case USERADD:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        case USERINFOUPDATE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        case GROUPINFOUPDATE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        case GROUPUSERCHANGE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        //タブ
        case TABSEARCH:
            LoadOff();
            managementDisp('タブリスト', 'タブ名', 'tab', data['resdata']);
            break;

        case TABINFOGET:
            CloseModal();
            tabInfoDisp(data['reqdata'], data['resdata']);
            break;

        case TABADD:
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                var tabGroup = new TabGroup(
                    data['resdata']['tabId'],
                    data['resdata']['tabName'],
                    data['resdata']['color'],
                    data['resdata']['textColor'],
                    data['resdata']['borderColor'],
                    0,
                    ""
                );
                tabDisp(tabGroup);
                var tabGroup = new TabGroup(data['resdata']['tabId'], data['resdata']['tabName'], data['resdata']['color'], 0, "");
                tabSelect(tabGroup.domTabId, tabGroup.domMainAreaId, tabGroup.domTabTextId, tabGroup.color);
            }
            CloseModal();
            break;

        case TABUPDATE:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        case TABINFOUPDATE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            reloadDispDecide(data['reqdata']['disp']);
            break;

        case TABGROUPCHANGE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            reloadDispDecide(data['reqdata']['disp']);

            break;

        case GROUPTABCHANGE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            reloadDispDecide(data['reqdata']['disp']);
            break;

        case TABDELETE:
            CloseModal();
            serverMessageDisp(data['resdata']);
            break;

        //カテゴリ
        case CATEGORYINFOGET:
            CloseModal();
            categoryDetailDisp(data['reqdata'], data['resdata']);
            break;

        case CATEGORYADD:
            LoadOff();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                var categoryGroup = new CategoryGroup(
                    data['reqdata']['tabId'],
                    data['resdata']['categoryId'],
                    data['reqdata']['categoryName'],
                    "",
                    1,
                    0,
                    0,
                    0,
                    data['reqdata']['categoryDeadline'],
                    data['reqdata']['color'],
                    data['reqdata']['textColor'],
                    data['reqdata']['borderColor'],
                    data['reqdata']['groupFlg'],
                    data['resdata']['userId'],
                    $("#userName").val(),
                    data['reqdata']['groupId'],
                    1,
                    $("#color").val(),
                    $("#textColor").val(),
                    $("#borderColor").val(),
                    0
                );
                categoryDisp(categoryGroup);
            }
            break;

        case CATEGORYUPDATE:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        case CATEGORYDEADLINEUPDATE:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        case CATEGORYDETAILUPDATE:
            var dispCreate = serverMessageDisp(data['resdata']);
            CloseModal();
            if (dispCreate) {
                $('#categoryName' + data['reqdata']['categoryId']).val(data['reqdata']['categoryName']);
                $('#categoryDeadline' + data['reqdata']['categoryId']).val(data['reqdata']['categoryDeadline']);
            }
            break;

        case CATEGORYSTATUSUPDATE:
            LoadOff();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                categoryStatusButtonUpdate(
                    data['reqdata']['categoryId'],
                    data['reqdata']['domCategoryAreaId'],
                    data['reqdata']['notstarted'],
                    data['reqdata']['working'],
                    data['reqdata']['waiting'],
                    data['reqdata']['done'],
                    data['reqdata']['archiveFlg'],
                );
            }
            break;

        case CATEGORYSUSPENDED:
            LoadOff();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                data['resdata'].data.forEach(function (value) {
                    categoryStatusButtonSuspended(
                        value.categoryId,
                        value.categoryWorking
                    )
                });
            }
            break;

        case CATEGORYUSERCHANGE:
            CloseModal();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                var categoryFilterSet = tabCategoryFilterLocalGet(data['reqdata']['tabCategoryList']);
                LoadOn();
                var data = {
                    'tabId': data['reqdata']['tabId'],
                    'groupFlg': data['reqdata']['groupFlg'],
                    'tabCategoryList': data['reqdata']['tabCategoryList'],
                    'color': data['reqdata']['tabColor'],
                    'textColor': data['reqdata']['tabTextColor'],
                    'borderColor': data['reqdata']['tabBorderColor'],
                    'categoryFilterSet': categoryFilterSet
                };
                ajaxPost(CATEGORYFILTER, data);
            }
            break;

        case CATEGORYDELETE:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        case CATEGORYDONEARCHIVE:
            CloseModal();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                var categoryFilterSet = tabCategoryFilterLocalGet(data['reqdata']['tabCategoryList']);
                LoadOn();
                var data = {
                    'tabId': data['reqdata']['tabId'],
                    'groupFlg': data['reqdata']['groupFlg'],
                    'tabCategoryList': data['reqdata']['tabCategoryList'],
                    'color': data['reqdata']['color'],
                    'textColor': data['reqdata']['textColor'],
                    'borderColor': data['reqdata']['borderColor'],
                    'categoryFilterSet': categoryFilterSet
                };
                ajaxPost(CATEGORYFILTER, data);
            }
            break;

        case CATEGORYFILTER:
            CloseModal();
            $("#" + data['reqdata']['tabCategoryList']).empty();

            data['resdata'].forEach(function (value) {
                var categoryGroup = new CategoryGroup(
                    data['reqdata']['tabId'],
                    value.categoryId,
                    value.categoryName,
                    "",
                    value.categoryNotstarted,
                    value.categoryWorking,
                    value.categoryWaiting,
                    value.categoryDone,
                    value.categoryDeadline,
                    data['reqdata']['color'],
                    data['reqdata']['textColor'],
                    data['reqdata']['borderColor'],
                    data['reqdata']['groupFlg'],
                    value.userId,
                    value.userName,
                    value.groupId,
                    value.myUserflg,
                    value.color,
                    value.textColor,
                    value.borderColor,
                    value.categorySort
                );
                categoryDisp(categoryGroup);

            });

            break;

        //タスク
        case TASKINFOGET:
            CloseModal();
            taskDetailDisp(data['reqdata'], data['resdata']);
            break;

        case TASKGET:
            LoadOff();
            data['resdata']['taskData'].forEach(function (value) {
                var taskGroup = new TaskGroup(
                    value.taskId,
                    value.taskName,
                    value.categoryId,
                    "",
                    value.taskNotstarted,
                    value.taskWorking,
                    value.taskWaiting,
                    value.taskDone,
                    value.taskDeadline,
                    1,
                    data['resdata']['color'],
                    data['resdata']['textColor'],
                    data['resdata']['borderColor'],
                    value.taskSort
                );
                taskDisp(taskGroup);
            });
            accordion("categorymainarea" + data.reqdata.categoryId);
            break;

        case TASKADD:
            LoadOff();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                var taskGroup = new TaskGroup(
                    data['resdata']['taskId'],
                    data['reqdata']['taskName'],
                    data['reqdata']['categoryId'],
                    "",
                    1,
                    0,
                    0,
                    0,
                    data['reqdata']['taskDeadline'],
                    1,
                    data['reqdata']['color'],
                    data['reqdata']['textColor'],
                    data['reqdata']['borderColor'],
                    0
                );
                MessageDisp(INFO, "タスク「" + data['reqdata']['taskName'] + "」を追加", 3000);
                taskDisp(taskGroup);
            }
            break;

        case TASKUPDATE:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        case TASKDEADLINEUPDATE:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        case TASKDETAILUPDATE:
            var dispCreate = serverMessageDisp(data['resdata']);
            CloseModal();
            if (dispCreate) {
                $('#taskname' + data['reqdata']['taskId']).val(data['reqdata']['taskName']);
                $('#taskDeadline' + data['reqdata']['taskId']).val(data['reqdata']['taskDeadline']);
            }
            break;

        case TASKSTATUSUPDATE:
            LoadOff();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                taskStatusButtonUpdate(
                    data['reqdata']['taskId'],
                    data['reqdata']['domTaskAreaId'],
                    data['reqdata']['notstarted'],
                    data['reqdata']['working'],
                    data['reqdata']['waiting'],
                    data['reqdata']['done'],
                    data['reqdata']['archiveFlg']
                );
            }
            break;

        case TASKSUSPENDED:
            LoadOff();
            var dispCreate = serverMessageDisp(data['resdata']);
            if (dispCreate) {
                data['resdata'].data.forEach(function (value) {
                    taskStatusButtonSuspended(
                        value.taskId,
                        value.taskWorking
                    )
                });
            }
            break;

        case TASKDELETE:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        case TASKSORT:
            LoadOff();
            serverMessageDisp(data['resdata']);
            break;

        //実績、レポート
        case PERFORMANCESEARCH:
            LoadOff();
            graphDisp(data['reqdata'], data['resdata']);
            break;

        case USERREPORT:
            LoadOff();
            reportDisp(data['reqdata'], data['resdata'], "userReport");
            break;

        case GROUPREPORT:
            LoadOff();
            reportDisp(data['reqdata'], data['resdata'], "groupReport");
            break;

        case TABREPORT:
            LoadOff();
            reportDisp(data['reqdata'], data['resdata'], "tabReport");
            break;

        //機械学習
        case GETWORDTOUSER:
            LoadOff();
            modalDispWordToUser(data['reqdata'], data['resdata']);
            MessageDisp(INFO, "ユーザリスト取得", 3000);
            break;

        case GETUSERTOWORD:
            LoadOff();
            dispUserToWord(data['reqdata'], data['resdata']);
            MessageDisp(INFO, "ワード取得", 3000);
            break;

        case GETALLNEWWORD:
            LoadOff();
            wordCloudDisp(data['reqdata'], data['resdata']);
            break;

        case GETFREEWORDTOID:
            LoadOff();
            graphDispWordToUser(data['reqdata'], data['resdata']);
            MessageDisp(INFO, "データ取得", 3000);
            break;

        //デモ
        case "./demo":
            MessageDisp(INFO, "デモ用データの初期化完了", 3000);
            Reload(0);
            break;

        case "./machinelearning1":
            MessageDisp(INFO, "機械学習データの作成完了", 3000);
            break;

        case "./machinelearning2":
            MessageDisp(INFO, "機械学習データの作成完了", 3000);
            break;

        case "./machineLearningDelete":
            MessageDisp(INFO, "機械学習データの削除完了", 3000);
            break;

    }

};

//サーバメッセージ表示共通
function serverMessageDisp(res) {
    if (res.status == 0) {
        MessageDisp(INFO, res.message, 3000);
        return true;
    } else if (res.status == 1) {
        MessageDisp(WARNING, res.message, 3000);
        return false;
    } else if (res.status == 2) {
        MessageDisp(ERROR, res.message, 9000);
        return false;
    } else {
        MessageDisp(ERROR, "サーバエラー[900]", 9000);
        return false;
    }

};

//現在の画面によりリロードするかどうかを判定
function reloadDispDecide(currentDisp) {
    switch (currentDisp) {
        case WORKDISP:
            Reload(0);
            break;
        case ADMINDISP:
            break;
    }
};

function ajaxPostErrorResult(jqXHR) {
    switch (jqXHR.status) {
        case 419:
            MessageDisp(WARNING, "接続が切れました。画面を再読み込みします。", 3000);
            Reload(0);
            break;
        case 500:
            MessageDisp(ERROR, "サーバ側でエラーが発生しました。頻発する場合管理者までご連絡ください。", 3000);
            break;
        case 504:
            MessageDisp(WARNING, "サーバとの通信がタイムアウトしました。処理は続行されてます。", 3000);
            break;
        default:
            MessageDisp(ERROR, "サーバ・ネットワークエラー[" + jqXHR.status + "]", 3000);
            break;
    }
};

