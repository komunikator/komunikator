<?php

const NETCONFIG_FILE='/etc/network/interfaces';

function writeNetworkConfig($params) {
	$tempconf = tempnam(sys_get_temp_dir(), 'kom');
	if ($f = fopen($tempconf, 'w')) {
		$type = intval($params['type'])? 'static': 'dhcp';
		fwrite($f, "auto lo\niface lo inet loopback\n\n");
		fprintf($f, "auto %s\niface %s inet %s\n", $params['dev'], $params['dev'], $type);
		fprintf($f, "\taddress %s\n", $params['ipaddress']);
		fprintf($f, "\tnetmask %s\n", $params['ipmask']);
		fprintf($f, "\tgateway %s\n", $params['ipgateway']);
		fclose($f);
		//return rename(NETCONFIG_FILE, NETCONFIG_FILE . '.bak') && rename($tempconf, NETCONFIG_FILE);
		//return true && 
		return ""==shell_exec("sudo mv ".NETCONFIG_FILE." ".NETCONFIG_FILE.".bak ; sudo mv ".$tempconf." ".NETCONFIG_FILE);
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
				if ($iface[3] != 'static' && $iface[3] != 'dhcp') continue;
				$ret[0]['type'] = $iface[3]=='static'?1:0;
				break;
			}
		}
		
		if ($ret[0]['type'] == 1) {
			while ($s = fgets($f)) {
				$s = trim($s);
				if (strpos($s, "#") !== 0 && $s != "\n") {
					$line = preg_split('/\s+/', $s);
					if (strpos($s, 'address') !== false) { $ret[0]['ipaddress'] = $line[1]; continue; }
					if (strpos($s, 'netmask') !== false) { $ret[0]['ipmask'] = $line[1]; continue; }
					if (strpos($s, 'gateway') !== false) { $ret[0]['ipgateway'] = $line[1]; continue; }
				}
			}
		}
		
		fclose($f);
	}
	
	return $ret;
}
if (isset($_REQUEST['type']))
{
		$ret = array(success => true, message => 'updated');
		if (!writeNetworkConfig($_REQUEST))
			$ret = array(success => false, message => 'error_updated');
}
	else
{
 $rec = readNetworkConfig();
 $ret=array("success"=>true, "data" => $rec[0]); 
}
 echo json_encode($ret);
?>