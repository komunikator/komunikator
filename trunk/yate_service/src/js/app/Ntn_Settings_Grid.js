Ext.define('app.Ntn_Settings_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	this.store_cfg.autorefresh = false;
	this.store_cfg.fields = ['id','param', 'value', 'description'];
        this.store_cfg.storeId ='ntn_settings';
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
