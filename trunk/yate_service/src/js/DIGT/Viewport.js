Ext.define('DIGT.Viewport', {
    extend : 'Ext.container.Viewport',
    style:'padding:2px;',
    layout: 'border',
    items: [{
        region: 'north',
        autoHeight: true,
        border: false,
        margins: '0 0 5 0'
    }, {
        region: 'west',
        collapsible: true,
        title: 'Navigation',
        width: 150
    },{
        region: 'center',
        xtype: 'tabpanel',
	id: 'main_tabpanel',
        bodyStyle:'padding:5px;',
        //style:'padding:2px;',
	defaults:{layout: 'fit'},	
        activeTab: 0,
        listeners:{
            tabchange : DIGT.tabchange_listeners
        }, 
        items: [
        {
            title: DIGT.msg.home,
            //layout: 'fit',
            id: 'home_tab',
            xtype: 'tabpanel',
	    defaults:{layout: 'fit'},	
            activeTab: 0,
	    layoutOnTabChange: true, 
            listeners:{
                tabchange : DIGT.tabchange_listeners
            }, 
            items:[
            { 
               title:DIGT.msg.call_logs,
               items:Ext.create('DIGT.Call_logs_Grid')
            },
           {   
                title:DIGT.msg.active_calls,
                items: Ext.create('DIGT.Active_calls_Grid')
            }
            ] 
        },/*{ 
            title: DIGT.msg.attendant,layout: 'anchor', 
            items: [{height:100,border: false,html:'test message'},Ext.create('DIGT.Prompts_Grid'/*,{height:300})]
        }*/
	Ext.create('DIGT.Attendant'),
	{
	    title:DIGT.msg.groups,
	    items:Ext.create('DIGT.Groups_Grid')
	},
        {
            title:DIGT.msg.extensions, 
            items:Ext.create('DIGT.Extensions_Grid')
        },
	{
	    title:DIGT.msg.gateways,
	    items:Ext.create('DIGT.Gateways_Grid')
	}
	]
    }],
    initComponent : function () {
        this.items[0].title =
        '<h1 class="x-panel-header">DIGT PBX</h1><div style="padding-left: 40px;">'+
        '<p>'+DIGT.msg.user+': <a href="#" onclick="DIGT.logout();return false" title="'+ DIGT.msg.changepassword +'">'+
        this.user_name+'</a></p><a href="#" onclick="DIGT.logout();return false">'+ DIGT.msg.logout +'</a></div>';
/*
        this.listeners = {
            afterrender: function(){
        	Ext.getCmp('main_tabpanel').setActiveTab(0);
                this.un('afterrender', arguments.callee); 
        	}
	}
*/
        this.callParent(arguments);
        Ext.TaskManager.start({
            run: function (){
                Ext.StoreMgr.each(function(item,index,length){
                    if (item.autorefresh && !this.dirtyMark) item.load();
                })
            }
            ,
            interval:DIGT.refreshTime
        });
    }    
});