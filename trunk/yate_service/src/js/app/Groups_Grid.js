Ext.define('app.Groups_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	//this.title = app.msg.extensions;
	this.store_cfg.autorefresh = undefined;
	this.store_cfg.fields = ['id', 'group', 'description', 'extension'];
        this.store_cfg.storeId ='groups';
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
	//console.log('group_grid:'+this.id);

        this.callParent(arguments); 
   }
})
