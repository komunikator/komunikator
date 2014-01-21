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

Ext.define('app.module.Time_Frames_Grid', {
    extend : 'app.Grid',
    
    no_adddelbuttons : true,
    
    store_cfg : {
        fields   : ['id', 'day', 'start_hour', 'end_hour'],
        storeId  : 'time_frames',
        
        sorters  : [{
            direction  : '',
            property   : ''	
        }]
    },
    
    // enableHdMenu : true,
    
    enableColumnHide : false,
    
    columns : [
    {  // 'id'
        hidden: true
    },
    {  // 'day'
        width : 125,
        
        sortable: false
    },
    {  // 'start_hour'
        width : 125,
        
        sortable: false,
        
        editor : {
            xtype	   : 'combobox',
            mode	   : 'local',
            editable	   : false,
            triggerAction  : 'all'
        }
    },
    {  // 'end_hour'
        width : 125,
        
        sortable: false,
        
        editor : {
            xtype	   : 'combobox',
            mode	   : 'local',
            editable	   : false,
            triggerAction  : 'all'
        }
    }
    ],
    
    columns_renderer :
    function(value, metaData, record, rowIndex, colIndex, store) {
        if (colIndex==1)
        {
            return app.msg[value];
        }
        if ((colIndex==2 || colIndex==3) && value==null)
        {
            return app.msg['notselected'];
        }
        return value;
    },
            
    initComponent : function() {
        var clock = [
        [null,app.msg['notselected']],
        ['1','1'],
        ['2','2'],
        ['3','3'],
        ['4','4'],
        ['5','5'],
        ['6','6'],
        ['7','7'],
        ['8','8'],
        ['9','9'],
        ['10','10'],
        ['11','11'],
        ['12','12'],
        ['13','13'],
        ['14','14'],
        ['15','15'],
        ['16','16'],
        ['17','17'],
        ['18','18'],
        ['19','19'],
        ['20','20'],
        ['21','21'],
        ['22','22'],
        ['23','23'],
        ['24','24']
        ];                      
        this.columns[2].editor.store = clock;
        this.columns[3].editor.store = clock;
        this.callParent(arguments); 
    }
});