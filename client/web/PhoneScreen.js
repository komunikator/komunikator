var PhoneScreen = {
	// Ringers state
	incomingRinger: false,
	outgoingRinger: false,

	onShow: function(isFirstShow) {

	},
	
	onHide: function() {
	
	},
	
	updateAccountsStatus: function() {
		var statusDiv = $('accountsStatus');
		statusDiv.innerHTML = '';
		PhoneCore.accounts.each(function(pair) {
			var account = pair.value;
			var bgcolor = account.isConnected ? '#00dd00' : '#cccccc';
			var span = document.createElement('span');
			span.style.backgroundColor = bgcolor;
			span.innerHTML = account.account;
			if (account.isConnected) {
				span.onclick = function() {
					account.disconnect();
				};
			} else {
				span.onclick = function() {
					account.connect();
				};
			}
			
			statusDiv.appendChild(span);			
		});
	},
	
	ringer: function(incoming, enable) {
		if (incoming) {
			PhoneScreen.incomingRinger = enable;
		} else {
			PhoneScreen.outgoingRinger = enable;
		}
		
		var ringerText = '';
		
		if (PhoneScreen.incomingRinger) {
			ringerText += 'Incoming ringing! ';
		}
		if (PhoneScreen.outgoingRinger) {
			ringerText += 'Outgoing ringing! ';
		}
		
		$('ringer').innerHTML = ringerText;
		console.log(ringerText);
	},
	
	addCall: function(channelId) {
		var channel = PhoneCore.channels.get(channelId);
		var callDiv = document.createElement('div');
		callDiv.id = 'call-' + this.filterChannelId(channelId);
		callDiv.addClassName('calldiv');
		
		var numberSpan = document.createElement('span');
		numberSpan.innerHTML = channel.incoming ?
			channel.caller.escapeHTML() : channel.callee.escapeHTML();
		callDiv.appendChild(numberSpan);

		var statusSpan = document.createElement('span');
		statusSpan.id = 'call-status-' + this.filterChannelId(channelId);
		statusSpan.innerHTML = channel.incoming ? "Incoming" : "Outgoing";
		callDiv.appendChild(statusSpan);
		
		if (channel.incoming) {
			var answerButton = document.createElement('button');
			answerButton.innerHTML = "Answer";
			answerButton.onclick = function(e) {
				channel.handlerObject.answer(channelId);
			};
			callDiv.appendChild(answerButton);
		}
		
		var dropButton = document.createElement('button');
		dropButton.innerHTML = "Drop";
		dropButton.onclick = function(e) {
			channel.handlerObject.drop(channelId);
		};
		callDiv.appendChild(dropButton);
		
		$('calls').appendChild(callDiv);
	},
	
	removeCall: function(channelId) {
		$('call-' + this.filterChannelId(channelId)).remove();
	},
	
	removeAllCalls: function() {
		$('calls').innerHTML = '';
	},
	
	pauseCall: function(channelId) {
		$('call-status-' + this.filterChannelId(channelId)).innerHTML = 'On hold';
	},
	
	callAnswered: function(channelId) {
		$('call-status-' + this.filterChannelId(channelId)).innerHTML = 'Talking!';
	},
	
	///////////////////////////////////////////////////////////
	//
	// GUI event handlers
	//
	
	onCallButton: function() {
		var callee = $('phone-callee').value;
		PhoneCore.defaultAccount.call(callee);
		return false;
	},
	
	///////////////////////////////////////////////////////////
	//
	// Utility functions
	//
	
	filterChannelId: function(channelId) {
		channelId = channelId.replace('/', '-');
		channelId = channelId.replace('\\', '-');
		return channelId;
	},
};
