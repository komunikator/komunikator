//
//  YateEngineProxy.cpp
//  FireBreath
//
//  Created by Петров Роман on 28.03.12.
//  Copyright 2012 __MyCompanyName__. All rights reserved.
//

#include "YateEngineProxy.h"
#include "PluginClientDriver.h"
#include <boost/thread.hpp>

///////////////////////////////////////////////////////////////////////
//
// Structures and data
//

// Struct used to build client relays array
struct MsgRelay
{
    const char* name;
    int id;
    int prio;
};

// Client relays
static const MsgRelay s_relays[] = {
    {"call.cdr",           YateEngineProxy::CallCdr,           90},
    {"ui.action",          YateEngineProxy::UiAction,          150},
    {"user.login",         YateEngineProxy::UserLogin,         50},
    {"user.notify",        YateEngineProxy::UserNotify,        50},
    {"resource.notify",    YateEngineProxy::ResourceNotify,    50},
    {"resource.subscribe", YateEngineProxy::ResourceSubscribe, 50},
    {"clientchan.update",  YateEngineProxy::ClientChanUpdate,  50},
    {"user.roster",        YateEngineProxy::UserRoster,        50},
    {"contact.info",       YateEngineProxy::ContactInfo,       50},
    {0,0,0},
};

// Channel slave type
const TelEngine::TokenDict s_slaveTypes[] = {
    {"conference",      TelEngine::ClientChannel::SlaveConference},
    {"transfer",        TelEngine::ClientChannel::SlaveTransfer},
    {0,0}
};

// Channel notifications
const TelEngine::TokenDict s_notification[] = {
    {"startup",         TelEngine::ClientChannel::Startup},
    {"destroyed",       TelEngine::ClientChannel::Destroyed},
    {"active",          TelEngine::ClientChannel::Active},
    {"onhold",          TelEngine::ClientChannel::OnHold},
    {"noticed",         TelEngine::ClientChannel::Noticed},
    {"addresschanged",  TelEngine::ClientChannel::AddrChanged},
    {"routed",          TelEngine::ClientChannel::Routed},
    {"accepted",        TelEngine::ClientChannel::Accepted},
    {"rejected",        TelEngine::ClientChannel::Rejected},
    {"progressing",     TelEngine::ClientChannel::Progressing},
    {"ringing",         TelEngine::ClientChannel::Ringing},
    {"answered",        TelEngine::ClientChannel::Answered},
    {"transfer",        TelEngine::ClientChannel::Transfer},
    {"conference",      TelEngine::ClientChannel::Conference},
    {"audioset",        TelEngine::ClientChannel::AudioSet},
    {0,0}
};

// Resource status names
const TelEngine::TokenDict s_statusName[] = {
    {"offline",   TelEngine::ClientResource::Offline},
    {"connecting",TelEngine::ClientResource::Connecting},
    {"online",    TelEngine::ClientResource::Online},
    {"busy",      TelEngine::ClientResource::Busy},
    {"dnd",       TelEngine::ClientResource::Dnd},
    {"away",      TelEngine::ClientResource::Away},
    {"xa",        TelEngine::ClientResource::Xa},
    {0,0}
};

// MucRoomMember affiliations
const TelEngine::TokenDict s_affName[] = {
    {"owner",   TelEngine::MucRoomMember::Owner},
    {"admin",   TelEngine::MucRoomMember::Admin},
    {"member",  TelEngine::MucRoomMember::Member},
    {"outcast", TelEngine::MucRoomMember::Outcast},
    {"none",    TelEngine::MucRoomMember::AffNone},
    {0,0}
};

// MucRoomMember roles
const TelEngine::TokenDict s_roleName[] = {
    {"moderator",   TelEngine::MucRoomMember::Moderator},
    {"participant", TelEngine::MucRoomMember::Participant},
    {"visitor",     TelEngine::MucRoomMember::Visitor},
    {"none",        TelEngine::MucRoomMember::RoleNone},
    {0,0}
};


YateEngineProxy::YateEngineProxy(VoipEngineEvents* events): VoipEngineProxy(events), s_generic("") {
    m_driver = new PluginClientDriver(this);
}

YateEngineProxy::~YateEngineProxy() {
    m_relays.clear();
    delete(m_driver);
    m_driver = NULL;
}



/////////////////////////////////////////////////////////////////////////
//
// VoipEngineProxy methods implementation
//

void YateEngineProxy::start() {
    boost::thread t(boost::bind(&YateEngineProxy::engineThread, this));
}

void YateEngineProxy::stop() {
    TelEngine::Engine::halt(0);
}

void YateEngineProxy::accountConnect(const std::string& protocol, const std::string& account, const std::string& username, const std::string& host, const std::string& password) {
    TelEngine::Message* m = new TelEngine::Message("user.login");
    m->addParam("protocol", protocol.c_str());
    m->addParam("operation", "login");
    m->addParam("account", account.c_str());
    m->addParam("password", password.c_str());
    m->addParam("username", username.c_str());
    m->addParam("server", host.c_str());
    TelEngine::Engine::enqueue(m);
}

void YateEngineProxy::accountDisconnect(const std::string& protocol, const std::string& account) {
    TelEngine::Message* m = new TelEngine::Message("user.login");
    m->addParam("protocol", protocol.c_str());
    m->addParam("operation", "logout");
    m->addParam("account", account.c_str());
    TelEngine::Engine::enqueue(m);
}

const std::string YateEngineProxy::call(const std::string& callee, const std::string& account) {
    TelEngine::NamedList params("params");
    params.addParam("target", callee.c_str());
    params.addParam("line", account.c_str());
    params.addParam("account", account.c_str());
    params.addParam("protocol", "sip");
    
    /*const TelEngine::String& ns = params["target"];
    if (cmd == s_actionCall) {
        // Check google voice target on gmail accounts
        TelEngine::String account = params.getValue("account", params.getValue("line"));
        if (account && isGmailAccount(m_accounts->findAccount(account))) {
            // Allow calling user@domain
            int pos = ns.find('@');
            bool valid = (pos > 0) && (ns.find('.',pos + 2) >= pos);
            if (!valid) {
                target = ns;
                Client::fixPhoneNumber(target,"().- ");
            }
            if (target) {
                target = target + "@voice.google.com";
                params.addParam("ojingle_version","0");
                params.addParam("ojingle_flags","noping");
                params.addParam("redirectcount","5");
                params.addParam("checkcalled",String::boolText(false));
                params.addParam("dtmfmethod","rfc2833");
                String callParams = params[YSTRING("call_parameters")];
                callParams.append("redirectcount,checkcalled,dtmfmethod,ojingle_version,ojingle_flags",",");
                params.setParam("call_parameters",callParams);
            }
            else if (!valid) {
                showError(wnd,"Incorrect number");
                //Debug(ClientDriver::self(),DebugNote, "Failed to call: invalid gmail number '%s'",params.getValue("target"));
                return false;
            }
        }
    }*/
    // Delete the number from the "callto" widget and put it in the callto history
    /*if (ns) {
        Client::self()->delTableRow(s_calltoList,ns);
        Client::self()->addOption(s_calltoList,ns,true);
        Client::self()->setText(s_calltoList,"");
    }*/
    /*if (target)
        params.setParam("target",target);*/
    if (!buildOutgoingChannel(params)) return "";
    // Activate the calls page
    //activatePageCalls();
    
    std::string chanId = std::string(params.getParam("channelid")->c_str());

    return chanId;
}

void YateEngineProxy::callAnswer(const std::string& channelId) {
    TelEngine::ClientChannel* chan = static_cast<TelEngine::ClientChannel*>(PluginClientDriver::self()->find(channelId.c_str()));
    if (chan) {
        //chan->callAnswer(setActive);
        chan->callAnswer(true);
    }
}

void YateEngineProxy::callDrop(const std::string& channelId) {
    const char* reason = NULL;
    const char* error = NULL;
    
    // Check if the channel exists
    //TelEngine::Lock lock(PluginClientDriver::self());
    //if (!PluginClientDriver::self())
    //    return;
    TelEngine::Channel* chan = PluginClientDriver::self()->find(channelId.c_str());
    if (!chan)
        return;
    bool hangup = chan->isAnswered();
    bool cancel = !hangup && chan->isIncoming();
    //lock.drop();

    // Drop the call
    TelEngine::Message* m = new TelEngine::Message("call.drop");
    m->addParam("id", channelId.c_str());
    if (hangup || cancel) {
        if (!reason && cancel)
            reason = "cancelled";
        if (!error)
            error = cancel ? "Cancelled" : "User hangup";
    }
    else {
        if (!reason)
            reason = "busy";
        if (!error)
            error = "Rejected";
    }
    m->addParam("error",error,false);
    m->addParam("reason",reason,false);
    TelEngine::Engine::enqueue(m);
}

void YateEngineProxy::callDropAll() {
    m_events->log("Dropping all calls...");
    PluginClientDriver::self()->dropCalls("hangup");
}



///////////////////////////////////////////////////////////////////////////
//
// Various utility methods
//

void YateEngineProxy::engineThread() {
#ifdef KMACOS
    const char* argv[3];
    int argc = 1;
    argv[0] = "yate";
    //argv[1] = "-l";
    //argv[2] = "/Projects/yate.log";
#endif
#ifdef KWINDOWS
    const char* argv[5];
    int argc = 5;
    argv[0] = "C:\\Yate\\yate.exe";
    argv[1] = "-l";
    argv[2] = "C:\\Yate\\yate.log";
    argv[3] = "-m";
    argv[4] = "C:\\Yate\\modules";
#endif
    
    TelEngine::Engine::self();
    //TelEngine::debugLevel(TelEngine::DebugAll);
    TelEngine::debugLevel(TelEngine::DebugNote);
    
    m_driver = new PluginClientDriver(this);
    
    installRelay("engine.start", EngineStart, 100);
    for (int i = 0; s_relays[i].name; i++) {
        installRelay(s_relays[i].name, s_relays[i].id, s_relays[i].prio);
    }
    
    TelEngine::Engine::main(argc, argv, NULL, TelEngine::Engine::Client);
    m_events->engineStopped();
}


void YateEngineProxy::installRelay(const char* name, int id, int prio) {
    TelEngine::MessageRelay* relay = new TelEngine::MessageRelay(name, this, id, prio);
    if (TelEngine::Engine::install(relay)) {
        m_relays.append(relay);
    } else {
        m_events->log("Failed to install message relay!");
        TelEngine::destruct(relay);
    }
}

bool YateEngineProxy::received(TelEngine::Message& msg, int id) {
    bool processed = false;
    
    switch (id) {
        case EngineStart:
            m_events->engineStarted();
            break;
        case UserNotify:
            processed = handleUserNotify(msg) || processed;
            break;
        case CallCdr:
            processed = handleCallCdr(msg) || processed;
            break;
        case ClientChanUpdate:
            processed = handleClientChanUpdate(msg) || processed;
            break;
        case UserLogin:
            // Ignore this message
            break;
        default:
            m_events->log(msg.c_str());
            break;
    }
    return processed;
}

bool YateEngineProxy::handleUserNotify(TelEngine::Message& msg) {
    const TelEngine::String& account = msg["account"];
    
    if (!account) return false;
    bool reg = msg.getBoolValue("registered");
    const TelEngine::String& reasonStr = msg["reason"];

    if (reg) {
        m_events->accountConnected(account.c_str());
    } else {
        const TelEngine::String& error = msg["error"];
        
        std::string reason;
        if (reasonStr) {
            reason = reasonStr.c_str();
            if (error && reasonStr != error) {
                reason += "(" + error + ")";
            }
        }
        
        m_events->accountDisconnected(account.c_str(), reason);
    }
    
    // msg.getBoolValue(YSTRING("autorestart")) - don't know what this field can be used to

    return false;
}

bool YateEngineProxy::handleCallCdr(TelEngine::Message& msg) {
    if (msg["operation"] != "finalize")
        return false;
    if (!msg["chan"].startsWith("client/",false))
        return false;
    // Update the call log history
    //callLogUpdate(msg, true, true);
    return false;
}

bool YateEngineProxy::handleClientChanUpdate(TelEngine::Message& msg) {
#define CHANUPD_ID (chan ? chan->id() : *id)
#define CHANUPD_ADDR (chan ? chan->address() : TelEngine::String::empty())
    
    // Ignore utility channels (playing sounds)
    if (msg.getBoolValue("utility"))
        return false;
    
    int notif = TelEngine::ClientChannel::lookup(msg.getValue("notify"));
    
    // Check for hang up / drop / destroy
    if (notif == TelEngine::ClientChannel::Destroyed) {
        TelEngine::String id = msg.getValue("id");
        int slave = TelEngine::ClientChannel::lookupSlaveType(msg.getValue("channel_slave_type"));
        if (slave) {
            bool conf = (slave == TelEngine::ClientChannel::SlaveConference);
            const TelEngine::String& masterId = msg["channel_master"];
            if (masterId) {
                TelEngine::ClientChannel* master = PluginClientDriver::findChan(masterId);
                unsigned int slaves = 0;
                if (master) {
                    master->removeSlave(id);
                    slaves = master->slavesCount();
                    TelEngine::destruct(master);
                }
                TelEngine::NamedList p("");
                int items = channelItemAdjustUiList(p, -1, false, masterId, conf);
                if (conf) {
                    if (slaves) {
                        TelEngine::String tmp;
                        tmp << "Conference (" << (slaves + 1) << ")";
                        p.addParam("status",tmp);
                    }
                    else
                        channelItemBuildUpdate(false,p,masterId,true,false,masterId);
                }
                channelItemBuildUpdate(false,p,masterId,conf,false,id);
                // Add transfer start
                if (!conf && !slaves && items)
                    channelItemBuildUpdate(true,p,masterId,false,true);
                //Client::self()->setTableRow(s_channelList,masterId,&p);
                m_events->log("Update channels list");
                if (!slaves) {
                    if (conf)
                        PluginClientDriver::setConference(masterId,false);
                }
            }
        }
        s_generic.clearParam(id,'_');
        // Reset init transfer if destroyed
        if (m_transferInitiated && m_transferInitiated == id)
            m_transferInitiated = "";
        // Stop incoming ringer if there are no more incoming channels
        bool haveIncoming = false;
        if (PluginClientDriver::self()) {
            TelEngine::Lock lock(PluginClientDriver::self());
            TelEngine::ObjList* o = PluginClientDriver::self()->channels().skipNull();
            for (; o; o = o->skipNext())
                if ((static_cast<TelEngine::Channel*>(o->get()))->isOutgoing()) {
                    haveIncoming = true;
                    break;
                }
        }
        if (!haveIncoming) {
            //removeTrayIcon(YSTRING("incomingcall"));
            m_events->ringer(true,false);
            m_events->ringer(false,false);
        }

        const char *reason = msg.getValue("reason");
        
        m_events->callDropped(id.c_str(), reason ? reason : "");
        
        return false;
    }
    
    // Set some data from channel
    TelEngine::ClientChannel* chan = static_cast<TelEngine::ClientChannel*>(msg.userData());
    // We MUST have an ID
    TelEngine::NamedString* id = 0;
    if (!chan)
        id = msg.getParam("id");
    if (!(chan || id))
        return false;
    bool outgoing = chan ? chan->isOutgoing() : msg.getBoolValue("outgoing");
    bool noticed = chan ? chan->isNoticed() : msg.getBoolValue("noticed");
    bool active = chan ? chan->active() : msg.getBoolValue("active");
    bool silence = msg.getBoolValue("silence");
    bool notConf = !(chan ? chan->conference() : msg.getBoolValue("conference"));
    
    // Stop ringing on not silenced active outgoing channels
    if (active && !outgoing && !silence) {
        m_events->ringer(false,false);
    }
    
    // Add slaves to master channels
    int slave = chan ? chan->slave() : TelEngine::ClientChannel::SlaveNone;
    if (slave) {
        const TelEngine::String& masterId = chan->master();
        TelEngine::ClientChannel* master = PluginClientDriver::findChan(masterId);
        if (!master) {
            PluginClientDriver::dropChan(chan->id());
            return false;
        }
        if (notif == TelEngine::ClientChannel::Startup) {
            // Update master
            bool conf = (slave == TelEngine::ClientChannel::SlaveConference);
            if (conf || slave == TelEngine::ClientChannel::SlaveTransfer) {
                TelEngine::NamedList p("");
                master->addSlave(chan->id());
                channelItemAdjustUiList(p,-1,true,masterId,conf);
                if (conf) {
                    int n = master->slavesCount();
                    if (n == 1) {
                        if (master->hasReconnPeer())
                            channelItemBuildUpdate(true,p,masterId,conf,false,masterId);
                    }
                    TelEngine::String tmp;
                    tmp << "Conference (" << (n + 1) << ")";
                    p.addParam("status",tmp);
                }
                else {
                    channelItemBuildUpdate(false,p,masterId,conf,true);
                }
                channelItemBuildUpdate(true,p,masterId,conf,false,chan->id());
                //Client::self()->setTableRow(s_channelList,masterId,&p);
                m_events->log("Update channel list");
            }
        }
        TelEngine::destruct(master);
    }
    
    // Update UI
    TelEngine::NamedList p("");
    bool updateFormats = !slave;
    bool enableActions = false;
    bool setStatus = !slave && notConf && !chan->transferId();
    TelEngine::String status;
    switch (notif) {
        case TelEngine::ClientChannel::Active:
    	    buildStatus(status,"Call active",CHANUPD_ADDR,CHANUPD_ID);
            if (slave)
                break;
            enableActions = true;
            updateFormats = false;
            
            //Client::self()->setSelect(s_channelList,CHANUPD_ID);
            //m_events->log("channel activated");
            //setImageParam(p,"status_image","activ.png",false);
            if (outgoing) {
                if (noticed) {
                    m_events->ringer(true,false);
                }
            }
            else {
                m_events->ringer(true,false);
                if (silence) {
                    m_events->ringer(false,true);
                }
            }
            break;
        case TelEngine::ClientChannel::AudioSet:
            if (chan) {
                bool mic = chan->muted() || (0 != chan->getSource());
                bool speaker = (0 != chan->getConsumer());
                //notifyNoAudio(!(mic && speaker),mic,speaker,chan);
                m_events->log("Notify on no audio");
            }
            break;
        case TelEngine::ClientChannel::OnHold:
            buildStatus(status,"Call inactive",CHANUPD_ADDR,CHANUPD_ID);
            if (slave)
                break;
            enableActions = true;
            //setImageParam(p,"status_image","hold.png",false);
            
            m_events->callPaused(CHANUPD_ID.c_str());
            
            if (outgoing) {
                if (noticed) {
                    m_events->ringer(true,false);
                }
            }
            else {
                m_events->ringer(true,false);
                m_events->ringer(false,false);
            }
            break;
        case TelEngine::ClientChannel::Ringing:
            buildStatus(status,"Call ringing",CHANUPD_ADDR,CHANUPD_ID);
            break;
        case TelEngine::ClientChannel::Noticed:
            // Stop incoming ringer
            m_events->ringer(true,false);
            buildStatus(status,"Call noticed",CHANUPD_ADDR,CHANUPD_ID);
            break;
        case TelEngine::ClientChannel::Progressing:
            buildStatus(status,"Call progressing",CHANUPD_ADDR,CHANUPD_ID);
            break;
        case TelEngine::ClientChannel::Startup:
            if (slave)
                break;
            enableActions = true;
            // Create UI entry
            if (chan/* && Client::self()->addTableRow(s_channelList,CHANUPD_ID,&p)*/) {
                //DurationUpdate* d = new DurationUpdate(this,false,CHANUPD_ID,"time");
                //chan->setClientData(d);
                //TelEngine::destruct(d);
            }
            else
                return false;
            if (outgoing) {
                //addTrayIcon(YSTRING("incomingcall"));
                //Client::self()->setUrgent(s_wndMain,true,Client::self()->getWindow(s_wndMain));
                //m_events->log("Add tray icon: incoming call. Set urgent");
            }
            p.addParam("active:answer", TelEngine::String::boolText(outgoing));
            p.addParam("party",chan ? chan->party() : "");
            p.addParam("status",outgoing ? "Incoming" : "Outgoing");
            //setImageParam(p,"direction",outgoing ? "incoming.png" : "outgoing.png",false);
            //setImageParam(p,"status_image",active ? "active.png" : "hold.png",false);
            p.addParam("show:frame_items", TelEngine::String::boolText(false));
            // Start incoming ringer if there is no active channel
            if (outgoing && notConf) {
                m_events->ringer(true,true);
                //TelEngine::ClientChannel* ch = PluginClientDriver::findActiveChan();
                //if (!ch) {
                //    m_events->ringer(true,true);
                //}
                //else
                //    TelEngine::destruct(ch);
            }
            setStatus = false;
            break;
        case TelEngine::ClientChannel::Accepted:
            buildStatus(status,"Calling target",0,0);
            break;
        case TelEngine::ClientChannel::Answered:
            if (outgoing) {
                //removeTrayIcon(YSTRING("incomingcall"));
                m_events->log("remove tray icon incoming call");
            }
            buildStatus(status,"Call answered",CHANUPD_ADDR,CHANUPD_ID);
            // Stop incoming ringer
            m_events->ringer(true,false);
            if (active) {
                m_events->ringer(false,false);
            }
            if (slave)
                break;
            enableActions = true;
            p.addParam("active:answer", TelEngine::String::boolText(false));
            m_events->callAnswered(CHANUPD_ID.c_str());
            break;
        case TelEngine::ClientChannel::Routed:
            updateFormats = false;
            buildStatus(status,"Calling",chan ? chan->party() : "",0,0);
            break;
        case TelEngine::ClientChannel::Rejected:
            updateFormats = false;
            buildStatus(status,"Call failed",CHANUPD_ADDR,CHANUPD_ID,msg.getValue("reason"));
            break;
        case TelEngine::ClientChannel::Transfer:
            updateFormats = false;
            if (slave)
                break;
            if (chan->transferId())
                p.addParam("status","Transferred");
            break;
        case TelEngine::ClientChannel::Conference:
            updateFormats = false;
            if (slave)
                break;
            break;
        default:
            enableActions = true;
            updateFormats = false;
            buildStatus(status,TelEngine::String("Call notification=") + msg.getValue("notify"), CHANUPD_ADDR,CHANUPD_ID);
    }
    
    if (enableActions && m_selectedChannel == CHANUPD_ID)
        enableCallActions(m_selectedChannel);
    if (status) {
        //Client::self()->setStatusLocked(status);
        m_events->log(status.c_str());
    }
    if (updateFormats && chan) {
        TelEngine::String fmt;
        fmt << (chan->peerOutFormat() ? chan->peerOutFormat().c_str() : "-");
        fmt << "/";
        fmt << (chan->peerInFormat() ? chan->peerInFormat().c_str() : "-");
        p.addParam("format",fmt);
    }
    if (setStatus && chan) {
        TelEngine::String s = chan->status().substr(0,1).toUpper() + chan->status().substr(1);
        p.setParam("status",s);
    }
    if (!slave) {
        //Client::self()->setTableRow(s_channelList,CHANUPD_ID,&p);
        //m_events->log("update channels list");
    }
    return false;
    
#undef CHANUPD_ID
#undef CHANUPD_ADDR    
}

int YateEngineProxy::channelItemAdjustUiList(TelEngine::NamedList& dest, int show, bool itemAdded, const TelEngine::String& chanId, bool conf) {
    return 0;
}

void YateEngineProxy::channelItemBuildUpdate(bool upd, TelEngine::NamedList& dest, const TelEngine::String& masterChan, bool conf, bool start, const TelEngine::String& slaveId, bool updateExistin) {
    
}

bool YateEngineProxy::enableCallActions(const TelEngine::String& id) {
    return true;
}

// Utility function used to build channel status
void YateEngineProxy::buildStatus(TelEngine::String& status, const char* stat, const char* addr, const char* id, const char* reason) {
    status << stat;
    if (addr || id)
        status << ": " << (addr ? addr : id);
    if (reason)
        status << " reason: " << reason;
}

/**
 * IM message routing handler called by the driver
 * @param msg The im.route message
 */
bool YateEngineProxy::imRouting(TelEngine::Message& msg) {
    return true;
}

/**
 * Call routing handler called by the driver
 * @param msg The call.route message
 */
bool YateEngineProxy::callRouting(TelEngine::Message& msg) {
    return true;
}


/**
 * Process an IM message
 * @param msg The im.execute of chan.text message
 */
bool YateEngineProxy::imExecute(TelEngine::Message& msg) {
    return true;
}

// Call execute handler called by the driver
bool YateEngineProxy::callIncoming(TelEngine::Message& msg, const TelEngine::String& dest) {
    static const TelEngine::String sect = "miscellaneous";
    
    // Let client decide whether to drop a call or not
    /*
    // if we have already a channel, reject the call
    if (PluginClientDriver::self() && PluginClientDriver::self()->isBusy()) {
        msg.setParam("error", "busy");
        msg.setParam("reason", "User busy");
        return false;
    }*/

    // Check for a preferred or only logic
    //TelEngine::String name = "callincoming";
    //TelEngine::String handle;    
    
    const TelEngine::String& fmt = msg["format"];
    if (!fmt || fmt != "data") {
        // Set params for incoming google voice call
        if (msg["module"] == "jingle") {
            TelEngine::URI uri(msg["callername"]);
            if (uri.getHost() == "voice.google.com") {
                msg.setParam("dtmfmethod","rfc2833");
                msg.setParam("jingle_flags","noping");
            }
        }
        return buildIncomingChannel(msg,dest);
    }
    
    return false;

    // Incoming file transfer handling
    // TODO: add engine event for file transfers
    
    /*
    if (!(msg.userData() && PluginClientDriver::self()))
        return false;
    
    TelEngine::CallEndpoint* peer = static_cast<TelEngine::CallEndpoint*>(msg.userData());
    if (!peer)
        return false;
    
    const TelEngine::String& file = msg["file_name"];
    if (!file)
        return false;
    
    const TelEngine::String& oper = msg["operation"];
    if (oper != "receive")
        return false;
    
    TelEngine::Message m(msg);
    m.userData(msg.userData());
    m.setParam("callto","dumb/");
    if (!TelEngine::Engine::dispatch(m))
        return false;
    TelEngine::String targetid = m["targetid"];
    if (!targetid)
        return false;
    msg.setParam("targetid",targetid);
    
    static const TelEngine::String extra = "targetid,file_name,file_size,file_md5,file_time";
    const TelEngine::String& contact = msg["callername"];
    const TelEngine::String& account = msg["in_line"];
    TelEngine::ClientAccount* a = account ? m_accounts->findAccount(account) : 0;
    TelEngine::ClientContact* c = a ? a->findContactByUri(contact) : 0;
    NamedList rows("");
    NamedList* upd = buildNotifArea(rows,"incomingfile",account,contact,"Incoming file",extra);
    upd->copyParams(msg,extra);
    String text;
    text << "Incoming file '" << file << "'";
    String buf;
    if (c)
        buildContactName(buf,*c);
    else
        buf = contact;
    text.append(buf,"\r\nContact: ");
    text.append(account,"\r\nAccount: ");
    upd->addParam("text",text);
    showNotificationArea(true,Client::self()->getWindow(s_wndMain),&rows);
    return true;
    */
}

bool YateEngineProxy::buildOutgoingChannel(TelEngine::NamedList& params) {
    TelEngine::NamedString* target = params.getParam("target");
    if (TelEngine::null(target)) return false;
    
    // Create the channel. Release driver's mutex as soon as possible
    //if (!driverLockLoop()) return false;
    
    int st = TelEngine::ClientChannel::SlaveNone;
    TelEngine::String masterChan;
    TelEngine::NamedString* slave = params.getParam("channel_slave_type");
    
    if (slave) {
        st = TelEngine::ClientChannel::lookupSlaveType(*slave);
        params.clearParam(slave);
        TelEngine::NamedString* m = params.getParam("channel_master");
        if (st && m)
            masterChan = *m;
        params.clearParam(m);
    }
    
    TelEngine::ClientChannel* chan = new TelEngine::ClientChannel(*target,params,st,masterChan);
    chan->initChan();
    if (!(chan->ref() && chan->start(*target,params)))
        TelEngine::destruct(chan);
    //driverUnlock();
    if (!chan) return false;
    params.addParam("channelid",chan->id());
    //if (!st && (getBoolOpt(OptActivateLastOutCall) || !PluginClientDriver::self()->activeId())) {
    PluginClientDriver::self()->setActive(chan->id());
    //}
    TelEngine::destruct(chan);
    return true;    
}

// Build an incoming channel
bool YateEngineProxy::buildIncomingChannel(TelEngine::Message& msg, const TelEngine::String& dest)
{
    if (!(msg.userData() && PluginClientDriver::self()))
        return false;
    TelEngine::CallEndpoint* peer = static_cast<TelEngine::CallEndpoint*>(msg.userData());
    if (!peer)
        return false;
    PluginClientDriver::self()->lock();
    TelEngine::ClientChannel* chan = new TelEngine::ClientChannel(msg,peer->id());
    chan->initChan();
    PluginClientDriver::self()->unlock();
    bool ok = chan->connect(peer,msg.getValue("reason"));
    // Activate or answer
    if (ok) {
        // Open an incoming URL if configured
        /*if (getBoolOpt(OptOpenIncomingUrl)) {
         String* url = msg.getParam(s_incomingUrlParam);
         if (!null(url) && Client::self() && !Client::self()->openUrlSafe(*url))
         Debug(ClientDriver::self(),DebugMild,"Failed to open incoming url=%s",url->c_str());
         }*/
        msg.setParam("targetid",chan->id());
        const TelEngine::String& called = msg["called"];
        const TelEngine::String& caller = msg["caller"];
        PluginClientDriver::self()->setActive(chan->id());
        m_events->callIncoming(caller.c_str(), called.c_str(), chan->id().c_str());

        /*if (!getBoolOpt(OptAutoAnswer)) {
         if (getBoolOpt(OptActivateLastInCall) && !ClientDriver::self()->activeId())
         ClientDriver::self()->setActive(chan->id());
         }
         else*/
        //chan->callAnswer();
    }
    TelEngine::destruct(chan);
    return ok;
}

