#!/bin/bash

#################### SCRIPT PARA BACKUP MYSQL ####################
# Gladyston Batista <glbatistabh@gmail.com>                      #
# Created Out, 2014                                              #
# Update Out, 2014                                               #
##################################################################

# Definindo parametros do MySQL
echo -e "\n  -- Definindo parametros do MySQL (`date +%T`h) ..."
QT_DIAS=60
DB_HOST='mysql.stonetech.info'
DB_NAME='comisadb'
DB_USER='gladbatista'
DB_PASS='mudeasenha'
DB_PARAM='--add-drop-table --add-locks --extended-insert --single-transaction --quick --routines'

# Definindo parametros do sistema
#echo "  -- Definindo parametros do sistema ..."
DATE=`date +%Y-%m-%d-%Hh`
MYSQLDUMP=mysqldump
BACKUP_DIR=/home/com1sa/backups/mysql/$DB_NAME
BACKUP_NAME=$DB_NAME-$DATE.sql
BACKUP_TAR=$DB_NAME-$DATE.tar.gz

#Gerando arquivo sql
echo "  -- Gerando Backup da base de dados $DB_NAME em $BACKUP_DIR/$BACKUP_NAME ..."
$MYSQLDUMP $DB_NAME -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_PARAM --result-file=$BACKUP_DIR/$BACKUP_NAME
#$MYSQLDUMP $DB_NAME -u $DB_USER -p$DB_PASS $DB_PARAM | mysql -f -h {IP} -u {USER} -p{SENHA}

# Compactando arquivo em tar
echo "  -- Compactando arquivo em tar ..."
tar -czf $BACKUP_DIR/$BACKUP_TAR -C $BACKUP_DIR $BACKUP_NAME

# Excluindo arquivos desnecessarios
#echo "  -- Excluindo arquivos desnecessarios ..."
#rm -rf $BACKUP_DIR/$BACKUP_NAME

#remove todos os arquivos com + de XX dias
find $BACKUP_DIR -mtime $QT_DIAS -exec rm {} +

echo "  -- Fim (`date +%T`h) ..."

