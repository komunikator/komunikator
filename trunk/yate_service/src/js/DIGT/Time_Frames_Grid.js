Ext.define('DIGT.Time_Frames_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.store.autorefresh = undefined;
	this.store.fields = ['id','status','key', 'prompt', 'destination','description'];
        this.store.storeId ='time_frames';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}}
	];
        this.callParent(arguments); 
   }
})
