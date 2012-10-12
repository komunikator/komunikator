Ext.define('app.Viewport', {
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
        title:'<center>Copyright 2012 app PBX</center>',
        border: false,
        margins: '10 10 10 0'
    }, {
        region: 'west',
        collapsible: true,
        title: 'Статус АТС',
        autoHeight: true,
        width: 240,
        //split: true,
        items: [  /*
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
            //title:app.msg.active_calls,
        }, */
        Ext.create('app.Mini_active_calls_Grid')
        ]
	
    },{
        region: 'center',
        //resizable: true,
        //split: true,
        //layout: 'anchor',
        layout: 'fit',
        autoHeight: true,
        xtype: 'tabpanel',
        //layout:'accordion',
        id: 'main_tabpanel',
        bodyStyle:'padding:15px;',
        //style:'padding:2px;',
        //        defaults:{
        //            layout: 'fit'
        //        },	
        //activeTab: 0, 
        items: [
                                             
/*        Ext.create('app.Attendant_Panel'),*/
        //Ext.create('app.Extensions_Panel'),   
        Ext.create('app.Card_Panel',{
	    title: app.msg.attendant,
	    items: [
	    Ext.create('app.Prompts_Panel',{
		title:'<center>'+'app.msg.prompts'+'</center>'
	    }),
	    Ext.create('app.Keys_Grid',{
		title:'app.msg.key'
	    }),
	    Ext.create('app.Time_Frames_Grid',{
		title:'app.msg.timeframes'
	    })
	    ]
	}),
        Ext.create('app.Card_Panel',{
            title: app.msg.extensions,
            items: [
            Ext.create('app.Extensions_Grid',{
                title:app.msg.extensions
            }),
            Ext.create('app.Groups_Grid',{
                title:app.msg.groups
            })
            ]

        }),
        Ext.create('app.Card_Panel',{
            title: 'app.msg.telephony',
            items: [
            Ext.create('app.DID_Grid',{
                title:app.msg.routing_rules
            }),
            Ext.create('app.Dial_plans_Grid',{
                title:app.msg.dial_plans
            }),
            Ext.create('app.Conferences_Grid',{
                title:app.msg.conferences
            }),
            Ext.create('app.Gateways_Grid',{
                title:app.msg.gateways
            })
            ]
        }),
        Ext.create('app.Card_Panel',{
            title: app.msg.music_on_hold,
            items: [         
            Ext.create('app.Playlist_Grid',{
                title:app.msg.playlist
            }), 
            Ext.create('app.Music_On_Hold_Grid',{
                title:app.msg.music_on_hold
            })     
            ]

        }),
        Ext.create('app.Card_Panel',{
            title:app.msg.call_logs,
            items: [
            Ext.create('app.Call_logs_Grid',{
                title:app.msg.call_logs
            }),
            Ext.create('app.Active_calls_Grid',{
                title:app.msg.active_calls
            })
            ]
        }),     
        Ext.create('app.Card_Panel',{
            title:app.msg.settings,
            items: [
            Ext.create('app.Settings_Grid',{
                title:'app.msg.settings'
            }),
            Ext.create('app.Ntn_Settings_Grid',{
                title:'app.msg.notification_settings'
            })
            ]
        }),   
        {
            title:app.msg.users,
            items:Ext.create('app.Users_Grid')
        }   
        //{ 
        //    title: app.msg.attendant,layout: 'anchor', 
        //    items: [{height:100,border: false,html:'test message'},Ext.create('app.Prompts_Grid'/*,{height:300})]
        //}
        ]  
    }],  
    initComponent : function () {
        this.items[0].title =
        '<h1 class="x-panel-header" style="text-align:center;">app PBX</h1><div style="padding-left: 40px;">'+
        '<p>'+app.msg.user+': <a href="#" onclick="app.logout();return false" title="'+ app.msg.changepassword +'">'+
        this.user_name+'</a></p><a href="#" onclick="app.logout();return false">'+ app.msg.logout +'</a></div>';
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
            interval:app.refreshTime
        });
    }    
});