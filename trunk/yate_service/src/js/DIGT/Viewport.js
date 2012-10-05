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
        region: 'south',
        title:'<center>Copyright 2012 DIGT PBX</center>',
        border: false,
        margins: '10 10 10 0'
    }, {
        region: 'west',
        collapsible: true,
        title: 'Статус АТС',
        autoHeight: true,
        width: 240,
        //split: true,
        items: [ /*
        {
            //bodyStyle: 'background:transparent;',

            //headerCfg
            headerCfg: {
                tag: 'div',
                cls: 'x-panel-header',
                children: [
                { 
                    tag: 'div', 
                    cls: 'panel_header_main', 
                    'html': 'Shopping Cart' 
                }
                ]
                }
            //title:DIGT.msg.active_calls,
        }, */
        Ext.create('DIGT.Mini_active_calls_Grid')
        ]
	
    },{
        region: 'center',
        //resizable: true,
        //split: true,
        //layout: 'anchor',
        layout: 'fit',
        xtype: 'tabpanel',
        //layout:'accordion',
        id: 'main_tabpanel',
        bodyStyle:'padding:15px;',
        //style:'padding:2px;',
        defaults:{
            layout: 'fit'
        },	
        activeTab: 0,
        items: [
        Ext.create('DIGT.Attendant_Panel'),
        //Ext.create('DIGT.Extensions_Panel'),
        Ext.create('DIGT.Card_Panel',{
            title: DIGT.msg.extensions,
            items: [
            {
                title:'<center>'+DIGT.msg.groups+'</center>',
                items:Ext.create('DIGT.Groups_Grid')
            }, 
            {
                title:'<center>'+DIGT.msg.extensions+'</center>',
                items:Ext.create('DIGT.Extensions_Grid')
            }
            ]

        }),
        /*{
            title:DIGT.msg.groups,
            items:Ext.create('DIGT.Groups_Grid')
        }, 
        {
            title:DIGT.msg.extensions, 
            items:Ext.create('DIGT.Extensions_Grid')
        },  */
        Ext.create('DIGT.Card_Panel',{
            title: 'DIGT.msg.telephony',
            items: [
            {
                title:DIGT.msg.routing_rules,
                items:Ext.create('DIGT.DID_Grid')
            },
            {
                title:DIGT.msg.dial_plans,
                items:Ext.create('DIGT.Dial_plans_Grid')
            },
            {
                title:DIGT.msg.conferences, 
                items:Ext.create('DIGT.Conferences_Grid')
            }, 
            {
                title:DIGT.msg.gateways,
                items:Ext.create('DIGT.Gateways_Grid')
            }
            ]
        }),
        Ext.create('DIGT.Card_Panel',{
            title: DIGT.msg.music_on_hold,
            items: [
            {
                title:DIGT.msg.playlist,
                items:Ext.create('DIGT.Playlist_Grid')
            }, 
            {
                title:DIGT.msg.music_on_hold, 
                items:Ext.create('DIGT.Music_On_Hold_Grid')
            }
            ]

        }),

        Ext.create('DIGT.Card_Panel',{
            title:DIGT.msg.call_logs,
            items: [
            {
                title:DIGT.msg.call_logs,
                items:Ext.create('DIGT.Call_logs_Grid')
            },
            {
                title:DIGT.msg.active_calls,
                items:Ext.create('DIGT.Active_calls_Grid')
            }
            ]
        }),
        Ext.create('DIGT.Card_Panel',{
            title:DIGT.msg.settings,
            items: [
            {
                title:'DIGT.msg.settings',
                items:Ext.create('DIGT.Settings_Grid')
            },
            {
                title:'DIGT.msg.notification_settings',
                items:Ext.create('DIGT.Ntn_Settings_Grid')
            }
            ]
        }),
        {
            title:DIGT.msg.users,
            items:Ext.create('DIGT.Users_Grid')
        }
        /*
        {
            title: DIGT.msg.home,
            //layout: 'fit',
            id: 'home_tab',
            xtype: 'tabpanel',
            defaults:{
                layout: 'fit'
            },	
            activeTab: 0,
            layoutOnTabChange: true, 
            items:[
            { 
                title:DIGT.msg.call_logs,
                items:Ext.create('DIGT.Call_logs_Grid')
            }
            ] 
        } */
        //{ 
        //    title: DIGT.msg.attendant,layout: 'anchor', 
        //    items: [{height:100,border: false,html:'test message'},Ext.create('DIGT.Prompts_Grid'/*,{height:300})]
        //}

        ]
    }],  
    initComponent : function () {
        this.items[0].title =
        '<h1 class="x-panel-header" style="text-align:center;">DIGT PBX</h1><div style="padding-left: 40px;">'+
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