<?php

/*
*  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

*    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
*    Copyright (C) 2012-2013, ООО «Телефонные системы»

*    ЭТОТ ФАЙЛ является частью проекта «Komunikator»

*    Сайт проекта «Komunikator»: http://4yate.ru/
*    Служба технической поддержки проекта «Komunikator»: E-mail: support@4yate.ru

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

*    "Komunikator" project site: http://4yate.ru/
*    "Komunikator" technical support e-mail: support@4yate.ru

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