Ext.define('app.Gateways_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	//this.title = app.msg.extensions;
	this.store_cfg.autorefresh = undefined;
	this.store_cfg.fields = ['id', 'enabled', 'gateway', 'protocol', 'server', 'username', 'password', 'description', 'authname', 'domain'];
        this.store_cfg.storeId ='gateways';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{ editor :  {
		xtype: 'numberfield',
		allowBlank: false
	}},	
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
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
