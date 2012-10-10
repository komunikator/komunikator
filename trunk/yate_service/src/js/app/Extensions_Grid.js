Ext.define('app.Extensions_Grid', {
    //requires: 'app.Groups_Grid',
    extend : 'app.Grid',
    store_cfg: {
        autorefresh : false,
        fields : ['id','status', 'extension', 'firstname', 'lastname', 'group'],
        storeId :'extensions'  
    },
 
    initComponent : function () {
        //this.title = app.msg.extensions;
        this.viewConfig.loadMask = false;
        this.columns = [
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
                xtype: "combobox",
                store: Ext.StoreMgr.lookup('groups'),
                displayField: "group",
                valueField: "group",
                queryMode: "remove"
            }
            }
        ];
        this.callParent(arguments); 
    }
})
