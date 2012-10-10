Ext.define('app.Prompts_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	this.store_cfg.autorefresh = undefined;
	this.store_cfg.fields = ['id','status', 'prompt', 'description','load'];
        this.store_cfg.storeId ='prompts';
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
