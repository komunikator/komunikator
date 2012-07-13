Ext.define('DIGT.Gateways_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	//this.title = DIGT.msg.extensions;
	this.store.autorefresh = false;
	this.store.fields = ['id', 'enabled', 'gateway', 'protocol', 'server', 'username', 'password', 'description', 'authname', 'domain'];
        this.store.storeId ='gateways';
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
