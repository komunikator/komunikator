//
//  PluginClientDriver.h
//  FireBreath
//
//  Created by Петров Роман on 05.03.12.
//  Copyright 2012 __MyCompanyName__. All rights reserved.
//

#ifndef FireBreath_PluginClientDriver_h
#define FireBreath_PluginClientDriver_h

#include "yatecbase.h"

class YateEngineProxy;

class PluginClientDriver : public TelEngine::ClientDriver {
public:
    PluginClientDriver(YateEngineProxy* proxy);
    ~PluginClientDriver();

    bool received(TelEngine::Message& msg, int id);

    virtual bool msgExecute(TelEngine::Message& msg, TelEngine::String& dest);
    virtual void msgTimer(TelEngine::Message& msg);
    virtual bool msgRoute(TelEngine::Message& msg);
    
protected:
    virtual void initialize();
    void setup();

    YateEngineProxy* proxy;
    bool m_init;                         // Already initialized flag
};

#endif
