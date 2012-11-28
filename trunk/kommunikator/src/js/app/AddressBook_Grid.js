Ext.define('app.AddressBook_Grid', {
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
