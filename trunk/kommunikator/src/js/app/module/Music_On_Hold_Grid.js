Ext.create('Ext.Window', {
    id         : 'upload_win',
    width      : 300,
    border     : false,
    modal      : true,
    closable   : false,
    resizable  : false,
    draggable  : false,
    
    open : function() {
        // this.getComponent('form').getForm().reset();
        this.show();
    },
            
    items      :
        Ext.create('Ext.form.Panel', {
        title: 'Upload Windows',
        fileUpload: true,
        bodyStyle: 'padding: 10px;  background: transparent;  border-top: 0px none;',
        xtype: 'form',
        itemId: 'form',
        defaults:
                {
                    anchor: '100%',
                    allowBlank: false,
                    msgTarget: 'side',
                    labelWidth: 50
                },
        items: [
            {
                xtype: 'fileuploadfield',
                name: 'file'
            }
        ],
        buttons: [{
                text: 'Upload',
                handler: function() {
                    var form = this.ownerCt.ownerCt.getForm();
                    if (form && form.isValid()) {
                        form.submit({
                            url: 'data.php?action=load_music_onhold',
                            //waitMsg: 'Wait..',
                            onSuccess: function(result, request) {
                                app.onSuccessOrFail(result, request, function() {
                                    app.msgShow(app.msg.saved ? app.msg.saved : 'Saved', 'info');
                                    form.reset();
                                })
                            },
                            onFailure: function(result, request) {
                                app.onSuccessOrFail(result, request)
                            }
                        });
                    }
                }
            }, {
                text: 'Reset',
                handler: function() {
                    this.ownerCt.ownerCt.ownerCt.hide();
                    //this.ownerCt.ownerCt.getForm().reset();
                }
            }]

    })
});

Ext.define('app.module.Music_On_Hold_Grid', {
    extend    : 'app.Grid',
    
    autoScroll : false,
    
    store_cfg : {
        // groupField : 'playlist',
        fields   : ['id', 'music_on_hold', 'file', 'playlist'],
        storeId  : 'music_on_hold',
        sorters  : [{
                direction  : 'DESC',
                property   : 'playlist'
            }]
    },
    
    requires  : ['Ext.ux.upload.Dialog', 'Ext.ux.grid.FiltersFeature'],
    features  : [{
            ftype : 'grouping'
    }],
    columns   : [
                {  // 'id'
                    hidden : true
                },
                {  // 'music_on_hold'
                    groupable  : false,
                    text       : app.msg['name'],
                    
                    editor     : {
                        xtype       : 'textfield',
                        allowBlank  : false
                    },
                    
                    width      : 320
                },
                {  // 'file'
                    groupable  : false,
                    sortable   : false,
                    width      : 320
                },
                {  // 'playlist'
                  editor  : {  
          xtype         : 'combobox',
          store         : Ext.create('app.Store', {
                    fields   : ['id', 'playlist', 'in_use'],
                    storeId  : 'playlist'
                }),
                valueField    : 'playlist',

                queryMode     : 'local',
                tpl           : Ext.create('Ext.XTemplate',
                    '<tpl for=".">',
                        '<div class="x-boundlist-item" style="min-height: 22px">{playlist}</div>',
                    '</tpl>'
                ),

                displayTpl    : Ext.create('Ext.XTemplate',
                    '<tpl for=".">',
                        '{playlist}',
                    '</tpl>'
                ),
                  }  }],
    
    initComponent: function() {
        this.columns_renderer = function(value, metaData, record, rowIndex, colIndex, store) {
            if (colIndex == 2)
                if (value)
                    return '<audio type="audio/wav" src="moh/' + value + '?dc_=' + new Date().getTime() + '" controls autobuffer>Your browser does not support the audio element.</audio>';
                else
                    '';
            return value;
        };
        this.onAddClick = function() {
            app.Loader.load('js/ux/upload/css/upload.css');

            //Ext.getCmp('upload_win').open();
            var uploadDialog = Ext.create('Ext.ux.upload.Dialog', {
                textOk: app.msg.OK ? app.msg.OK : 'OK',
                textClose: app.msg.close ? app.msg.close : 'Close',
                textUpload: app.msg.upload ? app.msg.upload : 'Upload',
                textBrowse: app.msg.browse ? app.msg.browse : 'Browse',
                textAbort: app.msg.abort ? app.msg.abort : 'Abort',
                textRemoveSelected: app.msg.remove_selected ? app.msg.remove_selected : 'Remove selected',
                textRemoveAll: app.msg.remove_all ? app.msg.remove_all : 'Remove all',
                // grid strings
                textFilename: app.msg.filename ? app.msg.filename : 'Filename',
                textSize: app.msg.size ? app.msg.size : 'Size',
                textType: app.msg.type ? app.msg.type : 'Type',
                textStatus: app.msg.status ? app.msg.status : 'Status',
                // status toolbar strings
                selectionMessageText: app.msg.selection_message ? app.msg.selection_message : 'Selected {0} file(s), {1}',
                uploadMessageText: app.msg.upload_message ? app.msg.upload_message : 'Upload progress {0}% ({1} of {2} )',
                // browse button
                buttonText: app.msg.browse ? app.msg.browse : 'Browse...',
                dialogTitle: app.msg.load ? app.msg.load : 'Upload Dialog',
                uploadUrl: 'data.php?action=upload_music_on_hold',
                listeners: {
                    'uploadcomplete': {
                        scope: this,
                        fn: function(upDialog, manager, items, errorCount) {
                            store: Ext.StoreMgr.lookup('playlists').load();

                            /*
                             var output = 'Uploaded files: <br>';
                             Ext.Array.each(items, function(item) {
                             output += item.getFilename() + ' (' + item.getType() + ', '
                             + Ext.util.Format.fileSize(item.getSize()) + ')' + '<br>';
                             });
                             
                             appPanel.update(output);
                             
                             if (!errorCount) {
                             upDialog.close();
                             }
                             */
                        }
                    }
                }
            });

            uploadDialog.show();

        };
        this.callParent(arguments);
    }
});
