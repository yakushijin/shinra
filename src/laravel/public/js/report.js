/*=================================
レポート
=================================*/


//レポート表示共通
function reportDisp(req, res, reportType) {
	CloseModal();
	ModalOn();

	$("#titelarea").append('<span id="groupName">レポート</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CloseModal()>閉</button></div>');
	$("#freearea").append('<div id="reportArea" class="modalSubArea reportArea"></div>');

	if (req["dayFrom"] == req["dayTo"]) {
		var report = req["dayTo"];
	} else {
		var report = req["dayFrom"] + '～' + req["dayTo"];
	}

	switch (reportType) {
		case "userReport":
			$("#reportArea").append('<div id="reportUserName" class="reportUserLine">【' + req["userName"] + '】' + report + '</div>');
			userReport(req, res);
			break;
		case "groupReport":
			$("#reportArea").append('<div id="reportUserName" class="reportUserLine">【' + req["groupName"] + '】' + report + '</div>');
			groupReport(req, res);
			break;
		case "tabReport":
			if(req["tabName"] == null){
				req["tabName"] = "";
			}
			$("#reportArea").append('<div id="reportUserName" class="reportUserLine">【' + req["tabName"] + '】' + report + '</div>');
			tabReport(req, res);
			break;
	}
};

//ユーザレポートデータ成形
function userReport(req, res) {
	switch (req["aggregationUnit"]) {
		case "aggregationUser":
			$("#reportArea").append('<div id="reportTabText' + req["userId"] + '" class="reportTabText">'
				+ '<span id="reportUserName' + req["userId"] + '" class="reportUserName">■' + req["userName"] + 'の全体進捗率</span>'
				+ '' + res[0].percentage + '％'
				+ '<div id="reportTabLine' + req["userId"] + '" class="reportTabLine"></div>'
				+ '</div>');
			break;

		case "aggregationGroup":
			res.forEach(function (tabValue, tabKey) {
				$("#reportArea").append('<div id="reportTabText' + tabKey + '" class="reportTabText">'
					+ '<span id="reportGroupName' + tabKey + '" class="reportGroupName">■' + tabValue.groupName + '</span>'
					+ '　進捗率' + tabValue.percentage + '％'
					+ '<div id="reportTabLine' + tabKey + '" class="reportTabLine"></div>'
					+ '</div>');
			});
			break;

		case "aggregationTab":
			res.forEach(function (tabValue, tabKey) {
				$("#reportArea").append('<div id="reportTabText' + tabKey + '" class="reportTabText">'
					+ '<span id="reportTabName' + tabKey + '" class="reportTabName">■' + tabValue.tabName + '</span>'
					+ '<span id="reportGroupName' + tabKey + '" class="reportGroupName">　' + tabValue.groupName + '</span>'
					+ '　進捗率' + tabValue.percentage + '％'
					+ '<div id="reportTabLine' + tabKey + '" class="reportTabLine"></div>'
					+ '</div>');

				tabValue.category.forEach(function (categoryValue, categoryKey) {
					var percentage = '';
					if (categoryValue.percentage != '') {
						percentage = '　進捗率' + categoryValue.percentage + '％';
					}
					$("#reportTabLine" + tabKey).after('<div id="reportCategoryName' + categoryKey + '" class="reportCategoryName">・' + categoryValue.categoryName + '⇒' + categoryValue.status + percentage + '</div>');
				});
			});
			break;
	}
};

//グループレポートデータ成形
function groupReport(req, res) {
	switch (req["aggregationUnit"]) {
		case "aggregationUser":
			res.forEach(function (tabValue, tabKey) {
				$("#reportArea").append('<div id="reportTabText' + tabKey + '" class="reportTabText">'
					+ '<span id="reportUserName' + tabKey + '" class="reportUserName">■' + tabValue.userName + '</span>'
					+ '　進捗率' + tabValue.percentage + '％'
					+ '<div id="reportTabLine' + tabKey + '" class="reportTabLine"></div>'
					+ '</div>');
			});
			break;

		case "aggregationGroup":
			$("#reportArea").append('<div id="reportTabText' + res[0].groupId + '" class="reportTabText">'
				+ '<span id="reportGroupName' + res[0].groupId + '" class="reportGroupName">■' + req["groupName"] + '</span>'
				+ '　進捗率' + res[0].percentage + '％'
				+ '<div id="reportTabLine' + res[0].groupId + '" class="reportTabLine"></div>'
				+ '</div>');
			break;

		case "aggregationTab":
			res.forEach(function (tabValue, tabKey) {
				$("#reportArea").append('<div id="reportTabText' + tabKey + '" class="reportTabText">'
					+ '<span id="reportTabName' + tabKey + '" class="reportTabName">■' + tabValue.tabName + '</span>'
					+ '<span id="reportGroupName' + tabKey + '" class="reportGroupName">　' + tabValue.groupName + '</span>'
					+ '　進捗率' + tabValue.percentage + '％'
					+ '<div id="reportTabLine' + tabKey + '" class="reportTabLine"></div>'
					+ '</div>');

			});
			break;
	}
};

//タブレポートデータ成形
function tabReport(req, res) {
	switch (req["aggregationUnit"]) {
		case "aggregationUser":
			res.forEach(function (tabValue, tabKey) {
				$("#reportArea").append('<div id="reportTabText' + tabKey + '" class="reportTabText">'
					+ '<span id="reportUserName' + tabKey + '" class="reportUserName">■' + tabValue.userName + '</span>'
					+ '　進捗率' + tabValue.percentage + '％'
					+ '<div id="reportTabLine' + tabKey + '" class="reportTabLine"></div>'
					+ '</div>');
			});
			break;

		case "aggregationTab":
			$("#reportArea").append('<div id="reportTabText' + res[0].tabId + '" class="reportTabText">'
				+ '<span id="reportTabName' + res[0].tabId + '" class="reportTabName">■' + req["tabName"] + '</span>'
				+ '　進捗率' + res[0].percentage + '％'
				+ '<div id="reportTabLine' + res[0].tabKey + '" class="reportTabLine"></div>'
				+ '</div>');
			break;
	}
};
