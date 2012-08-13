Ext.define('DIGT.Prompts_Form', {
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
    initComponent : function () {
        var me = this;
        DIGT.request(
        {
            action:'get_prompts'
        },
        function(result){
            var status = ['online','offline']
            if (result && result.data)
                for (var i in status) {
                    var file_name = '';
                    for (var p in result.data)
                        if (status[i] == result.data[p][1])
                            file_name = result.data[p][4];
                    me.add(
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
                            html: '<p><b>'+status[i] +'</b></p>'
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
                                name: 'status'
                            },
                            { 
                                width: 60,
                                xtype: 'button',
                                text: 'Upload',
                                handler: function(){  
                                    var form = this.ownerCt.getForm();  
                                    if(form && form.isValid() && form.findField("status").getValue()){ 
                                        form.submit({
                                            url: 'data.php?action=load_promt_'+status[i],
                                            waitMsg: '....'
                                        /* success: function(form,action){
                                            msg('Success', 'Processed file on the server');
                                        }
					*/
                                        });
                                    }
                                }
                            }]
                        },{
                            //columnWidth: .2,
                            style:'padding:0px',
                            'html': file_name?'<audio type="audio/wav" src="auto_attendant/'+file_name+'" controls autobuffer>Your browser does not support the audio element.</audio>':''
                        }]

                    })
                }
            me.doLayout();
        });


        DIGT.Loader.load('js/DIGT/prompts.css');
        this.callParent(arguments);
    }
});