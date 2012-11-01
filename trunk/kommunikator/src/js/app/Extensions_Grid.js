Ext.define('app.Extensions_Grid', {
    extend : 'app.Grid',
    store_cfg: {
        autorefresh : false,
        fields : ['id','status', 'extension', 'firstname', 'lastname', 'group'],
        storeId :'extensions'  
    },
    columns : [
    {
        hidden: true
    },

    {},

    { 
        editor :  {
            xtype: 'textfield',
            regex: /^\d{3}$/
        //,
        //allowBlank: false 
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
	    queryCaching: false,
            displayField: 'group',
            valueField: 'group',
            queryMode: 'remove'
        }

    }
    ],
    initComponent : function () {
        this.callParent(arguments);
    }
});
