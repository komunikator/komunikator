Ext.define('app.module.Active_calls_Grid', {
    extend : 'app.Grid',
    store_cfg: {
        autorefresh : false,  
        fields : ['time', 'caller', 'called', 'duration','gateway', 'status'],
        storeId :'active_calls'
    },
    columns : [
    {
        width:120
    },

    {
        width:160
    },
    {
        width:160
    },
    {},
    {
        width:100
    },
    {
        renderer: function(value) {
            if (app.msg[value])
                value = app.msg[value];
            return value
        }
    }
    ] ,
    //status_grid : true,
    initComponent : function () {
        this.callParent(arguments);
    }
})
