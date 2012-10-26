Ext.define('app.Ntn_Settings_Grid', {
    extend : 'app.Grid',
    store_cfg : {
        fields : ['id','param', 'value', 'description'],
        storeId : 'ntn_settings'
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
