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
    echo (out(array("success" => false, "message" => "User is undefined")));
    exit;
}

$id_call_back = getparam('id');
$callthrough_time = getparam('callthrough_time');
$host = $_SERVER['SERVER_ADDR'];

$call_back_code = '
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style type="text/css">
            .table_style
            {border: 2px solid black; border-collapse: collapse; valign:middle;}
            .table_style th
            {border: 2px solid black;}
            .table_style tr,.table_style td
            {border: 1px solid #BCBCBC;}
            .check
            {width:30px;}
            .background_style
            {background-color: #98CDEB; cursor: pointer;}
            .button_style
            {background:#98CDEB; cursor: pointer; width: 100px; height: 30px;border: 2px solid #98CDEB; border-radius: 5px;}
            input[class^="show_img"] {
                display: none;
            }
            .def_settings
            {
                cursor: pointer; margin-right: 15px; margin-top: 13px; float:left;font-size:smaller; text-decoration: underline;
            }

            input[class^="show_img"] + label + * { /* коробка */
                visibility: hidden;
                position: fixed;
                z-index: 11;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                overflow: auto;
                background: rgba(0,0,0,.5);
                text-align: center;
                line-height: 100vh;
            }
            input[class^="show_img"]:checked+ label + * {
                visibility: visible;
            }
            input[class^="show_img"] + label + * > * { /* position: absolute; как-то странно себя ведёт в родителе с position: fixed;, поэтому была задействована ещё одна обёртка */
                position: relative;
            }
            input[class^="show_img"] + label + * > * > :first-child { /* кликабельный фон */
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;

            }
            input[class^="show_img"] + label + * > * > :last-child { /* блок с окном */
                position: relative;
                display: inline-block;
                max-width: calc(100% -(8px + 2px)*2);
                margin: 8px;
                border: 2px solid rgb(51, 103, 153);
                box-shadow: 0 0 0 8px rgba(255,255,255,.2);
                background: #fff;
                text-align: left;
                line-height: normal;
                vertical-align: middle;
            }
            input[class^="show_img"] + label + * > * > :last-child > :first-child { /* заголовок */
                position: relative;
                padding: .5em 4em .5em .5em;
                overflow: hidden;
                white-space: nowrap;
                word-wrap: normal;
                text-overflow: ellipsis;
                color: #fff;
                background: linear-gradient(#669acc 50%, #5589bb 50%);
            }
            input[class^="show_img"] + label + * > * > :last-child > :first-child label { /* крестик "закрыть" */
                position: absolute;
                top: calc(.5em - 2px);
                right: calc(.5em - 2px);
                font-weight: 600;
                cursor: pointer;
            }
            input[class^="show_img"] + label + * > * > :last-child > :last-child { /* поле после заголовка */
                padding: .5em;
            }
            input[class^="show_img"] + label + * > * > :last-child > :last-child label {
                position: relative;
                z-index: 1;
                cursor: pointer;
            }
            input[class^="show_img"] + label + * label > button {
                position: relative;
                z-index: -1;
            }


            /*----------------------как это будет?-------------------------------*/

            .some_background 
            {  
                float:right;
                width:110px;
                height:24px;
                margin: -18px 10px 0px 0px;
                padding: 0px 0px 0px 0px;
                background-size: 110px;
                background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9ItCh0LvQvtC5XzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHdpZHRoPSI1MzlweCIgaGVpZ2h0PSI3N3B4IiB2aWV3Qm94PSItMC4yMDggLTIuMDUzIDUzOSA3NyIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAtMC4yMDggLTIuMDUzIDUzOSA3NyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZmlsbD0iIzU0OTVDNCIgZD0iTTExNy4yOCw1MC41MDljMCwxNC4wNjctOC4xMDQsMjEuMjU1LTIyLjU1NiwyMS4yNTVjLTE0LjQ1LDAtMjIuNTU1LTcuMTg4LTIyLjU1NS0yMS4yNTUKCQljMC0xNC4wNjksOC4xMDUtMjEuMTgsMjIuNTU1LTIxLjE4QzEwOS4xNzQsMjkuMzMsMTE3LjI4LDM2LjQzOSwxMTcuMjgsNTAuNTA5eiBNMTA0LjM1Nyw1MC41MDkKCQljMC05LjAyMy0zLjEzNC0xMi42OTMtOS42MzQtMTIuNjkzYy02LjQ5OSwwLTkuNjMzLDMuNjcxLTkuNjMzLDEyLjY5M2MwLDkuMDIxLDMuMTM1LDEyLjc3LDkuNjMzLDEyLjc3CgkJQzEwMS4yMjMsNjMuMjc5LDEwNC4zNTcsNTkuNTMxLDEwNC4zNTcsNTAuNTA5eiIvPgoJPHBhdGggZmlsbD0iIzU0OTVDNCIgZD0iTTE1MS41MzUsNDcuMjk4YzAtNi4zNDctMi4zNzEtOC45NDYtNi40OTktOC45NDZjLTMuMjEyLDAtNS41ODMsMi4wNjUtNy45NTIsNS4zNTR2MjUKCQljMCwxLjMwMi0wLjk5NSwyLjI5NC0yLjI5NCwyLjI5NGgtOC4zMzRjLTEuMjk5LDAtMi4yOTMtMC45OTItMi4yOTMtMi4yOTRWMzIuMzg4YzAtMS4zMDEsMC45OTUtMi4yOTQsMi4yOTMtMi4yOTRoOC4zMzQKCQljMS4zLDAsMi4yOTQsMC45OTMsMi4yOTQsMi4yOTR2Mi4zNjljMi45ODItMi45MDQsNi4xOTMtNS40MjgsMTEuNTQ1LTUuNDI4YzYuMTE3LDAsMTAuMjQ2LDIuNDQ1LDEzLjE1Miw3LjE4NwoJCWM0LjI4MS00LjQzNCw4Ljk0NS03LjE4NywxNC4yMjEtNy4xODdjOS4zMjgsMCwxNS44MjcsNS4yNzMsMTUuODI3LDE3Ljk2OHYyMS40MDdjMCwxLjMwMi0wLjk5NCwyLjI5NC0yLjI5NCwyLjI5NGgtOC4zMzQKCQljLTEuMjk5LDAtMi4yOTMtMC45OTItMi4yOTMtMi4yOTRWNDcuMjk3YzAtNi4zNDctMi4zNy04Ljk0NS02LjQ5OS04Ljk0NWMtMi41MjIsMC01LjUwNiwyLjA2NC04LjE4Myw1LjczNAoJCWMwLjE1NCwxLjA3LDAuMjI5LDIuMTQxLDAuMjI5LDMuMjExdjIxLjQwN2MwLDEuMzAyLTAuOTk0LDIuMjk0LTIuMjkzLDIuMjk0aC04LjMzNWMtMS4yOTksMC0yLjI5My0wLjk5Mi0yLjI5My0yLjI5NFY0Ny4yOTgKCQlMMTUxLjUzNSw0Ny4yOTh6Ii8+Cgk8cGF0aCBmaWxsPSIjNTQ5NUM0IiBkPSJNMjEzLjkyNyw1My43OThjMCw2LjM0NiwyLjkwNSw4Ljk0NCw3LjExMSw4Ljk0NGMzLjc0NywwLDcuMjY0LTIuNjAxLDEwLjAxNi01LjM1M1YzMi4zODcKCQljMC0xLjMwMSwwLjk5NC0yLjI5NCwyLjI5My0yLjI5NGg4LjMzNGMxLjI5OSwwLDIuMjkzLDAuOTkzLDIuMjkzLDIuMjk0djM2LjMxOGMwLDEuMzAyLTAuOTk0LDIuMjk0LTIuMjkzLDIuMjk0aC04LjMzNAoJCWMtMS4yOTksMC0yLjI5My0wLjk5Mi0yLjI5My0yLjI5NHYtMi4zNjljLTMuNTE3LDIuOTgtNy4zNCw1LjQyOS0xMy4wNzQsNS40MjljLTkuOTQsMC0xNi45NzUtNS4yNzQtMTYuOTc1LTE3Ljk2N3YtMjEuNDEKCQljMC0xLjMwMSwwLjk5My0yLjI5NCwyLjI5My0yLjI5NGg4LjMzNGMxLjMwMSwwLDIuMjkzLDAuOTkzLDIuMjkzLDIuMjk0djIxLjQxSDIxMy45Mjd6Ii8+Cgk8cGF0aCBmaWxsPSIjNTQ5NUM0IiBkPSJNMjgzLjIwMiw0Ny4yOThjMC02LjM0Ny0yLjkwNS04Ljk0Ni03LjExLTguOTQ2Yy0zLjc0NiwwLTcuMjY0LDIuNjAzLTEwLjAxNyw1LjM1NHYyNQoJCWMwLDEuMzAyLTAuOTk1LDIuMjk0LTIuMjk0LDIuMjk0aC04LjMzNGMtMS4yOTksMC0yLjI5My0wLjk5Mi0yLjI5My0yLjI5NFYzMi4zODhjMC0xLjMwMSwwLjk5NC0yLjI5NCwyLjI5My0yLjI5NGg4LjMzNAoJCWMxLjMsMCwyLjI5NCwwLjk5MywyLjI5NCwyLjI5NHYyLjM2OWMzLjUxOS0yLjk4LDcuMzQtNS40MjgsMTMuMDc1LTUuNDI4YzkuOTM5LDAsMTYuOTczLDUuMjczLDE2Ljk3MywxNy45Njh2MjEuNDA3CgkJYzAsMS4zMDItMC45OTIsMi4yOTQtMi4yOTIsMi4yOTRoLTguMzM1Yy0xLjMsMC0yLjI5NC0wLjk5Mi0yLjI5NC0yLjI5NFY0Ny4yOThMMjgzLjIwMiw0Ny4yOTh6Ii8+Cgk8cGF0aCBmaWxsPSIjNTQ5NUM0IiBkPSJNMzA1LjI5OCwyMC45OTVjMC0yLjY3NywxLjYwNC00LjI4Miw0LjI4MS00LjI4Mmg0LjY2MmMyLjY4LDAsNC4yODMsMS42MDUsNC4yODMsNC4yODJ2MS4zCgkJYzAsMi42NzUtMS42MDQsNC4yODEtNC4yODMsNC4yODFoLTQuNjYyYy0yLjY3OCwwLTQuMjgxLTEuNjA2LTQuMjgxLTQuMjgxVjIwLjk5NXogTTMwNy43NDYsNzAuOTk5CgkJYy0xLjMwMSwwLTIuMjk1LTAuOTkyLTIuMjk1LTIuMjk0di0zNS40YzAtMS4yOTksMC45OTQtMi4yOTQsMi4yOTUtMi4yOTRoOC4zMzRjMS4zMDEsMCwyLjI5NSwwLjk5NSwyLjI5NSwyLjI5NHYzNS40CgkJYzAsMS4zMDItMC45OTQsMi4yOTQtMi4yOTUsMi4yOTRIMzA3Ljc0NnoiLz4KCTxwYXRoIGZpbGw9IiM1NDk1QzQiIGQ9Ik0zNDAuNjI1LDQ2LjMwMmM0LjQzNiwwLDcuMzQtMS44MzUsOC44NjktNS4wNDZsMy41OTQtNy42NDZjMC45OTItMi4xNDIsMy4xMzUtMy41MTksNS4zNTItMy41MTloNy4wMzMKCQljMS45ODgsMCwyLjIxOSwxLjUyOSwxLjMwMSwzLjUxOWwtNC44MTQsMTAuMTdjLTEuNjA1LDMuMzYzLTQuNjY0LDYuMDQtNy45NTMsNi43MjljMy4xMzUsMC45MTcsNS42NTgsMy4wNTksNy45NTMsNi43MjkKCQlsNi4zNDYsMTAuMjQ0YzEuMjI1LDEuOTg3LDAuNjg4LDMuNTE4LTEuMjk5LDMuNTE4aC03LjAzNWMtMi40NDcsMC00LjUxMi0xLjUyOC01LjczNC0zLjUxOGwtNC43MzgtNy43MjMKCQljLTIuMDY0LTMuMzY0LTUuNDMtNC45NzEtOC44NjktNC45NzF2MTMuOTE1YzAsMS4zMDItMC45OTQsMi4yOTQtMi4yOTMsMi4yOTRoLTguMzM0Yy0xLjMwMywwLTIuMjk3LTAuOTkyLTIuMjk3LTIuMjk0VjE5Ljc2OQoJCWMwLTEuMzAxLDAuOTk0LTIuMjkzLDIuMjk3LTIuMjkzaDguMzM0YzEuMjk5LDAsMi4yOTMsMC45OTMsMi4yOTMsMi4yOTN2MjYuNTMyTDM0MC42MjUsNDYuMzAyTDM0MC42MjUsNDYuMzAyeiIvPgoJPHBhdGggZmlsbD0iIzU0OTVDNCIgZD0iTTM4Ni40MjUsNDAuMzRjLTAuOTk0LDEuMjIzLTIuMzcxLDEuOTg2LTQuMjgzLDEuOTg2aC01LjU4Yy0xLjMwMSwwLTIuMjk1LTAuOTkzLTIuMjk1LTIuMjkzCgkJYzAtNy45NTEsNy45NTEtMTAuNzA0LDIwLjM0LTEwLjcwNGMxMS4wMSwwLDE5LjY0OCw0LjU4OCwxOS42NDgsMTYuMjg1djIzLjA5YzAsMS4zMDItMC45OTQsMi4yOTQtMi4yOTMsMi4yOTRoLTcuMTg5CgkJYy0xLjI5OSwwLTIuMjkzLTAuOTkyLTIuMjkzLTIuMjk0di0xLjE0NmMtMy40MzksMi40NDctNy42NDUsNC4yMDUtMTMuNTMzLDQuMjA1Yy0xMC4wMTYsMC0xNy42NjItNC41MTEtMTcuNjYyLTEzLjE0OQoJCWMwLTguNjQyLDcuNjQ2LTEyLjk5OSwxNy42NjItMTIuOTk5aDEyLjM4OWMwLTUuOTY1LTIuNTIzLTcuOC03LjY0Ni03LjhDMzkwLjU1NCwzNy44MTYsMzg3LjcyNCwzOC43MzMsMzg2LjQyNSw0MC4zNHoKCQkgTTQwMS4zMzUsNTkuOTE0di01LjgxMmgtMTEuNDcxYy0zLjU5NCwwLTUuNjU2LDEuOTg3LTUuNjU2LDQuNTEzYzAsMi42NzUsMi4wNjIsNC42NjQsNi4wMzksNC42NjQKCQlDMzk0LjgzNSw2My4yNzksMzk4LjY1OCw2MS41OTYsNDAxLjMzNSw1OS45MTR6Ii8+Cgk8cGF0aCBmaWxsPSIjNTQ5NUM0IiBkPSJNNDM5LjQ4OCw3MS43NjRjLTEwLjcwMywwLTE2LjA1Ny01LjM1NC0xNi4wNTctMTYuODJWMTkuNzcxYzAtMS4zMDEsMC45OTQtMi4yOTMsMi4yOTUtMi4yOTNoOC4zMzQKCQljMS4yOTksMCwyLjI5MywwLjk5MywyLjI5MywyLjI5M3YxMi40NjNoMTAuMzk4YzEuMzAxLDAsMi4yOTUsMC45OTQsMi4yOTUsMi4yOTV2NC40MzZjMCwxLjMwMS0wLjk5NCwyLjI5NC0yLjI5NSwyLjI5NGgtMTAuMzk4CgkJdjEzLjY4OGMwLDUuMTk3LDIuNjc4LDcuOTUsNi41NzYsNy45NWMxLjk4NiwwLDMuNDM5LTAuNDU5LDUuMTIxLTAuNDU5aDEuMzc3YzEuMzAxLDAsMi4yOTMsMC45OTMsMi4yOTMsMi4yOTN2My45NzcKCQljMCwxLjE0Ny0wLjk5MiwyLjA2NC0yLjI5MywyLjI5NEM0NDcuMTM2LDcxLjQ1OSw0NDIuOTI5LDcxLjc2NCw0MzkuNDg4LDcxLjc2NHoiLz4KCTxwYXRoIGZpbGw9IiM1NDk1QzQiIGQ9Ik00OTkuODkyLDUwLjUwOWMwLDE0LjA2Ny04LjEwNCwyMS4yNTUtMjIuNTU3LDIxLjI1NWMtMTQuNDQ5LDAtMjIuNTU1LTcuMTg4LTIyLjU1NS0yMS4yNTUKCQljMC0xNC4wNjksOC4xMDQtMjEuMTgsMjIuNTU1LTIxLjE4QzQ5MS43ODksMjkuMzMsNDk5Ljg5MiwzNi40MzksNDk5Ljg5Miw1MC41MDl6IE00ODYuOTcsNTAuNTA5CgkJYzAtOS4wMjMtMy4xMzUtMTIuNjkzLTkuNjM1LTEyLjY5M2MtNi40OTgsMC05LjYzMywzLjY3MS05LjYzMywxMi42OTNjMCw5LjAyMSwzLjEzNSwxMi43Nyw5LjYzMywxMi43NwoJCUM0ODMuODM1LDYzLjI3OSw0ODYuOTcsNTkuNTMxLDQ4Ni45Nyw1MC41MDl6Ii8+Cgk8cGF0aCBmaWxsPSIjNTQ5NUM0IiBkPSJNNTM4LjU4MywzNy4yODFjMCwxLjMwMS0wLjk5NCwyLjI5My0yLjI5NSwyLjI5M2gtMS4yMjNjLTEuNzYsMC0zLjc0OC0wLjIyOS01LjczNC0wLjIyOQoJCWMtMy44MjIsMC02Ljg4MywyLjIxOC05LjYzNyw1LjUwNXYyMy44NTVjMCwxLjMwMi0wLjk5MiwyLjI5NC0yLjI5MywyLjI5NGgtOC4zMzRjLTEuMjk5LDAtMi4yOTMtMC45OTItMi4yOTMtMi4yOTRWMzIuMzg3CgkJYzAtMS4zMDEsMC45OTQtMi4yOTQsMi4yOTMtMi4yOTRoOC4zMzRjMS4zMDEsMCwyLjI5MywwLjk5MywyLjI5MywyLjI5NHYyLjM2OWM0LjI4My0zLjEzNSw4LjE4NC01LjQyOCwxNi41OTQtNS40MjgKCQljMS4zMDEsMCwyLjI5NSwwLjk5NCwyLjI5NSwyLjI5M1YzNy4yODFMNTM4LjU4MywzNy4yODF6Ii8+CjwvZz4KPGc+Cgk8cGF0aCBmaWxsPSIjRjQ4MDMzIiBkPSJNNjQuNDczLDExLjkyOWMwLDIuNTk3LTIuMTAzLDQuNjk2LTQuNjk3LDQuNjk2aC05LjI4OWMtMi41OTMsMC00LjY5Ni0yLjEwMi00LjY5Ni00LjY5NlYyLjY0MQoJCWMwLTIuNTkzLDIuMTA0LTQuNjk0LDQuNjk2LTQuNjk0aDkuMjg5YzIuNTk0LDAsNC42OTcsMi4xMDIsNC42OTcsNC42OTRWMTEuOTI5eiIvPgo8L2c+CjxnPgoJPHBhdGggZmlsbD0iI0Y0ODAzMyIgZD0iTTMxOC41ODIsMjIuMDU0YzAsMS44NjctMS41MTQsMy4zNzktMy4zNzksMy4zNzloLTYuNjg0Yy0xLjg2MywwLTMuMzc3LTEuNTEyLTMuMzc3LTMuMzc5di02LjY4CgkJYzAtMS44NjYsMS41MTQtMy4zNzcsMy4zNzctMy4zNzdoNi42ODRjMS44NjUsMCwzLjM3OSwxLjUxMSwzLjM3OSwzLjM3N1YyMi4wNTR6Ii8+CjwvZz4KPGc+Cgk8cGF0aCBmaWxsPSIjNTQ5NUM0IiBkPSJNMTkuNjg4LDY0LjE5MmMwLDIuNjU1LTIuMzYsNC44MDgtNS4yNzIsNC44MDhINS4yNzNDMi4zNjEsNjguOTk5LDAsNjYuODQ5LDAsNjQuMTkyVjIuNzUxCgkJYzAtMi42NTIsMi4zNi00LjgwNSw1LjI3NC00LjgwNWg5LjE0MmMyLjkxMywwLDUuMjcyLDIuMTUxLDUuMjcyLDQuODA1VjY0LjE5MnoiLz4KPC9nPgo8Zz4KCTxnPgoJCTxwYXRoIGZpbGw9IiNGNDgwMzMiIGQ9Ik00NS45MTMsNDQuODExYzAsMi41OTUtMi4xMDIsNC42OTUtNC42OTYsNC42OTVoLTkuMjg4Yy0yLjU5MywwLTQuNjk2LTIuMTAzLTQuNjk2LTQuNjk1di05LjI4OQoJCQljMC0yLjU5NCwyLjEwNC00LjY5NCw0LjY5Ni00LjY5NGg5LjI4OGMyLjU5NCwwLDQuNjk2LDIuMTAzLDQuNjk2LDQuNjk0VjQ0LjgxMXoiLz4KCTwvZz4KCTxnPgoJCTxwYXRoIGZpbGw9IiNGNDgwMzMiIGQ9Ik02NC40NzMsNjQuMzAyYzAsMi41OTYtMi4xMDMsNC42OTYtNC42OTcsNC42OTZoLTkuMjg5Yy0yLjU5MywwLTQuNjk2LTIuMTAzLTQuNjk2LTQuNjk2di05LjI4NwoJCQljMC0yLjU5MywyLjEwNC00LjY5NCw0LjY5Ni00LjY5NGg5LjI4OWMyLjU5NCwwLDQuNjk3LDIuMTA0LDQuNjk3LDQuNjk0VjY0LjMwMnoiLz4KCTwvZz4KCTxnPgoJCTxwYXRoIGZpbGw9IiNGNDgwMzMiIGQ9Ik00MC44OTUsNDkuNTA3YzAsMCwxLjg2NSwwLjY2OSwyLjk4NywxLjg1MmMxLjEyMywxLjE4NCwxLjkxLDMuNjU2LDEuOTEsMy42NTZsNS4xOC00LjY5NAoJCQljMCwwLTIuMDg0LTAuODEyLTMuMzI4LTIuMDIzYy0xLjI0NC0xLjIxMy0xLjczMS0zLjM5LTEuNzMxLTMuMzlMNDAuODk1LDQ5LjUwN3oiLz4KCTwvZz4KPC9nPgo8L3N2Zz4K");
                background-repeat: no-repeat;
                cursor: pointer; 
            }

            .button_calling_1712953875                        /* стиль окна заказа  */
            {
                width: 110px;
                text-align: center;
                float: right;
                margin-top: 9px;
                margin-right: 80px;
                background: #484848; 
                color: #FFFFFF; 
                font-size: 17pt; 
                -moz-border-radius:  4px; 
                -webkit-border-radius:  4px; 
                border-radius:4px; 
                cursor: pointer;
            }
            .text_message_2563964469 
            {   
                margin-top: 10px;
                float: left;
                margin-left: 70px;
                font-family: Univers_Medium;
                font-style:bold;
                font-size: 15pt; 
                color: #000000; 
                width: 60%;
                border-radius: 4px;
            }
            input.text_message_2563964469:-ms-input-placeholder 
            {
                font-family: Univers_Medium;
                font-style:bold;
                font-size: 15pt; 
                color: #BFBFBF; 
            }
            .text_zagolovka_order_4043482234 
            { 
                display: inline-block;
                width:100%;
                position: inherit;
                text-align: center;
                font-family: Univers_Medium;
                font-style:bold;
                font-size: 20pt;
                color: #606060;
                margin-top:0px;
                margin-bottom:0px;
                height: 80px;
            }   
            .text_call_free_4537679586
            {
                display: inline; 
                float: right;
                margin-right: 60px; 
                margin-top: 5px;
                font-size: 10pt;
                font-weight:bold;
                color: #606060;
            }   
            .mod_header_7894788111
            {
                height:50px;
            }
            .mod_body_1427621553
            {
                height:190px;
            }
            .mod_footer_2196269136
            {
                height:50px;
            }

            .text_silka_komunicator_9989142638
            { 
                color: #606060;
                text-align:right;
                font-weight:bold;
                margin-right: 125px;	
            }

            .box-modal {
                position: relative;
                width: 660px;
                height: 290px;
                padding: 4px;
                background: #fff;
                color: #3c3c3c;
                font: 14px/18px Arial, "Helvetica CY", "Nimbus Sans L", sans-serif;
                box-shadow: 0 0 0 2px rgba(153, 153, 153, .3);
                border-radius: 0px;
            }
            .box-modal_close {    
                float:right;
                margin-top:  2px;
                margin-right: 2px;
                border: 2px solid #c2c2c2;
                position: relative;
                padding: 1px 6px;
                background-color: #FFFFFF;
                border-radius: 20px;
                font-size: 18px;
                font-weight: bold;
                color: #606060;
                text-decoration: none;
                cursor:pointer;
            }

            .box-modal_close:hover { color: #666; }

            /*----------------------как это будет?(КОНЕЦ)-----------------------------*/
        </style>

    </head>
    <body>
    <form id="testt"  name="testtt" method="get">
        <div style="width: 90%; ">
       
            <table class = "table_style">
                <caption><h3>Настройка виджета "Перезвоните мне"</h3></caption>
                <col width="17%" valign="middle">
                <col width="5%" valign="middle">
                <col width="15%" valign="middle">
                <col width="50%" valign="middle">
                <col width="13%" valign="middle">
                <tr >
                    <th >Условие</th>
                    <th>Вкл/выкл</th>
                    <th>Условие</th>
                    <th>Текст сообщения</th>
                    <th></th>
                </tr>
                <tr>
                    <td>Вернувшийся посетитель</td>
                    <td> <input type="checkbox" id="check_return_visitor" name="check_return_visitor"/></td>
                    <td></td>
                    <td style="padding-left:2%; padding-top: 3px;"><textarea id="return_visitor" name="return_visitor" rows="3" style ="width:95%; resize: none;"></textarea></td>
                    <td>
                        <input type="checkbox" id="box1" class="show_img1"/>
                        <label for="box1" onclick="show_box1();">
                            <span class="background_style">как это будет?</span></label>
                        <div><div>
                                <label for="box1"></label>
                                <div>
                                    <div>Как это будет?<label for="box1">✖</label></div>
                                    <div style ="padding: 20px;">
                                        <div class="box-modal">
                                            <div class="mod_header_7894788111">
                                                <div class="box-modal_close">X</div>
                                            </div>
                                            <div class="mod_body_1427621553" >
                                                <div  class="text_zagolovka_order_4043482234"  id="box1_text"></div>
                                                <div style="display:inline-block;width:100%">
                                                    <input type="text" size="35" maxlength="25" placeholder="Введите ваш номер" class="text_message_2563964469">
                                                    <input type="button" value="Звоните!"  class="button_calling_1712953875"  style="background-color: rgb(72, 72, 72);">
                                                </div>
                                                <div>
                                                    <div class="text_call_free_4537679586">Звонок бесплатный</div>
                                                    <div style="display: none"></div>
                                                </div>
                                            </div>
                                            <div class="mod_footer_2196269136"  style="display: block;">
                                                <div class="text_silka_komunicator_9989142638">Работает на технологии</div>
                                                <a href="http://komunikator.ru/" target="_blank">
                                                    <div class="some_background"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></div>
                    </td>
                </tr>
                <tr>
                    <td>Выход с сайта</td>
                    <td> <input type="checkbox" id="check_output_site" name="check_output_site"/></td>
                    <td></td>
                    <td style="padding-left:2%; padding-top: 3px;"><textarea id="output_site" name="output_site" rows="3" style ="width:95%; resize: none;"></textarea></td>
                    <td>
                        <input type="checkbox" id="box2" class="show_img2"/>
                        <label for="box2" onclick="show_box2();">
                            <span class="background_style">как это будет?</span></label>
                        <div><div>
                                <label for="box2"></label>
                                <div>
                                    <div>Как это будет?<label for="box2">✖</label></div>
                                    <div style ="padding: 20px;">
                                        <div class="box-modal">
                                            <div class="mod_header_7894788111">
                                                <div class="box-modal_close">X</div>
                                            </div>
                                            <div class="mod_body_1427621553" >
                                                <div  class="text_zagolovka_order_4043482234"  id="box2_text"></div>
                                                <div style="display:inline-block;width:100%">
                                                    <input type="text" size="35" maxlength="25" placeholder="Введите ваш номер" class="text_message_2563964469">
                                                    <input type="button" value="Звоните!"  class="button_calling_1712953875"  style="background-color: rgb(72, 72, 72);">
                                                </div>
                                                <div>
                                                    <div class="text_call_free_4537679586">Звонок бесплатный</div>
                                                    <div style="display: none"></div>
                                                </div>
                                            </div>
                                            <div class="mod_footer_2196269136"  style="display: block;">
                                                <div class="text_silka_komunicator_9989142638">Работает на технологии</div>
                                                <a href="http://komunikator.ru/" target="_blank">
                                                    <div class="some_background"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></div>
                    </td>
                </tr>
                <tr>
                    <td>Посещение нескольких страниц сайта</td>
                    <td> <input type="checkbox" id="check_seen_fewpage" name="check_seen_fewpage"/></td>
                    <td> 
                        <label for="page_number">Количество страниц:</label>
                        <input type="text" name = "page_number" id="number_page" maxlength="3" class = "check"/></td>
                    <td style="padding-left:2%; padding-top: 3px;"><textarea id="seen_fewpage" name="seen_fewpage" rows="3" style ="width:95%; resize: none;"></textarea></td>
                    <td>
                        <input type="checkbox" id="box3" class="show_img3"/>
                        <label for="box3" onclick="show_box3();">
                            <span class="background_style">как это будет?</span></label>
                        <div><div>
                                <label for="box3"></label>
                                <div>
                                    <div>Как это будет?<label for="box3">✖</label></div>
                                    <div style ="padding: 20px;">
                                        <div class="box-modal">
                                            <div class="mod_header_7894788111">
                                                <div class="box-modal_close">X</div>
                                            </div>
                                            <div class="mod_body_1427621553" >
                                                <div  class="text_zagolovka_order_4043482234"  id="box3_text"></div>
                                                <div style="display:inline-block;width:100%">
                                                    <input type="text" size="35" maxlength="25" placeholder="Введите ваш номер" class="text_message_2563964469">
                                                    <input type="button" value="Звоните!"  class="button_calling_1712953875"  style="background-color: rgb(72, 72, 72);">
                                                </div>
                                                <div>
                                                    <div class="text_call_free_4537679586">Звонок бесплатный</div>
                                                    <div style="display: none"></div>
                                                </div>
                                            </div>
                                            <div class="mod_footer_2196269136"  style="display: block;">
                                                <div class="text_silka_komunicator_9989142638">Работает на технологии</div>
                                                <a href="http://komunikator.ru/" target="_blank">
                                                    <div class="some_background"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></div>
                    </td>
                </tr>
                <tr>
                    <td>Время, проведенное на сайте</td>
                    <td> <input type="checkbox" id="check_ua_seconds" name="check_ua_seconds"/></td>
                    <td> 
                        <label for="seconds">Время(сек):</label>
                        <input type="text" name = "ua_seconds" class = "check" id="ua_seconds" maxlength="3"/></td>
                    <td style="padding-left:2%; padding-top: 3px;"><textarea id="timespend" name="timespend" rows="3" style ="width:95%; resize: none;"></textarea></td>
                    <td>
                        <input type="checkbox" id="box4" class="show_img4"/>
                        <label for="box4" onclick="show_box4();">
                            <span class="background_style">как это будет?</span></label>
                        <div><div>
                                <label for="box4"></label>
                                <div>
                                    <div>Как это будет?<label for="box4">✖</label></div>
                                    <div style ="padding: 20px;">
                                        <div class="box-modal">
                                            <div class="mod_header_7894788111">
                                                <div class="box-modal_close">X</div>
                                            </div>
                                            <div class="mod_body_1427621553" >
                                                <div  class="text_zagolovka_order_4043482234"  id="box4_text"></div>
                                                <div style="display:inline-block;width:100%">
                                                    <input type="text"  size="35" maxlength="25" placeholder="Введите ваш номер" class="text_message_2563964469">
                                                    <input type="button" value="Звоните!"  class="button_calling_1712953875"  style="background-color: rgb(72, 72, 72);">
                                                </div>
                                                <div>
                                                    <div class="text_call_free_4537679586">Звонок бесплатный</div>
                                                    <div style="display: none"></div>
                                                </div>
                                            </div>
                                            <div class="mod_footer_2196269136"  style="display: block;">
                                                <div class="text_silka_komunicator_9989142638">Работает на технологии</div>
                                                <a href="http://komunikator.ru/" target="_blank">
                                                    <div class="some_background"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></div>
                    </td>
                </tr>
                <tr>
                    <td>Скролл вниз 100% страницы</td>
                    <td> <input type="checkbox" id="check_scroll_down" name="check_scroll_down"/></td>
                    <td></td>
                    <td style="padding-left:2%; padding-top: 3px;"><textarea id="scroll_down" name="scroll_down" rows="3" style ="width:95%; resize: none;"></textarea></td>
                    <td>
                        <input type="checkbox" id="box5" class="show_img5"/>
                        <label for="box5" onclick="show_box5();">
                            <span class="background_style">как это будет?</span></label>
                        <div><div>
                                <label for="box5"></label>
                                <div>
                                    <div>Как это будет?<label for="box5">✖</label></div>
                                    <div style ="padding: 20px;">
                                        <div class="box-modal">
                                            <div class="mod_header_7894788111">
                                                <div class="box-modal_close">X</div>
                                            </div>
                                            <div class="mod_body_1427621553" >
                                                <div  class="text_zagolovka_order_4043482234"  id="box5_text"></div>
                                                <div style="display:inline-block;width:100%">
                                                    <input type="text" size="35" maxlength="25" placeholder="Введите ваш номер" class="text_message_2563964469">
                                                    <input type="button" value="Звоните!"  class="button_calling_1712953875"  style="background-color: rgb(72, 72, 72);">
                                                </div>
                                                <div>
                                                    <div class="text_call_free_4537679586">Звонок бесплатный</div>
                                                    <div style="display: none"></div>
                                                </div>
                                            </div>
                                            <div class="mod_footer_2196269136"  style="display: block;">
                                                <div class="text_silka_komunicator_9989142638">Работает на технологии</div>
                                                <a href="http://komunikator.ru/" target="_blank">
                                                    <div class="some_background"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></div>
                    </td>
                </tr>
                <tr>
                    <td>Посещение конкретной страницы сайта</td>
                    <td> <input type="checkbox" id="check_URLforOpen" name="check_URLforOpen"/></td>
                    <td style="padding-left:2%; padding-top: 3px;"> 
                        <textarea id="URLforOpen" name="URLforOpen" rows="2" style="resize: none; width:90%;" placeholder="В формате: http://www.vesti.ru/"></textarea>
                        <span class="background_style" id="URLforOpen_link">Проверить URL</span></td>
                    <td style="padding-left:2%; padding-top: 3px;"><textarea id="specificpage" name="specificpage"  rows="3" style ="width:95%; resize: none;"></textarea></td>
                    <td>
                        <input type="checkbox" id="box6" class="show_img6"/>
                        <label for="box6" onclick="show_box6();">
                            <span class="background_style" >как это будет?</span></label>
                        <div><div>
                                <label for="box6"></label>
                                <div>
                                    <div>Как это будет?<label for="box6">✖</label></div>
                                    <div style ="padding: 20px;">
                                        <div class="box-modal">
                                            <div class="mod_header_7894788111">
                                                <div class="box-modal_close">X</div>
                                            </div>
                                            <div class="mod_body_1427621553" >
                                                <div  class="text_zagolovka_order_4043482234"  id="box6_text"></div>
                                                <div style="display:inline-block;width:100%">
                                                    <input type="text" size="35" maxlength="25" placeholder="Введите ваш номер" class="text_message_2563964469">
                                                    <input type="button" value="Звоните!"  class="button_calling_1712953875"  style="background-color: rgb(72, 72, 72);">
                                                </div>
                                                <div>
                                                    <div class="text_call_free_4537679586">Звонок бесплатный</div>
                                                    <div style="display: none"></div>
                                                </div>
                                            </div>
                                            <div class="mod_footer_2196269136"  style="display: block;">
                                                <div class="text_silka_komunicator_9989142638">Работает на технологии</div>
                                                <a href="http://komunikator.ru/" target="_blank">
                                                    <div class="some_background"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></div>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 15px; text-align: center; float:right; ">
                <div class="def_settings" id="default_settings">Вернуться к рекомендованным настройкам</div>
                <div style="float:left; margin-right: 15px;"><input class="button_style" type="button" value="Отменить"></div>
                <div style="float:left; margin-right: 15px;"><input class="button_style" type="button" value="Сохранить"></div>
                <div style="float:both;"></div>
                 <input type="submit" value="Сохранить">
            </div>
        </div>


        <script  type="text/javascript">
                            window.onload = function() {
                                document.getElementById("URLforOpen_link").onclick = function() {
                                    var url = document.getElementById("URLforOpen").value;
                                    url = url.replace(/\s/g, "");
                                    (url === null || url === "" || !url) ? alert("Укажите URL") : window.open(url);
                                };
                                
                                document.getElementById("ua_seconds").onkeypress = function(e) {
                                    e = e || event;
                                    if (e.ctrlKey || e.altKey || e.metaKey)
                                        return;
                                    var chr = getChar(e);
                                    if (chr == null)
                                        return;
                                    if (chr < "0" || chr > "9") {
                                        return false;
                                    }
                                };

                                document.getElementById("number_page").onkeypress = function(e) {
                                    e = e || event;
                                    if (e.ctrlKey || e.altKey || e.metaKey)
                                        return;
                                    var chr = getChar(e);
                                    if (chr == null)
                                        return;
                                    if (chr < "0" || chr > "9") {
                                        return false;
                                    }
                                };
                                
                                document.getElementById("default_settings").onclick = function() {
                                    document.getElementById("check_return_visitor").checked = true;
                                    document.getElementById("check_output_site").checked = false;
                                    document.getElementById("check_seen_fewpage").checked = false;
                                    document.getElementById("check_ua_seconds").checked = true;
                                    document.getElementById("check_scroll_down").checked = true;
                                    document.getElementById("check_URLforOpen").checked = false;
                                    
                                    document.getElementById("ua_seconds").value=40;
                                    document.getElementById("URLforOpen").value="www.mysite/books/50shadeofgray/";
                                    
                                    document.getElementById("return_visitor").value  = "Рады Вас видеть снова! Мы готовы перезвонить Вам за X секунд и ответить на все Ваши вопросы!";
                                    document.getElementById("output_site").value  = "Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!";
                                    document.getElementById("seen_fewpage").value  = "Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!";
                                    document.getElementById("timespend").value  = "Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!";
                                    document.getElementById("scroll_down").value  = "Не нашли, что искали? Возникли вопросы? Давайте мы Вам перезвоним за X секунд и поможем!";
                                    document.getElementById("specificpage").value = "Интересуетесь книгами? Давайте мы Вам перезвоним за X секунд и расскажем подробнее!";

                                };
                            };



                            function getChar(event) {
                                if (event.which == null) {
                                    if (event.keyCode < 32)
                                        return null;
                                    return String.fromCharCode(event.keyCode) // IE
                                }

                                if (event.which != 0 && event.charCode != 0) {
                                    if (event.which < 32)
                                        return null;
                                    return String.fromCharCode(event.which); // остальные
                                }
                                return null; // специальная клавиша
                            }
                            function show_box1()
                            {
                                var inp = document.getElementsByClassName("show_img1");
                                var text = document.getElementById("return_visitor").value;
                                document.getElementById("box1_text").innerHTML = text;
                                for (var i = 0; i < inp.length; i++) {
                                    inp[i].onclick = function() {

                                        document.documentElement.style.overflow = (this.checked ? "hidden" : "auto");
                                        document.documentElement.style.marginRight = (this.checked ? "17px" : "");

                                    }
                                }
                            }
                            function show_box2()
                            {
                                var inp = document.getElementsByClassName("show_img2");
                                var text = document.getElementById("output_site").value;
                                document.getElementById("box2_text").innerHTML = text;
                                for (var i = 0; i < inp.length; i++) {
                                    inp[i].onclick = function() {

                                        document.documentElement.style.overflow = (this.checked ? "hidden" : "auto");
                                        document.documentElement.style.marginRight = (this.checked ? "17px" : "");

                                    }
                                }
                            }
                            function show_box3()
                            {
                                var inp = document.getElementsByClassName("show_img3");
                                var text = document.getElementById("seen_fewpage").value;
                                document.getElementById("box3_text").innerHTML = text;
                                for (var i = 0; i < inp.length; i++) {
                                    inp[i].onclick = function() {

                                        document.documentElement.style.overflow = (this.checked ? "hidden" : "auto");
                                        document.documentElement.style.marginRight = (this.checked ? "17px" : "");

                                    }
                                }
                            }
                            function show_box4()
                            {
                                var inp = document.getElementsByClassName("show_img4");
                                var text = document.getElementById("timespend").value;
                                document.getElementById("box4_text").innerHTML = text;
                                for (var i = 0; i < inp.length; i++) {
                                    inp[i].onclick = function() {

                                        document.documentElement.style.overflow = (this.checked ? "hidden" : "auto");
                                        document.documentElement.style.marginRight = (this.checked ? "17px" : "");

                                    }
                                }
                            }

                            function show_box5()
                            {
                                var inp = document.getElementsByClassName("show_img5");
                                var text = document.getElementById("scroll_down").value;
                                document.getElementById("box5_text").innerHTML = text;
                                for (var i = 0; i < inp.length; i++) {
                                    inp[i].onclick = function() {

                                        document.documentElement.style.overflow = (this.checked ? "hidden" : "auto");
                                        document.documentElement.style.marginRight = (this.checked ? "17px" : "");

                                    }
                                }
                            }
                            function show_box6()
                            {
                                var inp = document.getElementsByClassName("show_img6");
                                var text = document.getElementById("specificpage").value;
                                document.getElementById("box6_text").innerHTML = text;
                                for (var i = 0; i < inp.length; i++) {
                                    inp[i].onclick = function() {

                                        document.documentElement.style.overflow = (this.checked ? "hidden" : "auto");
                                        document.documentElement.style.marginRight = (this.checked ? "17px" : "");

                                    }
                                }
                            }
        </script>
        </form>
    </body>

</html>
';

$obj = array("success" => true);
$obj["data"] = $call_back_code;

echo out($obj);