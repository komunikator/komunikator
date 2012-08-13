Ext.define('DIGT.Groups_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	//this.title = DIGT.msg.extensions;
	this.store.autorefresh = undefined;
	this.store.fields = ['id', 'group', 'description', 'extension'];
        this.store.storeId ='groups';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
		xtype: 'textfield',
 		regex: /^\d{2}$/,
		allowBlank: false
	}}
	];
        this.callParent(arguments); 
   }
})
