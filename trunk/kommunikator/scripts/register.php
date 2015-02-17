#!/usr/bin/php -q
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
require_once("libyate.php");
require_once("lib_queries.php");

$gateway_ev = array();
$storage_gateway = array();
$call_from1C = array();

$s_fallbacks = array();
$s_params_assistant_outgoing = array();
$s_statusaccounts = array();
$s_moh = array();
$stoperror = array("busy", "noanswer", "looping", "Request Terminated", "Routing loop detected");
$reg_init = false;
$next_time = 0;
$time_step = 90;

$moh_time_step = 60 * 5; // 5 minutes
$moh_next_time = 0;

$pickup_key = "000"; //key marking that a certain call is a pickup
$adb_keys = "**";  //keys marking that the address book should be used to find the real called number

$posib = array("no_groups", "no_pbx");

$max_routes = 3;

$international_calls = true;

$limits_international = array(
    "1minute" => 5,
    "10minutes" => 10,
    "1hour" => 20
);
$timers = array(
    "1minute" => 60,
    "10minutes" => 600,
    "1hour" => 3600
);

for ($i = 0; $i < count($posib); $i++) {
    if (isset(${$posib[$i]}))
        if (${$posib[$i]} !== true)
            ${$posib[$i]} = false;

    if (!isset(${$posib[$i]}))
        ${$posib[$i]} = false;
}

//$query_on = false;
$debug_on = true; //false;
$query_on = true;
$debug_on = true;

function debug($mess) {
    Yate::Debug("register.php : " . $mess);
}

function format_array($arr) {
    $str = str_replace("\n", "", print_r($arr, true));
    $str = str_replace("\t", "", $str);
    while (strlen($str) != strlen(str_replace("  ", " ", $str)))
        $str = str_replace("  ", " ", $str);
    return $str;
}

function set_caller_id() {
    global $caller_id, $timer_caller_id, $caller_name, $system_prefix;

    $query = "SELECT MAX(CASE WHEN param='callerid' THEN value ELSE NULL END) as callerid, MAX(CASE WHEN param='callername' THEN value ELSE NULL END) as callername, MAX(CASE WHEN param='prefix' THEN value ELSE NULL END) as system_prefix FROM settings";
    $res = query_to_array($query);
    if (count($res)) {
        $caller_id = $res[0]["callerid"];
        $caller_name = $res[0]["callername"];
        $system_prefix = $res[0]["system_prefix"];
    }
    debug("Reset CalledID to '$caller_id', Callername to '$caller_name', System prefix to '$system_prefix'");
    $timer_caller_id = 0;
}

function fix_settings_international($settings) {
    if (count($settings) == 0)
        $intlive = $int = true;
    elseif ($settings[0]["param"] == "international_calls")
        $intlive = true;
    else
        $int = true;
    if ($int)
        query_nores("INSERT INTO settings (param,value) VALUES('international_calls','yes')");
    if ($intlive)
        query_nores("INSERT INTO settings (param,value) VALUES('international_calls_live','yes')");
}

function set_prefixes() {
    global $national_prefixes, $international_prefixes, $limits_international, $international_calls;

    $national_prefixes = array();
    $international_prefixes = array();

    // yes, no the possible values, we want no at the end to override yes if values are different
    $query = "SELECT * FROM settings WHERE param LIKE 'international%' order by value desc";
    $res = query_to_array($query);
    for ($i = 0; $i < count($res); $i++)
        $international_calls = ($res[$i]["value"] == "yes") ? true : false;
    if (count($res) < 2)
        fix_settings_international($res);

    $query = "SELECT * FROM prefixes ORDER BY international";
    $res = query_to_array($query);
    for ($i = 0; $i < count($res); $i++) {
        if ($res[$i]["international"] == "t")
            $international_prefixes[] = $res[$i]["prefix"];
        else
            $national_prefixes[] = $res[$i]["prefix"];
    }

    $limits = "SELECT * FROM limits_international";
    $res = query_to_array($limits);
    for ($i = 0; $i < count($res); $i++)
        $limits_international[$res[$i]["name"]] = $res[$i]["value"];
}

/**
 * Set the array of music_on_hold by playlist. This array is updated periodically.
 * @param $time, Time when function was called. It's called with this param after engine.timer, If empty i want to update the list, probably because i didn't have the moh for a certain playlist
 */
function set_moh($time = NULL) {
    global $moh_time_step, $moh_next_time, $s_moh, $last_time, $uploaded_prompts;

    if (!$time)
        $time = $last_time;
    $moh_next_time = $time + $moh_time_step;
    $query = "SELECT playlists.playlist_id, playlists.in_use, music_on_hold.file as music_on_hold FROM playlists, music_on_hold, playlist_items WHERE playlists.playlist_id=playlist_items.playlist_id AND playlist_items.music_on_hold_id=music_on_hold.music_on_hold_id ORDER BY playlists.playlist_id";
    $playlists = query_to_array($query);
    $l_moh = array();
    for ($i = 0; $i < count($playlists); $i++) {
        $playlist_id = $playlists[$i]["playlist_id"];
        if (!isset($l_moh[$playlist_id]))
            $l_moh[$playlist_id] = '';
        $moh = "$uploaded_prompts/moh/" . $playlists[$i]["music_on_hold"];
        $l_moh[$playlist_id] .= ($l_moh[$playlist_id] != '') ? ' ' . $moh : $moh;
    }
    $s_moh = $l_moh;
}

function delete_old_calls() {
    $sql = "DELETE FROM call_logs WHERE FROM_UNIXTIME(time) < DATE_ADD(CURDATE(), INTERVAL - 1 day)";
    $res = query_nores($sql);
}

/**
 * Build the location to send a call depending on the protocol
 * @param $params array of type "field_name"=>"field_value"
 * @param $called Number where the call will be sent to
 * @return String representing the resource the call will be made to
 */
function build_location($params, $called, &$copy_ev) {
    set_additional_params($params, $copy_ev);

    if ($params["username"] && $params["username"] != '') {
        // this is a gateway with registration
        $copy_ev["line"] = $params["gateway"];
        return "line/$called";
    } else {
        switch ($params["protocol"]) {
            case "sip":
                return "sip/sip:$called@" . $params["server"] . ":" . $params["port"];
            case "h323":
                return "h323/$called@" . $params["server"] . ":" . $params["port"];
            case "pstn":
                $params["link"] = $params["gateway"];
                $copy_ev["link"] = $params["link"];
                return "sig/" . $called;
            case "PRI":
            case "BRI":
                $query = "SELECT sig_trunk FROM sig_trunks WHERE sig_trunk_id=" . $params["sig_trunk_id"];
                $res = query_to_array($query);
                if (!count($res))
                    debug("Can't find sig_trunk for gateway " . $params["gateway"]);
                $params["link"] = (count($res)) ? $res[0]["sig_trunk"] : $params["gateway"];
                $copy_ev["link"] = $params["link"];
                return "sig/" . $called;
            case "iax":
                if (!$params["iaxuser"])
                    $params["iaxuser"] = "";
                $location = "iax/" . $params["iaxuser"] . "@" . $params["server"] . ":" . $params["port"] . "/" . $called;
                if ($params["iaxcontext"])
                    $location .= "@" . $params["iaxcontext"];
                return $location;
        }
    }
    return NULL;
}

function set_additional_params($gateway, &$copy_ev) {
    $to_set = array("interval", "authname", "domain", "outbound", "localaddress", "rtp_localip", "oip_transport");

    foreach ($gateway as $name => $val)
        if (in_array($name, $to_set) && $val) {
            $copy_ev[$name] = $val;
        }
}

/**
 * Get the location where to send a call
 * @param $called Number the call was placed to
 * @return String representing the resource where to place the call
 * Note!! this function is used only when diverting calls. does not check for any kind of forward, and mimics the fallback when diverting using fork to send the call to each destination
 */
function get_location($called) {
    global $voicemail, $system_prefix;

    if ($called == "vm") {
        if ($voicemail)
            return $voicemail;
        else
            return NULL;
    }

    //  divert to a did
    $query = "SELECT destination FROM dids WHERE number='$called' OR '$system_prefix' || number='$called'";
    $res = query_to_array($query);
    if (count($res)) {
        if (is_numeric($res[0]["destination"]))
        // just translate the called number
            $called = $res[0]["destination"];
        else
        // route to a script
            return $res[0]["destination"];
    }

    // divert to an extension without thinking of it's divert functions
    $query = "SELECT location FROM extensions WHERE extension='$called' OR '$system_prefix' || extension='$called'";
    $res = query_to_array($query);
    if (count($res))
        return $res[0]["location"];

    // if we got here there divert is to a group or dial plans must be used
    // it's better to use the lateroute module
    return "lateroute/$called";

    /* 	$query = "SELECT * FROM dial_plans INNER JOIN gateways ON dial_plans.gateway_id=gateways.gateway_id WHERE prefix IS NULL OR '$called' LIKE prefix||'%' AND (gateways.username IS NULL OR gateways.status='online') ORDER BY length(coalesce(prefix,'')) DESC, priority";

      $res = query_to_array($query);
      if(!count($res)) {
      return NULL;
      }

      $callto = 'fork ';

      for($i=0; $i<count($res); $i++) {
      $newcalled = rewrite_digits($res[$i],$called);
      $location = build_location($res[$i],$newcalled);
      if (!$location)
      continue;
      if ($res[$i]["formats"])
      $location .= ';formats='.$res[$i]["formats"];
      if ($callto == 'fork ')
      $callto .= $location;
      else
      $callto .= ' | '.$location;
      }

      if($callto != 'fork ')
      return $callto;

      return NULL; */
}

/**
 * Get the modified number
 * @param $route Array of params representing the modificatios resulted usually from an sql query
 * @param $nr Number before rewriting
 * @return Number resulted after the modifications were applied. If resulted number is empty then original number is returned
 * Note!! The order in with the operations are performed is : cut, replace, add. So replacing will be performed on the resulted number after cutting. One must keep this in mind when using multiple transformations.
 */
function rewrite_digits($route, $nr) {
    $result = $nr;
    if ($route["nr_of_digits_to_cut"] && $route["position_to_start_cutting"]) {
        $result = substr($nr, 0, $route["position_to_start_cutting"] - 1) . substr($nr, $route["position_to_start_cutting"] - 1 + $route["nr_of_digits_to_cut"], strlen($nr));
    }
    if ($route["position_to_start_replacing"] && $route["digits_to_replace_with"]) {
        if (!$route["nr_of_digits_to_replace"])
            return $route["digits_to_replace_with"];
        $result = substr($result, 0, $route["position_to_start_replacing"] - 1) . $route["digits_to_replace_with"] . substr($result, $route["position_to_start_replacing"] + $route["nr_of_digits_to_replace"] - 1, strlen($result));
    }
    if ($route["position_to_start_adding"] && $route["digits_to_add"]) {
        $result = substr($result, 0, $route["position_to_start_adding"] - 1) . $route["digits_to_add"] . substr($result, $route["position_to_start_adding"] - 1, strlen($result));
    }
    if (!$result) {
        debug("Wrong: resulted number is empty when nr='$nr' and route=" . format_array($route));
        return $nr;
    }
    return $result;
}

/**
 * Route a call to a group. Using this function implies that the queues module is configured.
 * @param $called Number where the call was placed to
 * @return Bool true if call was routed to a group, false otherwise
 */
function routeToGroup($called) {
    global $uploaded_prompts, $ev, $s_moh, $system_prefix;

    //	debug("entered routeToGroup('$called')");
    $path = "$uploaded_prompts/moh/";

    $cnt = 2 + strlen($system_prefix);
    if (strlen($called) == 2 || (strlen($called) == $cnt && substr($called, 0, strlen($system_prefix)) == $system_prefix)) {
        debug("trying routeToGroup('$called')");
        // call to a group
        $query = "SELECT group_id, (CASE WHEN playlist_id IS NULL THEN (SELECT playlist_id FROM playlists WHERE in_use=1) else playlist_id END) as playlist_id FROM groups WHERE extension='$called' OR '$system_prefix' || extension='$called'";
        $res = query_to_array($query);
        if (!count($res))
            return false;
        set_retval("queue/" . $res[0]["group_id"]);
        if (!isset($s_moh[$res[0]["playlist_id"]]))
            set_moh();
        $ev->params["mohlist"] = $s_moh[$res[0]["playlist_id"]];
        if ($ev->GetValue("copyparams"))
            $ev->params["copyparams"] .= ",caller,callername,billid,orig_called";
        else
            $ev->params["copyparams"] = "caller,callername,billid,orig_called";
        return true;
    }
    return false;
}

/**
 * Detect whether a call is a pickup or not. Route the call to the appropriate resource if so
 * @param $called Number where the call was placed to
 * @param $caller Who innitiated the call
 * @return Bool true if call is a pickup. False otherwise
 */
function makePickUp($called, $caller) {
    global $pickup_key, $system_prefix;

    debug("entered makePickUp(called='$called',caller='$caller')");

    $keyforgroup = strlen($pickup_key) + 2;
    if (strlen($called) == $keyforgroup && substr($called, 0, strlen($pickup_key)) == $pickup_key) {
        // someone is trying to pickup a call that was made to a group, (make sure caller is in that group)
        $extension = substr($called, strlen($pickup_key), strlen($called));
        $query = "SELECT group_id FROM groups WHERE (extension='$extension' OR '$system_prefix' || extension='$extension') AND group_id IN (SELECT group_id FROM group_members, extensions WHERE group_members.extension_id=extensions.extension_id AND extensions.extension='$caller')";
        $res = query_to_array($query);
        if (!count($res))
            set_retval("tone/congestion");
        else
            set_retval("pickup/" . $res[0]["group_id"]);
        return true;
    }

    if (substr($called, 0, strlen($pickup_key)) == $pickup_key) {
        // try to improvize a pick up -> pick up the current call of a extension that is in the same group as the caller
        $extension = substr($called, strlen($pickup_key), strlen($called));
        $query = "SELECT chan FROM call_logs, extensions, group_members WHERE direction='outgoing' AND ended = 0 AND (extensions.extension=call_logs.called OR '$system_prefix' || extensions.extension=call_logs.called) AND (extensions.extension='$extension' OR '$system_prefix' || extensions.extension='$extension') AND extensions.extension_id=group_members.extension_id AND group_members.group_id IN (SELECT group_id FROM group_members NATURAL JOIN extensions WHERE extensions.extension='$caller')";
        $res = query_to_array($query);
        if (count($res))
            set_retval("pickup/" . $res[0]["chan"]);  //make the pickup
        else
            set_retval("tone/congestion");   //no call for this extension
        return true;
    }
    return false;
}

/**
 * Route a call to an extension. Set the params for all the types of divert.
 * @param $called Number the call was placed to
 * @return Bool true if number was routed, false otherwise
 */
function routeToExtension($called) {
    global $no_pbx, $ev, $voicemail, $system_prefix, $query_on, $debug_on;

    //	debug("entered routeToExtension('$called')");
    if (strlen($called) < 3)
        return false;

    $pref_ext = $system_prefix . $called;
    debug("trying routeToExtension(called='$called' or called='$pref_ext')");

    $query = "SELECT location,extension_id FROM extensions WHERE extension='$called' OR '$system_prefix' || extension='$called'";
    $res = query_to_array($query);
    if (!count($res))
        return false;

    $destination = $res[0]["location"];
    $extension_id = $res[0]["extension_id"];
    if (!$no_pbx) {
        // select voicemail location
        $query = "SELECT value FROM settings WHERE param='vm'";
        $res = query_to_array($query);
        if (!$res || !count($res)) {
            debug("Voicemail is not set!!!");
            $voicemail = NULL;
        }
        else
            $voicemail = $res[0]["value"];

        // select pbx settings for the called number
        $query = "SELECT MAX(CASE WHEN param='forward' THEN value END) as \"div\",MAX(CASE WHEN param='forward_busy' THEN value END) as div_busy,MAX(CASE WHEN param='forward_noanswer' THEN value END) as div_noanswer, MAX(CASE WHEN param='noanswer_timeout' THEN value END) as noans_timeout FROM pbx_settings WHERE extension_id='$extension_id'";
        $res = query_to_array($query);
        $div = $res[0]["div"];
        $div_busy = $res[0]["div_busy"];
        $div_noanswer = $res[0]["div_noanswer"];
        $noans_timeout = $res[0]["noans_timeout"];

        if ($div == $called || !$div) {
            // set the additional divert params
            if ($div_busy && $div_busy != '')
                $ev->params["divert_busy"] = get_location($div_busy);
            if ($div_noanswer != '' && $div_noanswer) {
                $ev->params["divert_noanswer"] = get_location($div_noanswer);
                $ev->params["maxcall"] = $noans_timeout * 1000;
            }
        } else {
            // all calls should be diverted to $div
            $destination = get_location($div);
            if ($destination && $div != "vm")
                $ev->params["called"] = $div;
        }
    }

    // if no destination found, try sending call to voicemail(it might be set or not)
    if (!$destination)
        $destination = $voicemail;
    $ev->params["query_on"] = ($query_on) ? "yes" : "no";
    $ev->params["debug_on"] = ($debug_on) ? "yes" : "no";
    set_retval($destination, "offline");
    return true;
}

/**
 * Verify whether $called is a defined did
 * @param $called Number that the call was sent to.
 * @return Bool value, true if destination is a script, false
 */
function routeToDid(&$called) {
    global $system_prefix;
    global $query_on;
    global $debug_on;
    global $ev;

    debug("entered routeToDid('$called')");
    // default route is a did
    $query = "SELECT destination FROM dids WHERE number='$called' OR '$system_prefix' || number='$called'";
    $res = query_to_array($query);
    if (count($res)) {
        if (is_numeric($res[0]["destination"]))
        // just translate the called number
            $called = $res[0]["destination"];
        else {
            $ev->params["query_on"] = ($query_on) ? "yes" : "no";
            $ev->params["debug_on"] = ($debug_on) ? "yes" : "no";
            // route to a script
            set_retval($res[0]["destination"]);
            return true;
        }
    }
    return false;
}

/**
 * Generate all the possible names that could match a certain number
 * @param $number The number that was received
 * @return String containing all the names separated by "', '"
 */
function get_possible_options($number) {
    $posib = array();

    $alph = array(
        2 => array("a", "b", "c"),
        3 => array("d", "e", "f"),
        4 => array("g", "h", "i"),
        5 => array("j", "k", "l"),
        6 => array("m", "n", "o"),
        7 => array("p", "q", "r", "s"),
        8 => array("t", "u", "v"),
        9 => array("w", "x", "y", "z")
    );

    for ($i = 0; $i < strlen($number); $i++) {
        $digit = $number[$i];
        $letters = $alph[$digit];
        if (!count($posib)) {
            $posib = $letters;
            continue;
        }
        $s_posib = $posib;
        for ($k = 0; $k < count($letters); $k++) {
            if ($k == 0)
                for ($j = 0; $j < count($posib); $j++)
                    $posib[$j] .= $letters[$k];
            else
                for ($j = 0; $j < count($s_posib); $j++)
                    array_push($posib, $s_posib[$j] . $letters[$k]);
        }
    }
    $options = implode("', '", $posib);
    return "'$options'";
}

/**
 * See if this call uses the address book. If so then find the real number the call should be sent to and modify $called param
 * @param $called Number the call was placed to
 */
function routeToAddressBook(&$called, $username) {
    global $adb_keys;

    //	debug("entered routeToAddressBook(called='$called', username='$username')");

    if (substr($called, 0, strlen($adb_keys)) != $adb_keys)
        return;

    debug("trying routeToAddressBook(called='$called', username='$username')");

    $number = substr($called, strlen($adb_keys), strlen($called));
    $possible_names = get_possible_options($number);
    $query = "SELECT short_names.number, 1 as option_nr FROM short_names, extensions WHERE extensions.extension='$username' AND extensions.extension_id=short_names.extension_id AND short_name IN ($possible_names) UNION SELECT number, 2 as option_nr FROM short_names WHERE extension_id IS NULL AND short_name IN ($possible_names) ORDER BY option_nr";
    $res = query_to_array($query);
    if (count($res)) {
        if (count($res) > 1)
            Yate::Output("!!!!!!! Problem with finding real number from address book. Multiple mathces. Picking first one");
        $called = $res[0]["number"];
    }
    else
        debug("Called number '$called' seems to be using the address book. No match found. Left routing to continue.");
    return;
}

/**
 * Handle the call.route message.
 */
function get_SQL_concat($data) {
    global $db_type_sql;
    if (!is_array($data))
        return $data;
    if (count($data) == 0)
        return '';
    if (count($data) == 1)
        return $data[0];
    if ($db_type_sql == 'mysql') {
        $str = 'CONCAT(';
        $sep = '';
        foreach ($data as $el) {
            $str .= $sep . $el;
            $sep = ',';
        };
        return $str . ')';
    } else {
        $str = '';
        $sep = '';
        foreach ($data as $el) {
            $str .= $sep . $el;
            $sep = ' || ';
        };
        return $str;
    }
}

function return_route($called, $caller, $no_forward = false) {
    global $ev, $pickup_key, $max_routes, $s_fallbacks, $no_groups, $no_pbx, $caller_id, $caller_name, $system_prefix;

    $rtp_f = $ev->GetValue("rtp_forward");
    // keep the initial called number
    $initial_called_number = $called;

    $username = $ev->GetValue("username");
    $address = $ev->GetValue("address");
    $address = explode(":", $address);
    $address = $address[0];
    $reason = $ev->GetValue("reason");
    $isdn_address = $ev->GetValue("address");
    $isdn_address = explode("/", $isdn_address);
    $sig_trunk = $isdn_address[0];

    $already_auth = $ev->GetValue("already-auth");
    $trusted_auth = $ev->GetValue("trusted-auth");
    $call_type = $ev->GetValue("call_type");
    debug("entered return_route(called='$called',caller='$caller',username='$username',address='$address',already-auth='$already_auth',reason='$reason', trusted='$trusted_auth', call_type='$call_type')");

    $params_to_copy = "maxcall,call_type,already-auth,trusted-auth";
    // make sure that if we forward any calls and for calls from pbxassist are accepted
    $ev->params["copyparams"] = $params_to_copy;
    $ev->params["pbxparams"] = "$params_to_copy,copyparams";

    if ($already_auth != "yes" && $reason != "divert_busy" && $reason != "divert_noanswer") {
        // check to see if user is allowed to make this call
        $query = "SELECT value FROM settings WHERE param='annonymous_calls'";
        $res = query_to_array($query);
        $anonim = $res[0]["value"];
        if (strtolower($anonim) != "yes" || $username)
        // if annonymous calls are not allowed the call has to be from a known extension or from a known ip
            $query = "SELECT extension_id,1 as trusted,'from inside' as call_type FROM extensions WHERE extension='$username' UNION SELECT incoming_gateway_id, trusted, 'from outside' as call_type FROM incoming_gateways,gateways WHERE gateways.gateway_id=incoming_gateways.gateway_id AND incoming_gateways.ip='$address' UNION SELECT gateway_id, trusted, 'from outside' as call_type FROM gateways LEFT OUTER JOIN sig_trunks ON gateways.sig_trunk_id=sig_trunks.sig_trunk_id WHERE server='$address' OR server LIKE '$address:%' OR sig_trunk='$sig_trunk'";
        else {
            // if $called is the same as one of our extensions try and autentify it -> in order to have pbx rights
            if (!$username) {
                $query = "SELECT * FROM extensions WHERE extension='$caller'";
                $res = query_to_array($query);
                if (count($res)) {
                    debug("could not auth call but '$caller' seems to be in extensions");
                    set_retval(NULL, "noauth");
                    return;
                }
            }

            // if annonymous calls are allowed call to be for a inner group or extension  or from a known ip
            $query = "SELECT incoming_gateway_id, trusted, 'from outside' as call_type FROM incoming_gateways, gateways WHERE incoming_gateways.gateway_id=gateways.gateway_id AND incoming_gateways.ip='$address' UNION SELECT gateway_id, trusted, 'from outside' as call_type FROM gateways LEFT OUTER JOIN sig_trunks ON gateways.sig_trunk_id=sig_trunks.sig_trunk_id WHERE server='$address' OR server LIKE '$address:%' OR sig_trunk='$sig_trunk' UNION SELECT extension_id,1 as trusted,'to inside' as call_type FROM extensions WHERE extension='$called' OR '$system_prefix' || extension='$called' OR extension='$username' UNION SELECT group_id, 1 as trusted,'to inside' as call_type  FROM groups WHERE extension='$called' OR '$system_prefix' || extension='$called' UNION SELECT did_id, 1 as trusted,'to inside' as call_type  FROM dids WHERE number='$called' OR '$system_prefix' || number='$called'";
        }
        $res = query_to_array($query);
        if (!count($res)) {
            debug("could not auth call");
            set_retval(NULL, "noauth");
            return;
        }
        $trusted_auth = ($res[0]["trusted"] == 1) ? "yes" : "no";
        $call_type = $res[0]["call_type"]; //($username) ? "from inside" : "from outside";  // from inside/outside of freesentral
    }

    debug("classified call as being '$call_type'");
    // mark call as already autentified
    $ev->params["already-auth"] = "yes";
    $ev->params["trusted-auth"] = $trusted_auth;
    $ev->params["call_type"] = $call_type;

    if ($call_type != "from inside")
        $ev->params["pbxguest"] = true;

    routeToAddressBook($called, $username);

    if (routeToDid($called))
        return;

    if (!$no_groups) {
        if (routeToGroup($called))
            return;
        if (makePickUp($called, $caller))
            return;
    }

    if (routeToExtension($called))
        return;

    if (!checkInternationalCalls($called)) {
        debug("Forbidding call to '$called' because because international calls are off.");
        set_retval(null, "forbidden");
        return;
    }

    if ($call_type == "from outside" && $initial_called_number == $called && $trusted_auth != "yes") {
        // if this is a call from outside our system and would be routed outside(from first step) and the number that was initially called was not modified with passing thought any of the above steps  => don't send it
        debug("forbidding call to '$initial_called_number' because call is 'from outside'");
        // set_retval(null, "forbidden");
        //return;
    }


    $query = "SELECT * FROM dial_plans INNER JOIN gateways ON dial_plans.gateway_id=gateways.gateway_id WHERE (prefix IS NULL OR '$called' LIKE " . get_SQL_concat(array("prefix", "'%'")) . ") AND (gateways.username IS NULL OR gateways.status='online') ORDER BY length(coalesce(prefix,'')) DESC, priority LIMIT $max_routes";
    $res = query_to_array($query);

    if (!count($res)) {
        debug("Could not find a matching dial plan=> rejecting with error: noroute");
        set_retval(NULL, "noroute");
        return;
    }
    $id = ($ev->GetValue("true_party")) ? $ev->GetValue("true_party") : $ev->GetValue("id");
    $start = count($res) - 1;
    $j = 0;
    $fallback = array();
    for ($i = $start; $i >= 0; $i--) {
        $fallback[$j] = $ev->params;
        $custom_caller_id = ($res[$i]["callerid"]) ? $res[$i]["callerid"] : $caller_id;
        $custom_caller_name = ($res[$i]["callername"]) ? $res[$i]["callername"] : $caller_name;
        $custom_domain = $res[$i]["domain"];
        if ($res[$i]["send_extension"] == 0) {
            $fallback[$j]["caller"] = $custom_caller_id;
            if ($custom_domain)
                $fallback[$j]["domain"] = $custom_domain;
            $fallback[$j]["callername"] = $custom_caller_name;
        }elseif ($system_prefix && $call_type == "from inside")
            $fallback[$j]["caller"] = $system_prefix . $fallback[$j]["caller"];
        $fallback[$j]["called"] = rewrite_digits($res[$i], $called);
        $fallback[$j]["formats"] = ($res[$i]["formats"]) ? $res[$i]["formats"] : $ev->GetValue("formats");
        $fallback[$j]["rtp_forward"] = ($rtp_f == "possible" && $res[$i]["rtp_forward"] == 1) ? "yes" : "no";
        $location = build_location($res[$i], rewrite_digits($res[$i], $called), $fallback[$j]);
        if (!$location)
            continue;
        $fallback[$j]["location"] = $location;
        $j++;
    }
    if (!count($fallback)) {
        set_retval(NULL, "noroute");
        return;
    }
    $best_option = count($fallback) - 1;
    set_retval($fallback[$best_option]["location"]);
    debug("Sending $id to " . $fallback[$best_option]["location"]);
    unset($fallback[$best_option]["location"]);
    $ev->params = $fallback[$best_option];
    unset($fallback[$best_option]);
    if (count($fallback))
        $s_fallbacks[$id] = $fallback;
    //	debug("There are ".count($s_fallbacks)." in fallback : ".format_array($s_fallbacks));
    debug("There are " . count($s_fallbacks) . " in fallback");
    return;
}

function checkInternationalCalls($called) {
    global $international_calls;

    if (is_international($called)) {
        if (!$international_calls)
            return false;
        update_counters($called);
        if (!$international_calls)
            return false;
    }
    return true;
}

function is_international($called) {
    global $national_prefixes, $international_prefixes;

    if (prefix_match($called, $international_prefixes) && !prefix_match($called, $national_prefixes)) {
        debug("$called is international or expensive");
        return true;
    }
    return false;
}

function prefix_match($called, $prefixes) {
    for ($i = 0; $i < count($prefixes); $i++) {
        if (substr($called, 0, strlen($prefixes[$i])) == $prefixes[$i]) {
            return true;
        }
    }
    return false;
}

function update_counters($called) {
    global $counters_international, $timers, $limits_international, $international_calls;

    /* 	if (!is_international($called))
      return; */

    if (!isset($counters_international)) {
        foreach ($timers as $name => $interval)
            $counters_international[$name] = 1;
        return;
    }
    foreach ($timers as $name => $interval) {
        $counters_international[$name]++;
        if ($limits_international[$name] < $counters_international[$name]) {
            $international_calls = false;
            query_nores("UPDATE settings SET value='no',description='Disabled at ' || now() || '., limit $name' WHERE param='international_calls_live'");
            debug("Disabled international calls. Counter per $name was reached, number of calls " . $counters_international[$name]);
        }
    }
}

function reset_counters($time) {
    global $time_counters, $timers, $counters_international;

    if (!isset($counters_international)) {
        foreach ($timers as $name => $interval)
            $counters_international[$name] = 0;
    }
    if (!isset($time_counters)) {
        $time_counters = array();
        // initialize timers
        foreach ($timers as $name => $interval)
            $time_counters[$name] = $time;
        return;
    }

    foreach ($timers as $name => $interval) {
        if ($time_counters[$name] + $interval <= $time) {
            //Yate::Debug("Reset counter for $name");
            $time_counters[$name] = $time;
            $counters_international[$name] = 0;
        }
    }
}

/**
 * Set the params needed for routing a call
 * @param $callto Resource were to place the call
 * @param $error If callto param is not set one can set an error. Ex: offline
 * @return Bool true if the event was handled, false otherwise
 */
function set_retval($callto, $error = NULL) {
    global $ev, $s_params_assistant_outgoing;

    if ($callto) {
        $id = $ev->GetValue("id");
        $s_params_assistant_outgoing[$id] = array();
        $s_params_assistant_outgoing[$id]["pbxparams"] = $ev->GetValue("pbxparams");
        $s_params_assistant_outgoing[$id]["copyparams"] = $ev->GetValue("copyparams");
        if ($ev->GetValue("line")) {
            // call is for outside
            $s_params_assistant_outgoing[$id]["pbxguest"] = true;
            $s_params_assistant_outgoing[$id]["already-auth"] = $ev->GetValue("already-auth");
            $s_params_assistant_outgoing[$id]["call_type"] = "from outside";
        } else {
            // call is for inside -> we can say the call_type for this party will be from inside
            $s_params_assistant_outgoing[$id]["already-auth"] = $ev->GetValue("already-auth");
            $s_params_assistant_outgoing[$id]["call_type"] = "from inside";
        }
        $ev->retval = $callto;
        $ev->handled = true;
        return true;
    }
    if ($error) {
        $ev->params["error"] = $error;
        //	$ev->handled = true;
    }
    return false;
}

// Always the first action to do 
Yate::Init();
if ($debug_on) {
    Yate::Debug(true);
    Yate::Output(true);
}

if (Yate::Arg()) {
    Yate::Output("Executing startup time CDR cleanup");
    $query = "UPDATE call_logs SET ended= 1 where ended = 0 or ended IS NULL";
    query_nores($query);
    $query = "UPDATE extensions SET inuse_count=0";
    query_nores($query);

    // Spawn another, restartable instance
    $cmd = new Yate("engine.command");
    $cmd->id = "";
    $cmd->SetParam("line", "external register.php");
    $cmd->Dispatch();
    sleep(1);
    exit();
}

// Install handler for the wave end notify messages 
Yate::Watch("engine.timer");
Yate::Install("user.register");
Yate::Install("user.unregister");
Yate::Install("user.auth");
Yate::Install("call.route");
Yate::Install("call.cdr");

Yate::Install("call.answered", 50);
Yate::Install("chan.disconnected", 50);
Yate::Install("chan.hangup");

Yate::Install("user.notify");
Yate::Install("engine.status");
Yate::Install("engine.command");
Yate::Install("engine.debug");

// Ask to be restarted if dying unexpectedly 
Yate::SetLocal("restart", "true");

$query = "SELECT enabled, protocol, username, description, 'interval', formats, authname, password, server, domain, outbound , localaddress, modified, gateway as account, gateway_id, status, 1 AS gw, ip_transport FROM gateways WHERE enabled = 1 AND gateway IS NOT NULL AND username IS NOT NULL ORDER BY gateway";
$res = query_to_array($query);

for ($i = 0; $i < count($res); $i++) {
    $m = new Yate("user.login");
    $m->params = $res[$i];
    $m->Dispatch();
}

set_caller_id();

set_moh();

set_prefixes();

// The main loop. We pick events and handle them 
for (;;) {
    $ev = Yate::GetEvent();
    // If Yate disconnected us then exit cleanly
    if ($ev === false)
        break;
    // No need to handle empty events in this application
    if ($ev === true)
        continue;
    // If we reached here we should have a valid object
    switch ($ev->type) {
        case "incoming":
            switch ($ev->name) {
                case "engine.debug":
                    $module = $ev->GetValue("module");
                    if ($module != "freesentral")
                        break;
                    $line = $ev->GetValue("line");
                    if ($line == "on") {
                        $debug_on = true;
                        Yate::Output(true);
                        Yate::Debug(true);
                        Yate::Output("Enabling debug on FreeSentral routing");
                    } elseif ($line == "off") {
                        $debug_on = false;
                        Yate::Output("Disabling debug on FreeSentral routing");
                        Yate::Output(false);
                        Yate::Debug(false);
                    }
                    else
                        break;
                    $ev->handled = true;
                    break;
                case "engine.command":
                    debug("Got engine.command : line=" . $ev->GetValue("line"));
                    $line = $ev->GetValue("line");
                    if ($line == "query on")
                        $query_on = true;
                    elseif ($line == "query off")
                        $query_on = false;
                    elseif ($line == "international on") {
                        $international_calls = true;
                        Yate::Output("Enabling international calls in Freesentral.");
                        query_nores("UPDATE settings SET value='yes' WHERE param='international_calls_live'");
                    } elseif ($line == "international off") {
                        $international_calls = false;
                        Yate::Output("Disabling international calls in Freesentral.");
                        query_nores("UPDATE settings SET value='no',description='Disabled at ' || now() || '. from telnet' WHERE param='international_calls_live' AND value='yes'");
                    }
                    else
                        break;
                    $ev->handled = true;
                    break;
                case "engine.status":
                    $module = $ev->GetValue("module");
                    if ($module && $module != "register.php" && $module != "misc")
                        break;
                    $query = "SELECT gateway,(CASE WHEN status IS NULL THEN 'offline' else status END) as status FROM gateways WHERE enabled = 1 AND username IS NOT NULL";
                    $res = query_to_array($query);
                    $str = $ev->retval;
                    $international = ($international_calls) ? "on" : "off";
                    $str .= 'name=register.php;international=' . $international . ';users=' . count($res);
                    for ($i = 0; $i < count($res); $i++) {
                        $str .= ($i) ? "," : ";";
                        $str .= $res[$i]["gateway"] . '=' . $res[$i]["status"];
                    }
                    $str .= "\r\n";
                    $ev->retval = $str;
                    $ev->handled = false;
                    break;
                case "user.notify":
                    $gateway = $ev->GetValue("account") . '(' . $ev->GetValue("protocol") . ')';
                    $status = ($ev->GetValue("registered") != 'false') ? "online" : "offline";
                    $s_statusaccounts[$gateway] = $status;
                    $query = "UPDATE gateways SET status='$status' WHERE gateway='" . $ev->GetValue("account") . "'";
                    $res = query_nores($query);
                    break;
                case "user.auth":
                    if (!$ev->GetValue("username"))
                        break;
                    $query = "SELECT password FROM extensions WHERE extension='" . $ev->GetValue("username") . "'";
                    $res = query($query);
                    if ($res)
                        $row = $res->fetchRow();
                    if ($row)
                        foreach ($row as $key => $value) {//Yate::Debug('test:'.$value);                            
                            $ev->retval = $value;
                            $ev->handled = true;
                        };
                    /*
                      if (pg_num_rows($res))
                      {
                      $ev->retval = pg_fetch_result($res,0,0);
                      $ev->handled = true;
                      }
                     */
                    break;
                case "user.register":
                    //подключение трубки: off/on
                    $query = "UPDATE extensions SET location='" . $ev->GetValue("data") . "',expires=" . (time() + $ev->GetValue("expires")) . " WHERE extension='" . $ev->GetValue("username") . "'";
                    $res = query_nores($query);
                    // echo(" ------------------ -------------------------" . $ev->GetValue("username") . "----------");
                    $ev->handled = true;
                    // echo(" ------------------ -------------------------" . $ev->GetValue("username") . "----------");
                    break;
                case "user.unregister":
                    $query = "UPDATE extensions SET location=NULL,expires=NULL WHERE expires IS NOT NULL AND extension='" . $ev->GetValue("username") . "'";
                    $res = query_nores($query);
                    $ev->handled = true;
                    break;
                case "call.route":
                    $caller = $ev->getValue("caller");
                    $called = $ev->getValue("called");
                    $callFrom = $ev->getValue("call_from");
                    return_route($called, $caller);
                    break;
                case "call.answered":
                    $id = $ev->GetValue("targetid");
                    //	debug("Got call.answered for '$id'. Removing fallback if setted in fallback array :".format_array($s_fallbacks));
                    if (isset($s_params_assistant_outgoing[$id])) {
                        $params = $s_params_assistant_outgoing[$id];
                        $m = new Yate("chan.operation");
                        $m->params["id"] = $ev->GetValue("id");
                        $m->params["operation"] = "setstate";
                        $m->params["state"] = "";
                        foreach ($params as $key => $value)
                            $m->params[$key] = $value;
                        $m->Dispatch();
                    }
                    if (isset($s_fallbacks[$id])) {
                        debug("Removing fallback for '$id'");
                        unset($s_fallbacks[$id]);
                    }
                    debug("There are " . count($s_fallbacks) . " in fallback : " . format_array($s_fallbacks));
                    break;
                case "chan.hangup":
                    $id = $ev->GetValue("id");
                    $reason = $ev->GetValue("reason");
                    //	debug("Got '".$ev->name."' for '$id' with reason '$reason'. Fallback :".format_array($s_fallbacks));
                    if (isset($params_assistant_outgoing[$id])) {
                        debug("Dropping pbxassist params for $id's party");
                        unset($s_params_assistant_outgoing[$id]);
                    }
                    if (isset($s_fallbacks[$id])) {
                        debug("Dropping all fallback for '$id'");
                        unset($s_fallbacks[$id]);
                    }
                    break;
                case "chan.disconnected":
                    $id = $ev->GetValue("id");
                    $reason = $ev->GetValue("reason");
                    //	debug("Got '".$ev->name."' for '$id' with reason '$reason'. Fallback :".format_array($s_fallbacks));
                    if (!isset($s_fallbacks[$id]))
                        break;
                    if (in_array($reason, $stoperror)) {
                        debug("Dropping all fallback for '$id'");
                        unset($s_fallbacks[$id]);
                        break;
                    }
                    $msg = new Yate("call.execute");
                    $msg->id = $ev->id;
                    $nr = count($s_fallbacks[$id]) - 1;

                    $callto = $s_fallbacks[$id][$nr]["location"];
                    debug("Doing fallback for '$id' to '$callto'");
                    unset($s_fallbacks[$id][$nr]["location"]);
                    $msg->params = $s_fallbacks[$id][$nr];
                    $msg->params["callto"] = $callto;
                    $msg->Dispatch();
                    if ($nr != 0)
                        unset($s_fallbacks[$id][$nr]);
                    else
                        unset($s_fallbacks[$id]);
                    debug("There are " . count($s_fallbacks) . " in fallback :" . format_array($s_fallbacks));
                    break;
                case "call.cdr":
                    delete_old_calls();
                    /* аккуратнее с запросами. Скрипт может быть перезагружен, если не будет отвечать ~10000msec,
                      что приведет к ошибкам в заполнении временных массивов и др. */
                    $operation = $ev->GetValue("operation");
                    $reason = $ev->GetValue("reason");

                    $ended_initialize = 0;
                    $ended_finalize = 1;

                    switch ($operation) {
                        case "initialize":

                            
                            $gateway_name = '';
                            $gateway_sql = "SELECT username FROM gateways";
                            $gateway_ev = query_to_array($gateway_sql);
                            //для тестовых таблиц------------------------------------------------------------------------------------------
                            //пропускаем значения звонящего и принимающего через цикл сравнения и тех и других с шлюзами
                            $billid_ev = $ev->GetValue("billid");
                            $i = 0;
                            while ($i <= count($gateway_ev)) {
                                if ($ev->GetValue("caller") == $gateway_ev[$i]['username']) {
                                    $gateway_name = $ev->GetValue("caller");
                                } else if ($ev->GetValue("called") == $gateway_ev[$i]['username']) {
                                    $gateway_name = $ev->GetValue("called");
                                }
                                $i = $i + 1;
                            }

                            $chan_ev = $ev->GetValue("chan");
                            $ended_ev = $ended_initialize;
                            $direction_ev = $ev->GetValue("direction");

                            /* обрабатываем событие - звонок на голосовую почту */
                            if ($ev->GetValue("status") == 'cs_voicemail' OR $ev->GetValue("status") == 'cs_attendant') {
                                $direction_ev = 'outgoing';
                                $ended_ev = $ended_finalize;
                            }

                            $query = "INSERT INTO call_logs (time, chan, address, direction, billid, caller, called, duration, billtime, ringtime, status, reason, ended, gateway)"
                                    . " VALUES ("
                                    . $ev->GetValue("time") . ", '"
                                    . $ev->GetValue("chan") . "', '"
                                    . $ev->GetValue("address") . "', '"
                                    . $direction_ev . "', '"
                                    . $ev->GetValue("billid") . "', '"
                                    . $ev->GetValue("caller") . "', '"
                                    . $ev->GetValue("called") . "', "
                                    . $ev->GetValue("duration") . ", "
                                    . $ev->GetValue("billtime") . ", "
                                    . $ev->GetValue("ringtime") . ", '"
                                    . $ev->GetValue("status") . "', '$reason', '$ended_ev', '$gateway_name')";
                            $res = query_nores($query);
                            $query = "UPDATE extensions SET inuse_count=(CASE WHEN inuse_count IS NOT NULL THEN inuse_count+1 ELSE 1 END) WHERE extension='" . $ev->GetValue("external") . "'";
                            $res = query_nores($query);


                            break;

                        case "update":

                            $chan_ev = $ev->GetValue("chan");
                            $caller_ev = $ev->GetValue("caller");
                            $called_ev = $ev->GetValue("called");
                            $direction_ev = $ev->GetValue("direction");
                            $ended_ev = 0;

                            if (substr($chan_ev, 0, 11) == 'ctc-dialer/') {                            
                                $direction_ev = "incoming";
                                $query = "UPDATE call_logs SET chan = '" . substr_replace($ev->GetValue("chan"), $callFrom, 0,10) . "', address='" . $ev->GetValue("address") . "', direction='" . $direction_ev . "', billid='" . $ev->GetValue("billid") .
                                        "', caller='" . $called_ev . "', called='" . $caller_ev . "', duration=" . $ev->GetValue("duration") . ", billtime=" .
                                        $ev->GetValue("billtime") . ", ringtime=" . $ev->GetValue("ringtime") . ", status='" . $ev->GetValue("status") .
                                        "', reason='$reason' WHERE chan='" . $ev->GetValue("chan") . "' AND time=" . $ev->GetValue("time");                             
                            } else {
                                $query = "UPDATE call_logs SET address='" . $ev->GetValue("address") . "', direction='" . $direction_ev . "', billid='" . $ev->GetValue("billid") .
                                        "', caller='" . $caller_ev . "', called='" . $called_ev . "', duration=" . $ev->GetValue("duration") . ", billtime=" .
                                        $ev->GetValue("billtime") . ", ringtime=" . $ev->GetValue("ringtime") . ", status='" . $ev->GetValue("status") .
                                        "', reason='$reason' WHERE chan='" . $ev->GetValue("chan") . "' AND time=" . $ev->GetValue("time");
                            }
                            $res = query_nores($query);
                            $query1 = "UPDATE call_logs t1
                                      JOIN call_logs t2 ON t2.billid = t1.billid
                                      SET t1.direction = 'unknown'
                                      WHERE t1.called = '" . $called_ev . "' and t1.billid = '" . $ev->GetValue("billid") . "' and (SUBSTRING(t1.chan,1, 11)!= 'ctc-dialer/' OR SUBSTRING(t1.chan,1, 11)!= 'order_call/')
                                      AND
                                      t2.caller = '" . $called_ev . "' and t2.billid = '" . $ev->GetValue("billid") . "' and (SUBSTRING(t2.chan,1, 11) = 'ctc-dialer/' OR SUBSTRING(t2.chan,1, 11) = 'order_call/')";
                            $res1 = query_nores($query1);
                            break;

                        case "finalize":
                            $billid_ev = $ev->GetValue("billid");
                            $query = "UPDATE call_logs SET address='" . $ev->GetValue("address") . "', billid='" . $ev->GetValue("billid") .
                                    "', caller='" . $ev->GetValue("caller") . "', called='" . $ev->GetValue("called") . "', duration=" . $ev->GetValue("duration") . ", billtime=" .
                                    $ev->GetValue("billtime") . ", ringtime=" . $ev->GetValue("ringtime") . ", status='" . $ev->GetValue("status") . "', reason='$reason', ended=1 WHERE chan='" .
                                    $ev->GetValue("chan") . "' AND time=" . $ev->GetValue("time");

                            $res = query_nores($query);

                            $sql = $query = "INSERT INTO call_history (time, chan, address, direction, billid, caller, called, duration, billtime, ringtime, status, ended, gateway)"
                                    . "SELECT
                                       b.time,
                                       b.chan,
                                       b.address,                                   
                                       CASE
                                           WHEN SUBSTRING(a.chan,1, 11) = 'order_call/' OR SUBSTRING(b.chan,1, 11) = 'order_call/'
                                               THEN 'order_call'
                                           WHEN x1.extension IS NOT NULL AND x2.extension IS NOT NULL
                                               THEN 'internal'
                                           WHEN x1.extension IS NOT NULL
                                               THEN 'outgoing'
                                           ELSE 'incoming'
                                       END direction,
                                       b.billid,
                                       CASE
                                           WHEN x1.firstname IS NULL
                                               THEN a.caller
                                           ELSE  a.caller
                                       END caller,
                                       CASE
                                           WHEN x2.firstname IS NULL
                                               THEN b.called
                                           ELSE b.called
                                       END called,
                                       b.duration,
                                       b.billtime,
                                       b.ringtime,
                                       CASE
                                           WHEN b.reason = ''
                                               THEN b.status
                                           ELSE REPLACE( LOWER(b.reason), ' ', '_' )
                                       END status,
                                       CASE
                                           WHEN SUBSTRING(b.chan,1, 11)!= 'ctc-dialer/'
                                               THEN b.ended = '1'
                                           WHEN SUBSTRING(b.chan,1, 11)!= 'order_call/'
                                               THEN b.ended = '1'
                                           ELSE b.ended
                                       END ended,
                                       CASE
                                           WHEN b.gateway = ''
                                               THEN a.gateway
                                           ELSE a.gateway
                                       END gateway
                                   FROM call_logs a
                                   JOIN call_logs b ON b.billid = a.billid AND b.ended = 1 AND b.direction = 'outgoing'
                                   LEFT JOIN extensions x1 ON x1.extension = a.caller
                                   LEFT JOIN extensions x2 ON x2.extension = a.called
                                   WHERE  a.direction = 'incoming' AND b.billid = '$billid_ev' ";
                            $res = query_nores($query);
                            //очищаем массив и удаляем ненужные записи- - - - - - - - -
                            //$sql = "DELETE FROM call_logs WHERE billid = '" . $billid_ev . "'";
                            //$res = query_nores($sql);


                            $query = "UPDATE extensions SET inuse_count=(CASE WHEN inuse_count>0 THEN inuse_count-1 ELSE 0 END), inuse_last=" . time() . " WHERE extension='" . $ev->GetValue("external") . "'";
                            $res = query_nores($query);
                            break;
                    }
                    break;
            }
            // This is extremely important.
            //	We MUST let messages return, handled or not
            if ($ev)
                $ev->Acknowledge();
            break;
        case "answer":
            switch ($ev->name) {
                case "engine.timer":
                    $time = $ev->GetValue("time");
                    $timer_caller_id++;
                    if ($timer_caller_id > 600) {
                        // update caller_id every 10 minutes
                        set_caller_id();
                    }
                    if ($time % 50 == "0") {
                        set_prefixes(); // update prefixes and international settings every 50 seconds
                    }
                    if ($time % 10 == "1" || $time % 10 == "6")
                        reset_counters($time);
                    if ($moh_next_time < $time)
                        set_moh($time);
                    if ($time < $next_time)
                        break;
                    $next_time = $time + $time_step;
                    $query = "SELECT enabled, protocol, username, description, 'interval', formats, authname, password, server, domain, outbound , localaddress, modified, gateway as account, gateway_id, status, 1 AS gw FROM gateways WHERE enabled = 1 AND modified = 1 AND username is NOT NULL";
                    $res = query_to_array($query);
                    for ($i = 0; $i < count($res); $i++) {
                        $m = new Yate("user.login");
                        $m->params = $res[$i];
                        $m->Dispatch();
                    }
                    $query = "UPDATE extensions SET location=NULL,expires=NULL WHERE expires IS NOT NULL AND expires<=" . time();
                    $res = query_nores($query);
                    $query = "UPDATE gateways SET modified=0 WHERE modified=1 AND username IS NOT NULL";
                    $res = query_nores($query);
                    break;
            }
            // Yate::Debug("PHP Answered: " . $ev->name . " id: " . $ev->id);
            break;
        case "installed":
            // Yate::Debug("PHP Installed: " . $ev->name);
            break;
        case "uninstalled":
            // Yate::Debug("PHP Uninstalled: " . $ev->name);
            break;
        default:
        // Yate::Output("PHP Event: " . $ev->type);
    }
}

/* vi: set ts=8 sw=4 sts=4 noet: */
?>