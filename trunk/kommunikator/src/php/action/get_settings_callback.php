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

if (!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined")));
    exit;
}

$id_call_back = getparam('id');
$callthrough_time = getparam('callthrough_time');
$host = $_SERVER['SERVER_ADDR'];

$call_back_code = '
    </head>
<body>
    <style type="text/css">
   .test{border: 2px solid black; border-collapse: collapse;}
  </style>
  </head>
<body>
 <table class = "test">
  <caption><h3>Настройка виджета "Перезвоните мне"</h3></caption>
  <tr style = \" border: 2px solid black;\">
    <th >Условие</th>
    <th>Вкл/выкл</th>
    <th>Условие</th>
    <th>Текст сообщения</th>
    <th></th>
   </tr>
  <tr>
    <td>Вернувшийся посетитель</td>
    <td> <input type=\"checkbox\"/></td>
     <td></td>
    <td>Рады Вас видеть снова! Мы готовы перезвонить Вам за X секунд и ответить на все Ваши вопросы!</td>
    <td>как это будет?</td>
  </tr>
    <tr>
    <td>Выход с сайта</td>
    <td> <input type=\"checkbox\"/></td>
     <td></td>
    <td>Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!</td>
    <td>как это будет?</td>
  </tr>
      <tr>
    <td>Посещение нескольких страниц сайта</td>
    <td> <input type=\"checkbox\"/></td>
     <td> 
       <label for=\"name1\">Количество страниц:</label>
         <input type=\"text\" name = \"name1\"/></td>
    <td>Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!</td>
    <td>как это будет?</td>
  </tr>
        <tr>
    <td>Время, проведенное на сайте</td>
    <td> <input type=\"checkbox\"/></td>
     <td> 
       <label for=\"name2\">Время(сек):</label>
         <input type=\"text\" name = \"name2\"/></td>
    <td>Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!</td>
    <td>как это будет?</td>
  </tr>
  <tr>
    <td>Скролл вниз 100% страницы</td>
    <td> <input type=\"checkbox\"/></td>
     <td></td>
    <td>Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!</td>
    <td>как это будет?</td>
  </tr>
         <tr>
    <td>Посещение конкретной страницы сайта</td>
    <td> <input type=\"checkbox\"/></td>
     <td> 
<input type=\"text\" /><br/>Проверить URL</td>
           
    <td>Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!</td>
    <td>как это будет?</td>
  </tr>
</table>
</body>
</html>
';

$obj = array("success"=>true);
$obj["data"] = $call_back_code;

echo out($obj);