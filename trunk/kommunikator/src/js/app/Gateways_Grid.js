Ext.define('app.Gateways_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        fields : ['id', 'enabled', 'gateway', 'protocol', 'server', 'username', 'password', 'description', 'authname', 'domain'],
        storeId : 'gateways'
    },
    columns : [
    {
        hidden: true
    },

    { 
        editor :  {
            xtype: 'numberfield',
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
            xtype: 'textfield'
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
