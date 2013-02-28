/*
Ext.require([
    'Ext.ux.CheckColumn'
    ]);
*/
app.Loader.load('js/ux/css/CheckHeader.css');
 
Ext.define('app.module.Gateways_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        autorefresh : false,
        fields : ['id','status','enabled', 'gateway',  'server', 'username', 'password', 'description', 'protocol','ip_transport','authname', 'domain','callerid'],
        storeId : 'gateways'
    },
    advanced :['ip_transport','authname', 'domain','callerid'],	
    columns : [
    {
        hidden: true
    },
    {
        width: 70,
        renderer : app.online_offline_renderer
    },
    { 
        renderer : app.checked_render,
        editor :  {
            xtype: 'checkbox',
            style: {
                textAlign: 'center'
            } 
        }
    },	

    { 
        editor :  {
            xtype: 'textfield',
            allowBlank: false
        }
    },

    { 
        editor :  {
            xtype: 'textfield',
            allowBlank: false
        }
    },

    { 
        editor :  {
            xtype: 'textfield',
            allowBlank: false
        }
    },

    { 
        sortable: false,
        renderer : function(){
            return '***';
        },
        editor :  {
            xtype: 'textfield',
            inputType: 'password',
            allowBlank: false
        }
    },

    { 
        editor :  {
            xtype: 'textfield'
        }
    },
    { 
        editor :  {
            xtype	: 'combobox',
            mode	: 'local',
            editable	: false,
            triggerAction:'all',
            store: [
            ['sip','sip'],
            ['h323','h323'],
            ['iax','iax']
            ]	
        }
    },

    { 
        editor :  {
            xtype	: 'combobox',
            mode	: 'local',
            editable	: false,
            triggerAction:'all',
            store: [
            ['UDP','UDP'],
            ['TLS','TLS'],
            ['TCP','TCP']
            ]	
        }
    },

    { 
        editor :  {
            xtype: 'textfield'
        }
    },

    { 
        editor :  {
            xtype: 'textfield'
        }
    }
    ],
    initComponent : function () {
        this.callParent(arguments); 
    }
})
