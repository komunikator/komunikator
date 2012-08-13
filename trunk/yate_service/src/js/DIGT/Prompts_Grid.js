Ext.define('DIGT.Prompts_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.store.autorefresh = undefined;
	this.store.fields = ['id','status', 'prompt', 'description','load'];
        this.store.storeId ='prompts';
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
