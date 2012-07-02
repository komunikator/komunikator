<?php
$default_ip = "ssl://127.0.0.1";	//	ip address where yate runs
$default_port = "5039";	// port used to connect to

// class used to open a socket, send and receive information from it
class SocketConn
{
	var $socket;
	var $error = "";

	function __construct($ip = null, $port = null)
	{
		global $default_ip, $default_port;

		$protocol_list = stream_get_transports();

		if(!in_array("ssl", $protocol_list))
			die("Don't have ssl support.");

		if(!$ip)
			$ip = $default_ip;
		if(!$port)
			$port = $default_port;

		$errno = 0;
		$socket = fsockopen($ip,$port,$errno,$errstr,30);
		if(!$socket) {
			$this->error = "Web page can't connect to ip=$ip, port=$port [$errno]  \"".$errstr."\"";
			$this->socket = false;
		}else{
			$this->socket = $socket;
			$line1 = $this->read(); // read and ignore header
		}
	}

	function write($str)
	{
		fwrite($this->socket, $str."\r\n");
	}

	function read($marker_end = "\r\n")
	{
		$keep_trying = true;
		$line = "";
		while($keep_trying) {
			$line .= fgets($this->socket,8192);
			if($line === false)
				continue;
			if(substr($line, -strlen($marker_end)) == $marker_end)
				$keep_trying = false;
		}
		$line = str_replace("\r\n", "", $line);
		return $line;
	}

	function close()
	{
		fclose($this->socket);
	}

	/**
		Commands
		status
		uptime
		reload
		restart
		stop
		.... -> will be mapped into an engine.command
	 */
	function command($command, $marker_end = "\r\n")
	{
		$this->write($command);
		return $this->read($marker_end);
	}
}

?>