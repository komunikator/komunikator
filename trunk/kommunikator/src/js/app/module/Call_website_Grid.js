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
/*var
 cb = new Ext.form.ComboBox({
 store: new Ext.data.ArrayStore({
 autoDestroy: true,
 idIndex: 0,
 fields: [
 { name: "id", type: "int" },
 "name"
 ],
 data: [
 [ 1, "Record# 1" ],
 [ 2, "Record# 2" ],
 [ 3, "Record# 3" ],
 [ 4, "Record# 4" ]
 ]
 }),
 displayField: "name",
 valueField: "id",
 mode: "local",
 tpl: "<tpl for=\".\"><div class=\"x-combo-list-item<tpl if=\"id==2\"> red</tpl>\">{name}</div></tpl>",
 renderTo: Ext.getBody()
 });*/
//var color = [['Gray', 'Gray'], ['blue', 'blue'], ['azure', 'azure'], ['yellow', 'yellow'], ['red', 'red'], ['dark gray', 'dark gray']];
//var tpl3=new Ext.Template(«<div class='domexample'><b>{f}</b> {i} {o}</div>»);
/*var tpll = new Ext.XTemplate(
 '<tpl for="."><div class="x-combo-list-item" style="color: #ffffc0;">{color}</div></tpl>'
 );*/
var color = ['Gray', 'blue', 'azure', 'yellow', 'red', 'dark gray'];
Ext.define('app.module.Call_website_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        fields: ['id', 'description', 'destination', 'short_name', 'color', 'button_code'],
        storeId: 'call_button'
    },
    columns: [
        {// 'id'
            hidden: true

        },
        {// 'description'  - описание
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'destination' - назначение

            editor: app.get_Source_Combo({
                allowBlank: false
            })
        },
        {// 'short_name' - псевдоним
            editor: {
                xtype: 'textfield'
            }
        },
        {//'button_color' - цвет кнопки
            editor: {
                xtype: 'combobox',
                // mode: 'local',
                editable: true,
            
                store: color,
              /*  tpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<div class="x-combo-list-item" style="background-color:{field1}">{field1}</div>',
                        '</tpl>'
                        ),
                displayTpl1: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '{field1}',
                        '</tpl>'
                        )*/
         
             
            }
        },
        {//'button_code' - код кнопки
            xtype: 'actioncolumn',
            sortable: false,
            groupable: false,
            icon: 'js/app/images/add.png', // Use a URL in the icon config
            tooltip: 'Generate code',
            handler: function() {
                Ext.create('app.Page_Code').show();
            }
        }
    ],
    initComponent: function() {
        this.callParent(arguments);
    }
});