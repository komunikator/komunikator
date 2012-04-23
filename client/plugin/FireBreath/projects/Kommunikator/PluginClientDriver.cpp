//
//  PluginClientDriver.cpp
//  FireBreath
//
//  Created by Петров Роман on 05.03.12.
//  Copyright 2012 __MyCompanyName__. All rights reserved.
//

#include "PluginClientDriver.h"
#include "YateEngineProxy.h"

PluginClientDriver::PluginClientDriver(YateEngineProxy* proxy) : ClientDriver() {
    this->proxy = proxy;
}

PluginClientDriver::~PluginClientDriver() {
    proxy->stop();
    proxy = NULL;
}

void PluginClientDriver::initialize() {
#ifdef KMACOS
    s_device = "coreaudio/*";
#endif
#ifdef KWINDOWS
    s_device = "dsound/*";
#endif
    if (!m_init) {
        m_init = true;
        setup();
    }
}

void PluginClientDriver::setup() {
    Driver::setup();
    installRelay(Halt);
    installRelay(Progress);
    installRelay(Route, 200);
    installRelay(Text);
    installRelay(ImRoute);
    installRelay(ImExecute);
}

bool PluginClientDriver::received(TelEngine::Message& msg, int id) {
    if (id == ImRoute) {
        // don't route here our own messages
        if (name() == msg.getValue("module"))
            return false;
        if (proxy->imRouting(msg))
            return false;
        msg.retValue() = name() + "/*";
        return true;
    }
    if (id == ImExecute || id == Text) {
        if (TelEngine::Client::isClientMsg(msg))
            return false;
        return proxy->imExecute(msg);
    }
    if (id == Halt) {
        dropCalls();
        // TODO: Shut down plugin (?)
        //if (Client::self())
        //    Client::self()->quit();
    }
    return TelEngine::Driver::received(msg,id);
}

bool PluginClientDriver::msgExecute(TelEngine::Message& msg, TelEngine::String& dest) {
    return proxy->callIncoming(msg, dest);
}

void PluginClientDriver::msgTimer(TelEngine::Message& msg) {
    TelEngine::Driver::msgTimer(msg);
}

bool PluginClientDriver::msgRoute(TelEngine::Message& msg) {
    // don't route here our own calls
    if (name() == msg.getValue("module"))
        return false;
    if (proxy->callRouting(msg)) {
        msg.retValue() = name() + "/*";
        return true;
    }
    return Driver::msgRoute(msg);
}
