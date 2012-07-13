Ext.define('DIGT.Store', {
    extend : 'Ext.data.Store',
    pageSize: DIGT.pageSize,
    remoteSort: true,
    buffered: true,
    //autoLoad: true,
    //restful: true,
    autoSync: true,
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
            read: 'data.php',
            create: 'data.php',
            update: 'data.php',
            destroy: 'data.php'
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
                               
    initComponent : function () {
        this.id = this.store.storeId+'_grid';
        //this.store.proxy.extraParams.action = 'get_'+this.store.storeId;
        this.store.proxy.api.read += '?get_'+this.store.storeId;
        this.store.proxy.api.update += '?update_'+this.store.storeId;
        this.store.proxy.api.create += '?create_'+this.store.storeId;
        this.callParent(arguments);
    }

})
