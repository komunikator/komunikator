Ext.define('app.module.Settings_Grid', {
    extend : 'app.Grid',
    store_cfg : { 
        fields : ['id','param', 'value', 'description'],
        storeId : 'settings'
    },
    columns : [
    {
        hidden: true
    },

    {},

    {
        editor :  {
            xtype: 'textfield'
        }
    },
    {}
    ],
    initComponent : function () {
        this.callParent(arguments); 
    }
})
