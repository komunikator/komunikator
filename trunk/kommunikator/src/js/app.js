Ext.Loader.setConfig({
    enabled : true//
//disableCaching: true,
});

//Ext.Loader.setPath('Ext.ux', 'ext/examples/ux/');
Ext.Loader.setPath('Ext.ux', 'js/ux/');
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

if (window['app'] == undefined) app = {};

app.pageSize = 50;//50;
app.date_format = 'd.m.y H:i:s';
//2010/08/11 06:33:00
app.php_date_format = 'Y/m/d H:i:s';
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


/*
 * BUG: SCROLLUPDATE
 * When updating a store of grid with the pagingscroller such that there's no need for a scrollbar, this scrollbar
 * cannot come back. When the scrollbar is hidden, its undocked from the grid panel. But the function that determines
 * if the scrollbar is needed again needs the scrollbar to be docked, resulting in an error (this.ownerCt undefined
 * in Ext.grid.PagingScroller.getSizeCalculation)
 *
 * http://www.sencha.com/forum/showthread.php?133050-4.0.0-Ext.grid.PagingScroller-this.ownerCt-undefined
 */
Ext.syncRequire('Ext.panel.Table');
Ext.override(Ext.panel.Table, {

    determineScrollbars:function(){
        var me=this,viewElDom,centerScrollWidth,centerClientWidth,scrollHeight,clientHeight;if(!me.collapsed&&me.view&&me.view.el){viewElDom=me.view.el.dom;centerScrollWidth=me.headerCt.getFullWidth();centerClientWidth=viewElDom.offsetWidth;if(me.verticalScroller&&me.verticalScroller.el){
// [SCROLLUPDATE]
// --
//        scrollHeight=me.verticalScroller.getSizeCalculation().height;
// ++
        scrollHeight=me.verticalScroller.getSizeCalculation(me).height;
//
        }else{scrollHeight=viewElDom.scrollHeight;}clientHeight=viewElDom.clientHeight;me.suspendLayout=true;me.scrollbarChanged=false;if(!me.collapsed&&scrollHeight>clientHeight){me.showVerticalScroller();}else{me.hideVerticalScroller();}if(!me.collapsed&&centerScrollWidth>(centerClientWidth+Ext.getScrollBarWidth()-2)){me.showHorizontalScroller();}else{me.hideHorizontalScroller();}me.suspendLayout=false;if(me.scrollbarChanged){me.doComponentLayout();}}
    }
});

// NO CHANGE 4.0.0, 4.01
Ext.syncRequire('Ext.grid.PagingScroller');
Ext.override(Ext.grid.PagingScroller,{
// [SCROLLUPDATE]
// --
//    getSizeCalculation: function() {
// ++
    getSizeCalculation: function(grid) {
//
        var owner = this.ownerCt,
// [SCROLLUPDATE]
// --
//        view   = owner.getView(),
// ++
        view = (owner && owner.getView()) || (grid && grid.getView()),
//
        store=this.store,dock=this.dock,elDom=this.el.dom,width=1,height=1;if(!this.rowHeight){this.rowHeight=view.el.down(view.getItemSelector()).getHeight(false,true);}height=store.getTotalCount()*this.rowHeight;if(isNaN(width)){width=1;}if(isNaN(height)){height=1;}return{width:width,height:height};
    }
});  