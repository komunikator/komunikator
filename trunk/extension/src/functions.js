localStorage['refresh'] = 5;
var lang = chrome.i18n.getMessage("@@ui_locale");
if (lang == 'ru')
    localStorage['lang'] = "ru";
else
    localStorage['lang'] = "en";

//localStorage['service_url'] = "http://ats.digt.local/freesentral/data.php";
localStorage['service_url'] = "http://172.17.2.48/service/data.php";

var bitrix_SOURCE_ID = 15;
var bitrix_url  = "http://digt.ru/bitrix/admin/ticket_edit.php?lang="+localStorage['lang'];
var time_offset = new Date().getTimezoneOffset();

function get_session_id(cb){  
    var req = new XMLHttpRequest();

    req.onreadystatechange = function() {
        // req.onload = function ()
        if (req.readyState == 4){
            if (req.status != 200) 
                cb(chrome.i18n.getMessage("service_not_available"))
            else
            {
                var doc = req.responseText;
                var status = chrome.i18n.getMessage("options_saved");
                if (doc) {
                    var json = JSON.parse(doc);
                    if (json.success && json.session_id){
                        localStorage['session_id'] =  json.session_id;
                        localStorage['session_name'] = json.session_name?json.session_name:"PHPSESSID";
                    }
                    else
                    {
                        if (localStorage['session_id'])
                            localStorage.removeItem('session_id');
                        status = chrome.i18n.getMessage("auth_failed")?chrome.i18n.getMessage("auth_failed"):json.message?json.message:'Failed'
                    }
                    if (cb) cb(status);
                };
            };
        }
    }

    req.open("GET", localStorage['service_url']+"?action=auth&extension="+localStorage['extension']+"&password="+localStorage['password'], true);
    req.send(null);
}

function restore_options()
{
    document.getElementById("_extension").innerHTML = chrome.i18n.getMessage("extension");
    document.getElementById("_password").innerHTML = chrome.i18n.getMessage("password");
    document.getElementById("_openform").innerHTML = chrome.i18n.getMessage("openform");
    document.getElementById("_click2call").innerHTML = chrome.i18n.getMessage("click2call");

    document.getElementById("_save").value = chrome.i18n.getMessage("save");
    document.getElementById("_exit").value = chrome.i18n.getMessage("exit");
    document.title = chrome.i18n.getMessage("options");

    document.getElementById("extension").value = localStorage["extension"]?localStorage["extension"]:'';
    document.getElementById("password").value  = localStorage["password"]?localStorage["password"]:'';
    document.getElementById("openform").checked = localStorage["openform"]=='true'?true:false;
    document.getElementById("click2call").checked  = localStorage["click2call"]=='true'?true:false;
}

function save_options()
{
    localStorage["extension"] = document.getElementById("extension").value;
    localStorage["password"]  = document.getElementById("password").value;
    localStorage["openform"]  = document.getElementById("openform").checked;
    localStorage["click2call"]  = document.getElementById("click2call").checked;

    get_session_id(function(msg){
        var status = document.getElementById("status");
        status.innerHTML = msg;
        setTimeout(function() {
            status.innerHTML = "";
            window.close();
            chrome.extension.sendRequest({
                'do': "update"
            });
        }, 1750);
    });
    
}

function main()
{
    if (!localStorage['session_id'])
        return;
    var req = new XMLHttpRequest();
    req.onload = function () {
        var doc = req.responseText;
        if (doc) {
            var text = '';
            var json = JSON.parse(doc);
            //console.log(localStorage['session_id']+":"+doc);
            if (json.success/* && json.status='online'*/){
                var status = json['status'];
                var calls = json['calls'];

                        var cur_state = localStorage['status'];
                        if (status && status!=cur_state)
                        {
                            text = chrome.i18n.getMessage("status")+' : ' + status;
                            localStorage['status'] = status;
                            init();
                        }

/*                for (var e in status)
                    if (status[e][0] == localStorage['extension']){
                        var cur_state = localStorage['extension_'+localStorage['extension']];
                        if (status[e][1]!=cur_state)
                        {
                            text = chrome.i18n.getMessage("status")+' : ' + status[e][1];
                            localStorage['extension_'+localStorage['extension']] = status[e][1];
                            init();
                        }
                    }
*/
                if (calls && calls.length) {
                    for (var e in calls)
                        text += ' '+chrome.i18n.getMessage("call_from")+' : ' + calls[e][1];
                    var caller=calls[e][1];
                    if (localStorage["openform"]=='true')
                        chrome.tabs.create({
                            url:bitrix_url
                        },function(tab){
                            chrome.tabs.executeScript(tab.id, {
                                code:"document.getElementById('SOURCE_ID').value="+bitrix_SOURCE_ID+";document.getElementById('OWNER_USER_ID').value="+caller
                            });
                        })
                }
            }
            else
            {
                localStorage.removeItem('session_id');
                text = json.message;
                init();
            }
            if (text) showNotification(localStorage['extension'], text);
        //log(author, text, link);
        
        };
    }
    req.open("GET", localStorage['service_url']+"?action=get_state&extension='"+localStorage['extension']+"'&time_offset="+time_offset+"&period="+localStorage['refresh']+"&"+localStorage['session_name']+'='+localStorage['session_id'], true);
    req.send(null);

    
}

function substr(str, startStr, endStr)
{
    start = str.indexOf(startStr) + startStr.length;
    return str.substring(start, str.indexOf(endStr, start));
}

function showNotification(title, text)
{
    var notification = webkitNotifications.createNotification(
        '',
        title,
        text
        );
    notification.show();
    window.setTimeout(function() {
        notification.cancel();
    }, 5000);
}
var interval;

function init() {
    console.log("session_id :"+ localStorage['session_id']);
    if (!localStorage['session_id']/* || localStorage['extension_'+localStorage['extension']]=='offline'*/)
    {
      
        if (interval!="")
            window.clearInterval(interval);
        interval = "";
        chrome.browserAction.setIcon({
            path:"images/icon_not_logged_in.png"
        });
        return;
    }  else
    chrome.browserAction.setIcon({
        path:"images/icon_logged_in.png"
    });
    window.clearInterval(interval);
    interval = window.setInterval(function() {
        main();
    }, localStorage['refresh'] * 1000);
//    console.log(localStorage['refresh'] * 1000);
}
