/*=================================
モーダル_管理者画面のみ使用
=================================*/

//会社名押下時の処理
function companyget() {
	var data = {};
	LoadOn();
	ajaxPost(COMPANYGET, data);
};

//会社に紐づく各種情報をモーダルで表示
function companyDisp(res) {
	LoadOff();
	ModalOn();
	$("#titelarea").append('<span id="companyInfoDispTitel" >' + res["companyName"] + 'の設定</span>'
	+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');


	$("#leftarea").append('<div id="companyInfoDispBasic" class="modalSubArea infoArea"></div>');
	$("#rightarea").append('<div id="companyInfoDispData" class="modalSubArea infoArea"></div>');

	$("#companyInfoDispBasic").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>システム基本情報</div><div class="modalSubTitelLine"></div>');
	$("#companyInfoDispBasic").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div id="companyInfovalidationMessage" class="validationMessage"></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="companyInfoCompanyName" class="baseLabel">ワークスペース名</label><input type="text" maxlength=' + companyTextMaxLength + ' id="companyInfoCompanyName" class="baseTextBox companyTextBox" value="' + res["companyName"] + '" autocomplete="off"></span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="systemUserName" class="baseLabel">システムユーザ</label><span id="systemUserName" class="userNameDisp">' + res["systemUser"]["userName"] + '</span></span></div>'
		+ '<div class="infoSubAreaGroup"><span class="infoSubAreaUnit"><label for="companyInfoCategorySaveDay" class="baseLabel">完了済みカテゴリ保持日数</label><input type="number" maxlength=' + numbersTextMaxLength + ' id="companyInfoCategorySaveDay" class="baseTextBox numberTextBox" value="' + res["categorySaveDay"] + '" autocomplete="off"></span></div>'

		+ '</div>'
	);
	$("#companyInfoDispBasic").append('<div class="centerArea"><button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=companyInfoUpdate()>更新</button></div>');

	colorSet('systemUserName', res["systemUser"]["color"], res["systemUser"]["textColor"], res["systemUser"]["borderColor"]);

	$("#companyInfoDispData").append('<div class="modalSubTitel"><span class="modalSubTitelIcon"></span>データ使用状況</div><div class="modalSubTitelLine"></div>');
	$("#companyInfoDispData").append('<div id="infoBasicArea" class="infoSubArea">'
		+ '<div class="infoSubAreaGroup">'
		+ '<label for="companyInfoUserCount" class="baseLabel infoSubAreaUnit">ユーザ数</label><span id="companyInfoUserCount">' + res["userCount"] + '/' + res["userMaxCount"] + '</span>'
		+ '<label for="companyInfoGroupCount" class="baseLabel infoSubAreaUnit">グループ数</label><span id="companyInfoGroupCount">' + res["groupCount"] + '/' + res["groupMaxCount"] + '</span>'
		+ '</div>'
		+ '<div class="infoSubAreaGroup">'

		+ '<label for="companyInfoTabCount" class="baseLabel infoSubAreaUnit">タブ数</label><span id="companyInfoTabCount">' + res["tabCount"] + '/' + res["tabMaxCount"] + '</span>'
		+ '<label for="companyInfoCategoryCount" class="baseLabel infoSubAreaUnit">カテゴリ数</label><span id="companyInfoCategoryCount">' + res["categoryCount"] + '/' + res["categoryMaxCount"] + '</span>'

		+ '</div>'
		+ '<div class="infoSubAreaGroup">'

		+ '<label for="companyInfoTaskCount" class="baseLabel infoSubAreaUnit">タスク数</label><span id="companyInfoTaskCount">' + res["taskCount"] + '/' + res["taskMaxCount"] + '</span></div>'

		+ '<div class="infoSubAreaGroup">'

		+ '<label for="companyInfoDbDataUse" class="baseLabel infoSubAreaUnit">通常データ</label><span id="companyInfoDbDataUse">' + res["dbDataUse"] + '/' + res["dbDataMaxSize"] + 'MB</span>'
		+ '<label for="companyInfoMlDataUse" class="baseLabel infoSubAreaUnit">学習データ</label><span id="companyInfoMlDataUse">' + res["mlDataUse"] + '/' + res["mlDataMaxSize"] + 'MB (' + res["mlDataAllCount"] + ')件</span>'

		+ '</div>'

		+ '</div>'
	);

};

function companyInfoUpdate() {
	if (checkSystem()) {
		$('#companyInfovalidationMessage').empty();
		var companyName = $('#companyInfoCompanyName').val();
		var categorySaveDay = $('#companyInfoCategorySaveDay').val();

		var postDataCheck = 1;
		if (!requiredCheck(companyName)) {
			var companyNameCheck = 1;
			postDataCheck = 0;
			$('#companyInfovalidationMessage').append("<div>ワークスペース名を入力してください</div>");
		}
		if (checkDoNotUseSymbol(companyName)) {
			var companyNameCheck = 1;
			postDataCheck = 0;
			$('#companyInfovalidationMessage').append("<div>使用禁止文字列「<>{}[\]()&\"'」が含まれています</div>");
		  }
		if (!requiredCheck(categorySaveDay)) {
			var categorySaveDayCheck = 1;
			postDataCheck = 0;
			$('#companyInfovalidationMessage').append("<div>日数を入力してください</div>");
		}
		if (numberRangeCheck(categorySaveDay,0,365)) {
			var categorySaveDayCheck = 1;
			postDataCheck = 0;
			$('#companyInfovalidationMessage').append("<div>入力可能な値の範囲は0～365までです</div>");
		}

		if (postDataCheck) {
			LoadOn();
			var data = { 'companyName': companyName, 'categorySaveDay': categorySaveDay };
			ajaxPost(COMPANYUPDATE, data);
		} else {
			textBoxColorChange('companyInfoCompanyName', companyNameCheck);
			textBoxColorChange('companyInfoCategorySaveDay', categorySaveDayCheck);
		}
	} else {
		notAdminAlert();
	}
};

// /*=================================
// システムユーザ変更一旦保留
// =================================*/
// function systemUserEdit(systemUserId, userName) {
// 	if (checkSystem()) {
// 		var data = { 'userId': systemUserId, 'userName': userName };
// 		LoadOn();
// 		ajaxPost(ADMINUSERGET, data);
// 	} else {
// 		notAdminAlert();
// 	}
// };

// //システムユーザ変更メニュー
// function systemUserEditDisp(req, res) {
// 	LoadOff();
// 	$("#systemUserEditButton").remove();

// 	$("#companyInfoSystemUser").append('<div id="systemUserChangDisp"</div>');

// 	var updateButton = '';
// 	$("#systemUserChangDisp").append('<div id="systemId" data-systemId=' + req["systemUserId"] + '></div>');

// 	$("#systemUserChangDisp").append('<div id="systemUserArea" class="modalSubArea systemUserArea"></div>');
// 	$("#systemUserChangDisp").append('<div id="allUserArea" class="modalSubArea allUserArea"></div>');

// 	$("#systemUserArea").append('<div id="systemUserText">変更後システムユーザ</div>');
// 	$("#allUserArea").append('<div id="allUserText">管理者ユーザ一覧</div>');

// 	updateButton = '<button type="button" id="closebutton" class="modalSubButton subButtonUpdate" onclick=systemUserChangRun(' + req['categoryId'] + ')>変更を反映</button>';
// 	$("#systemUserArea").append('<div id="newUserId"  class="userName" data-userId=' + req['userId'] + '>' + req['userName']
// 		+ '</div>');

// 	res.forEach(function (value) {
// 		var changUserId = 'changUserId' + value.userId;

// 		$("#allUserArea").append('<div id="' + changUserId + '" class="userName" name="deleteUser" data-userId=' + value.userId + '>' + value.userName
// 			+ '<div class="userSystemAdd modalBotton" onclick="dispSystemUserChange(\'' + value.userId + '\',\'' + value.userName + '\')">+</div>'
// 			+ '</div>');
// 		$("#" + changUserId).css({ 'background': value.color, 'color': value.textColor, 'border': 'solid 1px ' + value.borderColor });

// 	});

// 	$("#systemUserChangDisp").append('<div class="rightArea">' + updateButton
// 		+ '<button type="button" id="closebutton" class="modalSubButton subButtonSelect" onclick=systemUserChangDispClose(\'' + req["systemId"] + '\',\'' + req["systemName"] + '\')>キャンセル</button></div>');

// };

// function dispSystemUserChange(userId, userName) {
// 	$("#newUserId").remove();
// 	$("#systemUserArea").append('<div id="newUserId"  class="userName" data-userId=' + userId + '>' + userName
// 		+ '</div>');
// };

// function systemUserChangRun(categoryId) {

// 	var userId = $('#newUserId').attr("data-userId");
// 	var data = { 'userId': userId };
// 	LoadOn();
// 	ajaxPost(SYSTEMUSERCHANGE, data);
// };

// function dispSystemAdd(userId, userName) {
// 	var changUserId = 'changUserId' + userId;
// 	$("#" + changUserId).remove();
// 	$("#groupUserArea").append('<div id="' + changUserId + '" class="userName" name="addUser" data-userId=' + userId + '>' + userName
// 		+ '<div class="userGroupDelete modalBotton" onclick="dispDelete(' + userId + ',\'' + userName + '\')">×</div>'
// 		+ '</div>');
// };