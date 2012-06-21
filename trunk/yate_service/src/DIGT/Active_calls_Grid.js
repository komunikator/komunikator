Ext.define('DIGT.Active_calls_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.title = DIGT.msg.active_calls;
	this.store.autorefresh = true;
	this.store.fields =  ['time', 'caller', 'called', 'duration', 'status'];
        this.store.storeId ='active_calls';
	this.viewConfig.loadMask = false;
        this.callParent(arguments);
   }
})
