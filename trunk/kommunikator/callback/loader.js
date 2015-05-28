if (komunikatorCallback)
{
    var iframe = document.createElement('iframe');
    var content = [
        "\<!DOCTYPE html\>",
        "\<html\>\<head\>\</head\>\<body\>\<script type=\"text/javascript\"\>",
        "var dcb_seconds=" + komunikatorCallback.timer.main + ";", //"+komunikatorCallback.+"
        "var call_back_id=" + komunikatorCallback.id + ";",
        "var dcb_sec=" + komunikatorCallback.timer.sec + ";",
        "var dcb_id_server='" + komunikatorCallback.server + "';",
        "var color_hex_before='" + komunikatorCallback.color.before + "';",
        "var color_hex_after='" + komunikatorCallback.color.after + "';",
        "var ua_seconds=" + komunikatorCallback.timer.ua + ";",
        "var number_page=" + komunikatorCallback.nPage + ";",
        "var time_popup_blocker=" + komunikatorCallback.timePopupBlocker + ";",
        "var specificurl='" + komunikatorCallback.url + "';",
        "var msg_on_user_visit='" + komunikatorCallback.msg.onUserVisit + "';",
        "var msg_on_user_exit='" + komunikatorCallback.msg.onUserExit + "';",
        "var msg_on_check_urlhistory='" + komunikatorCallback.msg.onCheckURLHistory + "';",
        "var msg_on_user_activity2='" + komunikatorCallback.msg.onUserActivity2 + "';",
        "var msg_on_metrica='" + komunikatorCallback.msg.onMetrica + "';",
        "var msg_on_specificpage='" + komunikatorCallback.msg.onSpecificPage + "';",
        "var on_user_visit=" + komunikatorCallback.trigger.onUserVisit + ";",
        "var on_user_exit=" + komunikatorCallback.trigger.onUserExit + ";",
        "var on_check_urlhistory=" + komunikatorCallback.trigger.onCheckURLHistory + ";",
        "var on_user_activity2=" + komunikatorCallback.trigger.onUserActivity2 + ";",
        "var on_metrica=" + komunikatorCallback.trigger.onMetrica + ";",
        "var on_check_numberpage=" + komunikatorCallback.trigger.onCheckNumberPage + ";",
        "(function(){var x=document.createElement('script');x.type='text/javascript';x.async=true;x.src='"+komunikatorCallback.server+"/callback/komunikator_callback.js';",
        "var xx=document.getElementsByTagName('script')[0];xx.parentNode.insertBefore(x,xx);})();",
        "\</script\>\</body\>\</html\>"];
    iframe.src = 'javascript:void(0)';
	iframe.id = 'komunikatorCallbackFrame';
    iframe.setAttribute("frameBorder", "-");
    iframe.setAttribute("scrolling", "no");
    iframe.setAttribute("seamless", "seamless");
    iframe.setAttribute("height", "0px");
    iframe.setAttribute("width", "0px");
    iframe.style.position = "fixed";
    iframe.style.top = "0px";
    iframe.style.bottom = "0px";
    iframe.style.right = "0px";
    iframe.style.left = "0px";
    iframe.style.padding = "0px 0px 0px 0px";
    iframe.style.zIndex = "999999";
    iframe.style.border = "0px";
    iframe.allowTransparency = true;
    iframe.style.backgroundColor = "transparent";
    iframe.style.display = "block";
    iframe.onload = function ()
    {
	var iframeWindow = iframe.contentWindow;
	var global = iframeWindow.MY_GLOBAL;
    };
    if (!document.body)
    document.createElement('body');
	document.body.appendChild(iframe)
    var doc = iframe.contentDocument || iframe.contentWindow.document;
    doc.open();
    doc.write(content.join("\n"));
    doc.close();
} else
{
    console.log('Komunikator Callback | ERROR: settings undefined');
}
