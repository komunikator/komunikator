/*
 *  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
 *    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
 *    Copyright (C) 2012-2013, ООО «Телефонные системы»
 
 *    ЭТОТ ФАЙЛ является частью проекта «Komunikator»
 
 *    Сайт проекта «Komunikator»: http://komunikator.ru/
 *    Служба технической поддержки проекта «Komunikator»: E-mail: support@komunikator.ru
 
 *    В проекте «Komunikator» используются:
 *      исходные коды проекта «YATE», http://yate.null.ro/pmwiki/
 *      исходные коды проекта «FREESENTRAL», http://www.freesentral.com/
 *      библиотеки проекта «Sencha Ext JS», http://www.sencha.com/products/extjs
 
 *    Web-приложение «Komunikator» является свободным и открытым программным обеспечением. Тем самым
 *  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
 *  и иные права) согласно условиям GNU General Public License, опубликованной
 *  Free Software Foundation, версии 3.
 
 *    В случае отсутствия файла «License» (идущего вместе с исходными кодами программного обеспечения)
 *  описывающего условия GNU General Public License версии 3, можно посетить официальный сайт
 *  http://www.gnu.org/licenses/ , где опубликованы условия GNU General Public License
 *  различных версий (в том числе и версии 3).
 
 *  | ENG | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
 *    "Komunikator" is a web interface for IP-PBX "YATE" configuration and management
 *    Copyright (C) 2012-2013, "Telephonnyie sistemy" Ltd.
 
 *    THIS FILE is an integral part of the project "Komunikator"
 
 *    "Komunikator" project site: http://komunikator.ru/
 *    "Komunikator" technical support e-mail: support@komunikator.ru
 
 *    The project "Komunikator" are used:
 *      the source code of "YATE" project, http://yate.null.ro/pmwiki/
 *      the source code of "FREESENTRAL" project, http://www.freesentral.com/
 *      "Sencha Ext JS" project libraries, http://www.sencha.com/products/extjs
 
 *    "Komunikator" web application is a free/libre and open-source software. Therefore it grants user rights
 *  for distribution and (or) modification (including other rights) of this programming solution according
 *  to GNU General Public License terms and conditions published by Free Software Foundation in version 3.
 
 *    In case the file "License" that describes GNU General Public License terms and conditions,
 *  version 3, is missing (initially goes with software source code), you can visit the official site
 *  http://www.gnu.org/licenses/ and find terms specified in appropriate GNU General Public License
 *  version (version 3 as well).
 
 *  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 */

Ext.apply(Ext.form.field.VTypes, {
    ipVal: function(val, field) {
        if (/^([1-9][0-9]{0,1}|1[013-9][0-9]|12[0-689]|2[01][0-9]|22[0-3])([.]([1-9]{0,1}[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])){2}[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-4])$/.test(val))
        {
            return true;
        }
        return false;
    },
    ipValText: app.msg.invalid_ip_address,
    ipMask: function(val, field) {
        if (/^(128|192|224|24[08]|25[245].0.0.0)|(255.(0|128|192|224|24[08]|25[245]).0.0)|(255.255.(0|128|192|224|24[08]|25[245]).0)|(255.255.255.(0|128|192|224|24[08]|252))$/.test(val))
        {
            return true;
        }
        return false;
    },
    ipMaskText: app.msg.invalid_netmask
});

Ext.define('app.module.Network_Settings_Panel', {
    id: 'ID_Network_Settings',
    extend: 'Ext.form.Panel',
    url: 'data.php?action=net_settings',
    //autoLoad: true,
    style: 'padding:40px;',
    frame: true,
    waitMsgTarget: true,
    autoScroll: true,
    fieldDefaults: {
        labelAlign: 'right',
        labelWidth: 100,
        msgTarget: 'side'
    },
    buttons: [{
            monitorValid: true,
            formBind: true,
            text: app.msg.save,
            //action: 'save',
            handler: function() {
                var form = this.ownerCt.ownerCt.getForm();
                console.log(form.getValues());
                if (form.isValid()) {
                    form.submit();
                }
            }

        },
        {
            text: app.msg.load,
            handler: function() {
                var form = this.ownerCt.ownerCt.getForm();
                form.load();
            }

        }],
    initComponent: function() {

        this.items = [
            {
                xtype: 'radiogroup',
                columns: 1,
                listeners: {
                    change: function(field, newVal, oldVal) {
                        if (field.getValue().type) {
                            //field.enable();
                        } else {
                            //field.ownerCt.getComponent('type').disable();
                        }
                    }

                },
                items: [
                    {
                        boxLabel: app.msg.auto_dhcp,
                        name: 'type',
                        inputValue: 0,
                        checked: true
                    },
                    {
                        boxLabel: app.msg.static_ip,
                        itemId: 'type',
                        name: 'type',
                        inputValue: 1
                    }
                ]
            }, {
                xtype: 'fieldset',
                border: true,
                //disabled: true,
                items: [
                    {
                        xtype: 'textfield',
                        name: 'dev',
                        hidden: true
                    },
                    {
                        xtype: 'textfield',
                        name: 'ipaddress',
                        fieldLabel: app.msg.ip_address,
                        vtype: 'ipVal'
                    },
                    {
                        xtype: 'textfield',
                        name: 'ipmask',
                        fieldLabel: app.msg.netmask,
                        vtype: 'ipMask'
                    },
                    {
                        xtype: 'textfield',
                        name: 'ipgateway',
                        fieldLabel: app.msg.gateway,
                        vtype: 'ipVal',
                        allowBlank: true
                    }]
            }];
        this.callParent(arguments);
        var form = this.getForm();
        form.load();
    }
})