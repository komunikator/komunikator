Ext.define('app.Viewport_user', {
    extend: 'Ext.container.Viewport',
    style: 'padding : 2px 10px', // отступы: верх, низ - 2; право, лево - 10
    layout: 'border',
    items: [{
            region: 'north', // верх
            // autoHeight : true,
            border: false,
            margins: '0 0 5 0'
        }, {
            region: 'south', // низ
            title: '<div style="text-align : center"><p style="font-size : 8pt">' + app.msg.copyright + '</p></div>', // Телефонные системы®PBX © 2012
            border: false,
            margins: '10 0 10 0'
        }, {
            region: 'center', // центр
            // resizable : true,
            // split : true,
            // layout : 'anchor',
            layout: 'fit',
            // autoHeight : true,
            xtype: 'tabpanel',
            // layout : 'accordion',
            id: 'main_tabpanel',
            bodyStyle: 'padding : 15px', // отступы: верх, низ, право, лево - 15
            // style : 'padding : 2px',
            // defaults : {
            //     layout : 'fit'
            // },	
            // activeTab : 0,

            /*   listeners  : {
             
             afterrender : function() {
             var f = this.setActiveTab(0);
             // if (f && f.items) ;
             // grid.ownerCt.layout.setActiveItem(grid.ownerCt.items.indexOf(grid));
             // console.log(
             f.items.items[0].fireEvent('activate', f.items.items[0]);  // f.setActiveItem(0);
             },
             
             tabchange   : function(c, f, o) {
             if (f && f.items) {
             f.getLayout().setActiveItem(0);
             f.items.items[0].fireEvent('activate', f.items.items[0]);  // f.setActiveItem(0);
             }
             }
             
             },*/

        items: [
                Ext.create('app.Card_Panel', {
                    title: app.msg.private_office, // Личный кабинет
                    items: [
                        Ext.create('app.module.Call_logs_Grid', {
                            title: app.msg.call_logs  // История звонков
                        }),
                        {
                            title: app.msg.update_password, // изменить пароль

                            handler: function() {
                                Ext.create('app.UpdatePassword').show();

                            }
                        },
                        {
                            title: app.msg.forward, // Переадресация

                            handler: function() {
                                Ext.create('app.Call_Forwarding').show();

                            }
                        },
                    ]
         }),
            ]
        }],
    initComponent: function() {
        this.items[0].title =
                // '<div class="x-box-inner" style="padding-left: 20px; padding-right: 20px; height: 60px; background-color: #D5EAF3">'+
                '<div class="x-box-inner" style="padding-left: 20px; padding-right: 20px; height: 60px">' +
                '<img class="logo" src="js/app/images/logo.png" height="60px" alt="TS" border="0" align="left">' +
                '<p align="right"><a href="#" onclick="app.logout(); return false">' + app.msg.logout + '</a></p>' +
                '<p align="right">' + app.msg.user + ': ' + '</p>' +
                // '<p align="right"><a target="_blank" href="http://ats.digt.local/bugtracker/">BUG TRACKER</a></p>'+
                '</div>';

        this.callParent(arguments);

        Ext.TaskManager.start({
            run: function() {
                Ext.StoreMgr.each(function(item, index, length) {
                    if (item.storeId == 'statistic') {
                        if (item.autorefresh)
                            item.load();
                        // console.log(item.storeId + ":item.autorefresh-:" + item.autorefresh);
                    }
                    ;
                    if (Ext.getCmp(item.storeId + '_grid'))
                        if (app.active_store == item.storeId && item.autorefresh && !this.dirtyMark)
                            item.load();
                })
            },
            interval: app.refreshTime
        });
    }
});



