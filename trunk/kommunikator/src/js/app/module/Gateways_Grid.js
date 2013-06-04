/*
Ext.require([
    'Ext.ux.CheckColumn'
    ]);
*/

app.Loader.load('js/ux/css/CheckHeader.css');
 
Ext.define('app.module.Gateways_Grid', {
    extend    : 'app.Grid',
    
    store_cfg : {
        autorefresh  : false,
        fields       : ['id', 'status', 'enabled', 'gateway', 'server', 'username', 'password', 'description', 'protocol', 'ip_transport', 'authname', 'domain', 'callerid'],
        storeId      : 'gateways'
    },
    
    advanced  : ['ip_transport', 'authname', 'domain', 'callerid'],
    
    columns   : [
    {  // 'id'
        hidden : true
    },
    {  // 'status'
        width     : 70,
        renderer  : app.online_offline_renderer
    },
    {  // 'enabled'
        renderer  : app.checked_render,
        editor    : {
            xtype  : 'checkbox',
            style  : {
                textAlign : 'center'
            } 
        }
    },
    {  // 'gateway'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    },
    {  // 'server'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    },
    {  // 'username'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    },
    {  // 'password'
        sortable  : false,
        renderer  : function() {
            return '***';
        },
        editor    : {
            xtype       : 'textfield',
            inputType   : 'password',
            allowBlank  : false
        }
    },
    {  // 'description'
        editor : {
            xtype : 'textfield'
        }
    },
    {  // 'protocol'
        editor : {
            xtype          : 'combobox',
            mode           : 'local',
            editable       : false,
            triggerAction  : 'all',
            store          : [
            ['sip', 'sip'],
            ['h323', 'h323'],
            ['iax', 'iax']
            ],
            allowBlank  : false
        }
    },
    {  // 'ip_transport'
        editor : {
            xtype          : 'combobox',
            mode           : 'local',
            editable       : false,
            triggerAction  : 'all',
            store          : [
            ['UDP', 'UDP'],
            ['TLS', 'TLS'],
            ['TCP', 'TCP']
            ],
            allowBlank  : false
        }
    },
    {  // 'authname'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    },
    {  // 'domain'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    },
    {  // 'callerid'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    }
    ],
    
    initComponent : function() {
        this.callParent(arguments); 
    }
});