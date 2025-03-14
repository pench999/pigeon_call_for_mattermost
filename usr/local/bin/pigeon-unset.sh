#!/bin/bash

echo "pigeon設定を解除するよ"

### 社内Zabbix
echo "社内Zabbix設定"
ssh root@192.168.11.2 "/bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call.sh"
echo "終わり"

### ザビ家
echo "ザビ家設定"
ssh root@192.168.113.76 "/bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call.sh"
ssh root@192.168.113.76 "/bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call_ixmark.sh"
echo "終わり"

### Openstack unit1
echo "Openstack unit1設定"
ssh root@192.168.112.11 "/bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call.sh"
ssh root@192.168.112.11 "/bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call_kitsunai.sh"
ssh root@192.168.112.11 "/bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call_kudo.sh"
echo "終わり"

### Openstack unit2
echo "Openstack unit2設定"
ssh root@192.168.114.17 "/bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call_kudo.sh"
echo "終わり"

### グリーンイノベーション専用
echo "グリーンイノベーション専用設定"
ssh root@39.110.217.95 -p 10225 "/dev/null || /bin/cp -fp /usr/lib/zabbix/alertscripts/null.sh /usr/lib/zabbix/alertscripts/pigeon_call.sh"
echo "終わり"

