Ext.define('app.module.Conferences_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        autorefresh: false,
        fields: ['id', 'conference', 'number', 'participants'],
        storeId: 'conferences'
    },
    columns: [
        { //id
            hidden: true
        },
        { //conference - конференция
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        { //number
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // было создано отдельное хранилище number_extensions
            // в котором есть номера только внутренних пользователей

           editor  : {
                allowBlank: false,
                xtype         : 'combobox',
                
                store         : Ext.create('app.Store', {
                    fields   : ['id', 'name'],
                    storeId  : 'number_extensions'
                }),
                
                editable      : false,
                displayField  : 'name',
                valueField    : 'name',
                queryMode     : 'local'
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -






            //  editor :  {
            //    xtype: 'textfield',
            //  allowBlank: false
       
        },
        {
            //participants
        }
    ],
    initComponent: function() {
        //this.title = app.msg.extensions;       

        this.callParent(arguments);
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // при внесении изменений в хранилище number_extensions
        // повторная загрузка (обновление записей) хранилища sources

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
                        console.log('ERROR: ' + store.storeId + ' fail_load [code of Conferences_Grid.js]');
                    }


                    var repository_exists = Ext.StoreMgr.lookup('number_extensions');

                    if (repository_exists)
                        repository_exists.load()
                    else
                        console.log('ERROR: number_extensions - fail_load [code of Conferences_Grid.js]');
                }

        );
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -              
    }
})
