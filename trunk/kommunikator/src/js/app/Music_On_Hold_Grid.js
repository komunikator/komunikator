Ext.create('Ext.Window',{
    id:'upload_win',
    width : 300,
    border : false,
    modal : true,
    closable : false,
    resizable : false,
    draggable : false,
    open:function(){
        //this.getComponent('form').getForm().reset();
        this.show();
    },
    items:
    Ext.create('Ext.form.Panel', {
        title:'Upload Windows',
        fileUpload: true,
        bodyStyle : 'padding:10px; background: transparent;border-top: 0px none;',
        xtype:'form',
        itemId:'form',
        defaults:
        {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },
        items:[
        {
            xtype: 'fileuploadfield',
            name: 'file'
        }
        ],
        buttons: [{
            text: 'Upload',
            handler: function(){
                var form = this.ownerCt.ownerCt.getForm();
                if(form && form.isValid()){
                    form.submit({
                        url: 'data.php?action=load_music_onhold',
                        //waitMsg: 'Wait..',
                        onSuccess: function(result, request){
                            app.onSuccessOrFail(result, request,function(){
                                app.msgShow(app.msg.saved?app.msg.saved:'Saved','info');
                                form.reset();
                            })
                        },
                        onFailure: function(result, request){
                            app.onSuccessOrFail(result, request)
                        }
                    });
                }
            }
        },{
            text: 'Reset',
            handler: function() {
                this.ownerCt.ownerCt.ownerCt.hide();
            //this.ownerCt.ownerCt.getForm().reset();
            }
        }]
        
    })
}
);

Ext.define('app.Music_On_Hold_Grid', {
    requires : [
    'Ext.ux.upload.Dialog'
    ],
    extend : 'app.Grid',
    store_cfg : {
        fields : ['id', 'music_on_hold', 'description', 'file','playlist'],
        storeId : 'music_on_hold'
    },
    columns :
    [
    {
        hidden: true
    },                    
    {
        editor :  {
            xtype: 'textfield'
        },
        width:320
    },

    {
        hidden: true
    },
 
    {
        width:320
    },
{ 
        editor :  {
            xtype: 'combobox',
            store: Ext.StoreMgr.lookup('playlists')?
            Ext.StoreMgr.lookup('playlists'):
            Ext.create('app.Store',{
	        fields : ['id', 'playlist', 'in_use'],
        	storeId : 'playlists'
            }),
	    queryCaching: false,
            displayField: 'playlist',
            valueField: 'playlist',
            queryMode: 'remove'
        }

    }

    ],
    initComponent : function () {
        this.columns_renderer = function(value, metaData, record, rowIndex, colIndex, store) {
            if (colIndex == 3)
                if (value) 
                    return '<audio type="audio/wav" src="moh/'+value+'?dc_='+new Date().getTime()+'" controls autobuffer>Your browser does not support the audio element.</audio>';
                else '';
            return value; 
        }; 
        this.onAddClick = function(){
            app.Loader.load('js/ux/upload/css/upload.css');

            //Ext.getCmp('upload_win').open();
            var uploadDialog = Ext.create('Ext.ux.upload.Dialog', {
                textOk : 'OK2',
                textClose : 'Close',
                textUpload : 'Upload',
                textBrowse : 'Browse',
                textAbort : 'Abort',
                textRemoveSelected : 'Remove selected',
                textRemoveAll : 'Remove all',
                                   
                // grid strings
                textFilename : 'Filename',
                textSize : 'Size',
                textType : 'Type',
                textStatus : 'Status',
                textProgress : '%',
                                   
                // status toolbar strings
                selectionMessageText : 'Selected {0} file(s), {1}',
                uploadMessageText : 'Upload progress {0}% ({1} of {2} )',
                                   
                // browse button
                buttonText : 'Browse...',	
                dialogTitle : 'Upload Dialog',
                uploadUrl : 'data.php?action=upload_music_on_hold',

                listeners : {
                    'uploadcomplete' : {

                        scope : this,
                        fn : function(upDialog, manager, items, errorCount) {
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
})
