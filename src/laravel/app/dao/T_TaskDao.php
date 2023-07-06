<?php

namespace Dao;

class T_TaskDao extends BaseDao
{
    /*====================================================
   取得
   ====================================================*/
    //タスク件数取得
    public function getT_TaskCount($dbUser)
    {
        $taskCount = \DB::connection($dbUser)->table('T_Task')
            ->count();

        return $taskCount;
    }

    //タスク基本情報取得
    public function getT_Task($dbUser, $categoryId)
    {
        $taskdata = \DB::connection($dbUser)->table('T_Task')
            ->select([
                'T_Task.taskId',
                'T_Task.taskName',
                'T_Task.categoryId',
                'T_Task.userId',
                'T_Task.groupId',
                'T_Task.taskNotstarted',
                'T_Task.taskWorking',
                'T_Task.taskWaiting',
                'T_Task.taskDone',
                'T_Task.taskDeadline',
                'T_Task.archiveFlg',
                'T_Task.taskSort'
            ])
            ->where('T_Task.archiveFlg', '=', 0)
            ->where('T_Task.categoryId', '=', $categoryId)
            ->orderBy('taskSort')
            ->orderByRaw('taskDeadline IS NULL ASC')
            ->orderBy('taskDeadline')
            ->get();

        return $taskdata;
    }

    //タスク詳細情報取得
    public function getT_TaskInfo($dbUser, $taskId)
    {
        $data = \DB::connection($dbUser)->table('T_Task')
            ->select([
                'T_Task.taskName',
                'T_Task.tabId',
                'T_Task.categoryId',
                'T_Task.userId',
                'T_Task.groupId',
                'T_Task.taskRemarks',
                'T_Task.taskNotstarted',
                'T_Task.taskWorking',
                'T_Task.taskWaiting',
                'T_Task.taskDone',
                'T_Task.taskDeadline',
                'T_Task.learnedFlg',
                'T_Task.archiveFlg',
                'T_Task.notstartedDay',
                'T_Task.workingDay',
                'T_Task.waitingDay',
                'T_Task.doneDay',
                'T_Task.archiveDay',
                'T_Task.createUser',
                'T_Task.updateUser',
                'T_Task.createDay',
                'T_Task.updateDay',
                'M_User_user.userName as userName',
                'M_User_user.color as color',
                'M_User_user.textColor as textColor',
                'M_User_user.borderColor as borderColor',
                'M_User_createUser.userName as createUserName',
                'M_User_createUser.color as createUserColor',
                'M_User_createUser.textColor as createUserTextColor',
                'M_User_createUser.borderColor as createUserBorderColor',
                'M_User_updateUser.userName as updateUserName',
                'M_User_updateUser.color as updateUserColor',
                'M_User_updateUser.textColor as updateUserTextColor',
                'M_User_updateUser.borderColor as updateUserBorderColor',
            ])
            ->where('T_Task.taskId', '=', $taskId)
            ->join('M_User as M_User_user', 'M_User_user.userId', '=', 'T_Task.userId')
            ->join('M_User as M_User_createUser', 'M_User_createUser.userId', '=', 'T_Task.createUser')
            ->join('M_User as M_User_updateUser', 'M_User_updateUser.userId', '=', 'T_Task.updateUser')
            ->get();

        return $data[0];
    }

    /*====================================================
    登録
    ====================================================*/
    //タスク新規登録
    public function addT_Task($dbUser, $userId, $groupId, $tabId, $categoryId, $taskName, $taskDeadline, $now)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            $data = [
                'taskName' => $taskName,
                'categoryId' => $categoryId,
                'tabId' => $tabId,
                'userId' => $userId,
                'groupId' =>  $groupId,
                'taskRemarks' => '',
                'taskNotstarted' => 1,
                'taskWorking' =>  0,
                'taskWaiting' => 0,
                'taskDone' =>  0,
                'taskDeadline' => $taskDeadline,
                'learnedFlg' =>  0,
                'archiveFlg' => 0,
                'notstartedDay' => $now,
                'workingDay' => null,
                'waitingDay' => null,
                'doneDay' => null,
                'archiveDay' => null,
                'taskSort' => 0,
                'createUser' => $userId,
                'updateUser' => $userId,
                'createDay' => $now,
                'updateDay' => $now
            ];
            $taskId = \DB::connection($dbUser)->table('T_Task')->insertGetId($data, 'taskId');

            \DB::connection($dbUser)->table('T_Task')->where('taskId', $taskId)->update(
                [
                    'taskSort' => $taskId
                ]
            );

            $result['resultData'] = $taskId;
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
    //タスク名更新
    public function updateT_Task($dbUser, $userId, $taskId, $taskName)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Task')->where('taskId', $taskId)->update([
                'taskName' => $taskName,
                'updateUser' => $userId,
                'updateDay' => $now
            ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //タスク期日更新
    public function updateT_TaskDeadline($dbUser, $userId, $taskId, $deadline)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Task')->where('taskId', $taskId)
                ->update([
                    'taskDeadline' => $deadline,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //タスク詳細更新
    public function updateDetailT_Task($dbUser, $userId, $taskId, $taskName, $taskRemarks, $taskDeadline)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Task')->where('taskid', $taskId)
                ->update([
                    'taskName' => $taskName,
                    'taskRemarks' => parent::nullChangeString($taskRemarks),
                    'taskDeadline' => $taskDeadline,
                    'updateUser' => $userId,
                    'updateDay' => $now
                ]);
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    //タスク並び順変更
    public function updateT_TaskSort($dbUser, $userId, $taskId, $taskSort)
    {
        $now = \Carbon\Carbon::now();
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Task')->where('taskId', $taskId)
                ->update([
                    'taskSort' => $taskSort,
                    'updateUser' => $userId,
                    'updateDay' => $now
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
    //タスク削除
    public function deleteT_Task($dbUser, $taskId)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Task')->where('taskId', $taskId)->delete();
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    /*====================================================
    更新_特殊
    ====================================================*/
    public function statusUpdateT_Task($dbUser, $taskId, $notstarted, $working, $waiting, $done, $archiveFlg)
    {
        $result = ['code' => 0, 'resultData' => []];
        $now = \Carbon\Carbon::now();

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Task')->where('taskId', $taskId)
                ->update([
                    'taskNotstarted' => $notstarted,
                    'taskWorking' => $working,
                    'taskWaiting' => $waiting,
                    'taskDone' => $done,
                    'archiveFlg' => $archiveFlg,
                ]);

            if ($working) {
                \DB::connection($dbUser)->table('T_Task')
                    ->where('taskId', $taskId)
                    ->where('taskWorking', 1)
                    ->where('workingDay', null)
                    ->update([
                        'workingDay' => $now
                    ]);
            } else if ($waiting) {
                \DB::connection($dbUser)->table('T_Task')
                    ->where('taskId', $taskId)
                    ->where('taskWaiting', 1)
                    ->where('waitingDay', null)
                    ->update([
                        'waitingDay' => $now
                    ]);
            } else if ($done) {
                \DB::connection($dbUser)->table('T_Task')
                    ->where('taskId', $taskId)
                    ->where('taskDone', 1)
                    ->update([
                        'doneDay' => $now
                    ]);
            }
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }

    public function suspendedT_Task($dbUser, $taskId, $userId)
    {
        $result = ['code' => 0, 'resultData' => []];

        \DB::beginTransaction();
        try {
            \DB::connection($dbUser)->table('T_Task')
                ->where('taskWorking', 1)
                ->where('taskId', '<>', $taskId)
                ->where('userId', $userId)
                ->update([
                    'taskWorking' => 2
                ]);

            $result['resultData'] = \DB::connection($dbUser)->table('T_Task')
                ->select([
                    'T_Task.taskId',
                    'T_Task.taskWorking'
                ])
                ->where('T_Task.taskWorking', '=', 2)
                ->where('userId', $userId)
                ->get();
            \DB::commit();
        } catch (\Exception $e) {
            $result['code'] = 1;
            \DB::rollback();
        }

        return $result;
    }
}
