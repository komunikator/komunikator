Ext.define('DIGT.Grid', {
    extend : 'Ext.grid.Panel',
    store: {
        xtype: 'store',
        pageSize: DIGT.store_page,
        remoteSort: true,
        buffered: true,
        //autoLoad: true,
        proxy: {
            type: 'ajax',
            url: 'data.php',
            actionMethods: {
                read: 'POST'
            },
            limitParam: 'size',
            extraParams: {
            },
            reader: {
                type: 'array',
                root: 'data',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },

        sorters: [{
            direction: 'ASC'
        }]
                               
    },
    verticalScrollerType: 'paginggridscroller',
    loadMask: true,
    disableSelection: true,
    invalidateScrollerOnRefresh: false,
    viewConfig: {
        trackOver: false
    },

    initComponent : function () {
        this.id = this.store.storeId+'_grid';
        this.store.proxy.extraParams.action = 'get_'+this.store.storeId;
        //alert(this.store.storeId);
        this.columns = [/*{
            xtype: 'rownumberer',
            width: 50,
            sortable: false
        }*/];
        for (var key in this.store.fields)
            this.columns.push({
                text:DIGT.msg[this.store.fields[key]]?DIGT.msg[this.store.fields[key]]:this.store.fields[key],
                dataIndex:this.store.fields[key],
                renderer: this.columns_renderer?this.columns_renderer:null
            });
        this.store.sorters[0].property = this.store.fields[0];
        this.store.listeners = {
            load: function(store, records, success) {
                if(!success && store.storeId) {
                    store.removeAll();
                    DIGT.fail_load_show();
                    if (store.autorefresh!=undefined) store.autorefresh = false;
                    console.log (store.storeId+' fail_load');
                }
            },
            exception: function(proxy, response, options) {
                this.removeAll();
                console.log('exception: ',proxy, response, options); 
            }
        
        };
        this.callParent(arguments);
    }

})
