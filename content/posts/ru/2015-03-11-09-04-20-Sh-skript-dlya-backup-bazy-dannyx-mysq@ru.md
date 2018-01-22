---
$title@: Sh-skript-dlya-backup-bazy-dannyx-mysql
author@: Viktor Zharina
$order: 205
$dates:
  published: 2015-03-11 09:04:20
---
Данный скрипт делает полный бекап базы данных и копирует данный бекап на удаленный сервер



<code>

#!/bin/bash

# данный скрипт делает полный бекап базы данных и копирует данный бекап на удаленный сервер

# script full backup mysql database and then copy it to to the remote server

TD=`date +%y-%m-%d`

FN=$TD".sql.gz"

DB_NAME='database_name'

MYSQL_PATH_TO_BACKUP="/var/backups/dbProject/mysql/$FN"

REMOTE_IP="remote_ip"

REMOTE_USER="remote_user"

REMOTE_PATH="/remote_path/"

REMOTE_FN="database_name_last.sql.gz"

USER='db_user'

PASSWORD='db_password'



mysqldump -u $USER -p$PASSWORD --routines $DB_NAME | gzip > $MYSQL_PATH_TO_BACKUP

scp -B -l 49152 $MYSQL_PATH_TO_BACKUP $REMOTE_USER@$REMOTE_IP:$REMOTE_PATH$REMOTE_FN</code>

