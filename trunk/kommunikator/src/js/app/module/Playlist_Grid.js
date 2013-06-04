Ext.define('app.module.Playlist_Grid', {
    extend : 'app.Grid',
    load_after_edit: true,
    store_cfg : {
        fields : ['id', 'playlist', 'in_use'],
        storeId : 'playlists'
        // storeId : 'playlist_extended'
    },
    columns : [
    { //id
        hidden: true
    },
    {   //playlist
        editor :  {
            xtype: 'textfield',
            allowBlank: false
        }
    },
    {  //in_use
        renderer : app.checked_render,
        editor :  {
            xtype: 'checkbox',
            listeners:{
                change: function(c,n,o){
                //var store = Ext.StoreMgr.lookup('playlists');
                //	store.load();
                /*Ext.each(store.getRange(),function(record){
                        record.set({
                            in_use:!n
                        });
                        console.log(record);
                    });
		*/
                }
            },	
            style: {
                textAlign: 'center'
            } 
        }
    }
    ],
    initComponent : function () {
        this.callParent(arguments);
   // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // при внесении изменений в хранилище playlist
        // повторная загрузка (обновление записей) хранилища playlist_extended
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
                    console.log('ERROR: ' + store.storeId + ' fail_load [code of Playlist_Grid.js]');
                }
                
                
                var repository_exists = Ext.StoreMgr.lookup('playlist_extended');
                
                if (repository_exists)
                    repository_exists.load()
                else
                    console.log('ERROR: playlist_extended - fail_load [code of Playlist_Grid.js]');
            }

        );
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    }
})

