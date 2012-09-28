Ext.define('Adminka.view.network.Edit', {
	extend: 'Ext.form.Panel',
	alias : 'widget.networkedit',
	title : 'Edit Network Settings',
	bodyPadding: '5 5 5 5',
    fieldDefaults: {
        msgTarget: 'side',
        allowBlank: false
    },
	initComponent: function() {
		this.items = [{
			xtype: 'radiogroup',
			columns: 1,
			items: [
		        {boxLabel: 'Auto DHCP', name:'type', inputValue: 0, checked: true},
			    {boxLabel: 'Manual', name:'type', inputValue:1}
			]
		},{
			xtype: 'fieldset',
			border: false,
			disabled: true,
			items: [{
				xtype: 'textfield',
				name : 'ipaddress',
				fieldLabel: 'IP Address',
				vtype: 'ipVal'
			},
			{
				xtype: 'textfield',
				name : 'ipmask',
				fieldLabel: 'IP Mask',
				vtype: 'ipMask'
			},
			{
				xtype: 'textfield',
				name : 'ipgateway',
				fieldLabel: 'Default Gateway',
				vtype: 'ipVal',
				allowBlank: true
			}]
		}];

		this.callParent(arguments);
	}
});
