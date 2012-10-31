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

function exit(){
    window.close();
};

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById("_save").addEventListener('click', save_options);
    document.getElementById("_exit").addEventListener('click', exit);
    restore_options();
});


