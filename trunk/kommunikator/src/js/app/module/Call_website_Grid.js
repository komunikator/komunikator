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

Ext.define('app.module.Call_website_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        fields: ['id', 'group', 'name', 'description'],
        storeId: 'call_button'
    },
    columns: [
        {// 'id'
            hidden: true
        },
        {// 'group'
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        {// 'name'
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'description'
            editor: {
                xtype: 'textfield'
            }
        },
        {
            xtype: 'actioncolumn',
            width: 50,
            icon: 'js/app/images/add.png', // Use a URL in the icon config
            tooltip: 'Generate code',
            handler: function() {
                Ext.create('app.Page_Code').show();
                alert('Generate code');
            }

        }],
    initComponent: function() {
        // this.title = app.msg.extensions;
        this.columns[1] = {
            header: app.msg.group,
            sortable: true,
            groupable: true,
            text: app.msg.group,
            dataIndex: 'group_name',
            editor: {
                xtype: 'combobox',
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                // так, как хранилище создается только здесь,
                // код был поправлен
                /*
                 store         : Ext.StoreMgr.lookup('groups_extended') ?
                 Ext.StoreMgr.lookup('groups_extended') :
                 Ext.create('app.Store', {
                 fields   : ['id', 'group', 'description', 'extension'],
                 storeId  : 'groups_extended'
                 }),
                 */
                store: Ext.create('app.Store', {
                    fields: ['id', 'group', 'description', 'extension'],
                    storeId: 'groups_extended'
                }),
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                // queryCaching: false,
                // triggerAction: 'query',

                valueField: 'group',
                queryMode: 'local',
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                // настройка combobox под «себя»
                // «нормальное» отображение пустых полей в выпадающем списке

                // displayField  : 'group', <- заменено кодом ниже
                tpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<div class="x-boundlist-item" style="min-height: 22px">{group}</div>',
                        '</tpl>'
                        ),
                displayTpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '{group}',
                        '</tpl>'
                        ),
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                editable: false,
                listeners: {
                    afterrender: function() {
                        this.store.load();
                    }}
            }
        }
        ;
        this.callParent(arguments);


        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    }
});