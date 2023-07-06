/*=================================
機械学習関連
=================================*/


function modalDispWordToUser(req, res) {
	CloseModal();
	LoadOff();
	CanvasOn();
	$("#canvasName").append('<span id="groupName">' + req["text"] + 'の関連</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CanvasOff()>閉</button></div>');

	dispWordToUser(req, res, "modal");
}

function graphDispWordToUser(req, res) {
	LoadOff();
	$("#graphtitle").empty();
	$("#graphResult").empty();

	dispWordToUser(req, res, "graph");
}


function dispWordToUser(req, res, dispType) {

	var notDoneCountArray = res.map(function (value) {
		return value.notDoneCount;
	});
	var maxNotDoneCount = Math.max.apply(null, notDoneCountArray);

	var datasetsArray = [];
	res.forEach(function (value) {

		var occupancy = 1;
		if (maxNotDoneCount != 0) {
			occupancy = value.notDoneCount / maxNotDoneCount * 100;
		}

		if (occupancy == 0) {
			occupancy = 1;
		}

		var recommendedPoint = 0;
		if (value.score != 0) {
			recommendedPoint = value.score / occupancy + 5;
		} else {
			recommendedPoint = 1 / occupancy + 3;
		}

		var datasetsTemp = {};
		var scoreOrOccupancyArray = [];
		scoreOrOccupancyData = { x: value.score, y: occupancy, r: recommendedPoint };
		scoreOrOccupancyArray.push(scoreOrOccupancyData);

		datasetsTemp["label"] = value.name;
		datasetsTemp["backgroundColor"] = value.color;

		datasetsTemp["data"] = scoreOrOccupancyArray;
		datasetsArray.push(datasetsTemp);

	});


	switch (dispType) {
		case "modal":
			var canvasSize = $('#canvasarea');
			var backgroundWidth = canvasSize.width();
			var backgroundHeight = canvasSize.height();
			$("#canvasarea").append('<canvas id="graph" width="' + backgroundWidth + '" height="' + backgroundHeight + '"></canvas>');
			break;

		case "graph":
			$("#graphtitle").append('<div id="userInfoDispTitel" >' + req["freeWord"] + 'の関連</div>');
			$("#graphResult").append('<div id="graphArea" class="graphArea"></div>');
			$("#graphArea").append('<canvas id="graph"></canvas>');
			$("#graphArea").css('background', 'linear-gradient(to bottom, rgb(192, 193, 207), rgb(104, 104, 136))');
			break;
	}

	var ctx = document.getElementById("graph");
	var chart = new Chart(ctx, {
		type: 'bubble',
		data: {
			datasets: datasetsArray
		},
		options: {
			scales: {
				xAxes: [{
					scaleLabel: {
						display: true,
						labelString: "相対実績評価値",
						fontColor: "#00003f"
					},
					ticks: {
						fontColor: "#00003f"
					}
				}],
				yAxes: [{
					scaleLabel: {
						display: true,
						labelString: "稼働率",
						fontColor: "#00003f"
					},
					ticks: {
						suggestedMax: 100,
						suggestedMin: 0,
						fontColor: "#00003f"
					}
				}]
			}
		}

	});
};

//ユーザから学習データ取得
function getUserToWord(userId, userName, color, textColor, borderColor) {
	LoadOn();
	var data = { "type": "user", "userId": userId, "userName": userName, "color": color, "textColor": textColor, "borderColor": borderColor };
	ajaxPost(GETUSERTOWORD, data);
};

//グループから学習データ取得
function getGroupToWord(groupId, groupName, color, textColor, borderColor) {
	LoadOn();
	var data = { "type": "group", "groupId": groupId, "groupName": groupName, "color": color, "textColor": textColor, "borderColor": borderColor };
	ajaxPost(GETUSERTOWORD, data);
};


//タブから学習データ取得
function getTabToWord(tabId, tabName, color, textColor, borderColor) {
	LoadOn();
	var data = { "type": "tab", "tabId": tabId, "tabName": tabName, "color": color, "textColor": textColor, "borderColor": borderColor };
	ajaxPost(GETUSERTOWORD, data);
};


function dispUserToWord(req, res) {
	CloseModal();
	LoadOff();
	CanvasOn();

	switch (req['type']) {
		case "user":
			name = req['userName'];
			break;

		case "group":
			name = req['groupName'];
			break;

		case "tab":
			name = req['tabName'];
			break;
	}

	$("#canvasName").append('<span id="groupName">' + name + 'の関連ワード</span>'
		+ '<div class="blockRight"><button type="button" id="closebutton" class="modalDecisionButton" onclick=CanvasOff()>閉</button></div>');

	var canvasSize = $('#canvasarea');
	var backgroundWidth = canvasSize.width();
	var backgroundHeight = canvasSize.height();
	var baseStringSize = 1;
	if (backgroundWidth < MINSIZE) {
		baseStringSize = 0.6
	} else if (backgroundWidth < SMARTPHONE) {
		baseStringSize = 0.7
	}

	$("#canvasarea").append('<canvas id="graph" width="' + backgroundWidth + '" height="' + backgroundHeight + '"></canvas>');

	var graph = document.getElementById("graph");
	var ctx = graph.getContext('2d');

	ctx.font = '20' * baseStringSize + 'px bold';
	ctx.fillStyle = req['color'];
	var nameSize = ctx.measureText(name).width;
	var xCenter = nameSize / 2;

	ctx.fillText(name, backgroundWidth / 2 - xCenter, backgroundHeight / 2);
	ctx.stroke();

	var center_x = backgroundWidth / 2;
	var center_y = backgroundHeight / 2;
	var vertex = res.length;
	var size = 100;
	var tilt = 0;
	var scale = 1;

	var x;
	var y;
	var csize;
	var angle;
	var rad;

	ctx.globalCompositeOperation = 'destination-over';
	ctx.strokeStyle = req['borderColor'];
	ctx.fillStyle = req['textColor'];
	ctx.globalAlpha = 0.5;
	ctx.arc(center_x, center_y, nameSize + vertex, 0, Math.PI * 2, false);
	ctx.fill();

	ctx.stroke();


	for (var k = scale; k > 0; k--) {
		csize = size / scale * k;
		ctx.beginPath();

		var count = 0;
		res.forEach(function (value, key) {
			var hierarchy = 1;
			lineLength = csize;
			lineLength = ((backgroundHeight + backgroundWidth) * 0.2) - value.score * 30;
			if (lineLength < 1) {
				lineLength = 1;
			}
			angle = tilt + 360 / vertex * key;
			rad = angle * Math.PI / 180;

			x = center_x + Math.sin(rad) * lineLength;
			y = center_y + Math.cos(rad) * lineLength;

			var wordSize = ctx.measureText(value.word).width;
			x = positionOverResetting(backgroundWidth, x, wordSize);
			y = positionOverResetting(backgroundHeight, y, 0);
			x = positionDownResetting(x);
			y = positionDownResetting(y);

			ctx.moveTo(center_x, center_y);

			ctx.font = (value.score / 2 + 2) * 5 * baseStringSize + 'px bold';
			ctx.fillStyle = canvasStringColorSet(value.score, hierarchy);
			ctx.fillText(value.word, x, y);


			var canvasArray = [];

			Object.keys(value.dependedOnWord).forEach(function (dependedValue, key) {
				var length = 0;
				var array = value.dependedOnWord;
				var data = array[dependedValue];

				length += 1;
				if (typeof data[length] != 'undefined') {
					data[0].depended = data[length];
				}

				canvasArray.push(data[0]);

			});

			hierarchy = hierarchy + 1;

			wordMapDependedOnWordCanvas(ctx, canvasArray, x, y, wordSize, hierarchy, backgroundWidth, backgroundHeight, baseStringSize);
		});

		ctx.stroke();
	}



};

function wordMapDependedOnWordCanvas(ctx, data, dependedX, dependedY, wordSize, hierarchy, backgroundWidth, backgroundHeight, baseStringSize) {
	var center_x = dependedX + wordSize / 2;
	var center_y = dependedY;
	var vertex = data.length;
	var size = 10;
	var tilt = 30;
	var scale = 1;

	var x;
	var y;
	var csize;
	var angle;
	var rad;

	for (var k = scale; k > 0; k--) {
		subHierarchy = hierarchy;

		csize = size / scale * k;
		ctx.beginPath();

		data.forEach(function (value, key) {
			lineLength = csize;
			lineLength = ((backgroundHeight + backgroundWidth) / 30) / value.score;

			angle = tilt + 360 / vertex * key;
			rad = angle * Math.PI / 180;

			x = center_x + Math.sin(rad) * lineLength;
			y = center_y + Math.cos(rad) * lineLength;

			var wordSize = ctx.measureText(value.word).width;
			x = positionOverResetting(backgroundWidth, x, wordSize);
			y = positionOverResetting(backgroundHeight, y, 0);
			x = positionDownResetting(x);
			y = positionDownResetting(y);

			ctx.moveTo(center_x, center_y);

			ctx.globalCompositeOperation = 'destination-over';
			ctx.font = (value.score / 2 + 3) * 3 * baseStringSize + 'px bold';
			ctx.fillStyle = canvasStringColorSet(value.score, hierarchy);
			ctx.setLineDash([1, 3]);
			ctx.fillText(value.word, x, y);
			ctx.strokeStyle = canvasStringColorSet(value.score, hierarchy);
			ctx.lineTo(x, y);
			ctx.stroke();

			subHierarchy = hierarchy + 1;

			var canvasArray = [];
			if (typeof value.depended != 'undefined') {
				canvasArray.push(value.depended);
				wordMapDependedOnWordCanvas(ctx, canvasArray, x, y, wordSize, subHierarchy, backgroundWidth, backgroundHeight, baseStringSize);
			}

		});

		ctx.stroke();
	}
};

function positionOverResetting(backSize, position, wordSize) {
	if (position + wordSize >= backSize) {
		position = backSize - (wordSize + 10);
	}
	return position;
};

function positionDownResetting(position) {
	if (position < 20) {
		position = 20;
		console.log(position);
	}
	return position;
};

function canvasStringColorSet(score, hierarchy) {
	color = '#00007a';
	switch (hierarchy) {
		case 1:
			color = subCanvasStringColorSet(score, '#00007a', '#005500', '#8f6600', '#940000');
			break;
		case 2:
			color = subCanvasStringColorSet(score, '#0052eb', '#00c600', '#cb9b00', '#ff0000');
			break;
		case 3:
			color = subCanvasStringColorSet(score, '#0098ff', '#8cba61', '#ddae00', '#ff3e38');
			break;
	}

	return color;

};

function subCanvasStringColorSet(score, blue, green, yellow, red) {
	if (score <= 2) {
		return blue;
	} else if (score <= 6) {
		return green;
	} else if (score <= 10) {
		return yellow;
	} else if (score > 10) {
		return red;
	} else {
		return blue;
	}
}