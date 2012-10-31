$(function(){
    var headElement = document.getElementsByTagName("head")[0];
    if (headElement) {
        $(headElement).append($('<link type="text/css" rel="stylesheet">').attr('href', chrome.extension.getURL("options.css")));
    }
    var regexp = /(\+?[78] ?\(?\d{3,4}\)? ?\d{1,3}-?\d{2}-?\d{2})/;
    var service_url ;
    var click2callDesc;

    $.fn.digt_phone = function() {
        var id=100;
        function phone(node) {
            if (node.nodeType == 3 && node.parentNode.className!='digt_phone_text_span') {
                var str = $('<div></div>').text(node.data).html();
                if (regexp.test(str)){
                    $(node).replaceWith(str.replace(regexp, '<span id="phone_'+id+'" dir="ltr" title="'+click2callDesc+'" class="digt_phone_container"><span class="digt_phone_left_span">&nbsp;&nbsp;</span><span class="digt_phone_text_span">$1</span><span class="digt_phone_right_span">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>'));
                    $('#phone_'+id).click({
                        phone:str
                    },
                    
                    function(eventObject){
                        var number = eventObject.data.phone.replace(/\D/g,'');
                        var req = new XMLHttpRequest();
                        req.onload = function () {
                            var doc = req.responseText;
                            if (doc) {
                                var json = JSON.parse(doc);
                            //if (json.success)
                            //       alert('call: "'+number+'"')
                            }
                        };
                        req.open("GET", service_url+'&action=make_call&number='+number, true);
                        req.send(null);                       
                    }
                    );
                    id++;
                }

            } else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
                for (var i = 0; i < node.childNodes.length; i++) {
                    phone(node.childNodes[i]);
                }
            }
        }

        return this.each(function() {
            phone(this);
        });
    }

    $.digt_phone = function (html) {
        return $('<div>' + html + '</div>').digt_phone().html();
    }
    chrome.extension.sendRequest({
        method: "getLocalStorage",
        key: "click2call"
    }, function(response) {
        chrome.extension.sendRequest({
            method: "getLocalStorage",
            key: "service_url"
        }, function(url) {
            if (url && url.data) {
                service_url = url.data;
                chrome.extension.sendRequest({
                    method: "getLocalStorage",
                    key: "session_name"
                }, function(session_name) {
                    if (session_name.data)
                        service_url += '?'+session_name.data;
                    chrome.extension.sendRequest({
                        method: "getLocalStorage",
                        key: "session_id"
                    }, function(session_id) {
                        if (session_id.data)
                            service_url += '='+session_id.data;
                        chrome.extension.sendRequest({
                            method: "getLocalMsg",
                            key: "click2callDesc"
                        }, function(msg) {
                            if (msg.data)
                                click2callDesc = msg.data;
                            if (response.data=='true')
                                $('body').digt_phone();
                        }) 
                    })
                })
            }
        });
    })
});
