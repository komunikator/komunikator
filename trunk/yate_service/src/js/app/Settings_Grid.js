Ext.define('app.Settings_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	this.store_cfg.autorefresh = false;
	this.store_cfg.fields = ['id','param', 'value', 'description'];
        this.store_cfg.storeId ='settings';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{},
	{
	    editor :  {
		xtype: 'textfield'
	    }
	},
	{}
	];
        this.callParent(arguments); 
   }
})
