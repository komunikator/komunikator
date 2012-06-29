Ext.define('DIGT.Viewport', {
    extend : 'Ext.container.Viewport',
    layout: 'fit',
    items: [ {
        id:'Digt.container',
        xtype: 'container',
        //hidden: true,
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
        // could use a TreePanel or AccordionLayout for navigational items
        },{
            region: 'center',
            xtype: 'tabpanel',
            //bodyStyle:'padding:5px;',
            activeTab: 0,
            listeners:{
                tabchange : DIGT.tabchange_listeners
            }, 
            items: [
            {
                title: DIGT.msg.home,
                id: 'cal_logs_tab',
                //html: DIGT.msg.home,
                //items: [{
                xtype: 'tabpanel',
                activeTab: 0, 
                listeners:{
                    tabchange : DIGT.tabchange_listeners
                }, 

                items:[ 
                Ext.create('DIGT.Call_logs_Grid'),
                Ext.create('DIGT.Active_calls_Grid')
                ]
            //}]
            },{
                title: DIGT.msg.attendant,
                html: DIGT.msg.attendant
            },Ext.create('DIGT.Extensions_Grid')]
        }]
    }],
    initComponent : function () {
        this.items[0].items[0].title =
        '<h1 class="x-panel-header">DIGT PBX</h1><div style="padding-left: 40px;">'+
        '<p>'+DIGT.msg.login+': '+this.user_name+'</p><a href="#" onclick="DIGT.logout();return false">'+ DIGT.msg.logout +'</a></div>';
        this.callParent(arguments);
  
      Ext.TaskManager.start({
            run: function (){
                Ext.StoreMgr.each(function(item,index,length){
                    if (item.autorefresh) item.load();
                })
            }
            ,
            interval:DIGT.refresh_time
        });
    }    
});