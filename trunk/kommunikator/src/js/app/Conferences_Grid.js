Ext.define('app.Conferences_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        autorefresh : false,                                     
        fields :  ['id','conference', 'number', 'participants'],  
        storeId : 'conferences'
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
    {}
    ],
    initComponent : function () {
        //this.title = app.msg.extensions;       
       
        this.callParent(arguments); 
    }
})
