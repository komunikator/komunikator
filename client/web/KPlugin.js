///////////////////////////////////////////////////////////////
//
// Kommunikator Plugin wrapper
//

var KPlugin = {
	// VoIP plugin object
	plugin: null,
	// is engine started?
	isStarted: false,
	
	init: function() {
		if (this.isStarted) return;

		// Start Yate Engine
		this.plugin = document.getElementById('kommunikatorPlugin');
	
		window.onbeforeunload = function(e) {
			KPlugin.plugin.stop();
		};

		// Add event listeners
		for(var i in this.eventHandlers) {
			this.plugin.addEventListener(i, this.eventHandlers[i], false);
		}
		this.plugin.start();	
	},
	
	/**
	 * Connect
	 */
	connect: function(account) {
		if (!this.isStarted) return error("Engine not started");
		if (account.isConnected) return error("Already connected to this account");

	 	this.plugin.connect(account.protocol, account.account,
	 		account.username, account.hostname, account.password);
	},
	
	/**
	 * Disconnect
	 */
	disconnect: function(account) {
		if (!this.isStarted) return error("Engine not started");
		if (!account.isConnected) return error("Not connected");

		this.plugin.disconnect(account.protocol, account.account);
	},
	
	/**
	 *
	 */
	call: function(account, callee) {
		if (!this.isStarted) return error("Engine not started");
		if (!account.isConnected) return error("Not connected");

		var channelId = this.plugin.call(callee, account.account);
		
		PhoneCore.addOutgoingChannel(channelId, callee, account, this);
	},

	/**
	 * Answer a call
	 * channelId: channel with call to answer
	 */
	answer: function(channelId) {
		if (!this.isStarted) return error("Engine not started");

		this.plugin.answer(channelId);
	},
	
	/**
	 * Drop a call
	 * channelId: channel with call to drop
	 */
	drop: function(channelId) {
		if (!this.isStarted) return error("Engine not started");

		this.plugin.drop(channelId);
	},
	
	/**
	 * Drop all calls
	 */
	dropAll: function() {
		if (!this.isStarted) return error("Engine not started");

		this.plugin.dropAll();
	},

	///////////////////////////////////////////////////////////
	//
	// Event handlers
	//
	
	eventHandlers: {
		/**
		 * Log from plugin
		 */
		echo: function(message) {
			log('plugin log: ' + message);
		},

		/**
		 * VoIP engine started
		 */
		enginestarted: function() {
			KPlugin.isStarted = true;
			
			var autoConnect = Settings.isAutoConnect();
			
			PhoneCore.accounts.each(function(pair) {
				var account = pair.value;
				if (account.accountId.startsWith('kplugin:')) {
					account.isConnected = false;
					if (autoConnect) account.connect();
				}
			});

			PhoneScreen.updateAccountsStatus();
		},

		/**
		 * VoIP engine stopped
		 */
		enginestopped: function() {
			KPlugin.isStarted = false;
			PhoneCore.accounts.each(function(pair) {
				var account = pair.value;
				if (account.accountId.startsWith('kplugin:')) {
					account.isConnected = false;
				}
			});
			PhoneScreen.updateAccountsStatus();
		},
	
		/**
		 * Connected to server
		 */
		connected: function(account) {
			PhoneCore.accounts.get('kplugin:' + account).isConnected = true;
			PhoneScreen.updateAccountsStatus();
		},
	
		/**
		 * Disconnected from server
		 */
		disconnected: function(account, reason) {
			PhoneCore.accounts.get('kplugin:' + account).isConnected = false;
			
			if (reason.length) {
				setStatus(account + " disconnected. Reason: " + reason);
			} else {
				setStatus(account + " disconnected");		
			}
			PhoneScreen.updateAccountsStatus();
		},
	
		/**
		 * Ringer control from plugin
		 */
		ringer: function(incoming, enable) {
			PhoneScreen.ringer(incoming, enable);
		},
		
		/**
		 * Outgoing call answered
		 */
		callanswered: function(channelId) {
			PhoneScreen.callAnswered(channelId);
		},
		
		/**
		 * Call dropped
		 */
		calldropped: function(channelId, reason) {
			PhoneCore.removeChannel(channelId, reason);
		},
		
		/**
		 * Call on hold (paused)
		 */
		callpaused: function(channelId) {
			PhoneScreen.pauseCall(channelId);
		},
	
		/**
		 * Incoming call
		 */
		callincoming: function(caller, account, channelId) {
			PhoneCore.addIncomingChannel(channelId, caller, account, KPlugin);			
		}
	}
};
