Ext.define('app.module.Groups_Grid', {
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
            xtype: 'textfield',
            allowBlank: false 
        }
    },

    { 
        editor :  {
            xtype: 'textfield'
        }
    },

    { 
        width: 130,
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
