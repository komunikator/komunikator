Ext.define('app.module.Groups_Grid', {
    extend     : 'app.Grid',
    
    store_cfg  : {
        fields   : ['id', 'group', 'description', 'extension'],   
        storeId  : 'groups'
    },
    
    columns    : [
        
    {  // 'id'
        hidden : true
    },

    {  // 'group'
        editor : {
            xtype       : 'textfield',
            allowBlank  : false
        }
    },

    {  // 'description'
        editor : {
            xtype : 'textfield'
        }
    },

    {  // 'extension'
        width  : 130,
        
        editor : {
            xtype       : 'textfield',
            regex       : /^\d{2}$/,
            allowBlank  : false
        }
    }
    
    ],
    
    initComponent : function() {
        // this.title = app.msg.extensions;
        this.callParent(arguments);
        
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // при внесении изменений в хранилище groups
        // повторная загрузка (обновление записей) хранилища groups_extended

        this.store.on('load',

            function(store, records, success) {

                var grid = Ext.getCmp(this.storeId + '_grid');  // поиск объекта по ID
                if (grid && !this.autoLoad)
                    grid.ownerCt.body.unmask();  // «серый» экран – блокировка действий пользователя
                this.Total_sync();  // количество записей
                this.dirtyMark = false;  // измененных записей нет
                if (!success && store.storeId) {
                    store.removeAll();
                    if (store.autorefresh != undefined)
                        store.autorefresh = false;
                    console.log('ERROR: ' + store.storeId + ' fail_load [code of Groups_Grid.js]');
                }
                
                
                var repository_exists = Ext.StoreMgr.lookup('groups_extended');
                
                if (repository_exists)
                    repository_exists.load()
                else
                    console.log('ERROR: groups_extended - fail_load [code of Groups_Grid.js]');
            }

        );
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    }
})
