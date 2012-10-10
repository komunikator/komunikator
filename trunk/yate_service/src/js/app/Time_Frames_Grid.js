Ext.define('app.Time_Frames_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	this.store_cfg.autorefresh = undefined;
	this.store_cfg.fields = ['id','status','key', 'prompt', 'destination','description'];
        this.store_cfg.storeId ='time_frames';
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
