Ext.define('DIGT.Mini_active_calls_Grid', {
    extend : 'DIGT.Grid',
    status_grid : true,
    initComponent : function () {
        //this.title = DIGT.msg.active_calls;
        this.store.autorefresh = true;
        this.store.fields = ['time', 'caller', 'called', 'duration', 'status'];
        this.store.storeId ='active_calls';
        this.viewConfig.loadMask = false;
        this.columns = [
	{hidden: true},
	{width:70},
        {width:70},
	{hidden: true},
        {width:70}
	];
        this.callParent(arguments);
    }
})
