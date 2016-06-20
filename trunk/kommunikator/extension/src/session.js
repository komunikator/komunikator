function get_session_id(cb){  
    var req = new XMLHttpRequest();

    req.onreadystatechange = function() {
        // req.onload = function ()
        if (req.readyState == 4){
            if (req.status != 200) 
                cb(chrome.i18n.getMessage("service_not_available"));
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
                        status = chrome.i18n.getMessage("auth_failed")?chrome.i18n.getMessage("auth_failed"):json.message?json.message:'Failed';
                    }
                    if (cb) cb(status);
                };
            };
        }
    };

    req.open("GET", localStorage['service_url']+"?action=auth&extension="+localStorage['extension']+"&password="+localStorage['password'], true);
    req.send(null);
}
