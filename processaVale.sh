#!/bin/bash
ini="$(date --date '5 hours' +'%Y-%m-%d %T')"
cd /home/com1sa/comisa.stonetech.info/
/usr/bin/php ./processaVale.php
fim="$(date --date '5 hours' +'%T')"
ano="$(date --date '5 hours' +'%Y')"
echo "Exec: $ini-$fim" >> ./valesErro/importacao_$ano.log
