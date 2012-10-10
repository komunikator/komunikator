Ext.define('app.Active_calls_Grid', {
    extend : 'app.Grid',
    //status_grid : true,
    initComponent : function () {
        //this.title = app.msg.active_calls;
        this.store_cfg.autorefresh = true;  
        this.store_cfg.fields = ['time', 'caller', 'called', 'duration', 'status'];
        this.store_cfg.storeId ='active_calls';
        this.viewConfig.loadMask = false;
        //this.columns = [{width:120}];
        this.callParent(arguments);
    }
})
