#/**********************************************************\ 
#
# Auto-Generated Plugin Configuration file
# for Kommunikator
#
#\**********************************************************/

set(PLUGIN_NAME "Kommunikator")
set(PLUGIN_PREFIX "KOM")
set(COMPANY_NAME "DIGT")

# ActiveX constants:
set(FBTYPELIB_NAME KommunikatorLib)
set(FBTYPELIB_DESC "Kommunikator 1.0 Type Library")
set(IFBControl_DESC "Kommunikator Control Interface")
set(FBControl_DESC "Kommunikator Control Class")
set(IFBComJavascriptObject_DESC "Kommunikator IComJavascriptObject Interface")
set(FBComJavascriptObject_DESC "Kommunikator ComJavascriptObject Class")
set(IFBComEventSource_DESC "Kommunikator IFBComEventSource Interface")
set(AXVERSION_NUM "1")

# NOTE: THESE GUIDS *MUST* BE UNIQUE TO YOUR PLUGIN/ACTIVEX CONTROL!  YES, ALL OF THEM!
set(FBTYPELIB_GUID 1726d963-70d8-56bb-ba0f-4f898f15e272)
set(IFBControl_GUID 69dbb45d-7e45-5d4c-9439-aa780dcb9ead)
set(FBControl_GUID 518cb631-9df3-5d0a-b73a-474c7ccb2c4c)
set(IFBComJavascriptObject_GUID 7f8ca9f1-d9b3-5aca-bdc6-c96fe47c6842)
set(FBComJavascriptObject_GUID 22379850-1251-5e27-b088-4880cc984e12)
set(IFBComEventSource_GUID 024300be-8a03-5326-ba0d-aacb1b1fbeee)

# these are the pieces that are relevant to using it from Javascript
set(ACTIVEX_PROGID "DIGT.Kommunikator")
set(MOZILLA_PLUGINID "digt.ru/Kommunikator")

# strings
set(FBSTRING_CompanyName "DIGT")
set(FBSTRING_FileDescription "Provides SIP telephony Javascript API")
set(FBSTRING_PLUGIN_VERSION "1.0.0.0")
set(FBSTRING_LegalCopyright "Copyright 2012 DIGT")
set(FBSTRING_PluginFileName "np${PLUGIN_NAME}.dll")
set(FBSTRING_ProductName "Kommunikator")
set(FBSTRING_FileExtents "")
set(FBSTRING_PluginName "Kommunikator")
set(FBSTRING_MIMEType "application/x-kommunikator")

# Uncomment this next line if you're not planning on your plugin doing
# any drawing:

set (FB_GUI_DISABLED 1)

# Mac plugin settings. If your plugin does not draw, set these all to 0
set(FBMAC_USE_QUICKDRAW 0)
set(FBMAC_USE_CARBON 0)
set(FBMAC_USE_COCOA 0)
set(FBMAC_USE_COREGRAPHICS 0)
set(FBMAC_USE_COREANIMATION 0)
set(FBMAC_USE_INVALIDATINGCOREANIMATION 0)

# If you want to register per-machine on Windows, uncomment this line
#set (FB_ATLREG_MACHINEWIDE 1)
