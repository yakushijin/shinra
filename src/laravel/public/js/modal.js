/*=================================
モーダル_基本
=================================*/

/*----------------------------------------------<ユーザ>----------------------------------------------*/
//指定したIDのユーザ情報を取得
function userinfoget(userId) {
	LoadOn();
	var data = { 'userId': userId };
	ajaxPost(USERINFOGET, data);
};

//サーバからユーザデータ取得
function userInfoDisp(req, res) {

	ModalOn();
	$("#titelarea").append('<span id="userInfoDispTitel" >' + res["userName"] + 'さん</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');

	$("#leftarea").append('<div id="userInfoDispBasic" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="userInfoDispPerformance" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="userInfoDispWordMap" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="userInfoDispLoginData" class="modalSubArea infoArea"></div>');

	if (res["systemUserFlg"] == 1) {
		var disabled = 'disabled="disabled"';
	} else {
		var disabled = "";
	}

	$("#userInfoDispBasic").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>ユーザ基本情報</div><div class="modalSubTitelLine"></div>');
	$("#userInfoDispBasic").append(
		'<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="userInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<span class="infoSubAreaUnit"><label for="userInfoUserName" class="baseLabel">ユーザ名</label><input type="text" maxlength=' + userTextMaxLength + ' id="userInfoUserName" class="baseTextBox nameTextBox" value="' + res["userName"] + '" autocomplete="off"></span>'
		+ '<span class="infoSubAreaUnit"><input type="checkbox" id="userInfoDispActiveFlg" class="baseCheckBox" ' + disabled + '/><label for="userInfoDispActiveFlg" class="baseCheckBox-label baseLabel">無効化</label></span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoCreateUser" class="baseLabel infoSubAreaUnit">作成者</label><span id="userInfoCreateUser" class="userNameDisp">' + res["createUserName"] + '</span>'
		+ '<label for="userInfoCreateDay" class="baseLabel infoSubAreaUnit">作成日時</label><span id="userInfoCreateDay">' + res["createDay"] + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoUpdateUser" class="baseLabel infoSubAreaUnit">更新者</label><span id="userInfoUpdateUser" class="userNameDisp">' + res["updateUserName"] + '</span>'
		+ '<label for="userInfoUpdateDay" class="baseLabel infoSubAreaUnit">更新日時</label><span id="userInfoUpdateDay">' + res["updateDay"] + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<span class="infoSubAreaUnit"><input type="checkbox" id="userInfoDispAuthority" class="baseCheckBox" ' + disabled + '/><label for="userInfoDispAuthority" class="baseCheckBox-label baseLabel">管理者権限</label></span>'
		+ '<span class="infoSubAreaUnit"><input type="checkbox" id="userInfoDispDefaultDeadlineFlg" class="baseCheckBox"/><label for="userInfoDispDefaultDeadlineFlg" class="baseCheckBox-label baseLabel">期限日の自動挿入</label></span>'
		+ '<span class="infoSubAreaUnit"><input type="checkbox" id="userInfoDispDeleteMessageFlg" class="baseCheckBox"/><label for="userInfoDispDeleteMessageFlg" class="baseCheckBox-label baseLabel">削除時のポップアップ確認</label></span>'
		+ '<span class="infoSubAreaUnit"><input type="checkbox" id="userInfoDispDoneAutoActiveFlg" class="baseCheckBox"/><label for="userInfoDispDoneAutoActiveFlg" class="baseCheckBox-label baseLabel">ステータス「完了」の自動非表示</label></span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<span class="infoSubAreaUnit"><label class="baseLabel">背景<input id="userColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["color"] + '" list="color-list">'
		+ '<datalist id="color-list">' + userColorSet + '</option></datalist></label></span>'
		+ '<span class="infoSubAreaUnit"><label class="baseLabel">文字<input id="userTextColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["textColor"] + '" list="textColor-list">'
		+ '<datalist id="textColor-list">' + userTextColorSet + '</option></datalist></label></span>'
		+ '<span class="infoSubAreaUnit"><label class="baseLabel">枠線<input id="userBorderColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["borderColor"] + '" list="borderColor-list">'
		+ '<datalist id="borderColor-list">' + userBorderColorSet + '</option></datalist></label></span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<span class="infoSubAreaUnit"><textarea id="userInfoUserRemarks" class="remarksTextBox" rows="3" maxlength=' + remarksTextMaxLength + ' placeholder="備考入力欄">' + res["userRemarks"] + '</textarea></span>'
		+ '</div>'
		+ '</div>'
	);

	colorSet('userInfoCreateUser', res["createUserColor"], res["createUserTextColor"], res["createUserBorderColor"]);
	colorSet('userInfoUpdateUser', res["updateUserColor"], res["updateUserTextColor"], res["updateUserBorderColor"]);

	$('#userInfoDispAuthority').prop('checked', res["authority"]);
	$('#userInfoDispActiveFlg').prop('checked', res["activeFlg"]);
	$('#userInfoDispDefaultDeadlineFlg').prop('checked', res["defaultDeadlineFlg"]);
	$('#userInfoDispDeleteMessageFlg').prop('checked', res["deleteMessageFlg"]);
	$('#userInfoDispDoneAutoActiveFlg').prop('checked', res["doneAutoActiveFlg"]);

	$("#userInfoDispBasic").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=userInfoUpdate(' + res["userId"] + ')>更新</button></div>');

	$("#userInfoDispPerformance").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>レポート</div><div class="modalSubTitelLine"></div>');
	$("#userInfoDispPerformance").append(
		'<div id="infoPerformanceArea" class="infoSubArea">'

		+ '<div id="userReportvalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><label class="baseLabel">日付'
		+ '<input type="text" maxlength=' + dayTextMaxLength + ' id="performanceDayFrom" class="baseDayTextBox infoSubAreaUnit" name="searchDay" autocomplete="off" value="' + NowDate() + '">～'
		+ '<input type="text" maxlength=' + dayTextMaxLength + ' id="performanceDayTo" class="baseDayTextBox infoSubAreaUnit" name="searchDay" autocomplete="off" value="' + NowDate() + '">'
		+ '</label>'
		+ '</div>'

		+ '<div class="infoSubAreaGroup">'
		+ '<label class="baseLabel">集計単位<span class="baseRadioButtonBack">'
		+ '<input type="radio" id="aggregationUser" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationUser"><label for="aggregationUser" class="baseRadioButton-label" onclick="">ユーザ</label>'
		+ '<input type="radio" id="aggregationGroup" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationGroup"><label for="aggregationGroup" class="baseRadioButton-label" onclick="">グループ</label>'
		+ '<input type="radio" id="aggregationTab" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationTab" checked="checked"><label for="aggregationTab" class="baseRadioButton-label" onclick="">タブ</label>'
		+ '</span></label>'
		+ '</div>'

		+ '</div>'
	);

	$('[name="searchDay"]').datepicker();
	$("#userInfoDispPerformance").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=userReportGet(' + res["userId"] + ',\'' + res["userName"] + '\')>表示</button></div>');

	$("#userInfoDispWordMap").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>ワードマップ</div><div class="modalSubTitelLine"></div>');
	$("#userInfoDispWordMap").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=getUserToWord(' + res["userId"] + ',\'' + res["userName"] + '\',\'' + res["color"] + '\',\'' + res["textColor"] + '\',\'' + res["borderColor"] + '\')>表示</button></div>');

	$("#userInfoDispLoginData").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>ログイン情報</div><div class="modalSubTitelLine"></div>');
	$("#userInfoDispLoginData").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=getUserLoginInfo(' + res["userId"] + ')>表示</button></div>');



};

function getUserLoginInfo(userId) {
	LoadOn();
	var data = { 'userId': userId };
	ajaxPost(LOGININFOGET, data);
};

//ログイン情報表示
function loginInfoDisp(req, res) {
	CloseModal();
	ModalOn();
	$("#titelarea").append('<span id="userInfoDispTitel" >ログイン情報</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');

	$("#leftarea").append('<div id="userInfoDispLoginId" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="userInfoDispPassword" class="modalSubArea infoArea"></div>');


	if (res["systemUserFlg"] == 1) {
		var loginIdDisp = '<span class="infoSubAreaUnit"><label for="userInfoUserLoginId" class="baseLabel">ログインID</label><input type="text" maxlength=' + userTextMaxLength + ' id="userInfoUserLoginId" class="baseTextBox nameTextBox" value="' + res["email"] + '" autocomplete="off"></span>';
		var loginIdUpdateButton = '';
		var PasswordUpdateButton = '';
	} else {
		var loginIdDisp = '<span class="infoSubAreaUnit"><label for="userInfoUserLoginId" class="baseLabel">ログインID</label><input type="text" maxlength=' + userTextMaxLength + ' id="userInfoUserLoginId" class="baseTextBox nameTextBox" value="' + res["email"] + '" autocomplete="off">' + res["systemUserName"] + '</span>';
		var loginIdUpdateButton = '<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=loginIdUpdateRun(' + req["userId"] + ',\'' + res["systemUserName"] + '\')>更新</button></div>';
		var PasswordUpdateButton = '<div class="centerArea"><button type="button" id="loginPasswordEdit" class="modalSubButton subButtonSelect" onclick=loginPasswordEditDisp(' + req["userId"] + ')>編集メニュー</button></div>';
	}

	$("#userInfoDispLoginId").append(
		'<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="loginIdvalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup">'
		+ loginIdDisp
		+ '</div>'
		+ '</div>'
	);

	$("#userInfoDispLoginId").append(loginIdUpdateButton);


	$("#userInfoDispPassword").append(
		'<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="passwordvalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<span class="infoSubAreaUnit"><label for="userInfoUserLoginPassword" class="baseLabel">パスワード</label><input type="text" maxlength=' + userTextMaxLength + ' id="userInfoUserLoginPassword" class="baseTextBox nameTextBox" value="********" autocomplete="off"></span>'
		+ '</div>'
		+ '</div>'
	);

	$("#userInfoDispPassword").append(PasswordUpdateButton);

};

//ログインID更新実行
function loginIdUpdateRun(userId, systemUserName) {
	if (checkAdmin()) {
		$('#loginIdvalidationMessage').empty();
		var loginId = $('#userInfoUserLoginId').val();

		var postDataCheck = 1;
		if (!requiredCheck(loginId)) {
			var loginIdCheck = 1;
			postDataCheck = 0;
			$('#loginIdvalidationMessage').append("<div>ログインIDを入力してください</div>");
		}

		if (!checkAlphabetNumber(loginId)) {
			var loginIdCheck = 1;
			postDataCheck = 0;
			$('#loginIdvalidationMessage').append("<div>ログインIDは半角英数の中から入力してください</div>");
		}

		if (checkDoNotUseSymbol(loginId)) {
			var loginIdCheck = 1;
			postDataCheck = 0;
			$('#loginIdvalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
		}

		if (!textLengthCheck(loginId, 4, 8)) {
			var loginIdCheck = 1;
			postDataCheck = 0;
			$('#loginIdvalidationMessage').append("<div>ログインIDは4文字以上8文字以内で入力してください</div>");
		}

		if (postDataCheck) {
			LoadOn();
			loginId = loginId + systemUserName;
			var data = { 'userId': userId, 'loginId': loginId };
			ajaxPost(LOGINIDUPDATE, data);
		} else {
			textBoxColorChange('userInfoUserLoginId', loginIdCheck);
		}
	} else {
		notAdminAlert();
	}
};

//ログインパスワード編集メニュー表示
function loginPasswordEditDisp(userId) {
	if (checkAdmin()) {
		$('#userInfoUserLoginPassword').val("");
		$('#userInfoUserLoginPassword').val("");
		$('#loginPasswordEdit').remove();
		$("#userInfoDispPassword").append('<div class="centerArea"><sman><button type="button" id="loginPasswordEditClose" class="modalSubButton subButtonSelect" onclick=loginPasswordEditDispClose(' + userId + ')>キャンセル</button></span>'
			+ '<sman><button type="button" id="loginPasswordUpdate" class="modalSubButton subButtonUpdate" onclick=loginPasswordUpdateRun(' + userId + ')>更新</button></span></div>');
	} else {
		notAdminAlert();
	}
};

//ログインパスワード編集メニュー閉じる
function loginPasswordEditDispClose(userId) {
	$('#userInfoUserLoginPassword').val("");
	$('#userInfoUserLoginPassword').val("********");
	$('#loginPasswordEditClose').remove();
	$('#loginPasswordUpdate').remove();
	$("#userInfoDispPassword").append('<div class="centerArea"><button type="button" id="loginPasswordEdit" class="modalSubButton subButtonSelect" onclick=loginPasswordEditDisp(' + userId + ')>編集メニュー</button></div>');
};

//ログインパスワード更新実行
function loginPasswordUpdateRun(userId) {
	$('#passwordvalidationMessage').empty();
	var password = $('#userInfoUserLoginPassword').val();

	var postDataCheck = 1;
	if (!requiredCheck(password)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#passwordvalidationMessage').append("<div>パスワードを入力してください</div>");
	}

	if (!checkAlphabetNumberSymbol(password)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#passwordvalidationMessage').append("<div>パスワードは半角英数記号の中から入力してください</div>");
	}

	if (checkDoNotUseSymbol(password)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#passwordvalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
	}

	if (!textLengthCheck(password, 8, 12)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#passwordvalidationMessage').append("<div>パスワードは8文字以上12文字以内で入力してください</div>");
	}

	if (postDataCheck) {
		LoadOn();
		var data = { 'userId': userId, 'password': password };
		ajaxPost(LOGINPASSWORDUPDATE, data);
	} else {
		textBoxColorChange('userInfoUserLoginPassword', passwordCheck);
	}

};

//ユーザレポート表示
function userReportGet(userId, userName) {
	var postDataCheck = dateCheckWrap('userReportvalidationMessage', 'performanceDayTo', 'performanceDayFrom', reportDayRange, 'day');
	if (postDataCheck) {
		LoadOn();
		var dayTo = $('#performanceDayTo').val();
		var dayFrom = $('#performanceDayFrom').val();
		var aggregationUnit = $('input[name="aggregationUnit"]:checked').val();
		var data = { 'userId': userId, 'userName': userName, 'dayTo': dayTo, 'dayFrom': dayFrom, 'aggregationUnit': aggregationUnit };
		ajaxPost(USERREPORT, data);
	}
};

//ユーザ情報更新
function userInfoUpdate(userId) {
	if (checkAdmin()) {
		$('#userInfovalidationMessage').empty();
		var userName = $('#userInfoUserName').val();
		var userRemarks = $('#userInfoUserRemarks').val();
		var authority = Number($('#userInfoDispAuthority').prop('checked'));
		var activeFlg = Number($('#userInfoDispActiveFlg').prop('checked'));
		var defaultDeadlineFlg = Number($('#userInfoDispDefaultDeadlineFlg').prop('checked'));
		var deleteMessageFlg = Number($('#userInfoDispDeleteMessageFlg').prop('checked'));
		var doneAutoActiveFlg = Number($('#userInfoDispDoneAutoActiveFlg').prop('checked'));
		var color = $("#userColorSelect").val();
		var textColor = $("#userTextColorSelect").val();
		var borderColor = $("#userBorderColorSelect").val();

		var postDataCheck = 1;
		if (!requiredCheck(userName)) {
			var userNameCheck = 1;
			postDataCheck = 0;
			$('#userInfovalidationMessage').append("<div>ユーザ名を入力してください</div>");
		}

		if (checkDoNotUseSymbol(userName)) {
			var userNameCheck = 1;
			postDataCheck = 0;
			$('#userInfovalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
		}

		if (postDataCheck) {
			LoadOn();
			var data = { 'userId': userId, 'userName': userName, 'authority': authority, 'activeFlg': activeFlg, 'color': color, 'textColor': textColor, 'borderColor': borderColor, 'userRemarks': userRemarks, 'defaultDeadlineFlg': defaultDeadlineFlg, 'deleteMessageFlg': deleteMessageFlg, 'doneAutoActiveFlg': doneAutoActiveFlg };
			ajaxPost(USERINFOUPDATE, data);
		} else {
			textBoxColorChange('userInfoUserName', userNameCheck);
			textBoxColorChange('userInfoUserRemarks', userRemarksCheck);
		}
	} else {
		notAdminAlert();
	}
};

function userCreate() {
	LoadOn();
	var data = {};
	ajaxPost(SYSTEMLOGINNAMEGET, data);
};

//ユーザ新規作成
function userCreateDisp(req, res) {
	ModalOn();

	$("#titelarea").append('<span id="userInfoDispTitel" >ユーザ新規作成</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');


	$("#freearea").append('<div id="userInfoDispAddArea" class="modalSubArea infoArea"></div>');
	$("#userInfoDispAddArea").append('<div id="userInfoDispBasic" ></div>');


	$("#userInfoDispBasic").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>ユーザ基本情報</div><div class="modalSubTitelLine"></div>');
	$("#userInfoDispBasic").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="userInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="userInfoUserName" class="baseLabel">ユーザ名</label><input type="text" maxlength=' + userTextMaxLength + ' id="userInfoUserName" class="baseTextBox" autocomplete="off"></span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="userInfoLoginId" class="baseLabel">ログインID</label><input type="text" maxlength=' + userTextMaxLength + ' id="userInfoLoginId" class="baseTextBox loginTextBox" autocomplete="off">' + res["systemUserName"] + '</span>'
		+ '<span class="infoSubAreaUnit"><label for="userInfoPassword" class="baseLabel">パスワード</label><input type="text" maxlength=' + remarksTextMaxLength + ' id="userInfoPassword" class="baseTextBox loginTextBox" autocomplete="off"></span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><textarea id="userInfoUserRemarks" class="remarksTextBox" rows="3" maxlength=' + remarksTextMaxLength + ' placeholder="備考入力欄"></textarea></span></div>'
		+ '</div>'
	);

	$("#userInfoDispAddArea").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=userInfoCreate(\'' + res["systemUserName"] + '\')>作成</button></div>');

};

//ユーザ新規作成実行
function userInfoCreate(systemUserName) {
	$('#userInfovalidationMessage').empty();
	var userName = $('#userInfoUserName').val();
	var userRemarks = $('#userInfoUserRemarks').val();
	var loginId = $('#userInfoLoginId').val();
	var password = $('#userInfoPassword').val();

	var postDataCheck = 1;
	if (!requiredCheck(userName)) {
		var userNameCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>ユーザ名を入力してください</div>");
	}

	if (checkDoNotUseSymbol(userName)) {
		var userNameCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
	}

	if (!requiredCheck(loginId)) {
		var loginIdCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>ログインIDを入力してください</div>");
	}

	if (!checkAlphabetNumber(loginId)) {
		var loginIdCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>ログインIDは半角英数の中から入力してください</div>");
	}

	if (!textLengthCheck(loginId, 4, 8)) {
		var loginIdCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>ログインIDは4文字以上8文字以内で入力してください</div>");
	}

	if (checkDoNotUseSymbol(loginId)) {
		var loginIdCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
	}

	if (!requiredCheck(password)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>パスワードを入力してください</div>");
	}

	if (!checkAlphabetNumberSymbol(password)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>パスワードは半角英数記号の中から入力してください</div>");
	}

	if (!textLengthCheck(password, 8, 12)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>パスワードは8文字以上12文字以内で入力してください</div>");
	}

	if (checkDoNotUseSymbol(password)) {
		var passwordCheck = 1;
		postDataCheck = 0;
		$('#userInfovalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
	}

	if (postDataCheck) {
		LoadOn();
		var loginId = loginId + systemUserName;
		var data = { 'userName': userName, 'userRemarks': userRemarks, 'loginId': loginId, 'password': password };
		ajaxPost(USERADD, data);
	} else {
		textBoxColorChange('userInfoUserName', userNameCheck);
		textBoxColorChange('userInfoUserRemarks', userRemarksCheck);
		textBoxColorChange('userInfoLoginId', loginIdCheck);
		textBoxColorChange('userInfoPassword', passwordCheck);
	}


};

/*----------------------------------------------<グループ>----------------------------------------------*/
//指定したIDのグループ情報取得
function groupinfoget(groupId) {
	LoadOn();
	var data = { 'groupId': groupId };
	ajaxPost(GROUPINFOGET, data);
};

//グループ名押下時の処理
function groupUserGet(groupId, groupName) {
	CloseModal();
	LoadOn();
	var data = { 'disp': 'group', 'groupId': groupId, 'groupName': groupName };
	ajaxPost(GROUPUSERGET, data);
};

//サーバからグループデータ取得
function groupInfoDisp(req, res) {

	ModalOn();
	$("#titelarea").append('<span id="groupInfoDispTitel" >' + res["groupName"] + 'グループ</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');

	$("#freearea").append('<div id="groupInfoDispGroupUser" class="modalSubArea infoArea"></div>');

	$("#groupInfoDispGroupUser").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>グループユーザ</div><div class="modalSubTitelLine"></div>');
	$("#groupInfoDispGroupUser").append('<span id="groupInfoGroupUser" class="infoSubAreaUnit baseLabel" >現在のグループ所属ユーザ</span>');

	res['groupUser'].forEach(function (value) {
		var currentUserId = 'currentUserId' + value.userId;
		$("#groupInfoDispGroupUser").append('<span id="' + currentUserId + '" class="userNameDisp" name="currentUser">' + value.userName
			+ '</span>');
		$("#" + currentUserId).css({ 'background': value.color, 'color': value.textColor, 'border': 'solid 1px ' + value.borderColor });

	});

	$("#groupInfoDispGroupUser").append('<div class="centerArea"><button type="button" id="groupUserEditButton" class="modalSubButton subButtonSelect" onclick=groupUserChangModal(\'' + res["groupId"] + '\',\'' + res["groupName"] + '\')>編集メニュー</button></div>');


	$("#leftarea").append('<div id="groupInfoDispBasic" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="groupInfoDispPerformance" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="groupInfoDispWordMap" class="modalSubArea infoArea"></div>');

	$("#groupInfoDispBasic").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>グループ基本情報</div><div class="modalSubTitelLine"></div>');
	$("#groupInfoDispBasic").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="groupInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="groupInfoGroupName" class="baseLabel">グループ名</label><input type="text" id="groupInfoGroupName" class="baseTextBox" value="' + res["groupName"] + '" autocomplete="off"></span>'
		+ '<span class="infoSubAreaUnit"><input type="checkbox" id="groupInfoDispActiveFlg" class="baseCheckBox"/><label for="groupInfoDispActiveFlg" class="baseCheckBox-label baseLabel">無効化</label></span></div>'
		+ '<div class="infoSubAreaGroup"><label for="groupInfoCreateUser" class="baseLabel infoSubAreaUnit">作成者</label><span id="groupInfoCreateUser" class="userNameDisp">' + res["createUserName"] + '</span>'
		+ '<label for="groupInfoCreateDay" class="baseLabel infoSubAreaUnit">作成日時</label><span id="groupInfoCreateDay">' + res["createDay"] + '</span></div>'
		+ '<div class="infoSubAreaGroup"><label for="groupInfoUpdateUser" class="baseLabel infoSubAreaUnit">作成者</label><span id="groupInfoUpdateUser" class="userNameDisp">' + res["updateUserName"] + '</span>'
		+ '<label for="groupInfoUpdateDay" class="baseLabel infoSubAreaUnit">更新日時</label><span id="groupInfoUpdateDay">' + res["updateDay"] + '</span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label class="baseLabel">背景<input id="groupColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["color"] + '" list="color-list">'
		+ '<datalist id="color-list">' + groupColorSet + '</option></datalist></label></span>'
		+ '<span class="infoSubAreaUnit"><label class="baseLabel">文字<input id="groupTextColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["textColor"] + '" list="textColor-list">'
		+ '<datalist id="textColor-list">' + groupTextColorSet + '</option></datalist></label></span>'
		+ '<span class="infoSubAreaUnit"><label class="baseLabel">枠線<input id="groupBorderColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["borderColor"] + '" list="borderColor-list">'
		+ '<datalist id="borderColor-list">' + groupBorderColorSet + '</option></datalist></label></span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><textarea id="groupInfoGroupRemarks" class="remarksTextBox" rows="3" maxlength=' + remarksTextMaxLength + ' placeholder="備考入力欄">' + res["groupRemarks"] + '</textarea></span></div>'
		+ '</div>'
	);

	colorSet('groupInfoCreateUser', res["createUserColor"], res["createUserTextColor"], res["createUserBorderColor"]);
	colorSet('groupInfoUpdateUser', res["updateUserColor"], res["updateUserTextColor"], res["updateUserBorderColor"]);


	$('#groupInfoDispActiveFlg').prop('checked', res["activeFlg"]);

	$("#groupInfoDispBasic").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=groupInfoUpdate(' + res["groupId"] + ')>更新</button></div>');


	$("#groupInfoDispPerformance").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>レポート</div><div class="modalSubTitelLine"></div>');
	$("#groupInfoDispPerformance").append('<div id="infoPerformanceArea" class="infoSubArea">'
		+ '<div id="groupReportvalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><label class="baseLabel">日付'
		+ '<input type="text" maxlength=' + dayTextMaxLength + ' id="performanceDayFrom" class="baseDayTextBox infoSubAreaUnit" name="searchDay" autocomplete="off" value="' + NowDate() + '">～'
		+ '<input type="text" maxlength=' + dayTextMaxLength + ' id="performanceDayTo" class="baseDayTextBox infoSubAreaUnit" name="searchDay" autocomplete="off" value="' + NowDate() + '">'
		+ '</label>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label class="baseLabel">集計単位<span class="baseRadioButtonBack">'
		+ '<input type="radio" id="aggregationUser" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationUser"><label for="aggregationUser" class="baseRadioButton-label" onclick="">ユーザ</label>'
		+ '<input type="radio" id="aggregationGroup" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationGroup"><label for="aggregationGroup" class="baseRadioButton-label" onclick="">グループ</label>'
		+ '<input type="radio" id="aggregationTab" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationTab" checked="checked"><label for="aggregationTab" class="baseRadioButton-label" onclick="">タブ</label>'
		+ '</span></label>'
		+ '</div>'
		+ '</div>'
	);
	$("#groupInfoDispPerformance").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=groupReportGet(' + res["groupId"] + ',\'' + res["groupName"] + '\')>表示</button></div>');

	$("#groupInfoDispWordMap").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>ワードマップ</div><div class="modalSubTitelLine"></div>');
	$("#groupInfoDispWordMap").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=getGroupToWord(' + res["groupId"] + ',\'' + res["groupName"] + '\',\'' + res["color"] + '\',\'' + res["textColor"] + '\',\'' + res["borderColor"] + '\')>表示</button></div>');

	$('[name="searchDay"]').datepicker();

};

//グループモーダル内の編集ボタン押下時の処理
function groupUserAddModal() {
	var data = { 'type': 'add' };
	MiniLoadOn();
	ajaxPost(GROUPNOTUSERGET, data);
};

//グループモーダル内の編集ボタン押下時の処理
function groupUserChangModal(groupId, groupName) {
	if (checkAdmin()) {
		var data = { 'type': 'update', 'groupId': groupId, 'groupName': groupName };
		MiniLoadOn();
		ajaxPost(GROUPNOTUSERGET, data);
	} else {
		notAdminAlert();
	}
};

//グループ内の所属ユーザ変更
function groupUserChangDisp(req, res) {

	$("#groupUserEditButton").remove();

	$("#groupInfoDispGroupUser").append('<div id="groupUserChangDisp"</div>');

	var updateButton = '';

	$("#groupUserChangDisp").append('<div id="groupId" data-groupId=' + req["groupId"] + '></div>');

	$("#groupUserChangDisp").append('<div class="centerArea"><div id="groupUserArea" class="candidateListArea"></div></div>');
	$("#groupUserChangDisp").append('<div class="centerArea"><div id="allUserArea" class="selectArea"></div></div>');

	$("#groupUserArea").append('<div id="groupUserText">グループ所属ユーザ</div>');
	$("#allUserArea").append('<div id="allUserText">ユーザ一覧</div>');

	/*新規ではなく更新画面の場合の判定*/
	if (req['type'] == 'update') {
		updateButton = '<button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=groupUserChangRun()>変更を反映</button>';
		res['groupUser'].forEach(function (value) {
			var changUserId = 'changUserId' + value.userId;
			$("#groupUserArea").append('<div id="' + changUserId + '" class="userName" name="addUser" data-userId=' + value.userId + '>' + value.userName
				+ '<div class="userGroupDelete modalBotton" onclick="dispGroupDelete(\'' + value.userId + '\',\'' + value.userName + '\',\'' + value.color + '\',\'' + value.textColor + '\',\'' + value.borderColor + '\')">-</div>'
				+ '</div>');
			colorSet(changUserId, value.color, value.textColor, value.borderColor);

		});
	}


	res['groupNotUser'].forEach(function (value) {
		var changUserId = 'changUserId' + value.userId;

		$("#allUserArea").append('<div id="' + changUserId + '" class="userName" name="deleteUser" data-userId=' + value.userId + '>' + value.userName
			+ '<div class="userGroupAdd modalBotton" onclick="dispGroupAdd(\'' + value.userId + '\',\'' + value.userName + '\',\'' + value.color + '\',\'' + value.textColor + '\',\'' + value.borderColor + '\')">+</div>'
			+ '</div>');
		colorSet(changUserId, value.color, value.textColor, value.borderColor);

	});

	$("#groupUserChangDisp").append('<div class="centerArea">' + updateButton
		+ '<button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=groupUserChangDispClose(\'' + req["groupId"] + '\',\'' + req["groupName"] + '\')>キャンセル</button></div>');

};

function dispGroupDelete(userId, userName, color, textColor, borderColor) {
	var changUserId = 'changUserId' + userId;
	$("#" + changUserId).remove();
	$("#allUserArea").append('<div id="' + changUserId + '" class="userName" name="deleteUser" data-userId=' + userId + '>' + xssEscapeEncode(userName)
		+ '<div class="userGroupAdd modalBotton" onclick="dispGroupAdd(' + userId + ',\'' + xssEscapeEncode(userName) + '\',\'' + color + '\',\'' + textColor + '\',\'' + borderColor + '\')">+</div>'
		+ '</div>');
	colorSet(changUserId, color, textColor, borderColor);

};

function dispGroupAdd(userId, userName, color, textColor, borderColor) {
	var changUserId = 'changUserId' + userId;
	$("#" + changUserId).remove();
	$("#groupUserArea").append('<div id="' + changUserId + '" class="userName" name="addUser" data-userId=' + userId + '>' + xssEscapeEncode(userName)
		+ '<div class="userGroupDelete modalBotton" onclick="dispGroupDelete(' + userId + ',\'' + xssEscapeEncode(userName) + '\',\'' + color + '\',\'' + textColor + '\',\'' + borderColor + '\')">-</div>'
		+ '</div>');
	colorSet(changUserId, color, textColor, borderColor);
};

function groupUserChangRun() {
	LoadOn();
	var userIdArray = [];
	$('div[name="addUser"]').each(function (i) {
		userIdArray.push($(this).attr("data-userId"));
	});
	var groupId = $("#groupId").attr("data-groupId");
	var data = { 'groupId': groupId, 'userIdArray': userIdArray };
	ajaxPost(GROUPUSERCHANGE, data);
};

function groupUserChangDispClose(groupId, groupName) {
	$("#groupUserChangDisp").remove();
	$("#groupInfoDispGroupUser").append('<div class="centerArea"><button type="button" id="groupUserEditButton" class="modalSubButton subButtonSelect" onclick=groupUserChangModal(\'' + groupId + '\',\'' + groupName + '\')>編集メニュー</button></div>');
};

//グループレポート表示
function groupReportGet(groupId, groupName) {
	var postDataCheck = dateCheckWrap('groupReportvalidationMessage', 'performanceDayTo', 'performanceDayFrom', reportDayRange, 'day');
	if (postDataCheck) {
		LoadOn();
		var dayTo = $('#performanceDayTo').val();
		var dayFrom = $('#performanceDayFrom').val();
		var aggregationUnit = $('input[name="aggregationUnit"]:checked').val();
		var data = { 'groupId': groupId, 'groupName': groupName, 'dayTo': dayTo, 'dayFrom': dayFrom, 'aggregationUnit': aggregationUnit };
		ajaxPost(GROUPREPORT, data);
	}
};

//グループ新規作成
function groupCreate() {
	ModalOn();

	$("#titelarea").append('<span id="groupInfoDispTitel" >グループ新規作成</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');


	$("#freearea").append('<div id="groupInfoDispAddArea" class="modalSubArea infoArea"></div>');
	$("#groupInfoDispAddArea").append('<div id="groupInfoDispBasic" ></div>');
	$("#groupInfoDispAddArea").append('<div id="groupInfoDispGroupUser" ></div>');

	$("#groupInfoDispGroupUser").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>グループユーザ</div><div class="modalSubTitelLine"></div>');
	$("#groupInfoDispGroupUser").append('<span id="groupInfoGroupUser" class="infoSubAreaUnit baseLabel" >グループの所属ユーザを選択する※任意</span>');
	$("#groupInfoDispGroupUser").append('<button type="button" id="groupUserEditButton" class="modalSubButton subButtonSelect" onclick=groupUserAddModal()>選択メニュー</button>');

	$("#groupInfoDispBasic").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>グループ基本情報</div><div class="modalSubTitelLine"></div>');
	$("#groupInfoDispBasic").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="groupInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="groupInfoGroupName" class="baseLabel">グループ名</label><input type="text" maxlength=' + groupTextMaxLength + ' id="groupInfoGroupName" class="baseTextBox" autocomplete="off"></span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><textarea id="groupInfoGroupRemarks" class="remarksTextBox" rows="3" maxlength=' + remarksTextMaxLength + ' placeholder="備考入力欄"></textarea></span></div>'
		+ '</div>'
	);

	$("#groupInfoDispAddArea").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=groupInfoCreate()>作成</button></div>');

};

//グループ新規作成実行
function groupInfoCreate() {
	$('#groupInfovalidationMessage').empty();
	var groupName = $('#groupInfoGroupName').val();
	var groupRemarks = $('#groupInfoGroupRemarks').val();
	var userIdArray = [];
	$('div[name="addUser"]').each(function (i) {
		userIdArray.push($(this).attr("data-userId"));
	});

	var postDataCheck = 1;
	if (!requiredCheck(groupName)) {
		var groupNameCheck = 1;
		postDataCheck = 0;
		$('#groupInfovalidationMessage').append("<div>グループ名を入力してください</div>");
	}

	if (checkDoNotUseSymbol(groupName)) {
		var groupNameCheck = 1;
		postDataCheck = 0;
		$('#groupInfovalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
	}

	if (postDataCheck) {
		LoadOn();
		var data = { 'groupName': groupName, 'groupRemarks': groupRemarks, 'userIdArray': userIdArray };
		ajaxPost(GROUPADD, data);
	} else {
		textBoxColorChange('groupInfoGroupName', groupNameCheck);
		textBoxColorChange('groupInfoGroupRemarks', groupRemarksCheck);
	}


};

//グループ更新実行
function groupInfoUpdate(groupId) {
	if (checkAdmin()) {
		$('#groupInfovalidationMessage').empty();
		var groupName = $('#groupInfoGroupName').val();
		var groupRemarks = $('#groupInfoGroupRemarks').val();
		var activeFlg = Number($('#groupInfoDispActiveFlg').prop('checked'));
		var color = $("#groupColorSelect").val();
		var textColor = $("#groupTextColorSelect").val();
		var borderColor = $("#groupBorderColorSelect").val();

		var postDataCheck = 1;
		if (!requiredCheck(groupName)) {
			var groupNameCheck = 1;
			postDataCheck = 0;
			$('#groupInfovalidationMessage').append("<div>グループ名を入力してください</div>");
		}

		if (checkDoNotUseSymbol(groupName)) {
			var groupNameCheck = 1;
			postDataCheck = 0;
			$('#groupInfovalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
		}

		if (postDataCheck) {
			LoadOn();
			var data = { 'groupId': groupId, 'groupName': groupName, 'color': color, 'textColor': textColor, 'borderColor': borderColor, 'groupRemarks': groupRemarks, 'activeFlg': activeFlg };
			ajaxPost(GROUPINFOUPDATE, data);
		} else {
			textBoxColorChange('groupInfoGroupName', groupNameCheck);
			textBoxColorChange('groupInfoGroupRemarks', groupRemarksCheck);
		}
	} else {
		notAdminAlert();
	}
};




/*----------------------------------------------<タブ>----------------------------------------------*/
//タブ詳細
function tabInfoDisp(req, res) {
	ModalOn();
	$("#titelarea").append('<span id="tabInfoDispTitel" >' + res["tabName"] + 'タブ</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');


	$("#leftarea").append('<div id="tabInfoDispBasic" class="modalSubArea infoArea"></div>');
	if (req["disp"] == WORKDISP) {
		$("#leftarea").append('<div id="tabInfoDoneCategoryArchive" class="modalSubArea infoArea"></div>');
		$("#rightarea").append('<div id="tabInfoDispCategoryFilter" class="modalSubArea infoArea"></div>');
	}
	$("#rightarea").append('<div id="tabInfoDispPerformance" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="tabInfoDispWordMap" class="modalSubArea infoArea"></div>');
	$("#freearea").append('<div id="tabInfoDispOwn" class="modalSubArea infoArea"></div>');

	var userOrGroupHtml = "";
	var groupChacge = "";
	var userOrGroupColor = "";
	var userOrGroupTextColor = "";
	var userOrGroupBorderColor = "";
	if (res["groupFlg"] == "1") {
		userOrGroupHtml = '<span class="baseLabel">現在の所有グループ</span><span id="userOrGroupName" class="userNameDisp" onclick="groupinfoget(' + res["groupInfo"]["groupId"] + ')">' + res["groupInfo"]["groupName"] + '</span>';
		groupChacge = '<div class="centerArea"><button type="button" id="tabOwnEditButton" class="modalSubButton subButtonSelect" onclick="tabOwnDisp(\'group\',\'' + req["tabId"] + '\',\'' + res["groupInfo"]["groupId"] + '\',\'' + res["groupInfo"]["groupName"] + '\',\'' + req["disp"] + '\')">編集メニュー</button></div>';
		userOrGroupColor = res["groupInfo"]["color"];
		userOrGroupTextColor = res["groupInfo"]["textColor"];
		userOrGroupBorderColor = res["groupInfo"]["borderColor"];
	} else {
		userOrGroupHtml = '<span class="baseLabel">現在の所有ユーザ</span><span id="userOrGroupName" class="userNameDisp" onclick="userinfoget(' + res["userInfo"]["userId"] + ')">' + res["userInfo"]["userName"] + '</span>';
		groupChacge = '<div class="centerArea"><button type="button" id="tabOwnEditButton" class="modalSubButton subButtonSelect" onclick="tabOwnDisp(\'user\',' + res["tabId"] + ',\'' + res["userInfo"]["userId"] + '\',\'' + res["userInfo"]["userName"] + '\',\'' + req["disp"] + '\')">グループタブ化</button></div>';
		userOrGroupColor = res["userInfo"]["color"];
		userOrGroupTextColor = res["userInfo"]["textColor"];
		userOrGroupBorderColor = res["userInfo"]["borderColor"];
	}

	$("#tabInfoDispBasic").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>タブ基本情報</div><div class="modalSubTitelLine"></div>');
	$("#tabInfoDispBasic").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="tabInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaTab"><span class="infoSubAreaUnit"><label for="tabInfoTabName" class="baseLabel">タブ名</label><input type="text" maxlength=' + tabTextMaxLength + ' id="tabInfoTabName" class="baseTextBox" value="' + res["tabName"] + '" autocomplete="off"></span>'
		+ '<span class="infoSubAreaUnit"><input type="checkbox" id="tabInfoDispArchiveFlg" class="baseCheckBox"/><label for="tabInfoDispArchiveFlg" class="baseCheckBox-label baseLabel">無効化</label></span></div>'
		+ '<div class="infoSubAreaTab">'
		+ '<span class="infoSubAreaUnit"><label for="tabInfoTabDeadline" class="baseLabel">期限日</label><input type="text" maxlength=' + dayTextMaxLength + ' id="tabInfoTabDeadline" name="searchDay" class="baseDayTextBox" value="' + dateNullChesk(res["tabDeadline"]) + '" placeholder="期限なし" autocomplete="off"></span></div>'
		+ '<div class="infoSubAreaTab"><label for="tabInfoCreateUser" class="baseLabel infoSubAreaUnit">作成者</label><span id="tabInfoCreateUser" class="userNameDisp">' + res["createUserName"] + '</span>'
		+ '<label for="tabInfoCreateDay" class="baseLabel infoSubAreaUnit">作成日時</label><span id="tabInfoCreateDay">' + res["createDay"] + '</span></div>'
		+ '<div class="infoSubAreaTab"><label for="tabInfoUpdateUser" class="baseLabel infoSubAreaUnit">更新者</label><span id="tabInfoUpdateUser" class="userNameDisp">' + res["updateUserName"] + '</span>'
		+ '<label for="tabInfoUpdateDay" class="baseLabel infoSubAreaUnit">更新日時</label><span id="tabInfoUpdateDay">' + res["updateDay"] + '</span></div>'
		+ '<div class="infoSubAreaTab"><span class="infoSubAreaUnit"><label class="baseLabel">背景<input id="tabColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["color"] + '" list="color-list">'
		+ '<datalist id="color-list">' + tabColorSet + '</option></datalist></label></span>'
		+ '<span class="infoSubAreaUnit"><label class="baseLabel">文字<input id="tabTextColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["textColor"] + '" list="textColor-list">'
		+ '<datalist id="textColor-list">' + tabTextColorSet + '</option></datalist></label></span>'
		+ '<span class="infoSubAreaUnit"><label class="baseLabel">枠線<input id="tabBorderColorSelect" class="baseSelectBox colorSelectBox" type="color" value="' + res["borderColor"] + '" list="borderColor-list">'
		+ '<datalist id="borderColor-list">' + tabBorderColorSet + '</option></datalist></label></span></div>'
		+ '<span class="infoSubAreaUnit"><textarea id="tabInfoTabRemarks" class="remarksTextBox" rows="3" maxlength=' + remarksTextMaxLength + ' placeholder="備考入力欄">' + res["tabRemarks"] + '</textarea></span>'
		+ '</div>'
	);

	colorSet('tabInfoCreateUser', res["createUserColor"], res["createUserTextColor"], res["createUserBorderColor"]);
	colorSet('tabInfoUpdateUser', res["updateUserColor"], res["updateUserTextColor"], res["updateUserBorderColor"]);

	$('#tabInfoDispArchiveFlg').prop('checked', res["archiveFlg"]);

	$("#tabInfoDispBasic").append('<div class="centerArea"><button type="button" id="tabUpdateButton" class="modalSubButton subButtonUpdate" onclick=tabInfoUpdate(' + res["tabId"] + ',\'' + req["disp"] + '\')>更新</button>'
		+ '<button type="button" id="tabDeleteButton" class="modalSubButton subButtonDelete" onclick="tabDelete(' + res["tabId"] + ',\'' + req["domTabId"] + '\',\'' + req["domMainAreaId"] + '\',\'' + res["tabName"] + '\')">削除</button></div>');

	$("#tabInfoDoneCategoryArchive").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>完了済カテゴリのアーカイブ</div><div class="modalSubTitelLine"></div>');
	$("#tabInfoDoneCategoryArchive").append('<div id="categoryArchive" class="infoSubArea">'
		+ '<div class="infoSubAreaGroup">'
		+ '<label class="baseLabel">対象カテゴリ<span class="baseRadioButtonBack">'
		+ '<input type="radio" id="categoryArchiveMyUser" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryArchiveTarget" value="categoryArchiveMyUser" checked="checked"><label for="categoryArchiveMyUser" class="baseRadioButton-label" onclick="">自ユーザ</label>'
		+ '<input type="radio" id="categoryArchiveAll" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryArchiveTarget" value="categoryArchiveAll"><label for="categoryArchiveAll" class="baseRadioButton-label" onclick="">全て</label>'
		+ '</span> </label>'
		+ '</div>'
		+ '</div>'
	);
	$("#tabInfoDoneCategoryArchive").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=categoryArchiveRun(' + res["tabId"] + ',\'' + res["groupFlg"] + '\',\'' + req["tabCategoryList"] + '\',\'' + res["color"] + '\',\'' + res["textColor"] + '\',\'' + res["borderColor"] + '\')>実行</button></div>');

	$("#tabInfoDispPerformance").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>レポート</div><div class="modalSubTitelLine"></div>');
	$("#tabInfoDispPerformance").append('<div id="infoPerformanceArea" class="infoSubArea">'
		+ '<div id="tabReportvalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<div class="infoSubAreaTab"><label class="baseLabel">日付'
		+ '<input type="text" maxlength=' + dayTextMaxLength + ' id="performanceDayFrom" class="baseDayTextBox infoSubAreaUnit" name="searchDay" autocomplete="off" value="' + NowDate() + '">～'
		+ '<input type="text" maxlength=' + dayTextMaxLength + ' id="performanceDayTo" class="baseDayTextBox infoSubAreaUnit" name="searchDay" autocomplete="off" value="' + NowDate() + '">'
		+ '</label>'
		+ '</div>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label class="baseLabel">集計単位<span class="baseRadioButtonBack">'
		+ '<input type="radio" id="aggregationUser" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationUser" checked="checked"><label for="aggregationUser" class="baseRadioButton-label" onclick="">ユーザ</label>'
		+ '<input type="radio" id="aggregationTab" class="radioGroup infoSubAreaUnit baseRadioButton" name="aggregationUnit" value="aggregationTab"><label for="aggregationTab" class="baseRadioButton-label" onclick="">タブ</label>'
		+ '</span> </label>'
		+ '</div>'
		+ '</div>'
	);
	$("#tabInfoDispPerformance").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=tabReportGet(' + res["tabId"] + ',\'' + res["tabName"] + '\')>表示</button></div>');

	$("#tabInfoDispWordMap").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>ワードマップ</div><div class="modalSubTitelLine"></div>');
	$("#tabInfoDispWordMap").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=getTabToWord(' + res["tabId"] + ',\'' + res["tabName"] + '\',\'' + res["color"] + '\',\'' + res["textColor"] + '\',\'' + res["borderColor"] + '\')>表示</button></div>');

	$("#tabInfoDispCategoryFilter").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>フィルタ</div><div class="modalSubTitelLine"></div>');
	$("#tabInfoDispCategoryFilter").append('<div id="infoPerformanceArea" class="infoSubArea">'
		+ '<div class="infoSubAreaGroup">'
		+ '<label class="baseLabel">担当ユーザ<span class="baseRadioButtonBack">'
		+ '<input type="radio" id="categoryFilterAllOwner" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryFilterOwner" value="categoryFilterAllOwner" checked="checked"><label for="categoryFilterAllOwner" class="baseRadioButton-label">全て</label>'
		+ '<input type="radio" id="categoryFilterMyOwner" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryFilterOwner" value="categoryFilterMyOwner"><label for="categoryFilterMyOwner" class="baseRadioButton-label">自ユーザ</label>'
		+ '</span> </label>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label class="baseLabel">ステータス<span class="baseRadioButtonBack">'
		+ '<input type="radio" id="categoryFilterAllStatus" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryFilterStatus" value="categoryFilterAllStatus" checked="checked"><label for="categoryFilterAllStatus" class="baseRadioButton-label">全て</label>'
		+ '<input type="radio" id="categoryFilterSpecifyingStatus" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryFilterStatus" value="categoryFilterSpecifyingStatus"><label for="categoryFilterSpecifyingStatus" class="baseRadioButton-label">指定</label>'
		+ '</span> </label>'
		+ '<label class="baseLabel">：<select id="categoryFilterStatusValue" class="baseSelectBox">'
		+ '<option value="categoryFilterNotstarted">未着手</option>'
		+ '<option value="categoryFilterWorking">作業中</option>'
		+ '<option value="categoryFilterSuspended">中断中</option>'
		+ '<option value="categoryFilterWaiting">待機中</option>'
		+ '<option value="categoryFilterDone">完了済</option>'
		+ '</select></label>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label class="baseLabel">期限日<span class="baseRadioButtonBack">'
		+ '<input type="radio" id="categoryFilterAllDeadline" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryFilterDeadline" value="categoryFilterAllDeadline" checked="checked"><label for="categoryFilterAllDeadline" class="baseRadioButton-label">全て</label>'
		+ '<input type="radio" id="categoryFilterSpecifyingDeadline" class="radioGroup infoSubAreaUnit baseRadioButton" name="categoryFilterDeadline" value="categoryFilterSpecifyingDeadline"><label for="categoryFilterSpecifyingDeadline" class="baseRadioButton-label">指定</label>'
		+ '</span> </label>'
		+ '<span><label class="baseLabel">：～'
		+ '<input type="text" maxlength=' + dayTextMaxLength + ' id="categoryFilterToDeadline" class="baseDayTextBox infoSubAreaUnit" name="searchDay" autocomplete="off" value="' + NowDate() + '" placeholder="期限なし">'
		+ '</label>'
		+ '</span>'
		+ '</div>'
		+ '</div>'
	);
	$("#tabInfoDispCategoryFilter").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=categoryFilterRun(' + res["tabId"] + ',\'' + res["groupFlg"] + '\',\'' + req["tabCategoryList"] + '\',\'' + res["color"] + '\',\'' + res["textColor"] + '\',\'' + res["borderColor"] + '\')>実行</button></div>');

	tabCategoryFilterLocalSet(req["tabCategoryList"]);
	tabCategoryFilterCheckDisabled();

	$("#tabInfoDispOwn").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>タブ所有者</div><div class="modalSubTitelLine"></div>');
	$("#tabInfoDispOwn").append('<div class="infoSubAreaGroup">' + userOrGroupHtml + '</div>'
		+ groupChacge);
	$("#userOrGroupName").css({ 'background': userOrGroupColor, 'color': userOrGroupTextColor, 'border': 'solid 1px ' + userOrGroupBorderColor });

	$('[name="searchDay"]').datepicker();
};

//完了済みカテゴリのアーカイブ実行
function categoryArchiveRun(tabId, groupFlg, tabCategoryList, color, textColor, borderColor) {
	var categoryArchiveTarget = $('input[name="categoryArchiveTarget"]:checked').val();
	LoadOn();
	var data = { 'tabId': tabId, 'groupFlg': groupFlg, 'tabCategoryList': tabCategoryList, 'color': color, 'textColor': textColor, 'borderColor': borderColor, 'categoryArchiveTarget': categoryArchiveTarget };
	ajaxPost(CATEGORYDONEARCHIVE, data);
};

//ローカルのフィルター設定の読み込みと画面への設定
function tabCategoryFilterLocalSet(tabCategoryList) {
	var tabCategoryFilter = tabCategoryFilterLocalGet(tabCategoryList);

	switch (tabCategoryFilter["categoryFilterOwner"]) {
		case 'categoryFilterAllOwner':
			$('input[name=categoryFilterOwner]').val(['categoryFilterAllOwner']);
			break;
		case 'categoryFilterMyOwner':
			$('input[name=categoryFilterOwner]').val(['categoryFilterMyOwner']);
			break;
	}

	switch (tabCategoryFilter["categoryFilterDeadline"]) {
		case 'categoryFilterAllDeadline':
			$('input[name=categoryFilterDeadline]').val(['categoryFilterAllDeadline']);
			$('#categoryFilterToDeadline').prop('disabled', true);
			$('#categoryFilterToDeadline').css('background-color', '#CCCCCC');
			break;
		case 'categoryFilterSpecifyingDeadline':
			$('input[name=categoryFilterDeadline]').val(['categoryFilterSpecifyingDeadline']);
			$('#categoryFilterToDeadline').val(tabCategoryFilter["categoryFilterToDeadline"]);
			break;
	}

	switch (tabCategoryFilter["categoryFilterStatus"]) {
		case 'categoryFilterAllStatus':
			$('input[name=categoryFilterStatus]').val(['categoryFilterAllStatus']);
			$('#categoryFilterStatusValue').prop('disabled', true);
			$('#categoryFilterStatusValue').css('background-color', '#CCCCCC');
			break;
		case 'categoryFilterSpecifyingStatus':
			$('input[name=categoryFilterStatus]').val(['categoryFilterSpecifyingStatus']);
			$('#categoryFilterStatusValue').val(tabCategoryFilter["categoryFilterStatusValue"]);
			break;
	}
};

//カテゴリフィルタラジオボタンのチェックイベント定義
function tabCategoryFilterCheckDisabled() {
	$('input[name="categoryFilterStatus"]').change(function () {
		var checkd = $('input[name="categoryFilterStatus"]:checked').val();
		switch (checkd) {
			case 'categoryFilterAllStatus':
				$('#categoryFilterStatusValue').prop('disabled', true);
				$('#categoryFilterStatusValue').css('background-color', '#CCCCCC');
				break;
			case 'categoryFilterSpecifyingStatus':
				$('#categoryFilterStatusValue').prop('disabled', false);
				$('#categoryFilterStatusValue').css('background-color', '#e9edf7');
				break;
		}
	});

	$('input[name="categoryFilterDeadline"]').change(function () {
		var checkd = $('input[name="categoryFilterDeadline"]:checked').val();
		switch (checkd) {
			case 'categoryFilterAllDeadline':
				$('#categoryFilterToDeadline').prop('disabled', true);
				$('#categoryFilterToDeadline').css('background-color', '#CCCCCC');
				break;
			case 'categoryFilterSpecifyingDeadline':
				$('#categoryFilterToDeadline').prop('disabled', false);
				$('#categoryFilterToDeadline').css('background-color', '#e9edf7');
				break;
		}
	});
};

//タブ内カテゴリのフィルタ実行
function categoryFilterRun(tabId, groupFlg, tabCategoryList, color, textColor, borderColor) {

	var categoryFilterOwner = $('input[name="categoryFilterOwner"]:checked').val();
	var categoryFilterDeadline = $('input[name="categoryFilterDeadline"]:checked').val();
	var categoryFilterStatus = $('input[name="categoryFilterStatus"]:checked').val();

	var categoryFilterToDeadline = '';
	var categoryFilterStatusValue = '';

	var postDataCheck = 0;
	var warningMessage = "";

	switch (categoryFilterDeadline) {
		case 'categoryFilterAllDeadline':
			break;
		case 'categoryFilterSpecifyingDeadline':
			categoryFilterToDeadline = $("#categoryFilterToDeadline").val();
			if (requiredCheck(categoryFilterStatusValue)) {
				if (!dayCheck(categoryFilterStatusValue)) {
					postDataCheck = 1;
					warningMessage = "日付の形式が不正です";
				}
			}
			break;
	}

	switch (categoryFilterStatus) {
		case 'categoryFilterAllStatus':
			break;
		case 'categoryFilterSpecifyingStatus':
			categoryFilterStatusValue = $('#categoryFilterStatusValue').val();
			break;
	}

	var categoryFilterSet = {
		'categoryFilterOwner': categoryFilterOwner,
		'categoryFilterDeadline': categoryFilterDeadline,
		'categoryFilterToDeadline': categoryFilterToDeadline,
		'categoryFilterStatus': categoryFilterStatus,
		'categoryFilterStatusValue': categoryFilterStatusValue,
	};

	if (postDataCheck) {
		MessageDisp(WARNING, warningMessage, 5000);
	} else {
		LoadOn();
		var data = { 'tabId': tabId, 'groupFlg': groupFlg, 'tabCategoryList': tabCategoryList, 'color': color, 'textColor': textColor, 'borderColor': borderColor, 'categoryFilterSet': categoryFilterSet };
		localStorage.setItem(tabCategoryList, JSON.stringify(categoryFilterSet));
		ajaxPost(CATEGORYFILTER, data);
	}
};


function tabOwnChangDisp(req, res) {

	$("#tabOwnEditButton").remove();

	$("#tabInfoDispOwn").append('<div id="tabOwnChangDisp"</div>');

	var updateButton = '<button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=tabOwnChangRun(\'' + req["type"] + '\',\'' + req["tabId"] + '\',\'' + req["disp"] + '\')>変更を反映</button>';


	$("#tabOwnChangDisp").append('<div id="groupId" data-groupId=' + req["groupId"] + '></div>');

	$("#tabOwnChangDisp").append('<div class="centerArea"><div id="tabOwnArea" class="candidateListArea"></div></div>');
	$("#tabOwnChangDisp").append('<div class="centerArea"><div id="groupArea" class="selectArea"></div></div>');

	$("#tabOwnArea").append('<div id="tabOwnText" class="baseLabel">タブ所有グループ</div>');
	$("#groupArea").append('<div id="allUserText" class="baseLabel">グループ一覧</div>');


	var newOwnGroupInit;
	switch (req['type']) {
		case "user":
			newOwnGroupInit = '<div id="newOwnId" data-groupId=0>グループを選択してください</div>';

			break;
		case "group":
			newOwnGroupInit = '<div id="newOwnId"  class="userName" data-groupId=' + req['groupId'] + '>' + req['groupName'] + '</div>';

			break;
	}
	$("#tabOwnArea").append(newOwnGroupInit);

	res.forEach(function (value) {
		var changUserId = 'changUserId' + value.groupId;

		$("#groupArea").append('<div id="' + changUserId + '" class="userName" name="deleteUser" data-userId=' + value.groupId + '>' + value.groupName
			+ '<div class="userGroupAdd modalBotton" onclick="dispTabOwnChange(\'' + value.groupId + '\',\'' + value.groupName + '\',\'' + value.color + '\',\'' + value.textColor + '\',\'' + value.borderColor + '\')">+</div>'
			+ '</div>');
		colorSet(changUserId, value.color, value.textColor, value.borderColor);

	});

	$("#tabOwnChangDisp").append('<div class="centerArea">' + updateButton
		+ '<button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=tabOwnChangDispClose(\'' + req["type"] + '\',\'' + req["tabId"] + '\',\'' + req["groupId"] + '\',\'' + req["groupName"] + '\',\'' + req["disp"] + '\')>キャンセル</button></div>');

};

function dispTabOwnChange(groupId, groupName, color, textColor, borderColor) {
	$("#newOwnId").remove();
	$("#tabOwnArea").append('<div id="newOwnId" class="userName" data-groupId=' + groupId + '>' + xssEscapeEncode(groupName) + '</div>');
	colorSet("newOwnId", color, textColor, borderColor);
};

function tabOwnChangRun(type, tabId, disp) {

	var groupId = $('#newOwnId').attr("data-groupId");
	var data = { 'tabId': tabId, 'groupId': groupId, 'disp': disp };
	LoadOn();
	switch (type) {
		case "user":
			ajaxPost(GROUPTABCHANGE, data);

			break;
		case "group":
			ajaxPost(TABGROUPCHANGE, data);
			break;
	}

};

function tabOwnChangDispClose(type, tabId, groupId, groupName, disp) {
	$("#tabOwnChangDisp").remove();
	switch (type) {
		case "user":
			$("#tabInfoDispOwn").append('<div class="centerArea"><button type="button" id="tabOwnEditButton" class="modalSubButton subButtonSelect" onclick="tabOwnDisp(\'user\',' + tabId + ',\'' + groupId + '\',\'' + groupName + '\',\'' + disp + '\')">グループタブ化</button></div>');

			break;
		case "group":
			$("#tabInfoDispOwn").append('<div class="centerArea"><button type="button" id="tabOwnEditButton" class="modalSubButton subButtonSelect" onclick="tabOwnDisp(\'group\',\'' + tabId + '\',\'' + groupId + '\',\'' + groupName + '\',\'' + disp + '\')">編集メニュー</button></div>');

			break;
	}
};

function tabOwnDisp(type, tabId, id, name, disp) {
	if (checkAdmin()) {
		MiniLoadOn();
		switch (type) {
			case "user":
				var data = { 'type': 'user', 'tabId': tabId, 'userId': id, 'userName': name, 'disp': disp };
				ajaxPost(USERGROUPGET, data);
				break;
			case "group":
				var data = { 'type': 'group', 'tabId': tabId, 'groupId': id, 'groupName': name, 'disp': disp };
				ajaxPost(GROUPALLGET, data);
				break;
		}
	} else {
		notAdminAlert();
	}
};

function tabinfoget(tabId) {
	LoadOn();
	var data = { 'tabId': tabId, 'disp': ADMINDISP };
	ajaxPost(TABINFOGET, data);
};

function tabReportGet(tabId, tabName) {
	var postDataCheck = dateCheckWrap('tabReportvalidationMessage', 'performanceDayTo', 'performanceDayFrom', reportDayRange, 'day');
	if (postDataCheck) {
		LoadOn();
		var dayTo = $('#performanceDayTo').val();
		var dayFrom = $('#performanceDayFrom').val();
		var aggregationUnit = $('input[name="aggregationUnit"]:checked').val();
		var data = { 'tabId': tabId, 'tabName': tabName, 'dayTo': dayTo, 'dayFrom': dayFrom, 'aggregationUnit': aggregationUnit };
		ajaxPost(TABREPORT, data);
	}
};

//タブ更新実行
function tabInfoUpdate(tabId, disp) {
	if (checkAdmin()) {
		$('#tabInfovalidationMessage').empty();
		var tabName = $('#tabInfoTabName').val();
		var tabRemarks = $('#tabInfoTabRemarks').val();
		var tabDeadline = $("#tabInfoTabDeadline").val();
		var archiveFlg = Number($('#tabInfoDispArchiveFlg').prop('checked'));
		var color = $("#tabColorSelect").val();
		var textColor = $("#tabTextColorSelect").val();
		var borderColor = $("#tabBorderColorSelect").val();

		var postDataCheck = 1;
		if (!requiredCheck(tabName)) {
			var tabNameCheck = 1;
			postDataCheck = 0;
			$('#tabInfovalidationMessage').append("<div>タブ名を入力してください</div>");
		}

		if (requiredCheck(tabDeadline)) {
			if (!dayCheck(tabDeadline)) {
				var tabDeadlineCheck = 1;
				postDataCheck = 0;
				$('#tabInfovalidationMessage').append("<div>日付の形式が不正です</div>");
			}
		}

		if (postDataCheck) {
			LoadOn();
			var data = { 'tabId': tabId, 'tabName': tabName, 'tabDeadline': tabDeadline, 'color': color, 'textColor': textColor, 'borderColor': borderColor, 'tabRemarks': tabRemarks, 'archiveFlg': archiveFlg, 'disp': disp };
			ajaxPost(TABINFOUPDATE, data);
		} else {
			textBoxColorChange('tabInfoTabName', tabNameCheck);
			textBoxColorChange('tabInfoTabDeadline', tabDeadlineCheck);
			textBoxColorChange('tabInfoTabRemarks', tabRemarksCheck);
		}
	} else {
		notAdminAlert();
	}
};

//タブ追加ボタン押下時の処理
function tabAddDisp() {
	var dispDataSet = {};
	var popupPosition = { my: 'left top', at: 'right bottom', of: '#tabadd' };
	deleteCheckDialog("タブを新規作成", '<div class="baseLabel">タブ名<input type="text" maxlength=' + tabTextMaxLength + ' id="tabAddName" class="baseTextBox tabTextBox" autocomplete="off"></div>', "tabAdd", dispDataSet, popupPosition, 'subButtonUpdate');
};

function tabAddRun() {
	MiniLoadOn();
	var tabName = $("#tabAddName").val();
	var data = { 'tabName': tabName };
	ajaxPost(TABADD, data);
};


//タブ削除ボタン押下時の処理
function tabDelete(dbTabId, domTabId, domMainAreaId, tabName) {
	if (checkAdmin()) {
		var dispDataSet = { dbTabId: dbTabId, domTabId: domTabId, domMainAreaId: domMainAreaId, tabName: tabName };
		if (deleteCheck()) {
			var popupPosition = { my: 'left top', at: 'left top', of: '#tabDeleteButton' };
			deleteCheckDialog("タブを削除", xssEscapeEncode(tabName) + "を削除します", "tab", dispDataSet, popupPosition, 'subButtonDelete');
		} else {
			tabDeleteRun(dispDataSet);
		}
	} else {
		notAdminAlert();
	}
};


function tabDeleteRun(dispDataSet) {
	MiniLoadOn();
	var data = { 'tabId': dispDataSet["dbTabId"], 'tabName': dispDataSet["tabName"] };
	ajaxPost(TABDELETE, data);
	$("#" + dispDataSet["domTabId"]).remove();
	$("#" + dispDataSet["domMainAreaId"]).remove();
	tabAreaSize -= TABSIZE;
	$("#tabarea").css("width", tabAreaSize + "px");
};

/*----------------------------------------------<カテゴリ>----------------------------------------------*/

//カテゴリのユーザアイコン押下時の処理
function categoryUserDisp(categoryId, groupFlg, tabId, tabCategoryList, tabColor, tabTextColor, tabBorderColor, groupId, userId, userName, domCategoryAreaId) {
	if (checkCategoryMyUserAdmin(domCategoryAreaId)) {
		MiniLoadOn();
		var data = {
			'disp': 'user',
			'categoryId': categoryId, 'groupFlg': groupFlg, 'tabId': tabId, 'tabCategoryList': tabCategoryList, 'tabColor': tabColor, 'tabTextColor': tabTextColor, 'tabBorderColor': tabBorderColor,
			'groupId': groupId, 'userId': userId, 'userName': userName, 'domCategoryAreaId': domCategoryAreaId
		};
		ajaxPost(GROUPUSERGET, data);
	}
	else {
		categoryNotMyUserAlert();
	}
};

//カテゴリユーザ編集メニュー表示
function groupUserDisp(req, res) {

	$("#categoryUserEditButton").remove();

	$("#categoryUserEdit").append('<div id="categoryUserChangDisp"</div>');

	var updateButton = '';

	$("#categoryUserChangDisp").append('<div id="groupId" data-groupId=' + req["groupId"] + '></div>');

	$("#categoryUserChangDisp").append('<div class="centerArea"><div id="newUserArea" class="candidateListArea"></div></div>');
	$("#categoryUserChangDisp").append('<div class="centerArea"><div id="groupUserArea" class="selectArea"></div></div>');

	$("#newUserArea").append('<div id="newUserText" class="baseLabel">カテゴリ担当ユーザ</div>');
	$("#groupUserArea").append('<div id="groupUserText" class="baseLabel">グループ内ユーザ一覧</div>');

	updateButton = '<button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=categoryUserChangRun('
		+ '\'' + req["categoryId"] + '\',\'' + req["groupFlg"] + '\',\'' + req["tabId"] + '\',\'' + req["tabCategoryList"] + '\',\'' + req["tabColor"] + '\',\'' + req["tabTextColor"] + '\',\'' + req["tabBorderColor"] + '\')>変更を反映</button>';
	$("#newUserArea").append('<div id="newUserId"  class="userName" data-userId=' + req['userId'] + '>' + req['userName']
		+ '</div>');


	res.forEach(function (value) {
		var changUserId = 'changUserId' + value.userId;

		$("#groupUserArea").append('<div id="' + changUserId + '"  class="userName" name="changUser" data-userId=' + value.userId + '>' + value.userName
			+ '<div class="userGroupAdd modalBotton" onclick="dispCategoryUserChange(\'' + value.userId + '\',\'' + value.userName + '\')">+</div>'
			+ '</div>');
		colorSet(changUserId, value.color, value.textColor, value.borderColor);

	});

	$("#categoryUserChangDisp").append('<div class="centerArea">' + updateButton
		+ '<button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=categoryUserChangDispClose('
		+ '\'' + req["categoryId"] + '\',\'' + req["groupFlg"] + '\',\'' + req["tabId"] + '\',\'' + req["tabCategoryList"] + '\',\'' + req["tabColor"] + '\',\'' + req["tabTextColor"] + '\',\'' + req["tabBorderColor"] + '\','
		+ '\'' + req["groupId"] + '\',\'' + req["userId"] + '\',\'' + req["userName"] + '\',\'' + req["domCategoryAreaId"] + '\')>キャンセル</button></div>');
};

//画面上のカテゴリユーザ変更
function dispCategoryUserChange(userId, userName) {
	$("#newUserId").remove();
	$("#newUserArea").append('<div id="newUserId"  class="userName" data-userId=' + userId + '>' + xssEscapeEncode(userName)
		+ '</div>');
};

//カテゴリユーザ変更実行
function categoryUserChangRun(categoryId, groupFlg, tabId, tabCategoryList, tabColor, tabTextColor, tabBorderColor) {
	var userId = $('#newUserId').attr("data-userId");
	var data = { 'categoryId': categoryId, 'userId': userId, 'groupFlg': groupFlg, 'tabId': tabId, 'tabCategoryList': tabCategoryList, 'tabColor': tabColor, 'tabTextColor': tabTextColor, 'tabBorderColor': tabBorderColor };
	LoadOn();
	ajaxPost(CATEGORYUSERCHANGE, data);
};

//カテゴリユーザ変更メニュー終了
function categoryUserChangDispClose(categoryId, groupFlg, tabId, tabCategoryList, tabColor, tabTextColor, tabBorderColor, groupId, userId, userName, domCategoryAreaId) {
	$("#categoryUserChangDisp").remove();
	$("#categoryUserEdit").append('<div class="centerArea"><button type="button" id="categoryUserEditButton" class="modalSubButton subButtonSelect" onclick="categoryUserDisp('
		+ '\'' + categoryId + '\',\'' + groupFlg + '\',\'' + tabId + '\',\'' + tabCategoryList + '\',\'' + tabColor + '\',\'' + tabTextColor + '\',\'' + tabBorderColor + '\','
		+ '\'' + groupId + '\',\'' + userId + '\',\'' + userName + '\',\'' + domCategoryAreaId + '\')">編集メニュー</button></div>');
};

//おすすめユーザ表示
function categoryUserRecommended(groupId, tabId) {
	var text = $("#categoryInfoCategoryName").val();
	var target = $('input[name="wordToUserTarget"]:checked').val();
	var data = { 'groupId': groupId, 'tabId': tabId, 'text': text, 'target': target };
	LoadOn();
	ajaxPost(GETWORDTOUSER, data);
};

//カテゴリの詳細表示
function categoryDetailDisp(req, res) {
	console.log(req);
	ModalOn();
	$("#titelarea").append('<span id="companyId" >' + res["categoryName"] + '</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');

	$("#leftarea").append('<div id="categoryDetailEdit" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="categoryDetail" class="modalSubArea infoArea"></div>');
	$("#freearea").append('<div id="categoryUserEdit" class="modalSubArea infoArea"></div>');

	$("#categoryDetailEdit").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>カテゴリ基本情報</div><div class="modalSubTitelLine"></div>');
	$("#categoryDetailEdit").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="categoryInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="categoryInfoCategoryName" class="baseLabel">カテゴリ名</label><input type="text"  maxlength=' + categoryTextMaxLength + ' id="categoryInfoCategoryName" class="baseTextBox" value="' + res["categoryName"] + '" autocomplete="off"></span>'
		+ '<span class="infoSubAreaUnit"><label for="categoryInfoCategoryDeadline" class="baseLabel">期限日</label><input type="text" maxlength=' + dayTextMaxLength + ' id="categoryInfoCategoryDeadline" name="searchDay" class="baseDayTextBox" value="' + dateNullChesk(res["categoryDeadline"]) + '" placeholder="期限なし" autocomplete="off"></span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><textarea id="categoryInfoCategoryRemarks" class="remarksTextBox" rows="3" maxlength=' + remarksTextMaxLength + ' placeholder="備考入力欄">' + res["categoryRemarks"] + '</textarea></span></div>'
		+ '</div>'
	);

	$("#categoryInfoCategoryDeadline").datepicker();
	$("#categoryDetailEdit").append('<div class="centerArea"><button type="button" id="categoryDettailUpdate" class="modalSubButton subButtonUpdate" onclick="categoryDettailUpdate(' + req["categoryId"] + ',\'' + res["categoryName"] + '\')">更新</button></div>');

	$("#categoryUserEdit").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>カテゴリユーザ</div><div class="modalSubTitelLine"></div>');
	$("#categoryUserEdit").append('<div class="infoSubAreaGroup"><label for="categoryUserName" class="baseLabel infoSubAreaUnit">担当ユーザ名</label><span id="categoryUserName" class="userNameDisp">' + res["userName"] + '</span></div>');
	if (req["groupFlg"] == "1") {
		$("#categoryUserEdit").append('<div class="centerArea"><button type="button" id="categoryUserRecommendedButton" class="modalSubButton subButtonSelect" onclick="categoryUserRecommended(\'' + res["groupId"] + '\',\'' + res["tabId"] + '\')">おすすめユーザ</button>'
			+ '<span class="infoSubAreaGroup">'
			+ '<label class="baseLabel">対象範囲<span class="baseRadioButtonBack">'
			+ '<input type="radio" id="wordToUserTab" class="radioGroup infoSubAreaUnit baseRadioButton" name="wordToUserTarget" value="wordToUserTab" checked="wordToUserGroup"><label for="wordToUserTab" class="baseRadioButton-label" >タブ内</label>'
			+ '<input type="radio" id="wordToUserGroup" class="radioGroup infoSubAreaUnit baseRadioButton" name="wordToUserTarget" value="wordToUserGroup"><label for="wordToUserGroup" class="baseRadioButton-label" >グループ内</label>'
			+ '<input type="radio" id="wordToUserAll" class="radioGroup infoSubAreaUnit baseRadioButton" name="wordToUserTarget" value="wordToUserAll"><label for="wordToUserAll" class="baseRadioButton-label" >全ユーザ</label>'
			+ '</span></label></span></div>');
		$("#categoryUserEdit").append('<div class="centerArea"><button type="button" id="categoryUserEditButton" class="modalSubButton subButtonSelect" onclick="categoryUserDisp'
			+ '(\'' + req["categoryId"] + '\',\'' + req["groupFlg"] + '\',\'' + req["tabId"] + '\',\'' + req["tabCategoryList"] + '\',\'' + req["tabColor"] + '\',\'' + req["tabTextColor"] + '\',\'' + req["tabBorderColor"] + '\''
			+ ',\'' + res["groupId"] + '\',\'' + res["userId"] + '\',\'' + res["userName"] + '\',\'' + 1/*domCategoryAreaId*/ + '\')">編集メニュー</button></div>');
	}


	$("#categoryDetail").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>登録・更新情報</div><div class="modalSubTitelLine"></div>');
	$("#categoryDetail").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div class="infoSubAreaGroup"><label for="userInfoCreateUser" class="baseLabel infoSubAreaUnit">作成者</label><span id="userInfoCreateUser" class="userNameDisp">' + res["createUserName"] + '</span>'
		+ '<label for="userInfoCreateDay" class="baseLabel infoSubAreaUnit">作成日時</label><span id="userInfoCreateDay">' + res["createDay"] + '</span></div>'
		+ '<div class="infoSubAreaGroup"><label for="userInfoUpdateUser" class="baseLabel infoSubAreaUnit">更新者</label><span id="userInfoUpdateUser" class="userNameDisp">' + res["updateUserName"] + '</span>'
		+ '<label for="userInfoUpdateDay" class="baseLabel infoSubAreaUnit">更新日時</label><span id="userInfoUpdateDay">' + res["updateDay"] + '</span></div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoNotstartedDay" class="baseLabel infoSubAreaUnit">未着手登録</label><span id="userInfoNotstartedDay" class="userNameDisp">' + res["notstartedDay"] + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoWorkingDay" class="baseLabel infoSubAreaUnit">作業中登録</label><span id="userInfoWorkingDay" class="userNameDisp">' + nvl(res["workingDay"]) + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoWaitingDay" class="baseLabel infoSubAreaUnit">待機中登録</label><span id="userInfoWaitingDay" class="userNameDisp">' + nvl(res["waitingDay"]) + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoDoneDay" class="baseLabel infoSubAreaUnit">完了済登録</label><span id="userInfoDoneDay" class="userNameDisp">' + nvl(res["doneDay"]) + '</span>'
		+ '</div>'
		+ '</div>'
	);

	colorSet('categoryUserName', res["color"], res["textColor"], res["borderColor"]);
	colorSet('userInfoCreateUser', res["createUserColor"], res["createUserTextColor"], res["createUserBorderColor"]);
	colorSet('userInfoUpdateUser', res["updateUserColor"], res["updateUserTextColor"], res["updateUserBorderColor"]);

};

//カテゴリ更新実行
function categoryDettailUpdate(categoryId, categoryName) {
	$('#categoryInfovalidationMessage').empty();
	var categoryName = $("#categoryInfoCategoryName").val();
	var categoryRemarks = $("#categoryInfoCategoryRemarks").val();
	var categoryDeadline = $("#categoryInfoCategoryDeadline").val();

	var postDataCheck = 1;
	if (!requiredCheck(categoryName)) {
		var categoryNameCheck = 1;
		postDataCheck = 0;
		$('#categoryInfovalidationMessage').append("<div>カテゴリ名を入力してください</div>");
	}

	if (requiredCheck(categoryDeadline)) {
		if (!dayCheck(categoryDeadline)) {
			var categoryDeadlineCheck = 1;
			postDataCheck = 0;
			$('#categoryInfovalidationMessage').append("<div>日付の形式が不正です</div>");
		}
	}

	if (postDataCheck) {
		var data = { 'categoryId': categoryId, 'categoryName': categoryName, 'categoryRemarks': categoryRemarks, 'categoryDeadline': categoryDeadline };
		LoadOn();
		ajaxPost(CATEGORYDETAILUPDATE, data);
	} else {
		textBoxColorChange('categoryInfoCategoryName', categoryNameCheck);
		textBoxColorChange('categoryInfoCategoryDeadline', categoryDeadlineCheck);
		textBoxColorChange('categoryInfoCategoryRemarks', categoryRemarksCheck);
	}
};

/*----------------------------------------------<タスク>----------------------------------------------*/
//タスクの詳細表示
function taskDetailDisp(req, res) {
	ModalOn();
	$("#titelarea").append('<span id="companyId" >' + res["taskName"] + '</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');

	$("#leftarea").append('<div id="taskDetailEdit" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="taskDetail" class="modalSubArea infoArea"></div>');

	$("#taskDetailEdit").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>タスク基本情報</div><div class="modalSubTitelLine"></div>');
	$("#taskDetailEdit").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="taskInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="taskInfoTaskName" class="baseLabel">タスク名</label><input type="text"  maxlength=' + taskTextMaxLength + ' id="taskInfoTaskName" class="baseTextBox" value="' + res["taskName"] + '" autocomplete="off"></span>'
		+ '<span class="infoSubAreaUnit"><label for="taskInfoTaskDeadline" class="baseLabel">期限日</label><input type="text" maxlength=' + dayTextMaxLength + ' id="taskInfoTaskDeadline" name="searchDay" class="baseDayTextBox" value="' + dateNullChesk(res["taskDeadline"]) + '" placeholder="期限なし" autocomplete="off"></span></div>'
		+ '<div class="infoSubAreaGroup"><textarea id="taskInfoTaskRemarks" class="remarksTextBox" rows="3" maxlength=' + remarksTextMaxLength + ' placeholder="備考入力欄">' + res["taskRemarks"] + '</textarea></span></div>'
		+ '</div>'
	);

	$("#taskInfoTaskDeadline").datepicker();
	$("#taskDetailEdit").append('<div class="centerArea"><button type="button" id="taskDettailUpdate" class="modalSubButton subButtonUpdate" onclick="taskDettailUpdate(' + req["taskId"] + ',\'' + res["taskName"] + '\')">更新</button></div>');


	$("#taskDetail").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>登録・更新情報</div><div class="modalSubTitelLine"></div>');
	$("#taskDetail").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div class="infoSubAreaGroup"><label for="userInfoCreateUser" class="baseLabel infoSubAreaUnit">作成者</label><span id="userInfoCreateUser" class="userNameDisp">' + res["createUserName"] + '</span>'
		+ '<label for="userInfoCreateDay" class="baseLabel infoSubAreaUnit">作成日時</label><span id="userInfoCreateDay">' + res["createDay"] + '</span></div>'
		+ '<div class="infoSubAreaGroup"><label for="userInfoUpdateUser" class="baseLabel infoSubAreaUnit">更新者</label><span id="userInfoUpdateUser" class="userNameDisp">' + res["updateUserName"] + '</span>'
		+ '<label for="userInfoUpdateDay" class="baseLabel infoSubAreaUnit">更新日時</label><span id="userInfoUpdateDay">' + res["updateDay"] + '</span></div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoNotstartedDay" class="baseLabel infoSubAreaUnit">未着手登録</label><span id="userInfoNotstartedDay" class="userNameDisp">' + res["notstartedDay"] + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoWorkingDay" class="baseLabel infoSubAreaUnit">作業中登録</label><span id="userInfoWorkingDay" class="userNameDisp">' + nvl(res["workingDay"]) + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoWaitingDay" class="baseLabel infoSubAreaUnit">待機中登録</label><span id="userInfoWaitingDay" class="userNameDisp">' + nvl(res["waitingDay"]) + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="userInfoDoneDay" class="baseLabel infoSubAreaUnit">完了済登録</label><span id="userInfoDoneDay" class="userNameDisp">' + nvl(res["doneDay"]) + '</span>'
		+ '</div>'
		+ '</div>'
	);

	colorSet('userInfoCreateUser', res["createUserColor"], res["createUserTextColor"], res["createUserBorderColor"]);
	colorSet('userInfoUpdateUser', res["updateUserColor"], res["updateUserTextColor"], res["updateUserBorderColor"]);

};

//タスク更新実行
function taskDettailUpdate(taskId, taskName) {
	$('#taskInfovalidationMessage').empty();
	var taskName = $("#taskInfoTaskName").val();
	var taskRemarks = $("#taskInfoTaskRemarks").val();
	var taskDeadline = $("#taskInfoTaskDeadline").val();

	var postDataCheck = 1;
	if (!requiredCheck(taskName)) {
		var taskNameCheck = 1;
		postDataCheck = 0;
		$('#taskInfovalidationMessage').append("<div>タスク名を入力してください</div>");
	}

	if (requiredCheck(taskDeadline)) {
		if (!dayCheck(taskDeadline)) {
			var taskDeadlineCheck = 1;
			postDataCheck = 0;
			$('#taskInfovalidationMessage').append("<div>日付の形式が不正です</div>");
		}
	}

	if (postDataCheck) {
		var data = { 'taskId': taskId, 'taskName': taskName, 'taskRemarks': taskRemarks, 'taskDeadline': taskDeadline };
		LoadOn();
		ajaxPost(TASKDETAILUPDATE, data);
	} else {
		textBoxColorChange('taskInfoTaskName', taskNameCheck);
		textBoxColorChange('taskInfoTaskDeadline', taskDeadlineCheck);
		textBoxColorChange('taskInfoTaskRemarks', taskRemarksCheck);
	}
};

//タブ内カテゴリのフィルタ内容のローカル操作関連
function tabCategoryFilterLocalGet(tabCategoryList) {
	var tabLocalListSort = JSON.parse(localStorage.getItem(tabCategoryList));
	if ($.isEmptyObject(tabLocalListSort)) {
		var categoryFilterSet = {
			'categoryFilterOwner': 'categoryFilterAllOwner',
			'categoryFilterDeadline': 'categoryFilterAllDeadline',
			'categoryFilterToDeadline': '',
			'categoryFilterStatus': 'categoryFilterAllStatus',
			'categoryFilterStatusValue': '',
		};
		localStorage.setItem(tabCategoryList, JSON.stringify(categoryFilterSet));
		tabLocalListSort = categoryFilterSet;
	}

	return tabLocalListSort;
};

