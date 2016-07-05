localStorage['refresh'] = 5;

var lang = chrome.i18n.getMessage("@@ui_locale");

if (lang == 'ru')
    localStorage['lang'] = "ru";
else
    localStorage['lang'] = "en";

//localStorage['service_url'] = "http://" + localStorage['service'] + "/service/data.php";

var time_offset = new Date().getTimezoneOffset();
var interval;

function showNotification(theTitle, theBody, theIcon) {
    var icon = (theIcon) ? theIcon : 'images/logo.png';
    var title = (theTitle) ? theTitle : '';
    var options = {
        body: theBody,
        icon: icon
    };
    var notification = new Notification(title, options);
    window.setTimeout(function () {
        notification.close();
    }, 5000);
}

function main()
{
    if (!localStorage['session_id'])
        return;
    
    var iconLogged = 'images/icon_not_logged_in.png';
    var req = new XMLHttpRequest();
    
    req.onload = function () {
        var doc = req.responseText;
        console.log(doc);

        if (doc) {
            var text = '';
            var json = JSON.parse(doc);
            if (json.success) {
                var status = json['status'];
                var inuseCount = json['inuse_count'];
                var incomingCall = json['incoming_call'];

                iconLogged = (status == 'offline') ? iconLogged : 'images/icon_logged_in.png';
                chrome.browserAction.setIcon({
                    path: iconLogged
                });

                var cur_state = localStorage['status'];
                if (status && status != cur_state)
                {
                    text = chrome.i18n.getMessage("status") + ' : ' + status;
                    localStorage['status'] = status;
                    init();
                }

                if (incomingCall && inuseCount === '1') {

                    var caller = (incomingCall['caller']) ? incomingCall['caller'] : '';
                    var called = (incomingCall['called']) ? incomingCall['called'] : '';
                    var call_status = (incomingCall['call_status']) ? incomingCall['call_status'] : '';

                    /* not used
                     * var line = (incomingCall['line']) ? incomingCall['line'] : '';
                     * var time = (incomingCall['time']) ? incomingCall['time'] : '';
                     */

                    if (caller === localStorage['extension']) {

                    }
                    var number = (caller === localStorage['extension']) ? called : caller;

                    /*example: Incoming call, Internal call*/
                    text += chrome.i18n.getMessage(call_status) + ' ' + chrome.i18n.getMessage('call');
                    text += '\n' + chrome.i18n.getMessage('number') + ': ' + number;

                }
            } else
            {
                localStorage.removeItem('session_id');
                text = json.message;
                init();
            }
            if (text) {
                var msg = (chrome.i18n.getMessage(text)) ? chrome.i18n.getMessage(text) : text;
                showNotification('', msg);
            }
        }
        ;
    };
    req.open("GET", localStorage['service_url'] + "?action=get_state&extension='" + localStorage['extension'] + "'&time_offset=" + time_offset + "&period=" + localStorage['refresh'] + "&" + localStorage['session_name'] + '=' + localStorage['session_id'], true);
    req.send(null);
}

function substr(str, startStr, endStr)
{
    start = str.indexOf(startStr) + startStr.length;
    return str.substring(start, str.indexOf(endStr, start));
}

function init() {
    var extention = localStorage['extension'] ? localStorage['extension'] : '';
    var password = localStorage['password'] ? localStorage['password'] : '';
    var service = localStorage['service'] ? localStorage['service'] : '';

    if (!localStorage['session_id'])
    {
        console.info('session_id: ' + localStorage['session_id']);
        if (extention == '' || password == '' || service == '') {
            showNotification('', chrome.i18n.getMessage('settings_error'));
            chrome.browserAction.setIcon({
                path: 'images/icon_not_logged_in.png'
            });
        } else {
            showNotification('', chrome.i18n.getMessage('auth_failed'));
            chrome.browserAction.setIcon({
                path: 'images/icon_not_logged_in.png'
            });
        }

        if (interval !== '') {
            window.clearInterval(interval);
        }
        interval = '';

        chrome.browserAction.setIcon({
            path: 'images/icon_not_logged_in.png'
        });
        return;
    } else {
        chrome.browserAction.setIcon({
            path: 'images/icon_logged_in.png'
        });
    }
    window.clearInterval(interval);
    interval = window.setInterval(function () {
        //refresh_bitrix();
        main();
    }, localStorage['refresh'] * 1000);

}

document.addEventListener('DOMContentLoaded', function () {
    chrome.extension.onRequest.addListener(
            function (request, sender, sendResponse) {
                if (request.method == "getLocalStorage")
                    sendResponse({
                        data: localStorage[request.key]
                    });
                if (request.method == "getLocalMsg")
                    sendResponse({
                        data: chrome.i18n.getMessage(request.key)
                    });
                if (request.method == "showMsg")
                    showNotification('', request.msg);
                if (sender.url == chrome.extension.getURL("options.html") && request.do == "update")
                {
                    get_session_id(init);
                }
            }
    );
    init();
});

//chrome.extension.getBackgroundPage().sendEmail(); - пример

// {"success":true,"status":"online","inuse_count":"1",
//    "incoming_call":{"time":"01.01.70 00:33:36","call_status":"internal",
// caller":"133","called":"125","incoming_trunk":"","total":null}}