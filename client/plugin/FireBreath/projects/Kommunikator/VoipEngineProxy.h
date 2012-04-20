//
//  VoipEngineProxy.h
//  FireBreath
//
//  Created by Петров Роман on 28.03.12.
//  Copyright 2012 __MyCompanyName__. All rights reserved.
//

#ifndef FireBreath_VoipEngineProxy_h
#define FireBreath_VoipEngineProxy_h

#include <string>

class VoipEngineEvents {
public:
    virtual void log(const std::string& msg) = 0;
    virtual void engineStarted() = 0;
    virtual void engineStopped() = 0;
    virtual void accountConnected(const std::string& account) = 0;
    virtual void accountDisconnected(const std::string& account, const std::string& reason) = 0;
    virtual void callIncoming(const std::string& caller, const std::string& called, const std::string& channelId) = 0;
    virtual void callAnswered(const std::string& channelId) = 0;
    virtual void callDropped(const std::string& channelId, const std::string& reason) = 0;
    virtual void callPaused(const std::string& channelId) = 0;
    virtual void ringer(bool incoming, bool enable) = 0;
};

class VoipEngineProxy {
    
public:
    VoipEngineProxy(VoipEngineEvents* events) {
        m_events = events;
    };
    virtual ~VoipEngineProxy() {
        m_events = NULL;
    };
    
    VoipEngineEvents* events() {
        return m_events;
    };

    virtual void start() = 0;
    virtual void stop() = 0;
    virtual void accountConnect(const std::string& protocol, const std::string& account, const std::string& username, const std::string& host, const std::string& password) = 0;
    virtual void accountDisconnect(const std::string& protocol, const std::string& account) = 0;
    virtual const std::string call(const std::string& callee, const std::string& account) = 0;
    virtual void callAnswer(const std::string& channelId) = 0;
    virtual void callDrop(const std::string& channelId) = 0;
    virtual void callDropAll() = 0;

protected:
    VoipEngineEvents* m_events;
};

#endif
