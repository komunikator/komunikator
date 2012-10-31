Ext.define('app.DID_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        fields : ['id','did','number', 'destination','description','extension','group'],
        storeId : 'dids'
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
