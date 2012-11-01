Ext.define('app.Active_calls_Grid', {
    extend : 'app.Grid',
    store_cfg: {
        autorefresh : true,  
        fields : ['time', 'caller', 'called', 'duration', 'status'],
        storeId :'active_calls'
    },
    status_grid : true,
    initComponent : function () {
        this.callParent(arguments);
    }
})
