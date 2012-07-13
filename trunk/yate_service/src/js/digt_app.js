Ext.Loader.setConfig({
    enabled : true//
//disableCaching: true,
});

Ext.Loader.setPath('Ext.ux', 'ext/examples/ux/');
Ext.Loader.setPath('DIGT', 'js/DIGT');


Ext.require([
    'DIGT.Loader'
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

if (window['DIGT']== undefined) DIGT = {};

DIGT.pageSize = 50;
DIGT.refreshTime = 5000; 

Ext.Ajax.on('requestexception', function (conn, response, options) {
    if (response.status === 403) {
        window.location = 'login';
    }
});

Ext.Ajax.on ('requestcomplete', function(conn, response, options) {
    try {
        var jsonData = Ext.decode(response.responseText);
        if (jsonData.message) 
            DIGT.fail_load_show(jsonData.message,jsonData.success?'info':'error');
    }
    catch (err) {
        DIGT.fail_load_show(response.responseText);
    }
});

DIGT.onSuccessOrFail = function(result, request,onSuccess,onFail) {
    if (result && result.responseText)
        result = Ext.decode(result.responseText);
    if (result && result.success==true && onSuccess) onSuccess(result);
    else
    if (result&& result.success==false && onFail) onFail(result);
    else 
        DIGT.fail_load_show()
}

DIGT.request = function(params,onSuccess,onFail){
    Ext.Ajax.request({
        url: 'data.php',
        method: 'post',
        params: params,
        success: function(result, request){
            DIGT.onSuccessOrFail(result, request,onSuccess,onFail)
        },
        failure: function(result, request){
            DIGT.onSuccessOrFail(result, request,onSuccess,onFail)
        }
    });
}

DIGT.logout = function(){
    return DIGT.request(
    {
        action:'logout'
    },
    function(){
        window.location.reload();
    //Ext.getCmp('Digt.container').hide();if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('DIGT.LoginWindow').show();
    }
    )
};

DIGT.login = function(){
    Ext.Msg.hide();
    if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('DIGT.LoginWindow').show()
};
DIGT.fail_load_show = function(msg,type){
    Ext.Msg.show({
        title: (type=='info')?DIGT.msg.info?DIGT.msg.info:'Info':DIGT.msg.error?DIGT.msg.error:'Error',
        msg: msg?msg:(DIGT.msg.fail_load?DIGT.msg.fail_load:'Fail load'),
        buttons: Ext.Msg.OK,
        icon: (type=='info')?Ext.Msg.INFO:Ext.Msg.ERROR
    });  
} 

DIGT.tabchange_listeners = 
    function (t,i){
        Ext.StoreMgr.each(function(item,index,length){
            if (item.autorefresh!=undefined) item.autorefresh = false; 
        })
        var s = i.items.items[0];
        if (s.store) {
            s.store.load();
            if (s.store.autorefresh!=undefined) s.store.autorefresh = true;
        }
    //var cur_index  = t.items.indexOf(s);
    }

DIGT.main = function (msg_login){
    Ext.create('DIGT.Viewport',{
        user_name:msg_login
    });
}

Ext.application({
    name: 'DIGT',
    appFolder: 'js/DIGT',
    launch: function() {

        DIGT.request(
        {
            action:'get_status'
        },
        function(result){
            if (result['user'])
                DIGT.main(result['user']);
            else
                DIGT.login();
        },DIGT.login);
    }
});


