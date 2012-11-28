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
            xtype: 'combobox',
            store: Ext.StoreMgr.lookup('sources')?
            Ext.StoreMgr.lookup('sources'):
            Ext.create('app.Store',{
                fields : ['id', 'name'],   
                storeId : 'sources'
            }),
	    //queryCaching: false,
	    editable: true,	
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
    { 
        editor :  {
            xtype: 'combobox',
            store: Ext.StoreMgr.lookup('extensions')?
            Ext.StoreMgr.lookup('extensions'):
            Ext.create('app.Store',{
                fields : ['id','status', 'extension', 'firstname', 'lastname', 'group'],   
                storeId : 'groups'
            }),
	    //queryCaching: false,
            displayField: 'extension',
            valueField: 'extension',
            queryMode: 'remove'
        }

    },
    { 
        editor :  {
            xtype: 'combobox',
            store: Ext.StoreMgr.lookup('groups')?
            Ext.StoreMgr.lookup('groups'):
            Ext.create('app.Store',{
                fields : ['id', 'group', 'description', 'extension'],   
                storeId : 'groups'
            }),
	    //queryCaching: false,
            displayField: 'group',
            valueField: 'group',
            queryMode: 'remove'
        }

    }

    ],
    columns_renderer :
        function(value, metaData, record, rowIndex, colIndex, store) {
            if (colIndex==3 && app.msg[value])
	    {
		return app.msg[value];
	    }
            return value;
    },
    initComponent : function () {
        this.callParent(arguments); 
    }
})
