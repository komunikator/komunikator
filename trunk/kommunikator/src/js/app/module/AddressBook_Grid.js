Ext.define('app.module.AddressBook_Grid', {
    extend : 'app.Grid',
    store_cfg : { 
        fields : ['id','short_name', 'name', 'number'],
        storeId : 'short_names'
    },
    columns : [
    {
        hidden: true
    },

    {
        editor :  {
            xtype: 'textfield',
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
            xtype: 'textfield',
            regex: /^\d/,
             allowBlank: false
        }
    }
    ],
    initComponent : function () {
        this.callParent(arguments); 
    }
})
