Ext.define('app.module.Groups_Grid', {
    extend     : 'app.Grid',
    
    store_cfg  : {
        fields   : ['id', 'group', 'description', 'extension'],   
        storeId  : 'groups'
    },
    
    columns    : [
        
    {  // 'id'
        hidden : true
    },

    {  // 'group'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    },

    {  // 'description'
        editor : {
            xtype : 'textfield'
        }
    },

    {  // 'extension'
        width   : 130,
        
        editor  : {
            xtype       : 'textfield',
            regex       : /^\d{2}$/,
            allowBlank  : false
        }
    }
    
    ],
    
    initComponent : function () {
        // this.title = app.msg.extensions;
        this.callParent(arguments); 
    }
})
