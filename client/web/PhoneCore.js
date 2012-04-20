var PhoneCore = {
	accounts: null,
	defaultAccount: null,
	channels: null,

	init: function() {
		var accountsData = Settings.getAccounts();
		
		this.accounts = new Hash();
		this.channels = new Hash();
		
		accountsData.each(function(accountData) {
			if (accountData.type == "sip") {
				var account = new SIPAccount(accountData);
				PhoneCore.accounts.set(account.accountId, account);
				PhoneCore.defaultAccount = account;
			}
		});

		resetUI();
		activateScreen('phone');
		PhoneScreen.updateAccountsStatus();
		
		KPlugin.init();
	},
	
	addIncomingChannel: function(channelId, caller, account, handlerObject) {
		this.channels.set(channelId, {
			'id': channelId,
			'incoming': true,
			'account': account,
			'caller': caller,
			'handlerObject': handlerObject
		});
		PhoneScreen.addCall(channelId);
	},
	
	addOutgoingChannel: function(channelId, callee, account, handlerObject) {
		if (channelId.length) {
			this.channels.set(channelId, {
				'id': channelId,
				'incoming': false,
				'account': account,
				'callee': callee,
				'handlerObject': handlerObject
			});
			PhoneScreen.addCall(channelId);
		}
	},
	
	removeChannel: function(channelId, reason) {
		this.channels.unset(channelId);
		PhoneScreen.removeCall(channelId);
	},
	
	removeAllChannels: function() {
		this.channels = new HAsh();
		PhoneScreen.removeAllCalls();
	}
};
