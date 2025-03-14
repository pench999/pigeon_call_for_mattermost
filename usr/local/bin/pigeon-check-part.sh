#!/bin/bash

if [ $1 = "syanai" ]
then
    ssh root@192.168.11.2 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l
elif [ $1 = "zabike1" ]
then
    ssh root@192.168.113.76 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l
elif [ $1 = "zabike2" ]
then
    ssh root@192.168.113.76 "cat /usr/lib/zabbix/alertscripts/pigeon_call_ixmark.sh" |wc -l
elif [ $1 = "unit1_1" ]
then
    ssh root@192.168.112.11 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l
elif [ $1 = "unit1_2" ]
then
    ssh root@192.168.112.11 "cat /usr/lib/zabbix/alertscripts/pigeon_call_kitsunai.sh" |wc -l
elif [ $1 = "unit1_3" ]
then
    ssh root@192.168.112.11 "cat /usr/lib/zabbix/alertscripts/pigeon_call_kudo.sh" |wc -l
elif [ $1 = "unit2" ]
then
    ssh root@192.168.114.17 "cat /usr/lib/zabbix/alertscripts/pigeon_call_kudo.sh" |wc -l
elif [ $1 = "green" ]
then
    ssh root@39.110.217.95 -p 10225 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l
else
    echo "NG"
fi
