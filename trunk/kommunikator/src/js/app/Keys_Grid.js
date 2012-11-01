Ext.define('app.Keys_Grid', {
    extend : 'app.Grid',
    store_cfg:{ 
        fields : ['id','status','key', 'destination','description'],
        storeId : 'keys'
    },
    columns_renderer :
    function(value, metaData, record, rowIndex, colIndex, store) {
        if (colIndex==1 && app.msg[value])
        {
            return app.msg[value];
        }
        return value;
    },
    columns : [
    {
        hidden: true
    },

     { 
        editor :  {
            xtype: 'combobox',
            store: [['online',app.msg['online']?app.msg['online']:'online'],['offline',app.msg['offline']?app.msg['offline']:'offline']],
            displayField: 'group',
            valueField: 'group',
            queryMode: 'local'
        }

    } ,
        

    { 
        editor :  {
            xtype: 'textfield',
            regex: /^\d$/
        }
    },

    { 
        editor :  {
            xtype: 'combobox',
            store: Ext.StoreMgr.lookup('extensions_groups')?
            Ext.StoreMgr.lookup('extensions_groups'):
            Ext.create('app.Store',{
                fields : ['id', 'name'],   
                storeId : 'extensions_groups'
            }),
	    queryCaching: false,
	    //editable: false,	
            displayField: 'name',
            valueField: 'name',
            queryMode: 'remove'
        }

    } ,
    { 
        editor :  {
            xtype: 'textfield'
        }
    },
        
    ],
    initComponent : function () {
        this.callParent(arguments); 
    }
})
