Ext.define('DIGT.Prompts_Panel', {
    extend : 'Ext.Panel',
    defaults:{
        style:'padding:5px;'
    },
    bodyStyle:'padding:10px;',
    /*
    items: [
    {
 		fileUpload: true,
		xtype:'form',
                width: 500,
                autoHeight: true,
                bodyStyle: 'padding: 10px 10px 10px 10px;',
                labelWidth: 50,
                defaults1: {
                    anchor: '95%',
                    allowBlank: false,
                    msgTarget: 'side'
                },
                items:[
                {
                    xtype: 'fileuploadfield',
                    id: 'filedata',
                    emptyText: 'Select a document to upload...',
                    fieldLabel: 'File',
                    buttonText: 'Browse'
                },
		{
                name : 'action',
                value: 'get_status',
                hidden: true
            	}
		],
                buttons: [{
                    text: 'Upload',
                    handler: function(){
                        if(this.ownerCt.ownerCt.getForm().isValid()){
                            //form_action=1;
                            this.ownerCt.ownerCt.getForm().submit({
                                url: 'data.php',
                                waitMsg: 'Uploading file...',
                                success: function(form,action){
                                    msg('Success', 'Processed file on the server');
                                }
                            });
                        }
                    }
                }]
    }],*/
    reset : function () {
        var me = this;
        DIGT.request(
        {
            action:'get_prompts'
        },
        function(result){
            var status = ['online','offline']
            me.removeAll(true);
            me.doLayout();
            if (result && result.data)
                for (var i in status) {
                    var file_name = '';
                    for (var p in result.data)
                        if (status[i] == result.data[p][1])
                            file_name = result.data[p][4];
                    var item = me.add(
                    {
                        height: 52,
                        xype:'container',
                        layout: 'column',
                        bodyStyle: 'padding:5px',
                        defaults: {
                            border:false,
                            style:'padding:5px'
                        },
                        items: [{
                            width: 60,
                            style:'padding:5px',
                            html: '<b style="color:'+(file_name?'green"':'red"')+'>'+status[i] +'</b>'
                        },{
                            //width: 300,
                            layout: 'column',
                            fileUpload: true,
                            xtype:'form',
                            itemId:'form',
                            items:[
                            //{xtype:'textfield',filelabel:'comment'},
                            {
                                xtype: 'fileuploadfield',
                                width: 70,
                                buttonOnly : true,
                                buttonConfig:{
                                    width: 60,
                                    xtype: 'button'
                                },
                                name: 'prompt_file',
                                status_val : status[i]
                            },
                            {
                                width: 60,
                                xtype: 'button',
                                text: 'Upload',
                                handler: function(){
                                    var form = this.ownerCt.getForm();
                                    var f_field = form.findField("prompt_file");
                                    if(form && form.isValid() && f_field.getValue()){
                                        form.submit({
                                            url: 'data.php?action=load_prompt&status='+f_field.status_val,
                                            //waitMsg: 'Wait..',
                                            onSuccess: function(result, request){
                                                DIGT.onSuccessOrFail(result, request,function(){
                                                    DIGT.msgShow(DIGT.msg.saved?DIGT.msg.saved:'Saved','info');
                                                    me.reset();
                                                })
                                            },
                                            onFailure: function(result, request){
                                                DIGT.onSuccessOrFail(result, request)
                                            }
                                        });
                                    }
                                }
                            }
                            ]
                        },{
                            //columnWidth: .2,
                            style:'padding:0px',
                            'html': file_name?'<audio type="audio/wav" src="'+file_name+'?dc_='+new Date().getTime()+'" controls autobuffer>Your browser does not support the audio element.</audio>':''
                        }] 
                    });
                    if (file_name) item.add({
                        //style:'padding:4px',
                        xtype: 'button',
                        iconCls: 'icon-delete',
                        text: DIGT.msg['delete']?DIGT.msg['delete']:'Delete',
                        itemId: 'delete',
                        status_val : status[i],	
                        handler: function(){ 
                            DIGT.request(
                            {
                                action:'destroy_prompts',
                                status: this.status_val
                            },
                            function(){
                                DIGT.msgShow(DIGT.msg.saved?DIGT.msg.saved:'Saved','info');
                                me.reset();
                            }
                            )
                        }
                    })
                }
            me.doLayout();
        });
    },

    initComponent :function () {
        this.reset();
        DIGT.Loader.load('js/DIGT/prompts.css');
        this.callParent(arguments);
    }
});