Ext.define('app.Proxy', {
    extend: 'Ext.data.proxy.Ajax',
    alias: 'appproxy',
    
    listeners:
    {
        exception: function (proxy, request, operation)
        {
            if (request.responseText != undefined)
            {
                // responseText was returned, decode it
                responseObj = Ext.decode(request.responseText,true);
                if (responseObj != null && responseObj.msg != undefined)
                {
                    // message was returned
                    Ext.Msg.alert('Error',responseObj.msg);
                }
                else
                {
                    // responseText was decoded, but no message sent
                    Ext.Msg.alert('Error','Unknown error: The server did not send any information about the error.');
                }
            }
            else
            {
                // no responseText sent
                Ext.Msg.alert('Error','Unknown error: Unable to understand the response from the server');
            }
        }
    }
});
