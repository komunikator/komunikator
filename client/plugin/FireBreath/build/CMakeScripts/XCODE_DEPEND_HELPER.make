# DO NOT EDIT
# This makefile makes sure all linkable targets are
# up-to-date with anything they link to
default:
	echo "Do not invoke directly"

# For each target create a dummy rule so the target does not have to exist
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Debug/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/Debug/libKOM_PluginAuto.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/Debug/libNpapiCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Debug/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Debug/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Debug/libboost_system.a:
/usr/local/lib/libyate.so:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/MinSizeRel/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/MinSizeRel/libKOM_PluginAuto.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/MinSizeRel/libNpapiCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/MinSizeRel/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/MinSizeRel/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/MinSizeRel/libboost_system.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/RelWithDebInfo/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/RelWithDebInfo/libKOM_PluginAuto.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/RelWithDebInfo/libNpapiCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/RelWithDebInfo/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/RelWithDebInfo/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/RelWithDebInfo/libboost_system.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Release/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/Release/libKOM_PluginAuto.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/Release/libNpapiCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Release/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Release/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Release/libboost_system.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/Debug/libUnitTest++.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/MinSizeRel/libUnitTest++.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/RelWithDebInfo/libUnitTest++.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/Release/libUnitTest++.a:


# Rules to remove targets that are older than anything to which they
# link.  This forces Xcode to relink the targets from scratch.  It
# does not seem to check these dependencies itself.
PostBuild.FireBreath_Cmake.Debug:
PostBuild.boost_thread.Debug:
PostBuild.boost_system.Debug:
PostBuild.ScriptingCore.Debug:
PostBuild.PluginCore.Debug:
PostBuild.NpapiCore.Debug:
PostBuild.KOM_PluginAuto.Debug:
PostBuild.Kommunikator.Debug:
PostBuild.PluginCore.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator
PostBuild.KOM_PluginAuto.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator
PostBuild.NpapiCore.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator
PostBuild.ScriptingCore.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator
PostBuild.PluginCore.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator
PostBuild.boost_thread.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator
PostBuild.boost_system.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Debug/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/Debug/libKOM_PluginAuto.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/Debug/libNpapiCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Debug/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Debug/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Debug/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Debug/libboost_system.a\
	/usr/local/lib/libyate.so
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Debug/Kommunikator


PostBuild.UnitTest++.Debug:
PostBuild.UnitTest_ScriptingCore.Debug:
PostBuild.ScriptingCore.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Debug/UnitTest_ScriptingCore
PostBuild.PluginCore.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Debug/UnitTest_ScriptingCore
PostBuild.UnitTest++.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Debug/UnitTest_ScriptingCore
PostBuild.boost_thread.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Debug/UnitTest_ScriptingCore
PostBuild.boost_system.Debug: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Debug/UnitTest_ScriptingCore
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Debug/UnitTest_ScriptingCore:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Debug/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Debug/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/Debug/libUnitTest++.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Debug/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Debug/libboost_system.a
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Debug/UnitTest_ScriptingCore


PostBuild.FireBreath_Cmake.Release:
PostBuild.boost_thread.Release:
PostBuild.boost_system.Release:
PostBuild.ScriptingCore.Release:
PostBuild.PluginCore.Release:
PostBuild.NpapiCore.Release:
PostBuild.KOM_PluginAuto.Release:
PostBuild.Kommunikator.Release:
PostBuild.PluginCore.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator
PostBuild.KOM_PluginAuto.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator
PostBuild.NpapiCore.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator
PostBuild.ScriptingCore.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator
PostBuild.PluginCore.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator
PostBuild.boost_thread.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator
PostBuild.boost_system.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Release/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/Release/libKOM_PluginAuto.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/Release/libNpapiCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Release/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Release/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Release/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Release/libboost_system.a\
	/usr/local/lib/libyate.so
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/Release/Kommunikator


PostBuild.UnitTest++.Release:
PostBuild.UnitTest_ScriptingCore.Release:
PostBuild.ScriptingCore.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Release/UnitTest_ScriptingCore
PostBuild.PluginCore.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Release/UnitTest_ScriptingCore
PostBuild.UnitTest++.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Release/UnitTest_ScriptingCore
PostBuild.boost_thread.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Release/UnitTest_ScriptingCore
PostBuild.boost_system.Release: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Release/UnitTest_ScriptingCore
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Release/UnitTest_ScriptingCore:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Release/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Release/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/Release/libUnitTest++.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Release/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Release/libboost_system.a
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/Release/UnitTest_ScriptingCore


PostBuild.FireBreath_Cmake.MinSizeRel:
PostBuild.boost_thread.MinSizeRel:
PostBuild.boost_system.MinSizeRel:
PostBuild.ScriptingCore.MinSizeRel:
PostBuild.PluginCore.MinSizeRel:
PostBuild.NpapiCore.MinSizeRel:
PostBuild.KOM_PluginAuto.MinSizeRel:
PostBuild.Kommunikator.MinSizeRel:
PostBuild.PluginCore.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator
PostBuild.KOM_PluginAuto.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator
PostBuild.NpapiCore.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator
PostBuild.ScriptingCore.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator
PostBuild.PluginCore.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator
PostBuild.boost_thread.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator
PostBuild.boost_system.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/MinSizeRel/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/MinSizeRel/libKOM_PluginAuto.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/MinSizeRel/libNpapiCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/MinSizeRel/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/MinSizeRel/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/MinSizeRel/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/MinSizeRel/libboost_system.a\
	/usr/local/lib/libyate.so
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/MinSizeRel/Kommunikator


PostBuild.UnitTest++.MinSizeRel:
PostBuild.UnitTest_ScriptingCore.MinSizeRel:
PostBuild.ScriptingCore.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/MinSizeRel/UnitTest_ScriptingCore
PostBuild.PluginCore.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/MinSizeRel/UnitTest_ScriptingCore
PostBuild.UnitTest++.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/MinSizeRel/UnitTest_ScriptingCore
PostBuild.boost_thread.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/MinSizeRel/UnitTest_ScriptingCore
PostBuild.boost_system.MinSizeRel: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/MinSizeRel/UnitTest_ScriptingCore
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/MinSizeRel/UnitTest_ScriptingCore:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/MinSizeRel/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/MinSizeRel/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/MinSizeRel/libUnitTest++.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/MinSizeRel/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/MinSizeRel/libboost_system.a
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/MinSizeRel/UnitTest_ScriptingCore


PostBuild.FireBreath_Cmake.RelWithDebInfo:
PostBuild.boost_thread.RelWithDebInfo:
PostBuild.boost_system.RelWithDebInfo:
PostBuild.ScriptingCore.RelWithDebInfo:
PostBuild.PluginCore.RelWithDebInfo:
PostBuild.NpapiCore.RelWithDebInfo:
PostBuild.KOM_PluginAuto.RelWithDebInfo:
PostBuild.Kommunikator.RelWithDebInfo:
PostBuild.PluginCore.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator
PostBuild.KOM_PluginAuto.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator
PostBuild.NpapiCore.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator
PostBuild.ScriptingCore.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator
PostBuild.PluginCore.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator
PostBuild.boost_thread.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator
PostBuild.boost_system.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/RelWithDebInfo/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/PluginAuto/RelWithDebInfo/libKOM_PluginAuto.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/NpapiCore/RelWithDebInfo/libNpapiCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/RelWithDebInfo/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/RelWithDebInfo/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/RelWithDebInfo/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/RelWithDebInfo/libboost_system.a\
	/usr/local/lib/libyate.so
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/projects/Kommunikator/RelWithDebInfo/Kommunikator


PostBuild.UnitTest++.RelWithDebInfo:
PostBuild.UnitTest_ScriptingCore.RelWithDebInfo:
PostBuild.ScriptingCore.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/RelWithDebInfo/UnitTest_ScriptingCore
PostBuild.PluginCore.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/RelWithDebInfo/UnitTest_ScriptingCore
PostBuild.UnitTest++.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/RelWithDebInfo/UnitTest_ScriptingCore
PostBuild.boost_thread.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/RelWithDebInfo/UnitTest_ScriptingCore
PostBuild.boost_system.RelWithDebInfo: /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/RelWithDebInfo/UnitTest_ScriptingCore
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/RelWithDebInfo/UnitTest_ScriptingCore:\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/RelWithDebInfo/libScriptingCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/RelWithDebInfo/libPluginCore.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/RelWithDebInfo/libUnitTest++.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/RelWithDebInfo/libboost_thread.a\
	/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/RelWithDebInfo/libboost_system.a
	/bin/rm -f /Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/bin/RelWithDebInfo/UnitTest_ScriptingCore


