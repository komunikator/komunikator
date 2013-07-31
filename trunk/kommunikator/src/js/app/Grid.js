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
// turn on validation errors beside the field globally
//Ext.form.Field.prototype.msgTarget = 'side';

Ext.define('app.Grid', {
    //    extend : 'Ext.ux.LiveSearchGridPanel',
    //    frame:true,
    extend: 'Ext.grid.Panel',
    //verticalScrollerType: 'paginggridscroller',
    /*
     verticalScroller:{
     xtype:"paginggridscroller"//,
     //activePrefetch: false
     },
     */
    //height: 200,
    //style           : { overflow: 'auto', overflowX: 'hidden' },
    autoScroll: true,
    invalidateScrollerOnRefresh: false,
    listeners: {
        /*
         scrollershow: function(scroller) {
         if (scroller && scroller.scrollEl) {
         scroller.clearManagedListeners(); 
         scroller.mon(scroller.scrollEl, 'scroll', scroller.onElScroll, scroller); 
         }
         },
         
         viewready: function() {
         this.getView().scrollState.top=5;
         this.getView().restoreScrollState();
         // this.getView().focusRow(30);
         },
         */     afterrender: function() {
            if (this.filters)
            {
                this.filters.createFilters();
                this.store.load();
                /*
                 var store  = this.store;
                 store.prefetch({
                 start: 0,
                 limit: app.pageSize,
                 callback: function() {
                 store.guaranteeRange(0, app.pageSize-1);
                 }
                 });  
                 */
            }

            //this.getView().refresh(true);
        },
        render: function(c) {

            //if (item.autorefresh!=undefined) item.autorefresh = true;
            //this.store.load();
            this.on('activate', function(i) {
                //console.log(i.getId()+':activate');
                app.set_autorefresh(i, true);
            });
            this.on('deactivate', function(i) {
                //console.log(i.getId()+':deactivate');
                app.set_autorefresh(i, false);
            }); /*
             this.ownerCt.on('expand', function(i){ 
             set_autorefresh(i,true);
             //console.log(i.getId()+':expand');
             }); 
             this.ownerCt.on('collapse', function(i){ 
             set_autorefresh(i,false);
             //console.log(i.getId()+':collapse');
             });
             
             this.ownerCt.ownerCt.on('tabchange1', function(t,i){ 
             //console.log(i.getId()+':tabchange');
             //isVisible
             });
             */

        }
    },
    //loadMask: false,
    //loadMask: true,
    //selModel :new Ext.grid.RowSelectionModel({singleSelect:false}),
    //selModel: 'cellmodel',
    multiSelect: true,
    //disableSelection: true,
    invalidateScrollerOnRefresh: false,
            viewConfig: {
        loadMask: false, //true,
        //onStoreLoad: Ext.emptyFn,
        //preserveScrollOnRefresh : true,
        trackOver: false,
        stripeRows: true
    },
    onDeleteClick: function() {
        var selection = this.getView().getSelectionModel().getSelection();//[0];
        var me = this;
        var fn = function(btn) {
            if (btn == 'yes' && selection) {
                me.store.remove(selection);
                //this.store.Total_sync();
                var store = me.store;
                me.store.mySync({
                    success: function() {
                        //store.Total_sync();
                    }
                });
            }
        };
        if (selection.length)
            Ext.MessageBox.show({
                title: app.msg['delete'],
                msg: app.format_msg(app.msg.delete_record, selection.length),
                buttons: Ext.MessageBox.YESNOCANCEL,
                fn: fn,
                animEl: 'mb4',
                icon: Ext.MessageBox.QUESTION
            });
    },
    onSelectChange: function(sm, selection) {
        var selections = [];
        var reselect = null;

        if (sm && sm.hasSelection()) {
            for (var i = 0; i < selection.length; i++) {
                selections[i] = selection[i].index;
            }
            reselect = function(grid) {
                var s = grid.getSelectionModel();
                var recs = [];
                for (var i = 0; i < selection.length; i++) {
                    var rec = grid.store.getAt(selections[i]);
                    if (rec)
                        recs.push(rec);
                }
                //console.log(recs);
                s.select(recs);
            }
        }
        ;
        if (reselect)
            sm.getStore().reselect = reselect;
        //console.log(selections);

        var disabled = (selection.length === 0);
        if (this.down('#delete'))
            this.down('#delete').setDisabled(disabled);
        //this.store.dirtyMark = !disabled;
    },
    onAddClick: function() {
        var model = this.store.getProxy().getModel();
        var rec = new model;
        var editor = this.plugins[0];
        editor.cancelEdit();
        //editor.save
        this.store.insert(0, rec);
        //this.store.Total_sync();
        /*
         var store = this.store;	
         this.store.mySync({
         success:function(){
         store.Total_sync();
         }
         });
         
         */

        this.store.Total_sync();
        var i = 1;
        while (i < rec.fields.length && (this.headerCt.getHeaderAtIndex(i) && !this.headerCt.getHeaderAtIndex(i).getEditor(rec)))
            i++;
        /*
         editor.startEditByPosition({
         row: 0,
         column: i
         });
         */
        editor.startEdit(rec, this.headerCt.getHeaderAtIndex(i));

    },
    onSync: function() {
        var me = this;
        this.store.mySync({
            success: function() {
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
    onRefresh: function() {
        var me = this.ownerCt;
        me.body.mask(Ext.view.AbstractView.prototype.loadingText);
        this.store.load({
            callback: function() {
                me.body.unmask()
            }
        });
        /*
         var store  = this.store;
         store.prefetch({
         start: 0,
         limit: app.pageSize,
         callback: function() {
         store.guaranteeRange(0, app.pageSize-1);
         }
         })
         */
    },
    store_cfg: {},
    initComponent: function() {
        this.store_cfg = this.store || this.store_cfg;
        this.id = this.grid_id ? this.grid_id : this.store_cfg.storeId + '_grid';
        //console.log(this.id);
        this.itemId = this.id;

        if (!this.columns)
            this.columns = [/*{
             xtype: 'rownumberer',
             width: 50,
             sortable: false
             }*/];
        var need_editor = false;
        for (var key in this.store_cfg.fields) {
            var field_name = Ext.isString(this.store_cfg.fields[key]) ? this.store_cfg.fields[key] : this.store_cfg.fields[key]['name'];
            if (this.columns[key]) {
                if (this.columns[key].editor)
                    need_editor = true;
                if (this.columns[key].xtype != 'rownumberer') {
                    if (!this.columns[key].text /*&& !this.columns[key].header*/)
                        this.columns[key].text = app.msg[field_name] ? app.msg[field_name] : field_name;
                    //if (!this.columns[key].headers)
                    this.columns[key].dataIndex = field_name;
                    if (this.columns[key].dataIndex == 'id') {
                        this.columns[key].hidden = true;
                        this.columns[key].hideable = false;
                    }
                    ;
                    if (!this.columns[key].renderer && this.columns_renderer)
                        this.columns[key].renderer = this.columns_renderer;
                }
            }
            else if (!this.not_create_column)
                this.columns[this.columns.length] = {
                    text: app.msg[field_name] ? app.msg[field_name] : field_name,
                    dataIndex: field_name,
                    renderer: this.columns_renderer ? this.columns_renderer : null
                };

            if (this.advanced)
                for (var k in this.advanced)
                    if (this.columns[key] && this.advanced[k] == this.columns[key].dataIndex)
                        this.columns[key].hidden = true;

            if (key == 0 && !this.store_cfg.sorters) {
                this.store_cfg.sorters = [{
                        direction: 'ASC',
                        property: field_name
                    }];
                this.store_cfg.sorters[0].property = this.store_cfg.fields[1] ? Ext.isString(this.store_cfg.fields[1]) ? this.store_cfg.fields[1] : this.store_cfg.fields[1]['name'] : null;
            }


        }
        ;

        /*
         this.store_cfg.sorters = [{
         direction: 'ASC'
         }];
         
         this.store_cfg.sorters[0].property = this.store_cfg.fields[0];
         */
        if (need_editor) {
            this.dockedItems = [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                            iconCls: 'icon-add',
                            text: app.msg.add ? app.msg.add : 'Add',
                            scope: this,
                            handler: this.onAddClick
                        }, {
                            iconCls: 'icon-delete',
                            text: app.msg['delete'] ? app.msg['delete'] : 'Delete',
                            disabled: true,
                            itemId: 'delete',
                            scope: this,
                            handler: this.onDeleteClick
                        }
                        /*,{
                         iconCls: 'icon-save',
                         text: app.msg.save?app.msg.save:'Save',
                         //disabled: true,
                         itemId: 'save',
                         scope: this,
                         handler: this.onSync
                         },,{
                         iconCls: 'x-tbar-loading',
                         text: app.msg.refresh?app.msg.refresh:'Refresh',
                         //disabled: true,
                         itemId: 'refresh',
                         scope: this,
                         handler: this.onRefresh
                         }/*,
                         '->',
                         {
                         xtype: 'tbtext',
                         id: this.store_cfg.storeId+'_displayItem',
                         text:'TEST'
                         }  */
                    ]
                }, {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                            xtype: 'tbtext',
                            //xtype : 'panel',
                            width: '100%',
                            //border:false,
                            //bodyStyle:'padding:50px; 5px', 
                            style: 'padding:2px 10px;',
                            style1: {
                                //textAlign : 'center', 
                                //padding:'0px'
                            },
                            text: app.msg.for_edit
                                    //text :'<div><br>db click to edit<div>'
                        }]
                }
            ]
            //] ;

            if (this['no_adddelbuttons'])
                this.dockedItems[0].items.splice(0, 2);

            //this.store_cfg.sorters[0].property = this.store_cfg.fields[1]?Ext.isString(this.store_cfg.fields[1])?this.store_cfg.fields[1]:this.store_cfg.fields[1]['name']:null;

            if (need_editor)
                this.plugins = [Ext.create('Ext.grid.plugin.RowEditing', {
                        //this.plugins = [Ext.create('Ext.grid.plugin.CellEditing', {
                        //fieldDefaults : { labelAlign : 'left', msgTarget : 'side' },
                        //	msgTarget : 'side',
                        errorSummary: false,
                        clicksToEdit: 2,
                        //clicksToMoveEditor: 2,
                        disableUpdate: function() {
                            var me = this;
                            if (me.editor && me.editor.floatingButtons && !me.editor.floatingButtons.child('#update').disabled) {
                                me.editor.floatingButtons.child('#update').setDisabled(true);
                                //me.editor.floatingButtons.getEl().setStyle('visibility', 'hidden');
                            } else {
                                Ext.defer(me.disableUpdate, 10, me);
                            }
                        },
                        listeners: {
                            beforeedit: function(editor, e) {
                                this.disableUpdate();
                                e.grid.store.dirtyMark = true
                            },
                            validateedit: function() {
                                //console.log('validateedit')
                            },
                            //canceledit : function(){console.log('canceledit')},	            	
                            //edit : function(){console.log('edit')},	
                            edit: function(editor, e) {
                                if (!e.record.dirty && !e.record.data.id) { // only remove new records
                                    e.store.remove(e.record);
                                }
                                ;
                                if (!e.record.dirty)
                                    return false;
                                //if (e.record.isValid()) alert(e.record.regex);
                                //editor.msgTarget  = 'side';
                                //e.grid.store.sync();//
                                //var store = e.grid.store;
                                //var record = e.record;
                                //console.log('e.record:'+record);
                                //console.log(e);
                                e.grid.store.mySync({
                                    success: function() {
                                        if (!e.record.get('id') || e.grid.load_after_edit)
                                            e.grid.store.load();
                                        //console.log(e.grid.store.storeId +":load_after_edit:"+e.grid.load_after_edit);
                                        //store.Total_sync();
                                    }
                                });
                                //console.log(e.grid.store.storeId+' edit_stop');
                            },
                            canceledit: function(e, grid, obj) {
                                if (!grid.record.data.id) { // only remove new records
                                    grid.store.remove(grid.record);
                                }
                            }
                        },
                        autoCancel: false
                    })];
            app.Loader.load('js/app/editor.css');
        }
        ;
        /*
         if (!this['status_grid'])
         {
         if (!this.dockedItems)
         this.dockedItems = [];
         
         this.dockedItems.push({
         xtype: 'toolbar',
         dock: 'top',
         items: []
         });
         
         this.dockedItems[0].items.push(
         {
         iconCls: 'x-tbar-loading',
         text: app.msg.refresh ? app.msg.refresh : 'Refresh',
         //disabled: true,
         itemId: 'refresh',
         scope: this,
         handler: this.onRefresh
         });
         this.dockedItems[0].items.push('->');
         this.dockedItems[0].items.push({
         xtype: 'tbtext',
         id: this.store_cfg.storeId + '_displayItem',
         text: ''
         });
         }
         */
        var key_info = app.get_array_key(app.msg, this.title);

        if (false && app.msg[key_info + '_info'])
        {
            if (!this.dockedItems)
                this.dockedItems = [];
            this.dockedItems.push({
                xtype: 'toolbar',
                dock: 'bottom',
                items: [
                    {
                        xtype: 'panel',
                        width: '100%',
                        border: false,
                        bodyStyle: 'padding:10px 50px;',
                        style: {
                            //textAlign : 'center', 
                            //padding:'0px'
                        },
                        html: app.msg[key_info + '_info']
                    }]
            });

        }
        ;
        app.getColumnIndex = function(grid, dataIndex) {
            var gridColumns = grid.headerCt.getGridColumns();
            for (var i = 0; i < gridColumns.length; i++) {
                if (gridColumns[i].dataIndex == dataIndex) {
                    return i;
                }
            }
        };
        var me = this;

        //  if (!this.dockedItems)
        //      this.dockedItems = [];

        if (this.dockedItems && this.dockedItems[0] && this.advanced)
        {
//            if (!this.dockedItems)
//                this.dockedItems = [];
            this.dockedItems[0].items.push(
                    {
                        //xtype: 'button',
                        text: app.msg.advanced,
                        enableToggle: true,
                        //style: 'padding: 5px;',
                        labelWidth: 160,
                        listeners: {
                            toggle: function(b, newVal, eOpts) {
                                var stat = Ext.getCmp('Status_Grid').ownerCt;
                                if (stat && !stat.collapsed && newVal)
                                    stat.collapse();
                                for (var k in me.advanced) {
                                    me.columns[app.getColumnIndex(me, me.advanced[k])].setVisible(newVal);
                                }
                            }
                        }
                    });

        }


        this.store = Ext.StoreMgr.lookup(this.store_cfg.storeId) ?
                Ext.StoreMgr.lookup(this.store_cfg.storeId) :
                Ext.create('app.Store', this.store_cfg);
        if (!this.dockedItems)
            this.dockedItems = [];
        if (!this['status_grid'])
            this.dockedItems.push({
                xtype: 'pagingtoolbar',
                displayInfo: true,
                dock: 'bottom',
                pageSizes: app.pageSize,
                //width: 50,
                store: this.store//,
                //plugins: Ext.create('Ext.ux.ProgressBarPager',{
                //    width: 400
                //})

            }/*,
             '->',
             {
             xtype: 'text',
             itemId: 'displayItem'
             }*/);

        if (this.title)
            this.title = '<center>' + this.title + '</center>';

        //this.reconfigure(Ext.StoreMgr.lookup('groups'));
        this.callParent(arguments);
        //this.on('edit', function(editor, e) {alert(e.store.storeId)});
        this.getSelectionModel().on('selectionchange', this.onSelectChange, this);
    }

})
