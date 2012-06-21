//Ext.require ('Ext.container.Viewport');
//Ext.require ('DIGT.LoginWindow');

Ext.Loader.setConfig({
    enabled : true//
//disableCaching: true,
});

if (window['DIGT']== undefined) DIGT = {};
DIGT.request = function(params,onSuccess,onFail){
    var onSuccessOrFail = function(result, request) {
        var result = Ext.decode(result.responseText);
        if (result.success==true && onSuccess) onSuccess(result);
        else
        if (result.success==false && onFail) onFail(result);
        else {/*
            if (action.failureType == 'server' && result.message) {
                alert(result.message);
            } else */
            alert('failure');
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
            Ext.getCmp('Digt.container').hide();if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('DIGT.LoginWindow').show();
        }
        )
};
DIGT.store_page = 200;
DIGT.refresh_time = 5000; 

Ext.application({
    name: 'DIGT',
    appFolder: 'DIGT/',
    launch: function() {

        Ext.create('Ext.container.Viewport', {
            layout: 'fit',
            items: [ {

                id:'Digt.container',
                xtype: 'container',
                hidden: true,
                layout: 'border',
                onShowFn: function(msg_login){
                    this.show();
                    Ext.getCmp('main_title').setTitle ('<h1 class="x-panel-header">DIGT PBX</h1><div>'+
                        '<p>'+DIGT.msg.login+': '+msg_login+'</p><a href="#" onclick="DIGT.logout();return false">'+ DIGT.msg.logout +'</a></div>')
                    //Ext.getCmp('cal_logs_tab').setActiveTab(0);
                },
                items: [{
                    region: 'north',
                    id:'main_title',
                    autoHeight: true,
                    border: false,
                    margins: '0 0 5 0'
                }, {
                    region: 'west',
                    collapsible: true,
                    title: 'Navigation',
                    width: 150
                // could use a TreePanel or AccordionLayout for navigational items
                },/* {
                    region: 'south',
                    title: 'South Panel',
                    collapsible: true,
                    html: 'Information goes here',
                    split: true,
                    height: 100,
                    minHeight: 100
                }, {
                    region: 'east',
                    title: 'East Panel',
                    collapsible: true,
                    split: true,
                    width: 150
                }, */{
                    region: 'center',
                    xtype: 'tabpanel',
                    activeTab: 0,
                    items: [
                    {
                        title: DIGT.msg.home,
                        id: 'cal_logs_tab',
                        //html: DIGT.msg.home,
                        //items: [{
                        xtype: 'tabpanel',
                        activeTab: 0, 
                        listeners:{
                            tabchange : function (t,s){
                                var cur_index  = t.items.indexOf(s);
                                switch  (cur_index ){
                                    case 0:{
                                       // Ext.StoreMgr.lookup('call_logs').guaranteeRange(0, DIGT.store_page-1);
                                       // alert(Ext.StoreMgr.lookup('call_logs').getCount());
                                        break;
                                    }
                                    case 1:{
                                        break;
                                    }
                                    case 2:{
                                        break;
                                    }
                                    case 3:{

                                        break;
                                    }
                                    case 4:{
                                        break;
                                    }
                                }
                            }
                        }, 

                        items:[ 
                        Ext.create('DIGT.Call_logs_Grid'),
                        Ext.create('DIGT.Active_calls_Grid')
                        ]
                    //}]
                    },{
                        title: DIGT.msg.attendant,
                        html: DIGT.msg.attendant
                    }]
                }]
            }]
        });

//        Ext.StoreMgr.lookup('call_logs').guaranteeRange(0, DIGT.store_page-1);

        Ext.TaskManager.start({
            run: function (){
                Ext.StoreMgr.each(function(item,index,length){
                    if (item.autorefresh)
                    item.load();
                })
            }
            ,
            interval:DIGT.refresh_time
        });

        DIGT.request(
        {
            action:'get_status'
        },
        function(result){
            Ext.getCmp('Digt.container').onShowFn(result['user']);
        },function(){
            if (Ext.getCmp('loginWindow')) Ext.getCmp('loginWindow').show(); else Ext.create('DIGT.LoginWindow').show()
        });
    }
});


