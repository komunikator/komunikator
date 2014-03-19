localStorage['refresh'] = 30;
var time_offset = new Date().getTimezoneOffset();
function restore_options()
{
    document.getElementById("_extension").innerHTML = chrome.i18n.getMessage("extension");
    document.getElementById("_password").innerHTML = chrome.i18n.getMessage("password");
    //  document.getElementById("_openform").innerHTML = chrome.i18n.getMessage("openform");
    //--
    document.getElementById("_service").innerHTML = chrome.i18n.getMessage("service"); //надо или нет? с этим слово "Сервер" не видно
//--
    document.getElementById("_click2call").innerHTML = chrome.i18n.getMessage("click2call");
    document.getElementById("_page1").innerHTML = chrome.i18n.getMessage("co_worker");
    document.getElementById("_list_extensions").innerHTML = chrome.i18n.getMessage("List_extensions");
    document.getElementById("_extension_status").innerHTML = chrome.i18n.getMessage("Extension_status");
    document.getElementById("_extension_number").innerHTML = chrome.i18n.getMessage("Extension_number");
    document.getElementById("_name").innerHTML = chrome.i18n.getMessage("Name");
    document.getElementById("_surname").innerHTML = chrome.i18n.getMessage("Surname");
    document.getElementById("_time").innerHTML = chrome.i18n.getMessage("Time");
    document.getElementById("_caller").innerHTML = chrome.i18n.getMessage("Caller");
    document.getElementById("_called").innerHTML = chrome.i18n.getMessage("Caller");
    document.getElementById("_call_status").innerHTML = chrome.i18n.getMessage("Call_status");
    document.getElementById("_call_logs").innerHTML = chrome.i18n.getMessage("Call_logs");
    document.getElementById("_page2").innerHTML = chrome.i18n.getMessage("Call_logs");
    document.getElementById("_page3").innerHTML = chrome.i18n.getMessage("Settings");

    document.getElementById("_save").value = chrome.i18n.getMessage("save");
    document.getElementById("_exit").value = chrome.i18n.getMessage("exit");
    document.title = chrome.i18n.getMessage("options");

    document.getElementById("service").value = localStorage["service"] ? localStorage["service"] : '';
    document.getElementById("extension").value = localStorage["extension"] ? localStorage["extension"] : '';
    document.getElementById("password").value = localStorage["password"] ? localStorage["password"] : '';
    //document.getElementById("openform").checked = localStorage["openform"] == 'true' ? true : false;
    document.getElementById("click2call").checked = localStorage["click2call"] == 'true' ? true : false;
}

function save_options()
{
    localStorage["extension"] = document.getElementById("extension").value;
    localStorage["password"] = document.getElementById("password").value;
//    localStorage["openform"]  = document.getElementById("openform").checked;
    localStorage["click2call"] = document.getElementById("click2call").checked;
    localStorage['service'] = document.getElementById("service").value;

    get_session_id(function(msg) {
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

function exit() {
    window.close();
}
;
function history() {
    var req = new XMLHttpRequest();
    req.onload = function() {
        var doc = req.responseText;
        var json = JSON.parse(doc);
        console.log(json);

        var data = json.data;
        console.log(data);
        var time = 0, caller = 0, called = 0, statuss = 0;
        var tbody = document.getElementById("call_logs_table").getElementsByTagName("TBODY")[0];
if(tbody.rows.length>0){
  tbody.rows.remove();
}
        for (var i = 0; i < data.length; i++) {
            time = data[i][1];
            caller = data[i][3];
            called = data[i][4];
            statuss = data[i][7];

            var row = document.createElement("TR");
            var td1 = document.createElement("TD");
            td1.appendChild(document.createTextNode(time));
            var td2 = document.createElement("TD");
            td2.appendChild(document.createTextNode(caller));
            var td3 = document.createElement("TD");
            td3.appendChild(document.createTextNode(called));
            var td4 = document.createElement("TD");
            td4.appendChild(document.createTextNode(statuss));

            row.appendChild(td1);
            row.appendChild(td2);
            row.appendChild(td3);
            row.appendChild(td4);

            tbody.appendChild(row);
        }
    };
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    req.open("GET", localStorage['service_url'] + "?action=get_call_logs" + "&session=" + localStorage['session_id'] + "&filter=%5B%7B%22type%22%3A%22date%22%2C%22comparison%22%3A%22eq%22%2C%22value\":" + JSON.stringify(today.toLocaleString()) + ",\"field%22%3A%22time%22%7D%5D&page=1&start=0&size=50&sort=time&dir=DESC", true);
    req.send(null);
}
;
var interval;
var t = 2;
function list_extensions() {
    // document.getElementById('_page1').clear();
//var children = document.getElementById("list_extensions_table").childNodes;
//if(extensions>0){   
//var child = children[4];
//document.getElementById("list_extensions_table").removeChild(child);
//console.log(child); }
    var req = new XMLHttpRequest();
    req.onload = function() {
        var doc = req.responseText;
        var json = JSON.parse(doc);
        //  console.log(json);
        var extensions = json.extensions;
      //  console.log(extensions);
        var extension_status = 0, extension_number = 0, name = 0, surname = 0;
        var tbody = document.getElementById("list_extensions_table").getElementsByTagName("TBODY")[0];
   
    //console.log(tbody.rows);
   //tbody.rows.remove();
    
//mPers.removeAttribute("TR"); }

//mPers.removeChild(tbl_body);

//for(var i=0; i<document.body.childNodes.length; i++) {
  //  var child = document.getElementById("list_extensions_table").childNodes[i];
  //console.log(child);
//}

if(tbody.rows.length>0){
    console.log(tbody);
   tbody.rows.remove();
}

        for (var i = 0; i < extensions.length; i++) {

            extension_status = extensions[i][0];
            extension_number = extensions[i][1];
            name = extensions[i][2];
            surname = extensions[i][3];

            var row = document.createElement("TR");

            var td1 = document.createElement('img');
            if (extension_status === "online") {
                td1.setAttribute('src', 'images/online.gif');
                //// td1 = <img src="images/icon_not_logged_in.png">
            }
            if (extension_status === "offline") {
                td1.setAttribute('src', 'images/offline.gif');
            }
            if (extension_status === "busy") {
                td1.setAttribute('src', 'images/busy.gif');
            } 
            //td2.appendChild(document.createTextNode(extension_number));
            td1.setAttribute('style', 'float:right;');
//newBut.setAttribute('style', 'float:right;');
//newBut.setAttribute('id', 'del_but');
//newBut = document.getElementById('save_but').parentNode.appendChild(newBut);
//var newBut = document.createElement('img');
//newBut.setAttribute('src','images/icon_not_logged_in.png');
//newBut.setAttribute('alt', 'del');
            //  td1.appendChild(document.createTextNode(extension_status));
            //  td1.setAttribute('style', 'float:inherit');
            var td2 = document.createElement("TD");
            td2.appendChild(document.createTextNode(extension_number));
            var td3 = document.createElement("TD");
            td3.appendChild(document.createTextNode(name));
            var td4 = document.createElement("TD");
            td4.appendChild(document.createTextNode(surname));

            row.appendChild(td1);
            row.appendChild(td2);
            row.appendChild(td3);
            row.appendChild(td4);

            tbody.appendChild(row);
        }
    };
     
  //  req.open("GET", localStorage['service_url'] + "?action=get_list_extensions&session=" + localStorage['session_id'] + "&time_offset=" + time_offset + "&period=" + localStorage['refresh'], true);
     req.open("GET", localStorage['service_url'] + "?action=get_list_extensions&extension='" + localStorage['extension'] + "'&time_offset=" + time_offset + "&period=" + localStorage['refresh'] + "&" + localStorage['session_name'] + '=' + localStorage['session_id'], true);
    req.send(null);
     window.clearInterval(interval);
    interval = window.setInterval(function() {
        list_extensions();
    }, localStorage['refresh']*400);
}
;

window.onload = function() {
    document.getElementById('_page1').onclick = function() {


//node.removeChild(child);

//console.log(mPers); 
//document.getElementById("list_extensions_table").
        
        list_extensions();
        //   document.location.href='#top';
//return false;
    };
    document.getElementById('_page1').click();
    document.getElementById('_page2').onclick = function() {

        history();
        //  document.location.href='#top';
//return false;

    };
};
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("_save").addEventListener('click', save_options);
    document.getElementById("_exit").addEventListener('click', exit);
    //--
    document.getElementById("_update_history").addEventListener('click', history);
    document.getElementById("_update_extensions").addEventListener('click', list_extensions);
    //--
    restore_options();
});
