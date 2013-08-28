Ext.apply(Ext.form.field.VTypes, {
    fds : function(val, field) {
        if (val !== app.msg.attendant)
        {
            console.log(field.ownerCt.items.items[4].setVisible(false));
            console.log(field.ownerCt.items.items[4].setValue(null));            
            return true;
        } 
        console.log(field.ownerCt.items.items[4].setVisible(true));
        return true;
    }
});
Ext.define('app.module.DID_Grid', {
    extend : 'app.Grid',
    
    store_cfg : {
        fields   : ['id', 'number', 'destination', 'description', 'default_dest'],
        storeId  : 'dids'
    },
    
    // advanced : ['description'],
    
    columns : [
        {// 'id'
            hidden : true
        },
        {// 'number'
            editor : {
                xtype       : 'textfield',
                regex       : /^\d+$/,
                allowBlank  : false
            }
        },
        {// 'destination'
            editor : app.get_Source_Combo({
                allowBlank  : false,
                editable    : false,
                vtype:'fds'
                /*listeners: {
                    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  
                    //при изменении  значения в поле "Назначение" 
                    //меняется значение поля "Назначение по умолчанию"
                    change: function(f, val) {
                        var dest = f.ownerCt.items.items[4];
                        if (val !== app.msg.attendant) {
                            dest.setVisible(false);
                            dest.setValue(null);
                            return true;
                        }
                        (dest.setVisible(true));
                        return true;
                    }
                    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    
                }*/
            })
        },
        {// 'description'
            editor : {
                xtype : 'textfield',
                
            }
        },
        {// 'default_dest'
            width: 160,
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // было создано отдельное хранилище sources_exception
            // в котором отсутствуют: Автосекретарь, Голосовая почта

            editor: {
                xtype: 'combobox',
                store: Ext.create('app.Store', {
                    fields: ['id', 'name'],
                    storeId: 'sources_exception'
                }),
                editable: false,
                displayField: 'name',
                valueField: 'name',
                queryMode: 'local'
            }
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            //editor :  app.get_Source_Combo({/*validator:{}*/
            //allowBlank: false
            //})//TODO validator
        }
    ],
    
    columns_renderer:
            function(value, metaData, record, rowIndex, colIndex, store) {
                if (colIndex == 2 && app.msg[value]) {
                    return app.msg[value];
                }
                return value;
            },
    initComponent: function() {
        this.callParent(arguments);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // при внесении изменений в хранилище sources_exception
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
                        console.log('ERROR: ' + store.storeId + ' fail_load [code of DID_Grid.js]');
                    }


                    var repository_exists = Ext.StoreMgr.lookup('sources_exception');

                    if (repository_exists)
                        repository_exists.load()
                    else
                        console.log('ERROR: sources_exception - fail_load [code of DID_Grid.js]');
                }

        );
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -        

    }
})