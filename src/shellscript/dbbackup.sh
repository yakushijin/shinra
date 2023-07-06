#!/bin/bash

#設定ファイル読み込み
source ./dbbackup.conf

#ログディレクトリ作成
mkdir -p $backup_save_dir
mkdir -p $backup_log_dir

#ログファイル名
log=dbbackup_`date "+%Y%m%d"`.log

#エラー判定関数定義
#===============================================================================
function ErroHandler () {
	code=$1
	if [ $code -ne 0 ]; then
		echo "erorr ${code}" >> ${backup_log_dir}/${log}
		exit 1
	fi
}
#===============================================================================

echo `date "+%Y%m%d %H:%M:%S"`" start_backup" >> ${backup_log_dir}/${log}

#バックアップローテーション_作成
#===============================================================================
echo "rotation_create_fullbk" >> ${backup_log_dir}/${log}
file_existence=`find ${backup_save_dir}/ -name 'fullbk.dump' | wc -l`
if [ $file_existence -ne 0 ]; then
	cd ${backup_save_dir}
	tar zcvfS ${backup_save_dir}/`date "+%Y%m%d"`_fullbk.dump.tar.gz fullbk.dump --warning=no-file-changed >> ${backup_log_dir}/${log}
	ErroHandler $?
else
	echo "There is no rotation_create_fullbk" >> ${backup_log_dir}/${log}
fi

#===============================================================================

#バックアップローテーション_削除
#===============================================================================
echo `date "+%Y%m%d %H:%M:%S"`" rotation_delete_fullbk" >> ${backup_log_dir}/${log}
find ${backup_save_dir}/ -name '*'_fullbk.dump.tar.gz'*' -daystart -mtime +${full_backup_save_day} -exec rm -fv {} \; >> ${backup_log_dir}/${log}
ErroHandler $?

#===============================================================================

#バックアップ実施
#===============================================================================
echo `date "+%Y%m%d %H:%M:%S"`" backup_fullbk" >> ${backup_log_dir}/${log}
mysqldump -u root -p${root_db_pass} --single-transaction --all-databases  > ${backup_save_dir}/fullbk.dump
ErroHandler $?
echo `date "+%Y%m%d %H:%M:%S"`" backup_fullbk_ok" >> ${backup_log_dir}/${log}
#===============================================================================

#ログファイルローテーション_削除
find ${backup_log_dir}/ -name dbbackup_'*'.log  -mtime +${backup_save_log_day} -exec rm -fv {} \; >> ${backup_log_dir}/${log}

echo `date "+%Y%m%d %H:%M:%S"`" end_backup" >> ${backup_log_dir}/${log}

