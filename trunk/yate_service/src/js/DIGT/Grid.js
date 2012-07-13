
/*
DIGT.Loader.load([
	'ext/examples/ux/css/LiveSearchGridPanel.css',
 	'ext/examples/ux/statusbar/css/statusbar.css'
		],
    function() {
//        console.log('Loaded files successfully.  (loadtime: %dms)');
    },
    this        // scope
);

*/
Ext.define('DIGT.Grid', {
    //    extend : 'Ext.ux.LiveSearchGridPanel',
    extend : 'Ext.grid.Panel',
    store: {
        xtype: 'store',
        pageSize: DIGT.pageSize,
        remoteSort: true,
        buffered: true,
        //autoLoad: true,
        //restful: true,
        //autoSync: true,
        proxy: {
            type: 'ajax',
            //url: 'data.php',
            actionMethods: {
                read: 'POST',
                create: 'POST',
                update: 'POST',
                destroy: 'POST'
            },
            limitParam: 'size',
            extraParams: {
            //data:'test'
            },
            batchActions : true,	
            api: { /*
                read: 'data.php',
                create: 'data.php',
                update: 'data.php',
                destroy: 'data.php' */
            },
            writer: {
                type: 'json',
                writeAllFields: false,
                paramsAsHash: true,
                allowSingle: false
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
        }]
    },
    verticalScrollerType: 'paginggridscroller',
    loadMask: true,
    //disableSelection: true,
    invalidateScrollerOnRefresh: false,
    viewConfig: {
        trackOver: false,
        stripeRows: true
    },
    initComponent : function () {
        this.store.sorters = [{
            direction: 'ASC'
        }];
        this.id = this.store.storeId+'_grid';
        //this.store.proxy.extraParams.action = 'get_'+this.store.storeId;
        var url = 'data.php';
        this.store.proxy.api.read  = url+'?action=get_'+this.store.storeId;
        this.store.proxy.api.update = url+'?action=update_'+this.store.storeId;
        this.store.proxy.api.create = url+'?action=create_'+this.store.storeId;
        this.store.proxy.api.destroy = url+'?action=destroy_'+this.store.storeId;

        this.store.mySync =function(callback) {
            if (Ext.isDefined(callback.success))
                this.on("write", function(store) {
                    callback.success.call(this);
                    store.un("write", callback.success);
                });   

            this.sync();
        }

        this.bbar = {
            items:['->',{},{
                xtype: 'tbtext',
                id: this.store.storeId+'_displayItem',
                text:' '
            }]
        };
        if (!this.columns)
            this.columns = [/*{
            xtype: 'rownumberer',
            width: 50,
            sortable: false
        }*/];
        var need_editor = false;
        for (var key in this.store.fields)
            if (this.columns[key]) {
                if (this.columns[key].editor) need_editor=true;
                this.columns[key].text = DIGT.msg[this.store.fields[key]]?DIGT.msg[this.store.fields[key]]:this.store.fields[key],
                this.columns[key].dataIndex = this.store.fields[key],
                this.columns[key].renderer=  this.columns_renderer?this.columns_renderer:null
            }
            else
                this.columns[this.columns.length] = {
                    text:DIGT.msg[this.store.fields[key]]?DIGT.msg[this.store.fields[key]]:this.store.fields[key],
                    dataIndex:this.store.fields[key],
                    renderer: this.columns_renderer?this.columns_renderer:null
                };
        this.store.sorters[0].property = this.store.fields[0];
        this.store.Total_sync = function(){
            this.dirtyMark = true;
            var displayItem = Ext.getCmp(this.storeId+'_displayItem');
            if (this.storeId && displayItem) {
                displayItem.setText((DIGT.msg.total?DIGT.msg.total:'Total')+': '+this.getTotalCount());
                displayItem.ownerCt.doComponentLayout();
            };

        };
        this.store.listeners = {
            load: function(store, records, success) {
                this.Total_sync();
                this.dirtyMark = false;
                if(!success && store.storeId) {
                    store.removeAll();
                    DIGT.fail_load_show();
                    if (store.autorefresh!=undefined) store.autorefresh = false;
                    console.log (store.storeId+' fail_load');
                }
            },
            exception: function(proxy, response, options) {
                this.removeAll();
                console.log('exception: ', proxy, response, options);
            }

        };
        if (need_editor) {
            
            this.store.sorters[0].property = this.store.fields[1]?this.store.fields[1]:null;
            //	    this.plugins = [Ext.create('Ext.grid.plugin.RowEditing', {
            this.plugins = [Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToMoveEditor: 1,
                listeners: {
                    beforeedit: function(e) {
                        e.grid.store.dirtyMark = true
                    }
                },	
                autoCancel: false
            })];
	     	
            this.onDeleteClick = function(){
                var selection = this.getView().getSelectionModel().getSelection()[0];
                if (selection) {
                    this.store.remove(selection); this.store.Total_sync();
                }
            };
            this.onSelectChange = function(selModel, selections){
		var disabled = (selections.length === 0);
                this.down('#delete').setDisabled(disabled);
	        this.store.dirtyMark = !disabled;
            },

            this.onAddClick = function(){
                var model = this.store.getProxy().getModel();
                var rec = new model;
                var editor = this.plugins[0];
                editor.cancelEdit();
                this.store.insert(0, rec); this.store.Total_sync();
                var i=1;
                while (i<rec.fields.length && (this.headerCt.getHeaderAtIndex(i) && !this.headerCt.getHeaderAtIndex(i).getEditor(rec)))
                    i++;
                editor.startEditByPosition({
                    row: 0,
                    column: i
                });
            };

            this.onSync =  function(){
                var me = this;
                this.store.mySync({
                    success: function () {
			/*
                        Ext.Msg.show({
                            title: DIGT.msg.info?DIGT.msg.info:'Info',
                            msg: DIGT.msg.saved?DIGT.msg.saved:'Saved',
                            buttons: Ext.Msg.OK,
                            icon: Ext.Msg.INFO
                        }); 
                        */
                        me.store.dirtyMark = false;
                    }
                })
            };

            this.tbar = {
                items: [{
                    iconCls: 'icon-add',
                    text: DIGT.msg.add?DIGT.msg.add:'Add',
                    scope: this,
                    handler: this.onAddClick
                }, {
                    iconCls: 'icon-delete',
                    text: DIGT.msg['delete']?DIGT.msg['delete']:'Delete',
                    disabled: true,
                    itemId: 'delete',
                    scope: this,
                    handler: this.onDeleteClick
                },{
                    iconCls: 'icon-save',
                    text: DIGT.msg.save?DIGT.msg.save:'Save',
                    //disabled: true,
                    itemId: 'save',
                    scope: this,
                    handler: this.onSync
                }]
            } ;
            DIGT.Loader.load('js/DIGT/editor.css');

        };
        this.callParent(arguments);
        //this.on('edit', function(editor, e) {alert(e.store.storeId)});
        this.getSelectionModel().on('selectionchange', this.onSelectChange, this);
    }

})
