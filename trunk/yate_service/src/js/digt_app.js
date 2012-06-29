Ext.Loader.setConfig({
    enabled : true//
//disableCaching: true,
});

if (window['DIGT']== undefined) DIGT = {};

DIGT.store_page = 50;
DIGT.refresh_time = 5000; 

DIGT.request = function(params,onSuccess,onFail){
    var onSuccessOrFail = function(result, request) {
        if (result && result.responseText)
            result = Ext.decode(result.responseText);
        if (result && result.success==true && onSuccess) onSuccess(result);
        else
        if (result&& result.success==false && onFail) onFail(result);
        else {/*
            if (action.failureType == 'server' && result.message) {
                alert(result.message);
            } else */
            DIGT.fail_load_show()
        }
    }
    Ext.Ajax.request({
        url: 'data.php',
        method: 'post',
        params: params,
        success: onSuccessOrFail,
        failure: onSuccessOrFail
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
    if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('DIGT.LoginWindow').show()
};
DIGT.fail_load_show = function(){
    Ext.Msg.show({
        title: DIGT.msg.error?DIGT.msg.error:'Error',
        msg: DIGT.msg.fail_load?DIGT.msg.fail_load:'Fail load',
        buttons: Ext.Msg.OK,
        icon: Ext.Msg.ERROR
    });  
} 

DIGT.tabchange_listeners = 
    function (t,s){
        Ext.StoreMgr.each(function(item,index,length){
            if (item.autorefresh!=undefined) item.autorefresh = false; 
        })
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
            else DIGT.login();	
        },DIGT.login);
    }
});


