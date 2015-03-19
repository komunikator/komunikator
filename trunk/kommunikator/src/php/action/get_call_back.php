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
?><?

if (!$_SESSION['user']) {
    echo ( out(array("success" => false, "message" => "User is undefined")) );
    exit;
}

$total = compact_array(query_to_array("SELECT count(*) FROM call_back"));

if (!is_array($total["data"]))
    echo out(array("success" => false, "message" => $total));

$query = <<<EOD
SELECT
    call_back_id as id,
    CASE
        WHEN g.group != NULL OR g.group != ''
            THEN g.group
        ELSE c.destination 
    END destination,  
    name_site,
    callthrough_time,
    c.description
FROM call_back c
left join groups g ON g.extension = c.destination
EOD;

$data = compact_array(query_to_array($query));

if (!is_array($data["data"]))
    echo out(array("success" => false, "message" => $data));

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

$obj = array("success" => true);
$obj["total"] = $total['data'][0][0];
$obj["data"] = $data['data'];

$pictures = array(); 
if ($dir = opendir("records/"))  {     
     while (false !== ($file = readdir($dir))) {         
         if ($file == "." || $file == ".." || (is_dir("records/".$file))) continue; 
          $pictures[] = $file; 
          $i++;  //echo($file);
     } 
     closedir($dir); 
} 
//print_r($pictures);
//$today = date("d_m_Y");
//$today->modify('-1 month');
//echo $today;
//$date = new DateTime();
$date = date('d_m_Y',strtotime ('-3 month')); //echo gettype($date) . $date; exit;
  $date = strtotime($date);
foreach ($pictures as $key => $value)
{
    
  $value = explode("_", $value); //echo($value[2] . "<br />");
  $date1 = $value[0]."-" . $value[1] ."-". $value[2]; 
 // $date1 =  strtotime($date1);
  //echo (gettype($date) . "!" . gettype($date1)."<br />"); exit;
  //$date1 =  strtotime($date1);
 // echo("!" . $date1 . "<br />");
  //echo("?" . $date . "<br />");
  $date1 = strtotime($date1); //echo $date1 . "<br />"; exit;

  if($date1 > $date){
    //  echo "дата больше" .$date1 ."!". $date. "<br />";
      //$date1->format('d_m_Y');
  //echo("aga" . $date1 . "<br />");
  
  } //else echo "дата меньше" .$date1 ."!". $date. "<br />";
//$timestamp= strtotime("$value[2]-$value[1]-$value[0]");echo $timestamp .  "<br />";

//$timestamp->format('d_m_Y');
//echo $timestamp .  "<br />";
//$data1 = new DateTime("'".$data1."'"); echo $date1 .  "<br />";
//$date1 = new DateTime("'" .$value[0]. "'-'" . $value[1]. "'-'" .$value[2] . "'");  echo $date1;
//echo( $value[0] ."day" . $value[1] ."month" .$value[2] . "year".  "<br />");
//if($date>$date1)
    //echo($date1 .  "<br />");
  
}

echo out($obj);
