Ext.Loader.setConfig({
    enabled: true//
//disableCaching: true,
});

//Ext.Loader.setPath('Ext.ux', 'ext/examples/ux/');
Ext.Loader.setPath('Ext.ux', 'js/ux');
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

if (window['app'] == undefined)
    app = {};

app.pageSize = 50;//50;//50;
app.date_format = 'd.m.y H:i:s';
//2010/08/11 06:33:00
app.php_date_format = 'Y/m/d H:i:s';
app.refreshTime = 5000;
app.critical_cpu = 10;

Ext.Ajax.timeout = 600000;  //10min

Ext.Ajax.on('requestexception', function(conn, response, options) {
    if (response.status === 403) {
        window.location = 'login';
    }
});

Ext.Ajax.on('requestcomplete', function(conn, response, options) {
    try {
        var jsonData = Ext.decode(response.responseText);
        if (jsonData.message)
        {
            var cb = Ext.emptyFn;
            if (jsonData.message == 'session_failed')
                cb = function() {
                    window.location.reload();
                };
            app.msgShow(app.msg[jsonData.message] ? app.msg[jsonData.message] : jsonData.message, jsonData.success ? 'info' : 'error', cb);
        }
    }
    catch (err) {
        app.msgShow(response.responseText);
    }
});

app.onSuccessOrFail = function(result, request, onSuccess, onFail) {
    if (result && result.responseText)
        result = Ext.decode(result.responseText);
    if (result && result.success == true && onSuccess)
        onSuccess(result);
    else
    if (result && result.success == false && onFail)
        onFail(result);
    else
        app.msgShow()
}

app.request = function(params, onSuccess, onFail) {
    Ext.Ajax.request({
        url: 'data.php',
        method: 'post',
        params: params,
        success: function(result, request) {
            app.onSuccessOrFail(result, request, onSuccess, onFail)
        },
        failure: function(result, request) {
            app.onSuccessOrFail(result, request, onSuccess, onFail)
        }
    });
}

app.logout = function() {
    return app.request(
            {
                action: 'logout'
            },
    function() {
        window.location.reload();
        //Ext.getCmp('app.container').hide();if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('app.LoginWindow').show();
    }
    )
};

app.login = function() {

    /* if (Ext.getCmp('loginWindow'))
     Ext.getCmp('loginWindow').show();
     else*/
    Ext.create('app.LoginWindow').show();
    Ext.Msg.hide()
};
app.msgShow = function(msg, type, cb) {
    Ext.Msg.show({
        title: (type == 'info') ? app.msg.info ? app.msg.info : 'Info' : app.msg.error ? app.msg.error : 'Error',
        msg: msg ? msg : (app.msg.fail_load ? app.msg.fail_load : 'Fail load'),
        buttons: Ext.Msg.OK,
        fn: cb ? function() {
            cb();
        } : Ext.emptyFn,
        icon: (type == 'info') ? Ext.Msg.INFO : Ext.Msg.ERROR
    });
}

app.main = function(user, extension) {
    if (user) { 
        Ext.create('app.Viewport', {
            user_name: user
        });
    }
    if (extension) 
    {
        Ext.create('app.Viewport_user', {
            extension_name: extension
        });
    }
}

Ext.application({
    name: 'app',
    appFolder: 'js/app',
    //autoCreateViewport: true,
    launch: function() {

        Ext.MessageBox.bottomTb.items.each(function(b) {
            b.setText(Ext.MessageBox.buttonText[b.itemId]);
        });
        Ext.view.AbstractView.prototype.loadingText = Ext.view.AbstractView.prototype.msg;

        app.request(
                {
                    action: 'get_status'
                },
        function(result) {
            if (result['user'])
                app.main(result['user'], null);
            if (result['extension'])
                app.main(null, result['extension']);
    
        },
                app.login);
    }


    /*else
     app.login();
     }, app.login);
     }*/
    /*,
     controllers: ['Controller']*/

});

/*
 Ext.override(Ext.LoadMask, { 
 onHide: function() { 
 this.callParent(); 
 }
 });
 
 */
/*
 Ext.override(Ext.grid.Scroller, {
 onAdded: function() {
 this.callParent(arguments);
 var me = this;
 if (me.scrollEl) {
 me.mun(me.scrollEl, 'scroll', me.onElScroll, me);
 me.mon(me.scrollEl, 'scroll', me.onElScroll, me);
 }
 }
 });
 
 
 // http://www.sencha.com/forum/showthread.php?133050-4.0.0-Ext.grid.PagingScroller-this.ownerCt-undefined
 
 
 Ext.syncRequire('Ext.panel.Table');
 Ext.override(Ext.panel.Table, {
 
 determineScrollbars:function(){
 var me=this,viewElDom,centerScrollWidth,centerClientWidth,scrollHeight,clientHeight;if(!me.collapsed&&me.view&&me.view.el){
 viewElDom=me.view.el.dom;centerScrollWidth=me.headerCt.getFullWidth();centerClientWidth=viewElDom.offsetWidth;if(me.verticalScroller&&me.verticalScroller.el){
 // [SCROLLUPDATE]
 // --
 //        scrollHeight=me.verticalScroller.getSizeCalculation().height;
 // ++
 scrollHeight=me.verticalScroller.getSizeCalculation(me).height;
 //
 }else{
 scrollHeight=viewElDom.scrollHeight;
 }clientHeight=viewElDom.clientHeight;me.suspendLayout=true;me.scrollbarChanged=false;if(!me.collapsed&&scrollHeight>clientHeight){
 me.showVerticalScroller();
 }else{
 me.hideVerticalScroller();
 }if(!me.collapsed&&centerScrollWidth>(centerClientWidth+Ext.getScrollBarWidth()-2)){
 me.showHorizontalScroller();
 }else{
 me.hideHorizontalScroller();
 }me.suspendLayout=false;if(me.scrollbarChanged){
 me.doComponentLayout();
 }
 }
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
 store=this.store,dock=this.dock,elDom=this.el.dom,width=1,height=1;if(!this.rowHeight){
 this.rowHeight=view.el.down(view.getItemSelector()).getHeight(false,true);
 }height=store.getTotalCount()*this.rowHeight;if(isNaN(width)){
 width=1;
 }if(isNaN(height)){
 height=1;
 }return{
 width:width,
 height:height
 };
 }
 });
 */

app.get_array_key = function(arr, value) {
    if (!arr)
        return null;
    if (!value)
        return null;
    for (var key in arr)
        if (arr[key] == value)
            return key;
};

app.support_audio = function() {
    var a = document.createElement('audio');
    return !!(a.canPlayType && a.canPlayType('audio/mpeg;').replace(/no/, ''));
};

app.format_msg = function(s, p) {
    if (!Ext.isArray(p))
        p = [p];
    for (var k in p)
        s = s.replace('{' + k + '}', p[k])
    return s;
}
app.checked_render = function(value) {
    value = (value == '1' || value == 'true') ? true : false;
    var cssPrefix = Ext.baseCSSPrefix,
            cls = [cssPrefix + 'grid-checkheader'];

    if (value) {
        cls.push(cssPrefix + 'grid-checkheader-checked');
    }
    return '<div class="' + cls.join(' ') + '">&#160;</div>';
}


app.online_offline_renderer = function(value, metadata, record, rowIndex, colIndex, store) {
    if (colIndex == 1) {
        if (value == 'online')
        {
            // metadata.tdCls = 'icon-online';
            metadata.tdAttr = 'data-qtip="' + app.msg['registered'] + '"';
        }
        if (value == 'offline')
        {
            metadata.tdAttr = 'data-qtip="' + app.msg['unregistered'] + '"';
            //  return '<img src="js/app/images/online.gif">';
        }
        if (value == 'busy')
        {
            metadata.tdAttr = 'data-qtip="' + app.msg[value] + '"';
        }
        if (value == 0)
        {
            return '&nbsp';
        }
        return '<img src="js/app/images/' + value + '.gif">';
    }
    return   value;
    ;
}
app.get_Source_Combo = function(cfg) {
    var obj = {
        xtype: 'combo',
        store: Ext.StoreMgr.lookup('sources') ?
                Ext.StoreMgr.lookup('sources') :
                Ext.create('app.Store', {
            fields: ['id', 'name'],
            storeId: 'sources'
        }),
        //queryCaching: false,
        tpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',
                '<div class="x-boundlist-item" data-qtip="{[app.source_tip(values)]}">{[app.msg[values.name]?app.msg[values.name]:values.name]}</div>',
                '</tpl>'
                ),
        // template for the content inside text field
        displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',
                '{[app.msg[values.name]?app.msg[values.name]:values.name]}',
                '</tpl>'
                ),
        editable: true,
        displayField: 'name',
        valueField: 'name',
        queryMode: 'local',
        listeners: {
            afterrender: function() {
                this.store.load();
            }
        }
    }
    if (cfg)
        for (var key in cfg)
            obj[key] = cfg[key]
    return obj;
}

app.set_autorefresh = function(s, active) {
    if (s && s.store) {
        //console.log(s.store.storeId+':'+s.store.autorefresh);
        if (active)
        {
            s.store.load();
            if (s.store.storeId != 'statistic')
                app.active_store = s.store.storeId;
        }
        ;
        if (s.store.autorefresh != undefined) {
            s.store.autorefresh = active;
        }
    }
};
app.msg_renderer = function(value) {
    if (app.msg[value])
        value = app.msg[value];
    return value
}


app.dhms = function(s) {
    var f = 'hh:mm:ss'
    var d = h = m = 0;
    switch (true) {
        case (s > 86400):
            d = Math.floor(s / 86400);
            s -= d * 86400;
        case (s > 3600):
            h = Math.floor(s / 3600);
            s -= h * 3600;
        case (s > 60):
            m = Math.floor(s / 60);
            s -= m * 60;
    }
    if (f != null) {
        var f = f.replace('dd', (d < 10) ? "0" + d : d);
        f = f.replace('d', d);
        f = f.replace('hh', (h < 10) ? "0" + h : h);
        f = f.replace('h', h);
        f = f.replace('mm', (m < 10) ? "0" + m : m);
        f = f.replace('m', m);
        f = f.replace('ss', (s < 10) ? "0" + s : s);
        f = f.replace('s', s);
    }
    else {
        f = d + ':' + h + ':' + m + ':' + s;
    }
    return f
}
//alert(Ext.LoadMask.prototype.msg);             
//Ext.view.AbstractView.prototype.loadingText = Ext.LoadMask.prototype.msg;
   