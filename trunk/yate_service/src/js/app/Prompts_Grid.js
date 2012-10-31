Ext.define('app.Prompts_Grid', {
    extend : 'app.Grid',
    store_cfg : {
        fields : ['id','status', 'prompt', 'description','load'],
        storeId : 'prompts'
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
