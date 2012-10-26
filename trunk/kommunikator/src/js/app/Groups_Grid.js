Ext.define('app.Groups_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        fields : ['id', 'group', 'description', 'extension'],   
        storeId : 'groups'
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
            xtype: 'textfield',
            regex: /^\d{2}$/,
            allowBlank: false
        }
    }
    ],
    initComponent : function () {
        //this.title = app.msg.extensions;
        this.callParent(arguments); 
    }
})
