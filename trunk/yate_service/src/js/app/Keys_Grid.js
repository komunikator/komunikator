Ext.define('app.Keys_Grid', {
    extend : 'app.Grid',
    store_cfg:{ 
        fields : ['id','status','key', 'prompt', 'destination','description'],
        storeId : 'keys'
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
