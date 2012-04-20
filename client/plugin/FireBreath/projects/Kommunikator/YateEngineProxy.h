//
//  YateEngineProxy.h
//  FireBreath
//
//  Created by Петров Роман on 28.03.12.
//  Copyright 2012 __MyCompanyName__. All rights reserved.
//

#ifndef FireBreath_YateEngineProxy_h
#define FireBreath_YateEngineProxy_h

#include "yatengine.h"
#include "yatecbase.h"

#include "VoipEngineProxy.h"

class PluginClientDriver;

class YateEngineProxy : public VoipEngineProxy, public TelEngine::MessageReceiver {
public:
    enum {
        EngineStart     = 1,
        CallCdr         = 2,
        UiAction        = 3,
        UserLogin       = 4,
        UserNotify      = 5,
        ResourceNotify  = 6,
        ResourceSubscribe = 7,
        ClientChanUpdate = 8,
        UserRoster      = 9,
        ContactInfo     = 10
    } RelayID;
    
    YateEngineProxy(VoipEngineEvents* events);
    virtual ~YateEngineProxy();
    
    /////////////////////////////////////////////
    //
    // VoipEngineProxy methods
    //
    
    virtual void start();
    virtual void stop();
    virtual void accountConnect(const std::string& protocol, const std::string& account, const std::string& username, const std::string& host, const std::string& password);
    virtual void accountDisconnect(const std::string& protocol, const std::string& account);
    virtual const std::string call(const std::string& callee, const std::string& account);
    virtual void callAnswer(const std::string& channelId);
    virtual void callDrop(const std::string& channelId);
    virtual void callDropAll();
    
    //////////////////////////////////////////////////////
    //
    // TelEngine::MessageReceiver implementation
    
    virtual bool received(TelEngine::Message& msg, int id);

    ///////////////////////////////////////////////////
    //
    // Other methods
    //
    
    void engineThread();
    void installRelay(const char* name, int id, int prio);
    
    /**
     * IM message routing handler called by the driver
     * @param msg The im.route message
     */
    virtual bool imRouting(TelEngine::Message& msg);
    
    /**
     * Process an IM message
     * @param msg The im.execute of chan.text message
     */
    virtual bool imExecute(TelEngine::Message& msg);
    
    /**
     * Call execute handler called by the driver.
     * Ask the logics to create the channel
     * @param msg The call.execute message
     * @param dest The destination (target)
     * @return True if a channel was created and connected
     */
    bool callIncoming(TelEngine::Message& msg, const TelEngine::String& dest);
    
    /**
     * Call routing handler called by the driver
     * @param msg The call.route message
     */
    virtual bool callRouting(TelEngine::Message& msg);
    
    /**
     * Build an incoming channel.
     * Answer it if succesfully connected and auto answer is set.
     * Reject it if multiline is false and the driver is busy.
     * Set the active one if requested by config and there is no active channel.
     * Start the ringer if there is no active channel
     * @param msg The call.execute message
     * @param dest The destination (target)
     * @return True if a channel was created and connected
     */
    virtual bool buildIncomingChannel(TelEngine::Message& msg, const TelEngine::String& dest);
    
    bool buildOutgoingChannel(TelEngine::NamedList& params);
    
    // UI Related methods
    int channelItemAdjustUiList(TelEngine::NamedList& dest, int show, bool itemAdded, const TelEngine::String& chanId, bool conf);

    void channelItemBuildUpdate(bool upd, TelEngine::NamedList& dest, const TelEngine::String& masterChan, bool conf, bool start, const TelEngine::String& slaveId = TelEngine::String::empty(), bool updateExisting = true);

    bool enableCallActions(const TelEngine::String& id);

    void buildStatus(TelEngine::String& status, const char* stat, const char* addr, const char* id, const char* reason = 0);
    
    // Message handlers
    bool handleUserNotify(TelEngine::Message& msg);

    bool handleCallCdr(TelEngine::Message& msg);

    bool handleClientChanUpdate(TelEngine::Message& msg);

protected:
    PluginClientDriver* m_driver;
    TelEngine::ObjList m_relays;
    TelEngine::NamedList s_generic;
    TelEngine::String m_selectedChannel;            // The currently selected channel
    TelEngine::String m_transferInitiated;          // Tranfer initiated id
};

#endif
