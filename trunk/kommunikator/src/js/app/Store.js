Ext.define('app.Store', {
    extend : 'Ext.data.Store',
    constructor: function(cfg) {
        //console.log('store'+cfg.storeId);
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            pageSize: 50,//app.pageSize,
            leadingBufferZone: 50,
            remoteSort: true,
            buffered: true,
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
                    read: 'data.php?action=get_'+cfg.storeId,
                    create: 'data.php?action=create_'+cfg.storeId,
                    update: 'data.php?action=update_'+cfg.storeId,
                    destroy: 'data.php?action=destroy_'+cfg.storeId
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
            listeners : {
                totalcountchange: function () {
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
                beforeload:function(s){
                    var grid = Ext.getCmp(this.storeId+'_grid');
                    if (grid && !this.autoLoad)
                        grid.ownerCt.body.mask(Ext.view.AbstractView.prototype.loadingText);
                },

                load: function(store, records, success) {
                    //console.log (store.storeId+' loaded '+ store.getTotalCount());
                    var grid = Ext.getCmp(this.storeId+'_grid');
                    if (grid && !this.autoLoad)
                        grid.ownerCt.body.unmask();
                    this.Total_sync();
                    this.dirtyMark = false;
                    if(!success && store.storeId) {
                        store.removeAll();
                        //app.fail_load_show();
                        if (store.autorefresh!=undefined) store.autorefresh = false;
                        console.log (store.storeId+' fail_load');
                    }
                },
                exception: function(proxy, response, options) {
                    this.removeAll();
                    console.log('exception: ', proxy, response, options);
                }

            },
            mySync : function(callback) {
                if (Ext.isDefined(callback.success))
                    this.on("write", function(store) {
                        callback.success.call(this);
                        store.un("write", callback.success);
                    });   

                this.sync();
            },
            Total_sync : function(){
                this.dirtyMark = true;   
                var displayItem = Ext.getCmp(this.storeId+'_displayItem');
                //console.log(displayItem);
                if (this.storeId && displayItem) {
                    displayItem.setText((app.msg.total?app.msg.total:'Total')+': '+this.getTotalCount());
                    displayItem.ownerCt.doComponentLayout();
                //displayItem.ownerCt.ownerCt.getView().refresh(true);
                };
            }
	    	
        },cfg)]);
    }

})
