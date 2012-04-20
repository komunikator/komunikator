var Settings = {
	defaults: {
		'isAutoConnect': true,
		'accounts': [
			{	'type': 'sip',
				'account': '101@172.17.2.37',
				'password': '724873'
			}
		]
	},

	setDefaults: function() {
		this.defaults.each(function(item) {
			Settings.setItem(item, Settings.defaults[item]);
		});
		setAutoConnect(true);
	},

	setItem: function(itemName, value) {
		if (typeof value == "object") {
			localStorage.setItem(itemName, JSON.stringify(value));
		} else {
			localStorage.setItem(itemName, value);	
		}
	},
	
	getItem: function(itemName) {
		var item = localStorage.getItem(itemName);
		return item ? item : this.defaults[itemName];
	},
	
	getObject: function(itemName) {
		var item = localStorage.getItem(itemName);
		return item ? JSON.parse(item) : this.defaults[itemName];
	},
	
	///////////////////////////////////////////////////////////
	//
	// Individual getters/setters for various items
	//
	
	isAutoConnect: function() {
		return this.getItem('isAutoConnect');
	},
	
	setAutoConnect: function(isAutoConnect) {
		this.setItem('isAutoConnect', isAutoConnect);
	},
	
	getAccounts: function() {
		return this.getObject('accounts');
	},
	
	setAccounts: function(accounts) {
		this.setItem('accounts');
	}
};
