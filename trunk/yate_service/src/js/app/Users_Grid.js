Ext.define('app.Users_Grid', {
    extend : 'app.Grid',
    store_cfg : { 
        fields : ['id','username', 'password'],
        storeId : 'users'
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
    }
    ],
    initComponent : function () {
        this.callParent(arguments); 
    }
})
