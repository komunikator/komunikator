
Ext.define('app.module.DID_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        fields : ['id','number', 'destination','default_dest','description'],
      
        storeId : 'dids'
    },
    columns : [
    {
        hidden: true
    },

    { 
        editor :  {
            xtype: 'textfield',
            regex: /^\d+$/,
            allowBlank: false 

        }
    },

    { 
        editor :  app.get_Source_Combo({
            allowBlank: false,
            editable: false//,
            //vtype: 'fds'
        })
    } ,
    { 
        width: 160,
        editor :  app.get_Source_Combo({/*validator:{}*/
     //  vtype: 'fds'
            })//TODO validator
    } ,
    { 
        editor :  {
            xtype: 'textfield'
        }
    }
    ],
    columns_renderer :
        
    function(value, metaData, record, rowIndex, colIndex, store) {
        if (colIndex==2 && app.msg[value])
        {
            return app.msg[value];
        }
        return value;
    },
    initComponent : function () {
        this.callParent(arguments); 
    }
})
