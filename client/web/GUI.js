///////////////////////////////////////////////////////////////
//
// Globals
//

var mainMenu = {
	'phone': {
		obj: PhoneScreen
	},
	'settings': {
		obj: SettingsScreen
	}
};
var activeScreen = '';

///////////////////////////////////////////////
//
// GUI functions
//

function resetUI() {
	for (var tab in mainMenu) {
		mainMenu[tab].shown = false;
		$('screen-' + tab).hide();
	}
	activeScreen = '';
}

function activateScreen(tab) {
	if (activeScreen != '') {
		mainMenu[activeScreen].obj.onHide();
		
		$('screen-' + activeScreen).hide();
		$('tab-' + activeScreen).removeClassName('selected');
	}
	
	$('screen-' + tab).show();
	$('tab-' + tab).addClassName('selected');
	mainMenu[tab].obj.onShow(!mainMenu[tab].obj.shown);
	mainMenu[tab].obj.shown = true;
	activeScreen = tab;
}

function setStatus(status) {
	$('status').innerHTML = status;
}

function error(errorText) {
	console.error(errorText);
}
