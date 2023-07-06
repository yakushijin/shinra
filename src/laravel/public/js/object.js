/*=================================
各種オブジェクト
=================================*/
//タブ関連グループ
var TabGroup = function (tabId, tabName, color, textColor, borderColor, tabDeadline, groupFlg, userOrGroupName, userOrGroupId, userOrGroupColor, userOrGroupTextColor, userOrGroupBorderColor) {
    this.dbTabId = tabId;
    this.domTabId = "tab" + tabId;
    this.domMainAreaId = "mainarea" + tabId;
    this.domTabTextId = "tabtext" + tabId;
    this.domCategoryAddTextId = "categoryaddtext" + tabId;
    this.tabGroupIconId = "tabGroupIcon" + tabId;
    this.tabCategoryList = "tabCategoryList" + tabId;
    this.tabName = tabName;
    this.color = color;
    this.textColor = textColor;
    this.borderColor = borderColor;
    this.userOrGroupColor = userOrGroupColor;
    this.userOrGroupTextColor = userOrGroupTextColor;
    this.userOrGroupBorderColor = userOrGroupBorderColor;
    this.tabDeadline = dateNullChesk(tabDeadline);
    this.groupFlg = groupFlg;
    this.userOrGroupName = userOrGroupName;
    this.userOrGroupId = userOrGroupId;
};

//カテゴリ関連グループ
var CategoryGroup = function (
    tabId,
    categoryId,
    categoryName,
    categoryRemarks,
    categoryNotstarted,
    categoryWorking,
    categoryWaiting,
    categoryDone,
    categoryDeadline,
    color,
    textColor,
    borderColor,
    groupFlg,
    userId,
    userName,
    groupId,
    myUserflg,
    userColor,
    userTextColor,
    userBorderColor,
    categorySort) {

    this.dbTabId = tabId;
    this.tabCategoryList = "tabCategoryList" + tabId;

    this.dbCategoryId = categoryId;
    this.domCategoryAreaId = "categoryarea" + categoryId;
    this.domCategoryAddTextId = "categoryaddtext" + tabId;
    this.domCategoryMainAreaId = "categorymainarea" + categoryId;
    this.domTaskAddTextId = "taskaddtext" + categoryId;
    this.categoryTaskList = "categoryTaskList" + categoryId;

    this.domNameId = "categoryName" + categoryId;
    this.domCategoryNotstartedId = "categorynotstarted" + categoryId;
    this.domCategoryWorkingId = "categoryworking" + categoryId;
    this.domCategoryWaitingId = "categorywaiting" + categoryId;
    this.domCategoryDoneId = "categorydone" + categoryId;
    this.domRemarksId = "remarks" + categoryId;
    this.domCategoryDeadlineId = "categoryDeadline" + categoryId;
    this.domCategoryStatusSelectBoxId = "categoryStatusSelectBox" + categoryId;
    this.userNameIconId = "userNameIcon" + categoryId;

    this.categoryName = categoryName;
    this.categoryRemarks = categoryRemarks;
    this.categoryNotstarted = categoryNotstarted;
    this.categoryWorking = categoryWorking;
    this.categoryWaiting = categoryWaiting;
    this.categoryDone = categoryDone;
    this.categoryDeadline = dateNullChesk(categoryDeadline);

    this.color = color;
    this.textColor = textColor;
    this.borderColor = borderColor;
    this.userColor = userColor;
    this.userTextColor = userTextColor;
    this.userBorderColor = userBorderColor;

    this.groupFlg = groupFlg;
    this.userId = userId;
    this.userName = userName;
    this.groupId = groupId;
    this.myUserflg = myUserflg;

    this.categorySort = categorySort;
};

//タスク関連グループ
var TaskGroup = function (
    taskId,
    taskName,
    categoryId,
    taskRemarks,
    taskNotstarted,
    taskWorking,
    taskWaiting,
    taskDone,
    taskDeadline,
    categoryTaskFlg,
    color,
    textColor,
    borderColor,
    taskSort
    ) {
    this.domTaskAreaId = "taskarea" + taskId;
    this.categoryTaskList = "categoryTaskList" + categoryId;

    this.domCategoryAreaId = "categoryarea" + categoryId;
    this.domTaskAddTextId = "taskaddtext" + categoryId;

    this.domTaskNameId = "taskname" + taskId;
    this.domTaskNotstartedId = "tasknotstarted" + taskId;
    this.domTaskWorkingId = "taskworking" + taskId;
    this.domTaskWaitingId = "taskwaiting" + taskId;
    this.domTaskDoneId = "taskdone" + taskId;
    this.domTaskRemarksId = "taskremarks" + taskId;
    this.domtaskDeadlineId = "taskDeadline" + taskId;
    this.domTaskStatusSelectBoxId = "taskStatusSelectBox" + taskId;

    this.dbTaskId = taskId;
    this.taskName = taskName;
    this.taskRemarks = taskRemarks;
    this.taskNotstarted = taskNotstarted;
    this.taskWorking = taskWorking;
    this.taskWaiting = taskWaiting;
    this.taskDone = taskDone;
    this.taskDeadline = dateNullChesk(taskDeadline);
    this.categoryTaskFlg = categoryTaskFlg;
    this.color = color;
    this.textColor = textColor;
    this.borderColor = borderColor;

    this.taskSort = taskSort;
    
};

