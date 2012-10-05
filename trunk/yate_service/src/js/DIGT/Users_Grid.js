Ext.define('DIGT.Users_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.store.autorefresh = undefined;
	this.store.fields = ['id','username', 'password'];
        this.store.storeId ='users';
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
