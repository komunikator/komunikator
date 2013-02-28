Ext.define('app.module.Users_Grid', {
    extend : 'app.Grid',
    store_cfg : { 
        fields : ['id','username', 'password'],
        storeId : 'users'
    },
    columns : [
    {
        hidden: true
    },

    {
        width: 130,
        editor :  {
            xtype: 'textfield',
            regex: /^[a-zA-Z0-9_-]{3,16}$/
            ,
            allowBlank: false
        }
    },
    {
        sortable: false,
        renderer : function(){
            return '***';
        },
        editor :  {
            xtype: 'textfield',
            inputType: 'password',
            regex: /^[a-zA-Z0-9_-]{3,16}$/
            ,
            allowBlank: false
        }
    }
    ],
    initComponent : function () {
        this.callParent(arguments); 
    }
})
