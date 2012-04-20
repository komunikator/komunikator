var Account = Class.create({
	initialize: function(accountData) {
		this.account = accountData.account;
		this.password = accountData.password;
		
		this.accountId = this.account;
		
		this.isConnected = false;
	},
	
	getAccountData: function() {
		accountData = {};

		accountData.account = this.account;
		accountData.password = this.password;

		return accountData;
	},

	/**
	 * Return list of fields required for account
	 */
	getDataFields: function() {
		return {
			'account': ['text', 'Account'],
			'password': ['password', 'Password']
		};
	},
	
	validateDataField: function(field, value) {
		return false;
	},
	
	connect: function() {
		error("Account.connect method is not implemented!");
	},
	
	disconnect: function() {
		error("Account.disconnect method is not implemented!");	
	},
	
	call: function(callee) {
		error("Account.call method is not implemented!");
	}
});
