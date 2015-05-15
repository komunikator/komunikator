var not_android = navigator.userAgent.indexOf('Android') < 0;
var not_presto = navigator.userAgent.indexOf('Presto') < 0;
var not_ie = navigator.userAgent.indexOf('Trident') < 0;
var not_safari = navigator.userAgent.indexOf('OS X') < 0;
if ((not_ie)&&(not_safari)&&(not_presto)&&(not_android)) {

document.write(unescape("%3Cscript src='http://" + c2chostlocation + "/c2c/js/jquery.min.js' + (new Date).getTime(); type='text/javascript'%3E%3C/script%3E"));
document.write(unescape("%3Cscript src='http://" + c2chostlocation + "/c2c/js/jquery.jsonp-2.4.0.js' + (new Date).getTime(); type='text/javascript'%3E%3C/script%3E"));

document.write(unescape("%3Cscript src='http://" + c2chostlocation + "/c2c/js/SIPml-api.js' + (new Date).getTime(); type='text/javascript'%3E%3C/script%3E"));
document.write(unescape("%3Cscript src='http://" + c2chostlocation + "/c2c/js/patch_20140716.js' + (new Date).getTime(); type='text/javascript'%3E%3C/script%3E"));

//GEO INFO
//document.write(unescape("%3Cscript src='AgentInfo.js' + (new Date).getTime(); type='text/javascript'%3E%3C/script%3E"));
var about = {}; 
function geoData(data){
/*
if (0 && data.longitude){ //TODO make work
 data['lat/long'] = "<a href='https://www.google.ru/maps/@"+data.latitude+","+data.longitude+"z' target='_blank'>"+data.latitude+", "+data.longitude+"</a>";
 delete data.longitude;
 delete data.latitude;
};
*/
about = {geo:data/*,agent:getAgentInfo()*/};
}; 
//document.write(unescape("%3Cscript src='//freegeoip.net/json/?callback=geoData' + (new Date).getTime(); type='application/javascript'%3E%3C/script%3E"));
document.write(unescape("%3Cscript src='//www.telize.com/geoip?callback=geoData' + (new Date).getTime(); type='application/javascript'%3E%3C/script%3E"));
// GEO INFO

if(!window.c2c){
    c2c = { debug: false };
}
	
c2c.config = {
    websocket_proxy_url: 'ws://' + c2chostlocation + ':10060',
};
c2c.started = false;
c2c.callSession = null;

c2c.add_html_elts = function(parent, elements) {
    var tag_parent = document.getElementsByTagName(parent)[0];
    elements.forEach(function (element) {
        var tag_elt = document.createElement(element.type);
        element.attributes.forEach(function (attribute) {
            tag_elt.setAttribute(attribute.name, attribute.value);
        });
        var s = document.getElementsByTagName(element.type);
        if(s && s.length > 0){
            s[0].parentNode.insertBefore(tag_elt, s[0]);
        }
        else{
            tag_parent.appendChild(tag_elt);
        }
    });
};

if(c2c.debug){
    c2c.add_html_elts('head',
        [
            { type: 'script', attributes: [{ name: 'type', value: 'text/javascript' }, { name: 'src', value: './c2c-base64.js' }] },
            { type: 'script', attributes: [{ name: 'type', value: 'text/javascript' }, { name: 'src', value: './c2c-md5.js' }] }
        ]
    );
}
else{
c2c.add_html_elts('body',
        [
            { type: 'link', attributes: [{ name: 'href', value: 'http://' + c2chostlocation + '/c2c/css/call_us.css' }, { name: 'rel', value: 'stylesheet' }] }
        ]
    );
}

c2c.buildAuthToken = function (email, password) {    
    return MD5.hexdigest(password + ':' + email + ':' + 'click2call.org');
}

c2c.buildHa1 = function (impi, realm, password) {
    return MD5.hexdigest(impi + ':' + realm + ':' + password);
}

c2c.obfuscate = function (address) {
    return Base64.encode(address);
}

c2c.unobfuscate = function (address) {
    return Base64.decode(address);
}

function sipSendDTMF(c){
	c2c.callSession.dtmf(c);
	dtmfTone.play();
}

function openKeyPad(){
	c2c_btn_call.style.webkitTransform = "rotate(0deg)";
	c2c_btn_call.style.MozTransform = "rotate(0deg)";
	document.getElementById("c2c_btn_call").style.height = "40px";
	document.getElementById("c2cNumPad").style.display = 'block';
	if (c2c.vertical_align == 'top') {
		document.getElementById("c2c_btn_call").style.top = parseInt(c2c.vertical_margin) + parseInt(10) + "px";
	}
	if (c2c.vertical_align == 'bottom') {
		document.getElementById("c2c_btn_call").style.bottom = parseInt(c2c.vertical_margin) + parseInt(170) + "px";
	}	
	if (c2c.button_type == "vertical") {
		if (c2c.horizontal_align == "right") {		
			document.getElementById("c2c_btn_call").style.right = parseInt(c2c.horizontal_margin) + 80 + "px";
			document.getElementById("c2cNumPad").style.right = parseInt(c2c.horizontal_margin) + 70 + "px";
		}
		if (c2c.horizontal_align == "left") {
			document.getElementById("c2c_btn_call").style.left = parseInt(c2c.horizontal_margin) + 80 + "px";
			document.getElementById("c2cNumPad").style.left = parseInt(c2c.horizontal_margin) + 70 + "px";
		}
	}	
	if (c2c.button_type == "horizontal") {
		if (c2c.horizontal_align == "right") {		
			document.getElementById("c2c_btn_call").style.right = parseInt(c2c.horizontal_margin) + 10 + "px";
			document.getElementById("c2cNumPad").style.right = c2c.horizontal_margin + "px";
		}
		if (c2c.horizontal_align == "left") {
			document.getElementById("c2c_btn_call").style.left = parseInt(c2c.horizontal_margin) + 10 + "px";
			document.getElementById("c2cNumPad").style.left = c2c.horizontal_margin + "px";
		}
	}		
}

function closeKeyPad(){
	document.getElementById("c2cNumPad").style.display = 'none';
	document.getElementById("c2c_btn_call").style.height = "";	
	if (c2c.button_type == "vertical") {
		c2c_btn_call.style.webkitTransform = "rotate(-90deg)";
		c2c_btn_call.style.MozTransform = "rotate(-90deg)";	
		if (c2c.vertical_align == 'top') {
			document.getElementById("c2c_btn_call").style.top = parseInt(c2c.vertical_margin) + parseInt(70) + "px";
		}
		if (c2c.vertical_align == 'bottom') {
			document.getElementById("c2c_btn_call").style.bottom = parseInt(c2c.vertical_margin) + parseInt(70) + "px";
		}		
	}
	if (c2c.button_type == "horizontal") {
		if (c2c.vertical_align == 'top') {
			document.getElementById("c2c_btn_call").style.top = c2c.vertical_margin + "px";
		}
		if (c2c.vertical_align == 'bottom') {
			document.getElementById("c2c_btn_call").style.bottom = c2c.vertical_margin + "px";
		}		
	}	
	if (c2c.horizontal_align == "right") {	
		document.getElementById("c2c_btn_call").style.right= c2c.horizontal_margin + "px";
	}
	if (c2c.horizontal_align == "left") {	
		document.getElementById("c2c_btn_call").style.left= c2c.horizontal_margin + "px";
	}	 
}	 	

////
function jsonpCallbackStatus(data)
{
        if (data.status == "online")
        {
            document.getElementById("c2c_btn_call").style.display = 'block';
        } else {
            document.getElementById("c2c_btn_call").style.display = 'none';
        }
}

function checkWorkTime() 
{
    $.jsonp({url: "http://" + c2chostlocation + "/kommunikator" + "/data.php?action=get_work_status&callback=jsonpCallbackStatus"});
    setTimeout(checkWorkTime, 60000);
}
////

c2c.init = function () {
    tsk_utils_log_info('[C2C] c2c.init()');

    c2c.audio_remote = document.createElement('audio');
    c2c.audio_remote.autoplay = "autoplay";
    c2c.audio_ringbacktone = document.createElement('audio');
    c2c.audio_ringbacktone.src = "http://" + c2chostlocation + "/c2c/sounds/ringbacktone.wav";
    c2c.audio_microphoneaccess = document.createElement('audio');
    c2c.audio_microphoneaccess.src = "http://" + c2chostlocation + "/c2c/sounds/microphoneaccess.wav";
    c2c.audio_ringbacktone.loop = true;

    /*document.write(    
	    "<object id='fakeVideoDisplay' classid='clsid:5C2C407B-09D9-449B-BB83-C39B7802A684' style='display:none'> </object>"+
	    "<object id='fakeLooper' classid='clsid:7082C446-54A8-4280-A18D-54143846211A' style='display:none'> </object>"+
	    "<object id='fakeSessionDescription' classid='clsid:DBA9F8E2-F9FB-47CF-8797-986A69A1CA9C' style='display:none'> </object>"+
	    "<object id='fakeNetTransport' classid='clsid:5A7D84EC-382C-4844-AB3A-9825DBE30DAE' style='display:none'> </object>"+
	    "<object id='fakePeerConnection' classid='clsid:56D10AD3-8F52-4AA4-854B-41F4D6F9CEA3' style='display:none'> </object>"
    );*/
    document.write(
        "<a href='#' class='komunikator_btn-info' id='c2c_btn_call' style='width: 145px; position:fixed; z-index:98'>call us &raquo;</a>"+
        
        "<div id='c2cNumPad' class='komunikator_well' style='display: none; padding: 70px 9px 10px 10px; width: 170px; position:fixed; z-index:97;'>"+
			"<table style='width: 100%; height: 100%'>"+
				"<tr>"+
					"<td>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='1' onclick='sipSendDTMF(1);'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='2' onclick='sipSendDTMF(2);'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='3' onclick='sipSendDTMF(3);'/>"+
					"</td>"+
				"</tr>"+
				"<tr>"+
					"<td>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='4' onclick='sipSendDTMF(4);'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='5' onclick='sipSendDTMF(5);'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='6' onclick='sipSendDTMF(6);'/>"+
					"</td>"+
				"</tr>"+
				"<tr>"+
					"<td>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='7' onclick='sipSendDTMF(7);'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='8' onclick='sipSendDTMF(8);'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='9' onclick='sipSendDTMF(9);'/>"+
					"</td>"+
				"</tr>"+
				"<tr>"+
					"<td>"+
						"<input type='button' style='width: 33%' class='komunikator_btn komunikator_btn_asterisk' value='*' onclick='sipSendDTMF(\"*\");'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='0' onclick='sipSendDTMF(10);'/>"+
						"<input type='button' style='width: 33%' class='komunikator_btn' value='#' onclick='sipSendDTMF(\"#\");'/>"+
					"</td>"+
				"</tr>"+
			"</table>"+
		"</div>"+
			"<audio id='dtmfTone' src='http://" + c2chostlocation + "/c2c/sounds/dtmf.wav' />"
    );
    document.write(
        "<div id='c2c_div_glass' style='"+
            "visibility:hidden;"+
            "z-index: 99;"+
            "position: fixed;"+
            "width: 100%;"+
            "height: 100%;"+
            "margin: 0;"+
            "padding: 0;"+
            "top: 0;"+
            "left: 0;"+
            "opacity: 0.8;"+
            "background-color: Gray'"+
        "></div>"
    );
	if (c2c.horizontal_align == "right") {	    
	    document.getElementById("c2c_btn_call").style.right = c2c.horizontal_margin + "px";
	}
	if (c2c.horizontal_align == "left") {	    
	    document.getElementById("c2c_btn_call").style.left = c2c.horizontal_margin + "px";
	}
	if (c2c.button_type == "vertical") {
		c2c_btn_call.style.webkitTransform = "rotate(-90deg)";
		c2c_btn_call.style.MozTransform = "rotate(-90deg)";
		if (c2c.vertical_align == 'top') {
			document.getElementById("c2c_btn_call").style.top = parseInt(c2c.vertical_margin) + parseInt(70) + "px";				
		}
		if (c2c.vertical_align == 'bottom') {
			document.getElementById("c2c_btn_call").style.bottom = parseInt(c2c.vertical_margin) + parseInt(70) + "px";				
		}		
	}
	if (c2c.button_type == "horizontal") {
		c2c_btn_call.style.webkitTransform = "rotate(0deg)";
		c2c_btn_call.style.MozTransform = "rotate(0deg)";
		if (c2c.vertical_align == 'top') {
			document.getElementById("c2c_btn_call").style.top = c2c.vertical_margin + "px";									
		}
		if (c2c.vertical_align == 'bottom') {
			document.getElementById("c2c_btn_call").style.bottom = c2c.vertical_margin + "px";									
		}		
	}
	if (c2c.vertical_align == 'top') {	
	    document.getElementById("c2cNumPad").style.top = c2c.vertical_margin + "px";   
	}
	if (c2c.vertical_align == 'bottom') {	
	    document.getElementById("c2cNumPad").style.bottom = c2c.vertical_margin + "px";   
	}
	
    c2c.div_glass = document.getElementById('c2c_div_glass');
    c2c.button_call = document.getElementById('c2c_btn_call');
    if(c2c.cls){
        c2c.button_call.setAttribute("class", c2c.cls);
    }
    c2c.button_call.innerHTML = c2c.button_call._innerHTML = c2c.text ? c2c.text : 'call us &raquo;';
    c2c.button_call.onclick = function () {
        if (!c2c.stack) {
            var websocket_proxy_url = (tsk_string_is_null_or_empty(c2c.config.websocket_proxy_url) && window.localStorage) ? window.localStorage.getItem('org.doubango.click2dial.admin.websocket_server_url') : c2c.config.websocket_proxy_url;
            var sip_outbound_proxy_url = (tsk_string_is_null_or_empty(c2c.config.sip_outbound_proxy_url) && window.localStorage) ? window.localStorage.getItem('org.doubango.click2dial.admin.sip_outboundproxy_url') : c2c.config.sip_outbound_proxy_url;
            var ice_servers = (tsk_string_is_null_or_empty(c2c.config.ice_servers) && window.localStorage) ? window.localStorage.getItem('org.doubango.click2dial.admin.ice_servers') : c2c.config.ice_servers;
            
            if(tsk_string_is_null_or_empty(websocket_proxy_url)){        
                var port = (false/*secure*/ ? 10062 : 10060) + (((new Date().getTime()) % /*FIXME:5*/1) * 1000);
                var host = window.location.host;;
                websocket_proxy_url = "ws://" + host + ":" + port;
            }
            
            c2c.stack = new SIPml.Stack({ realm: 'click2dial.org', impi: c2c.from, impu: 'sip:' + c2c.from + '@click2dial.org', password: 'mysecret',
                events_listener: { events: '*', listener: function (e) {
                    tsk_utils_log_info('[C2C] stack event = ' + e.type);

                    switch (e.type) {
                        case 'started':
                            {
                                c2c.started = true;
                                c2c.call();
                                break;
                            }
                        case 'stopped':
                        case 'stopping':
                            {
                                c2c.callSession = null;
                                c2c.audio_ringbacktone.pause();
                                c2c.started = false;
                                c2c.button_call.innerHTML = c2c.button_call._innerHTML;
                                break;
                            }
                        case 'm_permission_requested':
                            {
                                if(c2c.glass){
                                    c2c.div_glass.style.visibility = 'visible';
                                }
                                break;
                            }
                        case 'm_permission_accepted':
                        case 'm_permission_refused':
                            {
                                c2c.div_glass.style.visibility = 'hidden';
                                break;
                            }
                            break;
                    }
                }
                },
                enable_rtcweb_breaker: true,
                enable_click2call: true,
                websocket_proxy_url: websocket_proxy_url,
                outbound_proxy_url: sip_outbound_proxy_url,
	        ice_servers: ice_servers
            });
        }
        if (!c2c.started) {
            c2c.stack.start();
        }
        else{
            c2c.call();
        }
    };

    document.body.appendChild(c2c.button_call);
    document.body.appendChild(c2c.audio_remote);

    SIPml.init(
                function (e) {
                    c2c.button_call.style.visibility = 'visible';
                },
                function (e) {
                    c2c.button_call.innerHTML = e.description;
                }
            );
}

c2c.signup = function (name, email, successCallback, errorCallback) {
    var JSONText = JSON.stringify
        (
               {
                   action: 'req_account_add',
                   name: name,
                   email: email
               }
        );
    return c2c._send_data(JSONText, successCallback, errorCallback);
}

c2c.activate = function (code, email, successCallback, errorCallback) {
    var JSONText = JSON.stringify
    (
            {
                action: 'req_account_activate',
                email: email,
                code: code
            }
    );
    return c2c._send_data(JSONText, successCallback, errorCallback);
}

c2c.linkaddress = function (base_url, email) {
    return base_url + '/u/' + c2c.obfuscate(email);
}

c2c.signin = function (email, password, successCallback, errorCallback) {
    var JSONText = JSON.stringify
    (
            {
                action: 'req_account_info',
                email: email,
                auth_token: c2c.buildAuthToken(email, password)
            }
    );
    return c2c._send_data(JSONText, successCallback, errorCallback);
}

c2c.add_sip_address = function (email, password, address, successCallback, errorCallback) {
    var JSONText = JSON.stringify
    (
            {
                action: 'req_account_sip_add',
                email: email,
                auth_token: c2c.buildAuthToken(email, password),
                sip: {
                    address: address
                }
            }
    );
    return c2c._send_data(JSONText, successCallback, errorCallback);
}

c2c.delete_sip_address = function (email, password, id, successCallback, errorCallback) {
    var JSONText = JSON.stringify
    (
            {
                action: 'req_account_sip_delete',
                email: email,
                auth_token: c2c.buildAuthToken(email, password),
                id: id
            }
    );
    return c2c._send_data(JSONText, successCallback, errorCallback);
}

c2c.add_sip_caller = function (email, password, display_name, impu, impi, realm, password_sip, address_id, successCallback, errorCallback) {
    var JSONText = JSON.stringify
    (
            {
                action: 'req_account_sip_caller_add',
                email: email,
                auth_token: c2c.buildAuthToken(email, password),
                display_name: display_name,
                impu: impu,
                impi: impi,
                realm: realm,
                account_sip_id: address_id,
                ha1: c2c.buildHa1(impi, realm, password_sip)
            }
    );
    return c2c._send_data(JSONText, successCallback, errorCallback);
}

c2c.delete_sip_caller = function (email, password, id, successCallback, errorCallback) {
    var JSONText = JSON.stringify
    (
            {
                action: 'req_account_sip_caller_delete',
                email: email,
                auth_token: c2c.buildAuthToken(email, password),
                id: id
            }
    );
    return c2c._send_data(JSONText, successCallback, errorCallback);
}

c2c.call = function(from){
    tsk_utils_log_info('[C2C] c2c.call()');

    if(!c2c.stack){
        c2c.button_call.click();
        return;
    }

    if(c2c.callSession){
        c2c.callSession.hangup();
        return;
    }

    var from = (from || c2c.from);
    var to = (c2c.to || from);

    var call_listener = function(e){
        tsk_utils_log_info('[C2C] session event = ' + e.type);
        switch (e.type) {
//GEO INFO
	    case 'connected': c2c.callSession.dtmf(escape(JSON.stringify(about)));//,'application/dtmf-relay');//dtmf hack
//GEO INFO			
            case 'connecting': case 'connected':
                {
                    if (e.session == c2c.callSession) {
                        c2c.button_call.innerHTML = ((e.type === 'connecting') ? (c2c.calling_text?c2c.calling_text:'calling...') : (c2c.in_call_text?c2c.in_call_text:'in call'));
                        if (e.type == 'connecting') {
							c2c.audio_microphoneaccess.play();
						}
                        openKeyPad();                        
                    }
                    break;
                }
               case 'i_ao_request':
                {
                    if(e.session == c2c.callSession){
                        var code = e.getSipResponseCode();
                        if (code == 180 || code == 183) {
							c2c.audio_microphoneaccess.pause();
                            c2c.audio_ringbacktone.play();
                            c2c.button_call.innerHTML = (c2c.ringing_text?c2c.ringing_text:'ringing...');
                        }
                    }
                    break;
                }
               case 'm_early_media':
                {
                    if(e.session == c2c.callSession){
                        c2c.audio_ringbacktone.pause();
                        c2c.button_call.innerHTML = (c2c.early_media_text?c2c.early_media_text:'early media...');
                    }
                    break;
                }

            case 'terminating': case 'terminated':
                {
                    if (e.session == c2c.callSession) {
			var descr_key = e.description.toLowerCase().replace(/\W/g, '').replace(/\./g, ''); 
			console.log(e.description);
			console.log(descr_key);
			if (descr_key == 'busyhere') {alert('Все операторы заняты, перезвоните позже')};
			console.log(c2c[descr_key]);
                        c2c.button_call.innerHTML = (c2c[descr_key]?c2c[descr_key]: e.description.toLowerCase());
                        c2c.button_call.innerHTML = (c2c.call_terminating_text?c2c.call_terminating_text:'terminating...');                        
                        c2c.callSession = null;
                        c2c.audio_ringbacktone.pause();
                        c2c.div_glass.style.visibility = 'hidden';
                        window.setTimeout(function(){ c2c.button_call.innerHTML = c2c.button_call._innerHTML; }, 2000);
                        closeKeyPad();  
                    }
                    break;
                }
        }
    };

    
    c2c.callSession = c2c.stack.newSession('call-audio', {
                from: from,
                audio_remote: c2c.audio_remote,
                video_local: null,
                video_remote: null,
                events_listener: { events: '*', listener: call_listener },
                sip_caps: [
                                { name: '+g.oma.sip-im' },
                                { name: '+sip.ice' },
                                { name: 'language', value: '\"en,fr\"' }
                            ]
            });
    c2c.callSession.call(to);
}

c2c._send_data = function(data, successCallback, errorCallback){
    var httServUrl = (tsk_string_is_null_or_empty(c2c.config.http_service_url) && window.localStorage) ? window.localStorage.getItem('org.doubango.click2dial.admin.http_server_url') : c2c.config.http_service_url;    
    var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : (window.XDomainRequest ? window.XDomainRequest : new ActiveXObject("MSXML2.XMLHTTP.3.0"));

    if(tsk_string_is_null_or_empty(httServUrl)){   
        var port = (false/*secure*/ ? 10072 : 10070) + (((new Date().getTime()) % /*FIXME:5*/1) * 1000);
        var host = window.location.host;
        httServUrl = "http://" + host + ":" + port;

    }

    xmlhttp.onreadystatechange = function (e) {
        var JSONObject;
        try{
            if (this.readyState == this.DONE) {
                if (this.status == 200){
                    if(this.responseText != null){
                        tsk_utils_log_info('[C2C] RECV: ' + this.responseText);
                        JSONObject = JSON.parse(this.responseText);
                    }
                    if(successCallback){
                        successCallback({ status: this.status, statusText: this.statusText, JSONObject: JSONObject });
                    }
                }
                else{
                    if(errorCallback){
                        errorCallback({ status: this.status, statusText: tsk_string_is_null_or_empty(this.statusText) ? 'timeout' : this.statusText, JSONObject: JSONObject });
                    }
                }
            }
            
        }
        catch(ex){
            if(errorCallback){
                errorCallback({ status: 600, statusText: ex.toString(), JSONObject: null });
            }
        }
    }

    xmlhttp.open("POST", httServUrl, true);
    xmlhttp.setRequestHeader("Content-type", "application/json");

    tsk_utils_log_info('[C2C] SEND['+httServUrl+']: ' + 'not displayed'/*data*/);

    xmlhttp.send(data);
};
}
