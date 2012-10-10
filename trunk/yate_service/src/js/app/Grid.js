/*
app.Loader.load([
	'ext/examples/ux/css/LiveSearchGridPanel.css',
 	'ext/examples/ux/statusbar/css/statusbar.css'
		],
    function() {
//        console.log('Loaded files successfully.  (loadtime: %dms)');
    },
    this        // scope
);

*/
Ext.define('app.Grid', {
    //    extend : 'Ext.ux.LiveSearchGridPanel',
    extend : 'Ext.grid.Panel',
    //verticalScrollerType: 'paginggridscroller',
    autoScroll: true, 
    listeners: {
        render: function(c) {
            //if (item.autorefresh!=undefined) item.autorefresh = true;
            this.store.load();
            var set_autorefresh = function(i,active){
                i.items.each(function(s){
                    if (s && s.store) {
                        //console.log(s.store.storeId+':'+s.store.autorefresh);
                        if (active) s.store.load();
                        if (s.store.autorefresh!=undefined) s.store.autorefresh = active;
                    }
                });	
            };

            this.ownerCt.on('activate', function(i){
                set_autorefresh(i,true);
            });
            this.ownerCt.on('deactivate', function(i){ 
                set_autorefresh(i,false);               
            });
            this.ownerCt.on('expand', function(i){ 
                set_autorefresh(i,true);
            });
            this.ownerCt.on('collapse', function(i){ 
                set_autorefresh(i,false);
            });
        }
    },	
    loadMask: true,
    //selModel :new Ext.grid.RowSelectionModel({singleSelect:false}),
    //selModel: 'cellmodel',
    multiSelect: true,
    //disableSelection: true,
    invalidateScrollerOnRefresh: false,
    viewConfig: {
        trackOver: false,
        stripeRows: true
    },
 	
    onDeleteClick : function(){
        var selection = this.getView().getSelectionModel().getSelection();//[0];
        if (selection) {
            this.store.remove(selection); 
            //this.store.Total_sync();
            var me = this;	
            this.store.mySync({
                success:function(){
                    alert(me.store.storeId());me.store.Total_sync()/*this.store.load*/
                }
            });
        }
    },

    onSelectChange : function(selModel, selections){
        var disabled = (selections.length === 0);
        if (this.down('#delete'))
            this.down('#delete').setDisabled(disabled);
        this.store.dirtyMark = !disabled;
    },

    onAddClick : function(){
        var model = this.store.getProxy().getModel();
        var rec = new model;
        var editor = this.plugins[0];
        editor.cancelEdit();
        this.store.insert(0, rec); 
        this.store.Total_sync();
        var i=1;
        while (i<rec.fields.length && (this.headerCt.getHeaderAtIndex(i) && !this.headerCt.getHeaderAtIndex(i).getEditor(rec)))
            i++;
        editor.startEditByPosition({
            row: 0,
            column: i
        });
    },

    onSync :  function(){
        var me = this;
        this.store.mySync({
            success: function () {
                /*
                        Ext.Msg.show({
                            title: app.msg.info?app.msg.info:'Info',
                            msg: app.msg.saved?app.msg.saved:'Saved',
                            buttons: Ext.Msg.OK,
                            icon: Ext.Msg.INFO
                        }); 
                        */
                me.store.dirtyMark = false;
            }
        })
    },
    onRefresh : function(){
        this.store.load();
    },
    
    store_cfg : {},	
    initComponent : function () {
        this.store_cfg = this.store || this.store_cfg;
        this.id = this.grid_id?this.grid_id:this.store_cfg.storeId+'_grid';
        //console.log(this.id);
        this.itemId = this.id;

        if (!this['status_grid'])
            this.bbar = {
                items:['->',{},{
                    xtype: 'tbtext',
                    id: this.store_cfg.storeId+'_displayItem',
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
        for (var key in this.store_cfg.fields)
            if (this.columns[key]) {
                if (this.columns[key].editor) need_editor=true;
                this.columns[key].text = app.msg[this.store_cfg.fields[key]]?app.msg[this.store_cfg.fields[key]]:this.store_cfg.fields[key],
                this.columns[key].dataIndex = this.store_cfg.fields[key],
                this.columns[key].renderer=  this.columns_renderer?this.columns_renderer:null
            }
            else
                this.columns[this.columns.length] = {
                    text:app.msg[this.store_cfg.fields[key]]?app.msg[this.store_cfg.fields[key]]:this.store_cfg.fields[key],
                    dataIndex:this.store_cfg.fields[key],
                    renderer: this.columns_renderer?this.columns_renderer:null
                };
        this.store_cfg.sorters = [{
            direction: 'ASC'
        }];

        this.store_cfg.sorters[0].property = this.store_cfg.fields[0];

        if (need_editor) {
            this.tbar = {
                items: [{
                    iconCls: 'icon-add',
                    text: app.msg.add?app.msg.add:'Add',
                    scope: this,
                    handler: this.onAddClick
                }, {
                    iconCls: 'icon-delete',
                    text: app.msg['delete']?app.msg['delete']:'Delete',
                    disabled: true,
                    itemId: 'delete',
                    scope: this,
                    handler: this.onDeleteClick
                }/*,{
                    iconCls: 'icon-save',
                    text: app.msg.save?app.msg.save:'Save',
                    //disabled: true,
                    itemId: 'save',
                    scope: this,
                    handler: this.onSync
                },*/,{
                    iconCls: 'x-tbar-loading',
                    text: app.msg.refresh?app.msg.refresh:'Refresh',
                    //disabled: true,
                    itemId: 'refresh',
                    scope: this,
                    handler: this.onRefresh
                }]
            } ,            
            this.store_cfg.sorters[0].property = this.store_cfg.fields[1]?this.store_cfg.fields[1]:null;
            //	    this.plugins = [Ext.create('Ext.grid.plugin.RowEditing', {
            this.plugins = [Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToMoveEditor: 1,
                listeners: {
                    beforeedit: function(e) {
                        e.grid.store.dirtyMark = true
                    },
                    //validateedit : function(){console.log('validateedit')},	            	
                    //canceledit : function(){console.log('canceledit')},	            	
                    //edit : function(){console.log('edit')},	
                    edit: function(e) {
                        //if (e.record.isValid())
                        //e.grid.store.sync();//
                        e.grid.store.mySync({
                            success:function(){
                                e.grid.store.Total_sync();
                            }
                        });
                    //console.log(e.grid.store.storeId+' edit_stop');
                    }
                },	
                autoCancel: false
            })];
              
            app.Loader.load('js/app/editor.css');
        };
        this.store = Ext.StoreMgr.lookup(this.store_cfg.storeId)?
        Ext.StoreMgr.lookup(this.store_cfg.storeId):
        Ext.create('app.Store',this.store_cfg);
        if (this.title) this.title ='<center>'+this.title+'</center>';
	
        //this.reconfigure(Ext.StoreMgr.lookup('groups'));
        this.callParent(arguments);
        //this.on('edit', function(editor, e) {alert(e.store.storeId)});
        this.getSelectionModel().on('selectionchange', this.onSelectChange, this);
    }

})
