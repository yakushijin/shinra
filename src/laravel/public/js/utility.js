
function ModalOn() {
	$('body').append('<div div id="loaderid"></div>');
	$('body').prepend('<div id="lockid" class="modalLock">');
	$('body').append('</div>');
	$("#modal-background").css("display", "block");
	$("#modal-background").scrollTop(0);
};

//モーダルクローズ
function CloseModal() {
	$("#leftarea").empty();
	$("#rightarea").empty();
	$("#titelarea").empty();
	$("#subtitelarea").empty();
	$("#freearea").empty();
	$("#buttonarea").empty();
	$("#modal-background").css("display", "none");
	LoadOff();
};

function LoadOn() {
	$("#loaderid").remove();
	$("#lockid").remove();
	$('body').append('<div div id="loaderid" class="loader"></div>');
	$('body').prepend('<div id="lockid" class="loadLock">');
	$('body').append('</div>');
};

function MiniLoadOn() {
	$("#loaderid").remove();
	$('body').append('<div div id="loaderid" class="miniLoader"></div>');
};

function LoadOff() {
	$("#loaderid").remove();
	$("#lockid").remove();
};

function MiniLoadOff() {
	$("#loaderid").remove();
};

function CanvasOn() {
	$('body').append('<div div id="loaderid"></div>');
	$('body').prepend('<div id="lockid" class="modalLock">');
	$('body').append('</div>');
	$("#canvas-background").css("display", "block");
};
function CanvasOff() {
	$("#loaderid").remove();
	$("#lockid").remove();
	$("#canvasarea").empty();
	$("#canvasName").empty();
	$("#canvasClose").empty();
	$("#canvas-background").css("display", "none");
};

function MessageDisp(type, messageText, delayTime) {
	var color;
	switch (type) {
		case INFO:
			color = "linear-gradient(to top, #3edd1e, #1f9b07)";
			break;
		case WARNING:
			color = "linear-gradient(to top, #c0dd1e, #919b07)";
			break;
		case ERROR:
			color = "linear-gradient(to top, #dd541e, #9b2a07)";
			break;
	}

	$("#resultdisp").empty();
	$("#resultdisp").prepend('<div id="resultmessege" class="Notice">' + messageText + '</div>');
	$("#resultmessege").css("background", color);
	$("#resultmessege").css("left", noticeLeft);
	$("#resultmessege").css("width", noticeSize);

	$(this).delay(delayTime).queue(function () {
		$("#resultmessege").remove();
		$(this).dequeue();
	});
};

function Reload(delayTime) {
	$(this).delay(delayTime).queue(function () {
		location.reload();
	});
};

//当日日付
function NowDate() {
	var dd = new Date();
	var YYYY = dd.getFullYear();
	var MM = date0Add(dd.getMonth() + 1);
	var DD = date0Add(dd.getDate());

	return YYYY + "/" + MM + "/" + DD;
};

//日付を指定した日数で進めるまたは戻す
function DayCalculationDate(day, calculation) {
	day = stringDateChange(day);
	day.setDate(day.getDate() + calculation);
	var YYYY = day.getFullYear();
	var MM = date0Add(day.getMonth() + 1);
	var DD = date0Add(day.getDate());
	return YYYY + "/" + MM + "/" + DD;
};

function stringDateChange(stringDate) {
	var arr = stringDate.split('/');
	return new Date(arr[0], arr[1] - 1, arr[2]);
};

//日付の0埋め
function date0Add(date) {
	date += "";
	if (date.length === 1) {
		date = "0" + date;
	}
	return date;
};

//ミリ秒を日に変換
function msDay(msDate) {
	day = msDate / 1000 / 60 / 60 / 24;
	return day;
};

//ミリ秒を月に変換
function msMonth(msDate) {
	month = msDate / 1000 / 60 / 60 / 24 / 30;
	return month;
};

//日付のnull値文字列変換
function dateNullChesk(date) {
	resultDate = date ? date.replace(/-/g, "/") : "";
	return resultDate;
};


//ユーザカラーセット
function colorSet(domId, color, textColor, borderColor) {
	$("#" + domId).css({ 'background': color, 'color': textColor, 'border': 'solid 1px ' + borderColor });

};

//nullを空白に変換
function nvl(str) {
	return (str == null) ? "" : str;
}