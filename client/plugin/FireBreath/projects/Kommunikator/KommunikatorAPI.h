/**********************************************************\

  Auto-generated KommunikatorAPI.h

\**********************************************************/

#include <string>
#include <sstream>
#include <boost/weak_ptr.hpp>
#include "JSAPIAuto.h"
#include "BrowserHost.h"
#include "Kommunikator.h"
#include "VoipEngineProxy.h"

#ifndef H_KommunikatorAPI
#define H_KommunikatorAPI

class KommunikatorAPI : public FB::JSAPIAuto, public VoipEngineEvents {
public:
    KommunikatorAPI(const KommunikatorPtr& plugin, const FB::BrowserHostPtr& host);
    virtual ~KommunikatorAPI();

    KommunikatorPtr getPlugin();
    
    //////////////////////////////////////////////////////////////////////////
    //
    // Plugin Javascript API: Properties
    //

    // Read/Write property ${PROPERTY.ident}
    std::string get_testString();
    void set_testString(const std::string& val);

    // Read-only property ${PROPERTY.ident}
    std::string get_version();

    //////////////////////////////////////////////////////////////////////////
    //
    // Plugin Javascript API: Plugin events
    //
    
    // Method test-event
    void testEvent(const FB::variant& s);
    
    //////////////////////////////////////////////////////////////////////////
    //
    // Plugin Javascript API: Methods
    //
    
    void start();
    void stop();
    void connect(const std::string& protocol, const std::string& account, const std::string& username, const std::string& host, const std::string& password);
    void disconnect(const std::string& protocol, const std::string& account);
    std::string call(const std::string& callee, const std::string& account);
    void answer(const std::string& callId);
    void drop(const std::string& callId);
    void dropAll();
    
    //////////////////////////////////////////////////////////////////////////
    //
    // Plugin Javascript API: JS Events
    //

    // Echo to JS console. Params: message
    FB_JSAPI_EVENT(echo, 1, (const FB::variant&));
    FB_JSAPI_EVENT(enginestarted, 0, ());
    FB_JSAPI_EVENT(enginestopped, 0, ());
    // Account connected. Params: account name
    FB_JSAPI_EVENT(connected, 1, (const FB::variant&));
    // Account disconnected. Params: account name, reason
    FB_JSAPI_EVENT(disconnected, 2, (const FB::variant&, const FB::variant&));
    // Ringer control. Params: incoming, enable
    FB_JSAPI_EVENT(ringer, 2, (const FB::variant&, const FB::variant&));
    // Incoming call Params: channel id
    FB_JSAPI_EVENT(callincoming, 3, (const FB::variant&, const FB::variant&, const FB::variant&));
    FB_JSAPI_EVENT(callanswered, 1, (const FB::variant&));
    FB_JSAPI_EVENT(calldropped, 2, (const FB::variant&, const FB::variant&));
    FB_JSAPI_EVENT(callpaused, 1, (const FB::variant&));

    //////////////////////////////////////////////////////////////////////////
    //
    // VoipEngineEvents
    //

    virtual void log(const std::string& msg);
    virtual void engineStarted();
    virtual void engineStopped();
    virtual void accountConnected(const std::string& account);
    virtual void accountDisconnected(const std::string& account, const std::string& reason);
    virtual void callIncoming(const std::string& caller, const std::string& called, const std::string& channelId);
    virtual void callAnswered(const std::string& channelId);
    virtual void callDropped(const std::string& channelId, const std::string& reason);
    virtual void callPaused(const std::string& channelId);
    virtual void ringer(bool incoming, bool enable);
    
private:
    KommunikatorWeakPtr m_plugin;
    FB::BrowserHostPtr m_host;
    
    std::string m_testString;
    
    VoipEngineProxy* proxy;
};

#endif // H_KommunikatorAPI

