Ext.define('DIGT.Ntn_Settings_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.store.autorefresh = false;
	this.store.fields = ['id','param', 'value', 'description'];
        this.store.storeId ='ntn_settings';
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
