Ext.define('DIGT.Extensions_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	//this.title = DIGT.msg.extensions;
	this.store.autorefresh = false;
	this.store.fields = ['id','status', 'extension', 'firstname', 'lastname'/*, 'groups'*/];
        this.store.storeId ='extensions';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{},
	{ editor :  {
                xtype: 'numberfield',
                allowBlank: false
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}}/*,
	{ editor :  {
                xtype: 'textfield'
	}}*/
	];
        this.callParent(arguments); 
   }
})
