#!/bin/bash

echo "pigeonの状態を確認するよ"
echo "============================================================================="
# 社内Zabbix
echo "■社内Zabbix設定確認"
syanai=`ssh root@192.168.11.2 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l`
if [ $syanai -eq 2 ]; then
    echo "ウィークデイ設定ですな"
else
    echo "休日夜間設定ですな"
fi

echo "============================================================================="
### ザビ家
echo "■ザビ家設定確認"
zabike1=`ssh root@192.168.113.76 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l`
zabike2=`ssh root@192.168.113.76 "cat /usr/lib/zabbix/alertscripts/pigeon_call_ixmark.sh" |wc -l`
if [ $zabike1 -eq 2 ]; then
    if [ $zabike2 -eq 2 ]; then
        echo "ウィークデイ設定ですな"
    else
        echo "デフォルトはウィークデイ設定だけど、ixMark設定が休日夜間になっとるな"
    fi
elif [ $zabike2 -eq 2 ]; then
    echo "デフォルトは休日夜間設定だけど、ixMark設定がウィークデイになっとるな"
else
    echo "休日夜間設定ですな"
fi

echo "============================================================================="
### Openstack unit1
echo "■Openstack unit1設定確認"
unit1_1=`ssh root@192.168.112.11 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l`
unit1_2=`ssh root@192.168.112.11 "cat /usr/lib/zabbix/alertscripts/pigeon_call_kitsunai.sh" |wc -l`
unit1_3=`ssh root@192.168.112.11 "cat /usr/lib/zabbix/alertscripts/pigeon_call_kudo.sh" |wc -l`
if [ $unit1_1 -eq 2 ]; then
    echo "デフォルトはウィークデイ設定ですな"
else
    echo "デフォルトは休日夜間設定ですな"
fi

if [ $unit1_2 -eq 2 ]; then
    echo "橘内向けはウィークデイ設定ですな"
else
    echo "橘内向けは休日夜間設定ですな"
fi

if [ $unit1_3 -eq 2 ]; then
    echo "工藤向けはウィークデイ設定ですな"
else
    echo "工藤向けは休日夜間設定ですな"
fi

echo "============================================================================="
### Openstack unit2
echo "■Openstack unit2設定確認"
unit2_1=`ssh root@192.168.114.17 "cat /usr/lib/zabbix/alertscripts/pigeon_call_kudo.sh" |wc -l`
if [ $unit2_1 -eq 2 ]; then
    echo "ウィークデイ設定ですな"
else
    echo "休日夜間設定ですな"
fi

echo "============================================================================="
### グリーンイノベーション専用
echo "■グリーンイノベーション専用設定確認"
green=`ssh root@39.110.217.95 -p 10225 "cat /usr/lib/zabbix/alertscripts/pigeon_call.sh" |wc -l`
if [ $green -eq 2 ]; then
    echo "ウィークデイ設定ですな"
else
    echo "休日夜間設定ですな"
fi
