Ext.define('app.Active_calls_Grid', {
    extend : 'app.Grid',
    store_cfg: {
        autorefresh : true,  
        fields : ['time', 'caller', 'called', 'duration', 'status'],
        storeId :'active_calls'
    },
    status_grid : true,
    initComponent : function () {
        //this.title = app.msg.active_calls;
        this.viewConfig.loadMask = false;
        //this.columns = [{width:120}];
        this.callParent(arguments);
    }
})
