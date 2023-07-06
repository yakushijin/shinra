<?php

namespace Dao;

class Debug extends BaseDao
{
    //デバッグ用
    public function demo($dbUser, $dbPassword)
    {
        //大量データ登録用件数
        $usercount = 0;
        $groupcount = 0;
        $tabcount = 0;
        $categorycount = 0;
        $taskcount = 0;

        \DB::connection($dbUser)->table('M_User')->truncate();
        \DB::connection($dbUser)->table('T_UserGroupMap')->truncate();
        \DB::connection($dbUser)->table('M_Group')->truncate();
        \DB::connection($dbUser)->table('T_Tab')->truncate();
        \DB::connection($dbUser)->table('T_Category')->truncate();
        \DB::connection($dbUser)->table('T_Task')->truncate();
        \DB::connection($dbUser)->table('T_Performance')->truncate();
        \DB::table('G_Login')->truncate();
        \DB::table('G_Company')->truncate();

        $now = \Carbon\Carbon::now();

        $data = [
            'userId' => 1,
            'companyId' => 1,
            'email' => 'test@test.com',
            'password' => bcrypt('test1234'),
            'createDay' => $now,
            'updateDay' => $now
        ];
        \DB::table('G_Login')->insert($data);
        $data = [
            'userId' => 2,
            'companyId' => 1,
            'email' => 'admin@test',
            'password' => bcrypt('test1234'),
            'createDay' => $now,
            'updateDay' => $now
        ];
        \DB::table('G_Login')->insert($data);
        $data = [
            'userId' => 3,
            'companyId' => 1,
            'email' => 'suzuki@test',
            'password' => bcrypt('test1234'),
            'createDay' => $now,
            'updateDay' => $now
        ];
        \DB::table('G_Login')->insert($data);
        $data = [
            'userId' => 4,
            'companyId' => 1,
            'email' => 'tanaka@test',
            'password' => bcrypt('test1234'),
            'createDay' => $now,
            'updateDay' => $now
        ];
        \DB::table('G_Login')->insert($data);
        $data = [
            'userId' => 5,
            'companyId' => 1,
            'email' => 'sato@test',
            'password' => bcrypt('test1234'),
            'createDay' => $now,
            'updateDay' => $now
        ];
        \DB::table('G_Login')->insert($data);

        for ($i = 1; $i <= 1; $i++) {
            $data = [
                'companyName' => '株式会社デモ商事',
                'contractStatus' => 1,
                'dbUser' => $dbUser,
                'dbPassword' => $dbPassword,
                'dbHost' => 'localhost',
                'mlHost' => 'localhost',
                'userMaxCount' => 1000,
                'groupMaxCount' => 1000,
                'tabMaxCount' => 10000,
                'categoryMaxCount' => 100000,
                'taskMaxCount' => 100000,
                'dbDataUse' => 0,
                'dbDataMaxSize' => 10,
                'mlDataUse' => 0,
                'mlDataAllCount' => 0,
                'mlDataMaxSize' => 10,
                'systemUserId' => 1,
                'categorySaveDay' => 30,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::table('G_Company')->insert($data);
        }


        $user = [
            'userName' => 'システム管理者',
            'authority' => 1,
            'activeFlg' => 0,
            'userRemarks' => "",
            'color' => "#ffd2ff",
            'textColor' => "#8e0000",
            'borderColor' => "#ffd2ff",
            'archiveDay' => null,
            'defaultDeadlineFlg' => 0,
            'deleteMessageFlg' => 0,
            'doneAutoActiveFlg' => 0,
            'createUser' => 1,
            'updateUser' => 1,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $userId = \DB::connection($dbUser)->table('M_User')->insertGetId($user, 'userId');
        $initColor = parent::colorInitSet($userId, "user");
        \DB::connection($dbUser)->table('M_User')->where('userId', $userId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $user = [
            'userName' => '管理者',
            'authority' => 1,
            'activeFlg' => 0,
            'userRemarks' => "",
            'color' => "#7886d3",
            'textColor' => "#FFFFFF",
            'borderColor' => "#7886d3",
            'archiveDay' => null,
            'defaultDeadlineFlg' => 0,
            'deleteMessageFlg' => 0,
            'doneAutoActiveFlg' => 0,
            'createUser' => 1,
            'updateUser' => 1,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $userId = \DB::connection($dbUser)->table('M_User')->insertGetId($user, 'userId');
        $initColor = parent::colorInitSet($userId, "user");
        \DB::connection($dbUser)->table('M_User')->where('userId', $userId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $user = [
            'userName' => '鈴木',
            'authority' => 0,
            'activeFlg' => 0,
            'archiveDay' => null,
            'userRemarks' => "社員です",
            'color' => "",
            'textColor' => "#a4d2ff",
            'borderColor' => "#00234f",
            'defaultDeadlineFlg' => 0,
            'deleteMessageFlg' => 0,
            'doneAutoActiveFlg' => 0,
            'createUser' => 2,
            'updateUser' => 2,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $userId = \DB::connection($dbUser)->table('M_User')->insertGetId($user, 'userId');
        $initColor = parent::colorInitSet($userId, "user");
        \DB::connection($dbUser)->table('M_User')->where('userId', $userId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $user = [
            'userName' => '田中',
            'authority' => 0,
            'activeFlg' => 0,
            'archiveDay' => null,
            'userRemarks' => "バイトです",
            'color' => "#00cc71",
            'textColor' => "#005800",
            'borderColor' => "#00cc71",
            'defaultDeadlineFlg' => 0,
            'deleteMessageFlg' => 0,
            'doneAutoActiveFlg' => 0,
            'createUser' => 2,
            'updateUser' => 2,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $userId = \DB::connection($dbUser)->table('M_User')->insertGetId($user, 'userId');
        $initColor = parent::colorInitSet($userId, "user");
        \DB::connection($dbUser)->table('M_User')->where('userId', $userId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $user = [
            'userName' => '佐藤',
            'authority' => 0,
            'activeFlg' => 0,
            'userRemarks' => "",
            'color' => "#8e0000",
            'textColor' => "#ffd2ff",
            'borderColor' => "#8e0000",
            'archiveDay' => null,
            'defaultDeadlineFlg' => 0,
            'deleteMessageFlg' => 0,
            'doneAutoActiveFlg' => 0,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $userId = \DB::connection($dbUser)->table('M_User')->insertGetId($user, 'userId');
        $initColor = parent::colorInitSet($userId, "user");
        \DB::connection($dbUser)->table('M_User')->where('userId', $userId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        //ユーザ大量データ登録
        for ($i = 0; $i < $usercount; $i++) {
            $user = [
                'userName' => 'テスト' . $i,
                'authority' => 0,
                'activeFlg' => 0,
                'userRemarks' => "",
                'color' => "#8e0000",
                'textColor' => "#ffd2ff",
                'borderColor' => "#8e0000",
                'archiveDay' => null,
                'defaultDeadlineFlg' => 0,
                'deleteMessageFlg' => 0,
                'doneAutoActiveFlg' => 0,
                'createUser' => 1,
                'updateUser' => 1,
                'createDay' => $now,
                'updateDay' => $now
            ];
            $userId = \DB::connection($dbUser)->table('M_User')->insertGetId($user, 'userId');
        }

        //グループとユーザマッピング
        for ($i = 1; $i < 6; $i++) {
            for ($j = 1; $j < 4; $j++) {
                $data = [
                    'userId' => $i,
                    'groupId' => $j
                ];
                \DB::connection($dbUser)->table('T_UserGroupMap')->insert($data);
            
            }
        }

        
        $data = [
            'groupName' => '営業',
            'color' => "#bcaa00",
            'textColor' => "#fff6b5",
            'borderColor' => "#9c7a2a",
            'groupRemarks' => '',
            'activeFlg' => 0,
            'archiveDay' => null,
            'createUser' => 1,
            'updateUser' => 1,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $groupId = \DB::connection($dbUser)->table('M_Group')->insertGetId($data, 'groupId');
        $initColor = parent::colorInitSet($groupId, "group");
        \DB::connection($dbUser)->table('M_Group')->where('groupId', $groupId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'groupName' => '開発',
            'color' => "#abe3bc",
            'textColor' => "#005300",
            'borderColor' => "#abe3bc",
            'groupRemarks' => '',
            'activeFlg' => 0,
            'archiveDay' => null,
            'createUser' => 2,
            'updateUser' => 2,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $groupId = \DB::connection($dbUser)->table('M_Group')->insertGetId($data, 'groupId');
        $initColor = parent::colorInitSet($groupId, "group");
        \DB::connection($dbUser)->table('M_Group')->where('groupId', $groupId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'groupName' => '管理',
            'color' => "#ff00ff",
            'textColor' => "#000000",
            'borderColor' => "#0000ff",
            'groupRemarks' => '',
            'activeFlg' => 0,
            'archiveDay' => null,
            'createUser' => 2,
            'updateUser' => 2,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $groupId = \DB::connection($dbUser)->table('M_Group')->insertGetId($data, 'groupId');
        $initColor = parent::colorInitSet($groupId, "group");
        \DB::connection($dbUser)->table('M_Group')->where('groupId', $groupId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        //グループ大量データ登録
        for ($i = 0; $i < $groupcount; $i++) {
            $data = [
                'groupName' => 'グループ' . $i,
                'color' => "#ff00ff",
                'textColor' => "#000000",
                'borderColor' => "#0000ff",
                'groupRemarks' => '',
                'activeFlg' => 0,
                'archiveDay' => null,
                'createUser' => 1,
                'updateUser' => 1,
                'createDay' => $now,
                'updateDay' => $now
            ];
            $groupId = \DB::connection($dbUser)->table('M_Group')->insertGetId($data, 'groupId');
        }

        $data = [
            'tabName' => 'F社A案件', //tabid1
            'type' => 1,
            'color' => "#e7d1ec",
            'textColor' => "#850000",
            'borderColor' => "#850000",
            'tabRemarks' => "",
            'userOrGroupId' => 1,
            'groupFlg' => 1,
            'tabDeadline' => null,
            'archiveFlg' => 0,
            'archiveDay' => null,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        $initColor = parent::colorInitSet($tabId, "tab");
        \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'tabName' => 'N社B案件', //tabid2
            'type' => 1,
            'color' => "#ece8d1",
            'textColor' => "#000078",
            'borderColor' => "#858800",
            'tabRemarks' => "",
            'userOrGroupId' => 2,
            'groupFlg' => 1,
            'tabDeadline' => null,
            'archiveFlg' => 0,
            'archiveDay' => null,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        $initColor = parent::colorInitSet($tabId, "tab");
        \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'tabName' => '自社C案件', //tabid3
            'type' => 1,
            'color' => "#c3c6ec",
            'textColor' => "#000000",
            'borderColor' => "#000000",
            'tabRemarks' => "",
            'userOrGroupId' => 3,
            'groupFlg' => 1,
            'tabDeadline' => null,
            'archiveFlg' => 0,
            'archiveDay' => null,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        $initColor = parent::colorInitSet($tabId, "tab");
        \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'tabName' => '質問票', //tabid4
            'type' => 1,
            'color' => "#c3c6ec",
            'textColor' => "#c57a00",
            'borderColor' => "#c3c6ec",
            'tabRemarks' => "",
            'userOrGroupId' => 1,
            'groupFlg' => 1,
            'tabDeadline' => null,
            'archiveFlg' => 0,
            'archiveDay' => null,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        $initColor = parent::colorInitSet($tabId, "tab");
        \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'tabName' => 'M社課題票', //tabid5
            'type' => 1,
            'color' => "#d1ecea",
            'textColor' => "#004a00",
            'borderColor' => "#d1ecea",
            'tabRemarks' => "",
            'userOrGroupId' => 2,
            'groupFlg' => 1,
            'tabDeadline' => null,
            'archiveFlg' => 0,
            'archiveDay' => null,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        $initColor = parent::colorInitSet($tabId, "tab");
        \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'tabName' => '自社作業', //tabid6
            'type' => 1,
            'color' => "#e4ecd1",
            'textColor' => "#000000",
            'borderColor' => "#000000",
            'tabRemarks' => "",
            'userOrGroupId' => 2,
            'groupFlg' => 0,
            'tabDeadline' => null,
            'archiveFlg' => 0,
            'archiveDay' => null,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        $initColor = parent::colorInitSet($tabId, "tab");
        \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        $data = [
            'tabName' => 'メモ', //tabid7
            'type' => 1,
            'color' => "#ecdad1",
            'textColor' => "#000000",
            'borderColor' => "#000000",
            'tabRemarks' => "",
            'userOrGroupId' => 3,
            'groupFlg' => 0,
            'tabDeadline' => null,
            'archiveFlg' => 0,
            'archiveDay' => null,
            'createUser' => 4,
            'updateUser' => 4,
            'createDay' => $now,
            'updateDay' => $now
        ];
        $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        $initColor = parent::colorInitSet($tabId, "tab");
        \DB::connection($dbUser)->table('T_Tab')->where('tabId', $tabId)->update(
            [
                'color' => $initColor["color"],
                'textColor' => $initColor["textColor"],
                'borderColor' => $initColor["borderColor"]
            ]
        );

        //タブ大量データ登録
        for ($i = 0; $i < $tabcount; $i++) {
            $data = [
                'tabName' => 'テストタブテストタブテストタブテストタブ' . $i,
                'type' => 1,
                'color' => "#ecdad1",
                'textColor' => "#000000",
                'borderColor' => "#000000",
                'tabRemarks' => "",
                'userOrGroupId' => 3,
                'groupFlg' => 0,
                'tabDeadline' => null,
                'archiveFlg' => 0,
                'archiveDay' => null,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            $tabId = \DB::connection($dbUser)->table('T_Tab')->insertGetId($data, 'tabId');
        }

        $array = ['概算見積を提出', '次回打ち合わせの日程調整', '製品資料のアップデート'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 1,
                'userId' => 3,
                'groupId' => 2,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => $now,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }


        $array = ['機能追加の件を返答する', '売上管理の機能追加', '帳票のエラー対応'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 3,
                'userId' => 3,
                'groupId' => 3,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        $array = ['エラーログを調べる', '事象を確認する', '文章まとめて返答する'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'taskName' => $array[$i],
                'categoryId' => 4,
                'tabId' => 3,
                'userId' => 1,
                'groupId' => 3,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }

        $array = ['ログ確認し再現させる', '担当者にサーバを確認する', 'コードの修正、テスト', '本番にデプロイする'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'taskName' => $array[$i],
                'categoryId' => 6,
                'tabId' => 1,
                'userId' => 1,
                'groupId' => 0,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }


        $array = ['設計書ID202の作成', '開発環境の標準化、展開', '機能ID101の作成'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 2,
                'userId' => 5,
                'groupId' => 2,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        $array = ['設計書ID302の作成', 'データベースの設計', '機能ID101の作成'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 2,
                'userId' => 2,
                'groupId' => 1,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'createUser' => 4,
                'categorySort' => 0,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        $array = ['要件定義書の作成', '設計書ID201のレビュー', '見積書修正'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 2,
                'userId' => 3,
                'groupId' => 1,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        $array = ['先方に既存保守費用の件確認する', 'ベンダーに概算見積依頼する', '課題解決の提案資料用意する'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 2,
                'userId' => 4,
                'groupId' => 1,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => $now,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }


        $array = ['機能一覧整理する', '各名称まとめる', '型とサイズを決める', 'リレーション決める'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'taskName' => $array[$i],
                'categoryId' => 11,
                'tabId' => 3,
                'userId' => 1,
                'groupId' => 3,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }

        $array = ['RFPの内容洗い出す', '不明事項まとめる', '作成後、社内でレビューする'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'taskName' => $array[$i],
                'categoryId' => 13,
                'tabId' => 3,
                'userId' => 1,
                'groupId' => 3,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }

        //カテゴリ大量データ登録
        for ($i = 0; $i < $categorycount; $i++) {
            $data = [
                'categoryName' => 'テストカテゴリテストカテゴリテストカテゴリ' . $i,
                'tabId' => 10,
                'userId' => 10,
                'groupId' => 10,
                'categoryRemarks' => '',
                'categoryNotstarted' => 0,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  1,
                'categoryDeadline' => $now,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        //タスク大量データ登録
        for ($i = 0; $i < $taskcount; $i++) {
            $data = [
                'taskName' => 'テストタスクテストタスクテストタスクテストタスク' . $i,
                'categoryId' => $i,
                'tabId' => 10,
                'userId' => 10,
                'groupId' => 10,
                'taskRemarks' => '',
                'taskNotstarted' => 0,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  1,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }


        $array = ['雑務関連', 'URLリンク関連', '定型文', 'コマンドリスト', 'アイデア'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 7,
                'userId' => 3,
                'groupId' => 0,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => $now,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        $array = ['交通費申請', '歓迎会調整', '手当の件管理部門に確認'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'taskName' => $array[$i],
                'categoryId' => 19,
                'tabId' => 7,
                'userId' => 3,
                'groupId' => 0,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }

        $array = ['××製品の仕様について', '▲▲の値段とオプションについて', '●●社との協業について', '提案資料のフォーマットについて'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 4,
                'userId' => 3,
                'groupId' => 1,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => $now,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        $array = ['追加機能分は上乗せして大丈夫なのか', '可能です。詳細は備考欄を参照ください', '値下げは可能か', '原価を下回らなければ可能です'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'taskName' => $array[$i],
                'categoryId' => 25,
                'tabId' => 7,
                'userId' => 3,
                'groupId' => 0,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }

        $array = ['機能300にて発生するバグについて', 'サーバ側でハングアップが発生する件', '画面400に存在する脆弱性', '通信の速度が特定時間帯で遅くなる件'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'categoryName' => $array[$i],
                'tabId' => 5,
                'userId' => 3,
                'groupId' => 1,
                'categoryRemarks' => '',
                'categoryNotstarted' => 1,
                'categoryWorking' =>  0,
                'categoryWaiting' => 0,
                'categoryDone' =>  0,
                'categoryDeadline' => $now,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'categorySort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now

            ];
            \DB::connection($dbUser)->table('T_Category')->insert($data);
        }

        $array = ['問題個所の特定が完了しました', '再現検証が完了しました', '対応策を考案しました、ご確認ください', 'この対応時サービス停止は必要ですか'];

        for ($i = 0; $i < count($array); $i++) {
            $data = [
                'taskName' => $array[$i],
                'categoryId' => 29,
                'tabId' => 7,
                'userId' => 3,
                'groupId' => 0,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => null,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => 4,
                'updateUser' => 4,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('T_Task')->insert($data);
        }

        //実績データ
        for ($i = 0; $i < 100; $i++) {
            $day = $now->subDay();
            for ($j = 1; $j < 5; $j++) {
                $data = [
                    'tabId' => $j,
                    'performanceDay' => $day,
                    'notDoneCount' => 100 - $i,
                    'doneCount' => 1 + $i,
                    'percentage' => rand(0, 100),
                    'createDay' =>  $now,
                    'updateDay' =>  $now
                ];
                \DB::connection($dbUser)->table('T_Performance')->insert($data);
            }
        }
    }



    public function datadelete()
    {
        \DB::connection('dbUser1')->table('T_Tab')->truncate();
        \DB::connection('dbUser1')->table('T_Category')->truncate();
        \DB::connection('dbUser1')->table('T_Task')->truncate();
    }

    public function alldelete()
    {
        \DB::connection('dbUser1')->table('M_User')->truncate();
        \DB::connection('dbUser1')->table('T_UserGroupMap')->truncate();
        \DB::connection('dbUser1')->table('M_Group')->truncate();
        \DB::connection('dbUser1')->table('T_Tab')->truncate();
        \DB::connection('dbUser1')->table('T_Category')->truncate();
        \DB::connection('dbUser1')->table('T_Task')->truncate();
        \DB::table('G_Login')->truncate();
        \DB::table('G_Company')->truncate();
    }
}
