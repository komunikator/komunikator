Ext.define('app.Users_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	this.store_cfg.autorefresh = undefined;
	this.store_cfg.fields = ['id','username', 'password'];
        this.store_cfg.storeId ='users';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{
	    editor :  {
		xtype: 'textfield'
	    }
	},
	{
	    editor :  {
		xtype: 'textfield',
	    }
	}
	];
        this.callParent(arguments); 
   }
})
