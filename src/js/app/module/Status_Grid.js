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

app.grid_show = function(storeId) {
    var grid = Ext.getCmp(storeId + '_grid');
    if (grid) {
        grid.ownerCt.ownerCt.setActiveTab(grid.ownerCt.ownerCt.items.indexOf(grid.ownerCt));
        grid.ownerCt.layout.setActiveItem(grid.ownerCt.items.indexOf(grid));
        grid.getStore().load();
        grid.fireEvent('activate', grid);
    }
};

Ext.define('app.module.Status_Grid', {
    // title : 'day statistic',
    extend: 'app.Grid',
    border: false,
    style: 'padding : 15px',
    status_grid: true,
    grid_id: 'Status_Grid',
    store_cfg: {
        autorefresh: true,
        fields: ['name', 'value'],
        storeId: 'statistic'
    },
    height: 500,
    hideHeaders: true,
    columns: [
        {
            width: 120
        },
        {
            flex: 1
                    // width : 50
        }],
    columns_renderer: function(value, metadata, record, rowIndex, colIndex, store) {
        if (colIndex == 0 || colIndex == 1)
            if (app.msg[value])
                value = app.msg[value];

        if (colIndex == 1) {
            if (record.data.name == 'status') {
                var color = (value == app.msg['online']) ? 'green' : 'red';
                return '<span style="color:' + color + ';">' + value + '</span>';
            }
            if (record.data.name == 'cpu_use') {
                var color = (parseFloat(value.replace(/^([\d\.]+)\s%$/, "$1")) < app.critical_cpu) ? 'green' : 'red';
                return '<span style="color:' + color + ';">' + value + '</span>';
            }
        }
        ;

        if (colIndex == 0) {
            if (record.data.name == 'day_total_calls') {
                return '<a href="#" onclick="app.grid_show(' + "'call_logs'" + ');">' + value + '</a>';
            }
            ;
            if (record.data.name == 'active_calls') {
                return '<a href="#" onclick="app.grid_show(' + "'active_calls'" + ');">' + value + '</a>';
            }
            ;
            if (record.data.name == 'active_gateways') {
                return '<a href="#" onclick="app.grid_show(' + "'gateways'" + ');">' + value + '</a>';
            }
        }
        
        if(colIndex == 0){
            if(record.data.name == 'provider_wizard'){
                 return '<a href="#" onclick="Ext.create(\'app.Provider_Wizard\').show();">Мастер настройки<br>провайдеров</a>';
            }
        }

        return value;
    },
    initComponent: function() {
        this.callParent(arguments);
    }
})