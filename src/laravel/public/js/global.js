/*=================================
グローバル変数
=================================*/
//各種API名
//ページ
const ADMINPAGE = "./admin";
const USERPAGE = "./work";

//システム
const DATABASECREATE = "/databasecreate";
const INITDATAADD = "/initdataadd";
const COMPANYGET = "./companyget";
const COMPANYUPDATE = "./companyupdate";
//const SYSTEMUSERCHANGE = "./systemuserchange";システムユーザ変更関連API保留
const LOGININFOGET = "./logininfoget";
const LOGINIDUPDATE = "./loginidupdate";
const LOGINPASSWORDUPDATE = "./loginpasswordupdate";
const SYSTEMLOGINNAMEGET = "./systemloginnameget";

//ユーザ、グループ
//const ADMINUSERGET = "./adminuserget";システムユーザ変更関連API保留
const GROUPUSERGET = "./groupuserget";
const GROUPNOTUSERGET = "./groupnotuserget";
const USERGROUPGET = "./usergroupget";
const GROUPALLGET = "./groupallget";
const USERSEARCH = "./usersearch";
const GROUPSEARCH = "./groupsearch";
const USERINFOGET = "./userinfoget";
const GROUPINFOGET = "./groupinfoget";
const USERADD = "./useradd";
const GROUPADD = "./groupadd";
const USERINFOUPDATE = "./userinfoupdate";
const GROUPINFOUPDATE = "./groupinfoupdate";
const GROUPUSERCHANGE = "./groupuserchange";

//タブ
const TABSEARCH = "./tabsearch";
const TABINFOGET = "./tabinfoget";
const TABADD = "./tabadd";
const TABUPDATE = "./tabupdate";
const TABINFOUPDATE = "./tabinfoupdate";
const TABGROUPCHANGE = "./tabgroupchange";
const GROUPTABCHANGE = "./grouptabchange";
const TABDELETE = "./tabdelete";

//カテゴリ
const CATEGORYINFOGET = "./categoryinfoget";
const CATEGORYADD = "./categoryadd";
const CATEGORYUPDATE = "./categoryupdate";
const CATEGORYDEADLINEUPDATE = "./categorydeadlineupdate";
const CATEGORYDETAILUPDATE = "./categorydetailupdate";
const CATEGORYSTATUSUPDATE = "./categorystatusupdate";
const CATEGORYSUSPENDED = "./categorysuspended";
const CATEGORYUSERCHANGE = "./categoryuserchange";
const CATEGORYDELETE = "./categorydelete";
const CATEGORYSORT = "./categorysort";
const CATEGORYDONEARCHIVE = "./categorydoneArchive";
const CATEGORYFILTER = "./categoryfilter";

//タスク
const TASKINFOGET = "./taskinfoget";
const TASKGET = "./taskget";
const TASKADD = "./taskadd";
const TASKUPDATE = "./taskupdate";
const TASKDEADLINEUPDATE = "./taskdeadlineupdate";
const TASKDETAILUPDATE = "./taskdetailupdate";
const TASKSTATUSUPDATE = "./taskstatusupdate";
const TASKSUSPENDED = "./tasksuspended";
const TASKDELETE = "./taskdelete";
const TASKSORT = "./tasksort";

//実績、レポート
const PERFORMANCESEARCH = "./performancesearch";
const USERREPORT = "./userreport";
const GROUPREPORT = "./groupreport";
const TABREPORT = "./tabreport";

//機械学習
const GETWORDTOUSER = "./getwordtouser";
const GETUSERTOWORD = "./getusertoword";
const GETALLNEWWORD = "./getallnewword";
const GETFREEWORDTOID = "./getfreewordtoid";
const GETNEWWORDTOID = "./getnewwordtoid";

//ステータスボタン表示名
var notstartedText = "未着手";
var workingText = "作業中";
var waitingText = "待機中";
var doneText = "完了済";
var suspendedText = "中断中";

//各機種の画面サイズ
const TABLET = 860;
const SMARTPHONE = 485;
const MINSIZE = 320;

//タブ基本情報
var tabAreaSize = 0;
const TABSIZE = 156;
var tabListSort = [];
var tabServerListSort = [];

//リンク
const HOST = "https://" + $(location).attr("host");
const HELPPATH = HOST + "/images/help.html";
const MANUALPATH = HOST + "/images/sousa.html";
const KIYAKU = HOST + "/images/freeKiyaku.html";
const PRIVACY = HOST + "/images/freePrivacy.html";
const TOPPATH = HOST + "/login";
const WORKPATH = HOST + "/work";
const ADMINPATH = HOST + "/admin";

//メッセージレベル
const INFO = "info";
const WARNING = "warning";
const ERROR = "error";

var noticeSize = "40%";


const companyTextMaxLength = 20;
const userTextMaxLength = 20;
const groupTextMaxLength = 20;
const tabTextMaxLength = 10;
const categoryTextMaxLength = 30;
const taskTextMaxLength = 20;
const remarksTextMaxLength = 100;
const dayTextMaxLength = 10;
const numbersTextMaxLength = 3;

const reportDayRange = 7;
const graphDayRange = 10;
const graphMonthRange = 6;


const userColorSet =
    '<option value="#c3f4c0"></option>'
    + '<option value="#a4d2ff"></option>'
    + '<option value="#eed7ee"></option>'
    + '<option value="#ffd7c6"></option>'
    + '<option value="#e7cc9f"></option>'
    + '<option value="#002371"></option>'
    + '<option value="#008822"></option>'
    + '<option value="#8e0000"></option>'
    + '<option value="#b48b16"></option>'
    + '<option value="#661b4e"></option>';
const userTextColorSet =
    '<option value="#008822"></option>'
    + '<option value="#002371"></option>'
    + '<option value="#661b4e"></option>'
    + '<option value="#8e0000"></option>'
    + '<option value="#b48b16"></option>'
    + '<option value="#a4d2ff"></option>'
    + '<option value="#c3f4c0"></option>'
    + '<option value="#e7d1ec"></option>'
    + '<option value="#e7cc9f"></option>'
    + '<option value="#eed7ee"></option>';
const userBorderColorSet =
    '<option value="#c3f4c0"></option>'
    + '<option value="#a4d2ff"></option>'
    + '<option value="#eed7ee"></option>'
    + '<option value="#ffd7c6"></option>'
    + '<option value="#e7cc9f"></option>'
    + '<option value="#002371"></option>'
    + '<option value="#008822"></option>'
    + '<option value="#8e0000"></option>'
    + '<option value="#b48b16"></option>'
    + '<option value="#661b4e"></option>';

const groupColorSet =
    '<option value="#c3f4c0"></option>'
    + '<option value="#a4d2ff"></option>'
    + '<option value="#eed7ee"></option>'
    + '<option value="#ffd7c6"></option>'
    + '<option value="#e7cc9f"></option>'
    + '<option value="#002371"></option>'
    + '<option value="#008822"></option>'
    + '<option value="#8e0000"></option>'
    + '<option value="#b48b16"></option>'
    + '<option value="#661b4e"></option>';
const groupTextColorSet =
    '<option value="#008822"></option>'
    + '<option value="#002371"></option>'
    + '<option value="#661b4e"></option>'
    + '<option value="#8e0000"></option>'
    + '<option value="#b48b16"></option>'
    + '<option value="#a4d2ff"></option>'
    + '<option value="#c3f4c0"></option>'
    + '<option value="#e7d1ec"></option>'
    + '<option value="#e7cc9f"></option>'
    + '<option value="#eed7ee"></option>';
const groupBorderColorSet =
    '<option value="#c3f4c0"></option>'
    + '<option value="#a4d2ff"></option>'
    + '<option value="#eed7ee"></option>'
    + '<option value="#ffd7c6"></option>'
    + '<option value="#e7cc9f"></option>'
    + '<option value="#002371"></option>'
    + '<option value="#008822"></option>'
    + '<option value="#8e0000"></option>'
    + '<option value="#b48b16"></option>'
    + '<option value="#661b4e"></option>';

const tabColorSet =
    '<option value="#00ffc0"></option>'
    + '<option value="#d1ecea"></option>'
    + '<option value="#e4ecd1"></option>'
    + '<option value="#ecdad1"></option>'
    + '<option value="#c3c6ec"></option>'
    + '<option value="#ffd000"></option>'
    + '<option value="#e7d1ec"></option>'
    + '<option value="#abe3bc"></option>'
    + '<option value="#00d0ff"></option>'
    + '<option value="#e7d1ec"></option>';
const tabTextColorSet =
    '<option value="#132b15"></option>'
    + '<option value="#000083"></option>'
    + '<option value="#6a7c00"></option>'
    + '<option value="#c6a400"></option>'
    + '<option value="#550071"></option>'
    + '<option value="#6a4d00"></option>'
    + '<option value="#6d2b55"></option>'
    + '<option value="#005300"></option>'
    + '<option value="#000000"></option>'
    + '<option value="#000078"></option>';
const tabBorderColorSet =
    '<option value="#00ffc0"></option>'
    + '<option value="#d1ecea"></option>'
    + '<option value="#e4ecd1"></option>'
    + '<option value="#ecdad1"></option>'
    + '<option value="#c3c6ec"></option>'
    + '<option value="#ffd000"></option>'
    + '<option value="#e7d1ec"></option>'
    + '<option value="#abe3bc"></option>'
    + '<option value="#00d0ff"></option>'
    + '<option value="#e7d1ec"></option>';

//画面ID
const ADMINDISP = "admindisp";
const WORKDISP = "workdisp";

//ヘルプ（トップ画面から）リンク
function help() {
    window.open(HELPPATH, "_blank", "top=50,left=50,width=800,height=800,scrollbars=1,location=0,menubar=0,toolbar=0,status=1,directories=0,resizable=1");
};

//ヘルプ（ワーク画面、管理画面から）リンク
function manual(id) {
    window.open(MANUALPATH + id, "_blank", "top=50,left=50,width=800,height=800,scrollbars=1,location=0,menubar=0,toolbar=0,status=1,directories=0,resizable=1");
};

//利用規約リンク
function kiyaku() {
    window.open(KIYAKU, "_blank", "top=50,left=50,width=800,height=800,scrollbars=1,location=0,menubar=0,toolbar=0,status=1,directories=0,resizable=1");
};

//プライバシーポリシーリンク
function privacy() {
    window.open(PRIVACY, "_blank", "top=50,left=50,width=800,height=800,scrollbars=1,location=0,menubar=0,toolbar=0,status=1,directories=0,resizable=1");
};