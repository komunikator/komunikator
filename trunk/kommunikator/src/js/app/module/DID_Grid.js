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
        width: 160,
        editor :  app.get_Source_Combo({/*validator:{}*/
        //allowBlank: false
            })//TODO validator
    } ,
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
    }
})
