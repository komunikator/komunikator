Ext.define('Adminka.model.NicItem', {
	extend: 'Ext.data.Model',
	fields: ['dev', 'ipaddress', 'ipmask', 'ipgateway', 'type', 'dns1', 'dns2'],
	proxy: {
		type: 'ajax',
		url: 'network.php',
		reader: {
			type: 'json'
		}
	}
});