# DO NOT EDIT
# This makefile makes sure all linkable targets are
# up-to-date with anything they link to
default:
	echo "Do not invoke directly"

# For each target create a dummy rule so the target does not have to exist
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Debug/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Debug/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/Debug/libUnitTest++.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Debug/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Debug/libboost_system.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/MinSizeRel/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/MinSizeRel/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/MinSizeRel/libUnitTest++.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/MinSizeRel/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/MinSizeRel/libboost_system.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/RelWithDebInfo/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/RelWithDebInfo/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/RelWithDebInfo/libUnitTest++.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/RelWithDebInfo/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/RelWithDebInfo/libboost_system.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/ScriptingCore/Release/libScriptingCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/PluginCore/Release/libPluginCore.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/unittest-cpp/UnitTest++/Release/libUnitTest++.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/thread/Release/libboost_thread.a:
/Projects/Kommunikator/kommunikator/client/plugin/FireBreath/build/boost/libs/system/Release/libboost_system.a:


# Rules to remove targets that are older than anything to which they
# link.  This forces Xcode to relink the targets from scratch.  It
# does not seem to check these dependencies itself.
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


