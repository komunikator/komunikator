Ext.define('DIGT.Settings_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.store.autorefresh = false;
	this.store.fields = ['id','param', 'value', 'description'];
        this.store.storeId ='settings';
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
