Ext.define('app.module.Conferences_Grid', {
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
            xtype: 'textfield',
            allowBlank: false
        }
    },

    { 
        editor :  {
            xtype: 'textfield',
            allowBlank: false
        }
    },
    {}
    ],
    initComponent : function () {
        //this.title = app.msg.extensions;       
       
        this.callParent(arguments); 
    }
})
