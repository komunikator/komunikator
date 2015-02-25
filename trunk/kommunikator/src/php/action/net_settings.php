<?php

/*
 *  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

 *    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
 *    Copyright (C) 2012-2013, ООО «Телефонные системы»

 *    ЭТОТ ФАЙЛ является частью проекта «Komunikator»

 *    Сайт проекта «Komunikator»: http://komunikator.ru/
 *    Служба технической поддержки проекта «Komunikator»: E-mail: support@komunikator.ru

 *    В проекте «Komunikator» используются:
 *      исходные коды проекта «YATE», http://yate.null.ro/pmwiki/
 *      исходные коды проекта «FREESENTRAL», http://www.freesentral.com/
 *      библиотеки проекта «Sencha Ext JS», http://www.sencha.com/products/extjs

 *    Web-приложение «Komunikator» является свободным и открытым программным обеспечением. Тем самым
 *  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
 *  и иные права) согласно условиям GNU General Public License, опубликованной
 *  Free Software Foundation, версии 3.

 *    В случае отсутствия файла «License» (идущего вместе с исходными кодами программного обеспечения)
 *  описывающего условия GNU General Public License версии 3, можно посетить официальный сайт
 *  http://www.gnu.org/licenses/ , где опубликованы условия GNU General Public License
 *  различных версий (в том числе и версии 3).

 *  | ENG | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

 *    "Komunikator" is a web interface for IP-PBX "YATE" configuration and management
 *    Copyright (C) 2012-2013, "Telephonnyie sistemy" Ltd.

 *    THIS FILE is an integral part of the project "Komunikator"

 *    "Komunikator" project site: http://komunikator.ru/
 *    "Komunikator" technical support e-mail: support@komunikator.ru

 *    The project "Komunikator" are used:
 *      the source code of "YATE" project, http://yate.null.ro/pmwiki/
 *      the source code of "FREESENTRAL" project, http://www.freesentral.com/
 *      "Sencha Ext JS" project libraries, http://www.sencha.com/products/extjs

 *    "Komunikator" web application is a free/libre and open-source software. Therefore it grants user rights
 *  for distribution and (or) modification (including other rights) of this programming solution according
 *  to GNU General Public License terms and conditions published by Free Software Foundation in version 3.

 *    In case the file "License" that describes GNU General Public License terms and conditions,
 *  version 3, is missing (initially goes with software source code), you can visit the official site
 *  http://www.gnu.org/licenses/ and find terms specified in appropriate GNU General Public License
 *  version (version 3 as well).

 *  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 */
?><?php

        const NETCONFIG_FILE = '/etc/network/interfaces';

function writeNetworkConfig($params) {
    $tempconf = tempnam(sys_get_temp_dir(), 'kom');
    if ($f = fopen($tempconf, 'w')) {
        $type = intval($params['type']) ? 'static' : 'dhcp';
        fwrite($f, "auto lo\niface lo inet loopback\n\n");
        fprintf($f, "auto %s\niface %s inet %s\n", $params['dev'], $params['dev'], $type);
        fprintf($f, "\taddress %s\n", $params['ipaddress']);
        fprintf($f, "\tnetmask %s\n", $params['ipmask']);
        fprintf($f, "\tgateway %s\n", $params['ipgateway']);
        fclose($f);
        //return rename(NETCONFIG_FILE, NETCONFIG_FILE . '.bak') && rename($tempconf, NETCONFIG_FILE);
        //return true && 
        return "" == shell_exec("sudo mv " . NETCONFIG_FILE . " " . NETCONFIG_FILE . ".bak ; sudo mv " . $tempconf . " " . NETCONFIG_FILE);
        //$out = shell_exec('sudo ./network.sh '.$tempconf.' '.NETCONFIG_GILE);
        return true;
    }
    return false;
}

function readNetworkConfig() {
    $ret = array();
    if ($f = fopen(NETCONFIG_FILE, 'r')) {
        while ($s = fgets($f)) {
            $s = trim($s);
            if (strpos($s, '#') !== 0 && strpos($s, 'iface') !== false) {
                $iface = preg_split('/\s+/', $s);
                $ret[0]['dev'] = $iface[1];
                if ($iface[3] != 'static' && $iface[3] != 'dhcp')
                    continue;
                $ret[0]['type'] = $iface[3] == 'static' ? 1 : 0;
                break;
            }
        }

        if ($ret[0]['type'] == 1) {
            while ($s = fgets($f)) {
                $s = trim($s);
                if (strpos($s, "#") !== 0 && $s != "\n") {
                    $line = preg_split('/\s+/', $s);
                    if (strpos($s, 'address') !== false) {
                        $ret[0]['ipaddress'] = $line[1];
                        continue;
                    }
                    if (strpos($s, 'netmask') !== false) {
                        $ret[0]['ipmask'] = $line[1];
                        continue;
                    }
                    if (strpos($s, 'gateway') !== false) {
                        $ret[0]['ipgateway'] = $line[1];
                        continue;
                    }
                }
            }
        }
        fclose($f);
    }
    return $ret;
}

if (isset($_REQUEST['type'])) {
    $ret = array(success => true, message => 'updated');
    if (!writeNetworkConfig($_REQUEST))
        $ret = array(success => false, message => 'error_updated');
}
else {
    $rec = readNetworkConfig();
    $ret = array("success" => true, "data" => $rec[0]);
}
echo json_encode($ret);
?>