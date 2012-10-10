Ext.Loader.setConfig({
    enabled : true//
//disableCaching: true,
});

Ext.Loader.setPath('Ext.ux', 'ext/examples/ux/');
Ext.Loader.setPath('app', 'js/app');


Ext.require([
    'app.Loader'
    ]);

/*
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.tip.QuickTipManager',
    'Ext.ux.LiveSearchGridPanel'
]);
*/

if (window['app']== undefined) app = {};

app.pageSize = 50;
app.refreshTime = 5000; 

Ext.Ajax.on('requestexception', function (conn, response, options) {
    if (response.status === 403) {
        window.location = 'login';
    }
});

Ext.Ajax.on ('requestcomplete', function(conn, response, options) {
    try {
        var jsonData = Ext.decode(response.responseText);
        if (jsonData.message) 
            app.msgShow(jsonData.message,jsonData.success?'info':'error');
    }
    catch (err) {
        app.msgShow(response.responseText);
    }
});

app.onSuccessOrFail = function(result, request,onSuccess,onFail) {
    if (result && result.responseText)
        result = Ext.decode(result.responseText);
    if (result && result.success==true && onSuccess) onSuccess(result);
    else
    if (result&& result.success==false && onFail) onFail(result);
    else 
        app.msgShow()
}

app.request = function(params,onSuccess,onFail){
    Ext.Ajax.request({
        url: 'data.php',
        method: 'post',
        params: params,
        success: function(result, request){
            app.onSuccessOrFail(result, request,onSuccess,onFail)
        },
        failure: function(result, request){
            app.onSuccessOrFail(result, request,onSuccess,onFail)
        }
    });
}

app.logout = function(){
    return app.request(
    {
        action:'logout'
    },
    function(){
        window.location.reload();
    //Ext.getCmp('app.container').hide();if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('app.LoginWindow').show();
    }
    )
};

app.login = function(){
    Ext.Msg.hide();
    if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('app.LoginWindow').show()
};
app.msgShow = function(msg,type){
    Ext.Msg.show({
        title: (type=='info')?app.msg.info?app.msg.info:'Info':app.msg.error?app.msg.error:'Error',
        msg: msg?msg:(app.msg.fail_load?app.msg.fail_load:'Fail load'),
        buttons: Ext.Msg.OK,
        icon: (type=='info')?Ext.Msg.INFO:Ext.Msg.ERROR
    });  
} 

app.main = function (msg_login){
    Ext.create('app.Viewport',{
        user_name:msg_login
    });
}

Ext.application({
    name: 'app',
    appFolder: 'js/app',
    //autoCreateViewport: true,
    launch: function() {

        app.request(
        {
            action:'get_status'
        },
        function(result){
            if (result['user'])
                app.main(result['user']);
            else
                app.login();
        },app.login);
    }/*,
    controllers: ['Controller']*/

});

Ext.override(Ext.LoadMask, { 
    onHide: function() { this.callParent(); }
});

