<?php

namespace Dao;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateDao extends BaseDao
{

    public function createTable($dbUser)
    {

        Schema::connection($dbUser)->create('M_User', function (Blueprint $table) {
            $table->increments('userId')->index();
            $table->string('userName',20);
            $table->boolean('authority');
            $table->string('color',7);
            $table->string('textColor',7);
            $table->string('borderColor',7);
            $table->string('userRemarks',255)->nullable();
            $table->boolean('defaultDeadlineFlg');
            $table->boolean('deleteMessageFlg');
            $table->boolean('doneAutoActiveFlg');
            $table->boolean('activeFlg');
            $table->dateTime('archiveDay')->nullable();
            $table->integer('createUser');
            $table->integer('updateUser');
            $table->dateTime('createDay');
            $table->dateTime('updateDay');
        });

        Schema::connection($dbUser)->create('M_Group', function (Blueprint $table) {
            $table->increments('groupId')->index();
            $table->string('groupName',20);
            $table->string('color',7);
            $table->string('textColor',7);
            $table->string('borderColor',7);
            $table->string('groupRemarks',255);
            $table->boolean('activeFlg');
            $table->dateTime('archiveDay')->nullable();
            $table->integer('createUser');
            $table->integer('updateUser');
            $table->dateTime('createDay');
            $table->dateTime('updateDay');
        });

        Schema::connection($dbUser)->create('T_Tab', function (Blueprint $table) {
            $table->increments('tabId')->index();
            $table->string('tabName',10)->nullable();
            $table->tinyInteger('type');
            $table->string('color',7);
            $table->string('textColor',7);
            $table->string('borderColor',7);
            $table->string('tabRemarks',255);
            $table->integer('userOrGroupId');
            $table->boolean('groupFlg');
            $table->date('tabDeadline')->nullable();
            $table->boolean('archiveFlg');
            $table->dateTime('archiveDay')->nullable();
            $table->integer('createUser');
            $table->integer('updateUser');
            $table->dateTime('createDay');
            $table->dateTime('updateDay');
        });

        Schema::connection($dbUser)->create('T_Category', function (Blueprint $table) {
            $table->increments('categoryId')->index();
            $table->string('categoryName',30);
            $table->integer('tabId')->index();
            $table->integer('userId')->index();
            $table->integer('groupId')->index();
            $table->string('categoryRemarks',255)->nullable();
            $table->tinyInteger('categoryNotstarted');
            $table->tinyInteger('categoryWorking');
            $table->tinyInteger('categoryWaiting');
            $table->tinyInteger('categoryDone');
            $table->date('categoryDeadline')->nullable();
            $table->boolean('learnedFlg');
            $table->boolean('archiveFlg');
            $table->dateTime('notstartedDay')->nullable();
            $table->dateTime('workingDay')->nullable();
            $table->dateTime('waitingDay')->nullable();
            $table->dateTime('doneDay')->nullable();
            $table->dateTime('archiveDay')->nullable();
            $table->integer('categorySort');
            $table->integer('createUser');
            $table->integer('updateUser');
            $table->dateTime('createDay');
            $table->dateTime('updateDay');
        });

        Schema::connection($dbUser)->create('T_Task', function (Blueprint $table) {
            $table->increments('taskId')->index();
            $table->string('taskName',20);
            $table->integer('categoryId')->index();
            $table->integer('tabId')->index();
            $table->integer('userId')->index();
            $table->integer('groupId')->index();
            $table->string('taskRemarks',255)->nullable();
            $table->tinyInteger('taskNotstarted');
            $table->tinyInteger('taskWorking');
            $table->tinyInteger('taskWaiting');
            $table->tinyInteger('taskDone');
            $table->date('taskDeadline')->nullable();
            $table->boolean('learnedFlg');
            $table->boolean('archiveFlg');
            $table->dateTime('notstartedDay')->nullable();
            $table->dateTime('workingDay')->nullable();
            $table->dateTime('waitingDay')->nullable();
            $table->dateTime('doneDay')->nullable();
            $table->dateTime('archiveDay')->nullable();
            $table->integer('taskSort');
            $table->integer('createUser');
            $table->integer('updateUser');
            $table->dateTime('createDay');
            $table->dateTime('updateDay');
        });

        Schema::connection($dbUser)->create('T_UserGroupMap', function (Blueprint $table) {
            $table->integer('userId')->index();
            $table->integer('groupId')->index();
        });

        Schema::connection($dbUser)->create('T_Performance', function (Blueprint $table) {
            $table->integer('tabId')->index();
            $table->date('performanceDay')->index();
            $table->integer('notDoneCount');
            $table->integer('doneCount');
            $table->tinyInteger('percentage');
            $table->dateTime('createDay');
            $table->dateTime('updateDay');
            $table->primary(['tabId','performanceDay']);
        });
    }

    public function initDataInsert($dbUser)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            $data = [
                'userName' => 'system',
                'authority' => 1,
                'activeFlg' => 0,
                'archiveDay' => null,
                'color' => "#000033",
                'textColor' => "#00df02",
                'borderColor' => "#000033",
                'userRemarks' => "システムユーザです",
                'defaultDeadlineFlg' => 1,
                'deleteMessageFlg' => 1,
                'doneAutoActiveFlg' => 1,
                'createUser' => 1,
                'updateUser' => 1,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('M_User')->insert($data);

            $data = [
                'groupName' => 'デフォルトグループ',
                'color' => "#002371",
                'textColor' => "#a4d2ff",
                'borderColor' => "#002371",
                'groupRemarks' => 'デフォルトグループです',
                'activeFlg' => 0,
                'archiveDay' => null,
                'createUser' => 1,
                'updateUser' => 1,
                'createDay' => $now,
                'updateDay' => $now
            ];
            \DB::connection($dbUser)->table('M_Group')->insert($data);

            \DB::connection($dbUser)->table('T_UserGroupMap')
                ->insert(
                    [
                        'userId' => 1,
                        'groupId' => 1,
                    ]
                );

            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    /*====================================================
    更新
    ====================================================*/
    //ログイン情報削除
    public function updateDoneCommit($email, $companyId, $dbUser)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Login')
                ->where('email', $email)
                ->where('accountStatus', 9)
                ->update([
                    'G_Login.accountStatus' => 1,
                ]);

            \DB::table('G_Company')
                ->where('companyId', $companyId)
                ->where('dbUser', $dbUser)
                ->where('contractStatus', 9)
                ->update([
                    'G_Company.contractStatus' => 1,
                ]);

            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    /*====================================================
    削除
    ====================================================*/

    //ログイン情報削除
    public function deleteG_LoginSystemInit($email)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Login')
                ->where('email', $email)
                ->where('accountStatus', 9)
                ->delete();
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //ワークスペース削除
    public function deleteG_CompanySystemInit($companyId, $dbUser)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::table('G_Company')
                ->where('companyId', $companyId)
                ->where('dbUser', $dbUser)
                ->where('contractStatus', 9)
                ->delete();
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //機械学習用NOSQLDBテーブル作成SQL
    public function createMlTable($dbUser)
    {
        $userWordset = '
        CREATE TABLE ' . $dbUser . '.userWordset (
            userId int,
            word varchar,
            dependWord varchar,
            dependedOnWord varchar,
            score int,
            createDate date,
            PRIMARY KEY (userId,word,dependWord,dependedOnWord)
         ); 
         CREATE INDEX user_word_idx ON ' . $dbUser . '.userWordset ( word );
         CREATE INDEX user_dependWord_idx ON ' . $dbUser . '.userWordset ( dependWord );
         CREATE INDEX user_dependedOnWord_idx ON ' . $dbUser . '.userWordset ( dependedOnWord );
        ';

        $groupWordSet = '
        CREATE TABLE ' . $dbUser . '.groupWordSet (
            groupId int,
            word varchar,
            dependWord varchar,
            dependedOnWord varchar,
            score int,
            createDate date,
            PRIMARY KEY (groupId,word,dependWord,dependedOnWord)
         ); 
         CREATE INDEX group_word_idx ON ' . $dbUser . '.groupWordSet ( word );
         CREATE INDEX group_dependWord_idx ON ' . $dbUser . '.groupWordSet ( dependWord );
         CREATE INDEX group_dependedOnWord_idx ON ' . $dbUser . '.groupWordSet ( dependedOnWord );
         CREATE INDEX group_createDate_idx ON ' . $dbUser . '.groupWordSet ( createDate );
        ';

        $tabWordSet = '
        CREATE TABLE ' . $dbUser . '.tabWordSet (
            tabId int,
            word varchar,
            dependWord varchar,
            dependedOnWord varchar,
            score int,
            createDate date,
            PRIMARY KEY (tabId,word,dependWord,dependedOnWord)
         ); 
         CREATE INDEX tab_word_idx ON ' . $dbUser . '.tabWordSet ( word );
         CREATE INDEX tab_dependWord_idx ON ' . $dbUser . '.tabWordSet ( dependWord );
         CREATE INDEX tab_dependedOnWord_idx ON ' . $dbUser . '.tabWordSet ( dependedOnWord );
        ';

        $newSummaryWordSet = '
        CREATE TABLE ' . $dbUser . '.newSummaryWordSet (
            word varchar,
            score int,
            userId int,
            groupId int,
            tabId int,
            createDate date,
            PRIMARY KEY (word,userId,groupId,tabId)
         ); 
        ';

        $allTables = $userWordset.$groupWordSet.$tabWordSet.$newSummaryWordSet;
        // $allTables = $wordset;
        $createTable = str_replace(array("\r\n","\r","\n"), '', $allTables);
        return $createTable;

    }
}
