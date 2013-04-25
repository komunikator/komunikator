 Ext.apply(Ext.form.field.VTypes, {
    fds: function(val, field) {
        if ( val !== app.msg.attendant )   
          { 
              console.log(field.ownerCt.items.items[3].setVisible(false));
            console.log(field.ownerCt.items.items[3].setValue(null));
          return true;
        } 
              console.log(field.ownerCt.items.items[3].setVisible(true));   
    return true;
    }
   });  
                                                                                                               
Ext.define('app.module.DID_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        fields : ['id','number', 'destination','default_dest','description'],
      
        storeId : 'dids'
    },  
            advanced :['description'],
        columns : [
    {
        hidden: true
    },

    { 
        editor :  {
            xtype: 'textfield',
            regex: /^\d+$/,
            allowBlank: false 

        }
    },

    { 
        editor :  app.get_Source_Combo({
            allowBlank: false,
            editable: false,
            vtype: 'fds'
        })
    } ,
    
    { 
        width   : 160,
        
        // - - - - -
        editor  : {
            xtype  : 'combobox',
            store  : Ext.create('app.Store', {
                fields   : ['id', 'name'],
                storeId  : 'sources_exception'
            }),
            
            editable: false,
            displayField  : 'name',
            valueField    : 'name',

            queryMode     : 'local'
        }
        // - - - - -
        
        //editor :  app.get_Source_Combo({/*validator:{}*/
        //allowBlank: false
            //})//TODO validator
    },
    
    { 
        editor :  {
            xtype: 'textfield'
        }
    }
    ],
    columns_renderer :
        
    function(value, metaData, record, rowIndex, colIndex, store) {
        if (colIndex==2 && app.msg[value])
        {
            return app.msg[value];
        }
        return value;
    },
    initComponent : function () {
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
                    console.log('ERROR: ' + store.storeId + ' fail_load [code of ...]');
                }
                
                
                var repository_exists = Ext.StoreMgr.lookup('sources_exception');
                
                if (repository_exists)
                    repository_exists.load()
                else
                    console.log('ERROR: sources_exception - fail_load [code of ...]');
            }

        );
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -        

    }
})
