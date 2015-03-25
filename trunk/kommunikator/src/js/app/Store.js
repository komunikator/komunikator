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

Ext.define('app.Store', {
    extend: 'Ext.data.Store',
    constructor: function(cfg) {
        //console.log('store'+cfg.storeId);
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
                pageSize: app.pageSize,
                ////leadingBufferZone: 50,
                remoteSort: true,
                ////buffered: true,
                remoteFilter: true,
                autoLoad: true,
                //restful: true,
                //autoSync: true,
                proxy: {
                    type: 'ajax',
                    //url: 'data.php',
                    actionMethods: {
                        read: 'POST'
                    },
                    limitParam: 'size',
                    extraParams: {
                    },
                    api: {
                        read: 'data.php?action=get_' + cfg.storeId,
                        create: 'data.php?action=create_' + cfg.storeId,
                        update: 'data.php?action=update_' + cfg.storeId,
                        destroy: 'data.php?action=destroy_' + cfg.storeId
                    },
                    writer: {
                        type: 'json',
                        writeAllFields: false//,
                                //root: 'data'
                    },
                    reader: {
                        type: 'array',
                        root: 'data',
                        totalProperty: 'total',
                        messageProperty: 'message'
                    },
                    simpleSortMode: true
                },
                sorters: [{
                        direction: 'ASC'
                    }],
                listeners: {
                    totalcountchange: function() {
                        /*
                         {
                         xtype: 'component',
                         itemId: 'status',
                         tpl: 'Matching threads: {count}',
                         style: 'margin-right:5px'
                         }
                         */
                        //this.Total_sync();
                        //grid.down('#status').update({count: store.getTotalCount()});
                    },
                    beforeload: function(s) {
                        var grid = Ext.getCmp(this.storeId + '_grid');
                        if (grid && !this.autoLoad)
                            //grid.ownerCt.body.
                            Ext.getBody().mask(Ext.view.AbstractView.prototype.loadingText);
                        /*
                         setTimeout(function() {
                         if (grid && grid.ownerCt && grid.ownerCt.body) {
                         //console.log(grid.getId());
                         grid.ownerCt.body.unmask();
                         }
                         ;
                         }, 30000);
                         */

                    },
                    load: function(store, records, success) {
                        //console.log (store.storeId+' loaded '+ store.getTotalCount());
                        var grid = Ext.getCmp(store.storeId + '_grid');
                        if (grid && !this.autoLoad)
                            Ext.getBody().unmask(Ext.view.AbstractView.prototype.loadingText);
                        //grid.ownerCt.body.unmask();
                        if (grid && store.reselect) {
                            store.reselect(grid);
                        }
                        //this.Total_sync();
                        this.dirtyMark = false;
                        if (!success && store.storeId) {
                            store.removeAll();
                            //app.fail_load_show();
                            if (store.autorefresh != undefined)
                                store.autorefresh = false;
                            console.log(store.storeId + ' fail_load');
                        }
                    },
                    exception: function(proxy, response, options) {
                        Ext.getBody().unmask(Ext.view.AbstractView.prototype.loadingText);
                        this.removeAll();
                        console.log('exception: ', proxy, response, options);
                    }

                },
                mySync: function(callback) {
                    if (Ext.isDefined(callback.success))
                        this.on("write", function(store) {
                            callback.success.call(this);
                            store.un("write", callback.success);
                        });

                    this.sync();
                },
                Total_sync: function() {
                    this.dirtyMark = true;
                    var displayItem = Ext.getCmp(this.storeId + '_displayItem');
                    //console.log(displayItem);
                    if (this.storeId && displayItem) {
                        displayItem.setText((app.msg.total ? app.msg.total : 'Total') + ': ' + this.getTotalCount());
                        displayItem.ownerCt.doComponentLayout();
                        //displayItem.ownerCt.ownerCt.getView().refresh(true);
                    }
                    ;
                }

            }, cfg)]);
    }

})