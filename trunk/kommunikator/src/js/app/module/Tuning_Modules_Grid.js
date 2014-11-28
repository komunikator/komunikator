/*
 *  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
 *    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
 *    Copyright (C) 2012-2013, ООО «Телефонные системы»
 
 *    ЭТОТ ФАЙЛ является частью проекта «Komunikator»
 
 *    Сайт проекта «Komunikator»: http://4yate.ru/
 *    Служба технической поддержки проекта «Komunikator»: E-mail: support@4yate.ru
 
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
 
 *    "Komunikator" project site: http://4yate.ru/
 *    "Komunikator" technical support e-mail: support@4yate.ru
 
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

Ext.define('app.module.Tuning_Modules_Grid', {
    extend: 'app.Grid',
    no_adddelbuttons: true,
    store_cfg: {
        autorefresh: false,
        fields: ['id', 'module_name', 'description', 'version', 'condition'],
        storeId: 'modules'
    },
    columns: [
        {// 'id'
            hidden: true
        },
        {// 'module_name'
            width: 150,
            renderer: function(v) {
                if (v == 'Mail_Settings_Panel')
                    return app.msg.Mail_Settings_Panel;
                else
                if (v == 'Call_website_Grid')
                    return app.msg.Call_website_Grid;
                else
                if (v == 'Call_Record_Grid')
                    return app.msg.Call_Record_Grid;
            },
            editor: {
                xtype: 'textfield',
                disabled: true
            }
        },
        {// 'description'
            width: 500,
            renderer: function(v) {
                if (v == 'text_call_website')
                    return app.msg.text_call_website;
                else
                if (v == 'text_mail_Settings')
                    return app.msg.text_mail_Settings;
                else
                if (v == 'text_call_record')
                    return app.msg.text_call_record;
            },
            editor: {
                xtype: 'textfield',
                disabled: true
            }
        },
        {// 'version'
            width: 70,
            editor: {
                xtype: 'textfield',
                disabled: true
            }
        },
        {//'condition'
            renderer: app.checked_render,
            editor: {
                xtype: 'checkbox',
                style: {
                    textAlign: 'center'
                },
                queryMode: 'local'
            }
        }
    ],
    initComponent: function() {
        this.callParent(arguments);
        this.store.on('load',
                function(store, records, success) {
                    store.each(function(record)
                    {
                        Ext.getCmp('main_tabpanel').remove('modules', true);
                        var items = [];
                        store.each(function(record)
                        {
                            var module_name = null;
                            var condition = null;
                            record.fields.each(function(field)
                            {
                                var fieldValue = record.get(field.name);
                                if (field.name == 'module_name')
                                    module_name = fieldValue;
                                if (field.name == 'condition')
                                    condition = fieldValue;
                            });
                            //console.log(module_name + ':' + condition);
                            if (condition == '1')
                                items.push(Ext.create('app.module.' + module_name, {title: app.msg[module_name]}));
                        });
                        if (items.length !== 0)
                            Ext.getCmp('main_tabpanel').add(Ext.create('app.Card_Panel', {
                                id: 'modules',
                                title: app.msg.modules,
                                items: items
                            }));
                    });
                }, this);
    }
});