/**********************************************************\

  Auto-generated KommunikatorAPI.cpp

\**********************************************************/

#include "JSObject.h"
#include "variant_list.h"
#include "DOM/Document.h"
#include "global/config.h"

#include "boost/thread.hpp"

#include "Kommunikator.h"
#include "KommunikatorAPI.h"
#include "YateEngineProxy.h"

///////////////////////////////////////////////////////////////////////////////
/// @fn KommunikatorAPI::KommunikatorAPI(const KommunikatorPtr& plugin, const FB::BrowserHostPtr host)
///
/// @brief  Constructor for your JSAPI object.  You should register your methods, properties, and events
///         that should be accessible to Javascript from here.
///
/// @see FB::JSAPIAuto::registerMethod
/// @see FB::JSAPIAuto::registerProperty
/// @see FB::JSAPIAuto::registerEvent
KommunikatorAPI::KommunikatorAPI(const KommunikatorPtr& plugin, const FB::BrowserHostPtr& host) : m_plugin(plugin), m_host(host) {
    registerMethod("testEvent",     make_method(this, &KommunikatorAPI::testEvent));
    registerMethod("start",         make_method(this, &KommunikatorAPI::start));
    registerMethod("stop",          make_method(this, &KommunikatorAPI::stop));
    registerMethod("connect",       make_method(this, &KommunikatorAPI::connect));
    registerMethod("disconnect",    make_method(this, &KommunikatorAPI::disconnect));
    registerMethod("call",          make_method(this, &KommunikatorAPI::call));
    registerMethod("answer",        make_method(this, &KommunikatorAPI::answer));
    registerMethod("drop",          make_method(this, &KommunikatorAPI::drop));
    registerMethod("dropAll",       make_method(this, &KommunikatorAPI::dropAll));

    // Read-write property
    registerProperty("testString", make_property(this,
        &KommunikatorAPI::get_testString,
        &KommunikatorAPI::set_testString));

    // Read-only property
    registerProperty("version", make_property(this, &KommunikatorAPI::get_version));
    
    proxy = new YateEngineProxy(this);
}

///////////////////////////////////////////////////////////////////////////////
/// @fn KommunikatorAPI::~KommunikatorAPI()
///
/// @brief  Destructor.  Remember that this object will not be released until
///         the browser is done with it; this will almost definitely be after
///         the plugin is released.
KommunikatorAPI::~KommunikatorAPI() {
    delete(proxy);
}

///////////////////////////////////////////////////////////////////////////////
/// @fn KommunikatorPtr KommunikatorAPI::getPlugin()
///
/// @brief  Gets a reference to the plugin that was passed in when the object
///         was created.  If the plugin has already been released then this
///         will throw a FB::script_error that will be translated into a
///         javascript exception in the page.
KommunikatorPtr KommunikatorAPI::getPlugin() {
    KommunikatorPtr plugin(m_plugin.lock());
    if (!plugin) {
        throw FB::script_error("The plugin is invalid");
    }
    return plugin;
}

//////////////////////////////////////////////////////////////////////////
//
// Plugin Javascript API: Properties
//

// Read/Write property testString
std::string KommunikatorAPI::get_testString() {
    return m_testString;
}
void KommunikatorAPI::set_testString(const std::string& val) {
    m_testString = val;
}

// Read-only property version
std::string KommunikatorAPI::get_version() {
    return FBSTRING_PLUGIN_VERSION;
}

//////////////////////////////////////////////////////////////////////////
//
// Plugin Javascript API: Plugin events
//

void KommunikatorAPI::testEvent(const FB::variant& var) {

}

//////////////////////////////////////////////////////////////////////////
//
// Plugin Javascript API: Methods
//

void KommunikatorAPI::start() {
    log("Starting VoIP engine...");
    proxy->start();
}

void KommunikatorAPI::stop() {
    log("Stopping VoIP engine...");
    proxy->stop();
}

void KommunikatorAPI::connect(const std::string& protocol, const std::string& account, const std::string& username, const std::string& host, const std::string& password) {
    log("Connecting as " + account + "...");
    proxy->accountConnect(protocol, account, username, host, password);
}

void KommunikatorAPI::disconnect(const std::string& protocol, const std::string& account) {
    log("Disconnecting " + account + "...");
    proxy->accountDisconnect(protocol, account);
}

std::string KommunikatorAPI::call(const std::string& callee, const std::string& account) {
    log(account + " is calling " + callee + "...");
    return proxy->call(callee, account);
}

void KommunikatorAPI::answer(const std::string& callId) {
    log("Answering call " + callId + "...");
    proxy->callAnswer(callId);
}

void KommunikatorAPI::drop(const std::string& callId) {
    log("Dropping call " + callId + "...");
    proxy->callDrop(callId);
}

void KommunikatorAPI::dropAll() {
    log("Dropping all calls...");
    proxy->callDropAll();
}


//////////////////////////////////////////////////////////////////////////
//
// VoipEngineEvents
//

void KommunikatorAPI::log(const std::string& msg) {
    fire_echo(msg);    
}

void KommunikatorAPI::engineStarted() {
    log("VoIP engine started");
    fire_enginestarted();
}

void KommunikatorAPI::engineStopped() {
    log("VoIP engine stopped");
    fire_enginestopped();
}

void KommunikatorAPI::accountConnected(const std::string& account) {
    log("Connected as " + account);
    fire_connected(account);
}

void KommunikatorAPI::accountDisconnected(const std::string& account, const std::string& reason) {
    log(account + " is disconnected. Reason: " + reason);
    fire_disconnected(account, reason);
}

void KommunikatorAPI::callIncoming(const std::string& caller, const std::string& called, const std::string& channelId) {
    log("Incoming call from " + caller + " to " + called + " (channelId: " + channelId + ")");
    fire_callincoming(caller, called, channelId);
}

void KommunikatorAPI::callAnswered(const std::string& channelId) {
    log("Call " + channelId + " answered");
    fire_callanswered(channelId);
}

void KommunikatorAPI::callDropped(const std::string& channelId, const std::string& reason) {
    log("Call " + channelId + " dropped. Reason: " + reason);
    fire_calldropped(channelId, reason);
}

void KommunikatorAPI::callPaused(const std::string& channelId) {
    log("Call " + channelId + " paused");
    fire_callpaused(channelId);
}

void KommunikatorAPI::ringer(bool incoming, bool enable) {
    fire_ringer(incoming, enable);
}
