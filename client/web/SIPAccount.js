var SIPAccount = Class.create(Account, {
	/**
	 * Constructor
	 */
	initialize: function($super, accountData) {
		$super(accountData);

		this.protocol = 'sip';
		
		var items = this.account.split('@');
		this.username = items[0];
		this.hostname = items[1];
		
		this.accountId = 'kplugin:' + this.account;
	},
	
	validateDataField: function(field, value) {
		// TODO
		return true;
	},

	/**
	 * Connect to account
	 */
	connect: function() {
		KPlugin.connect(this);
	},

	/**
	 * Disconnect
	 */
	disconnect: function() {
		KPlugin.disconnect(this);
	},
	
	/**
	 * Call a number
	 * callee: number to call
	 */
	call: function(callee) {
		KPlugin.call(this, callee);
	}	
});
