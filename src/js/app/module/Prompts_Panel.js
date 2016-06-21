/*
 *  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
 *    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
 *    Copyright (C) 2012-2013, ООО «Телефонные системы»
 
 *    ЭТОТ ФАЙЛ является частью проекта «Komunikator»
 
 *    Сайт проекта «Komunikator»: http://komunikator.ru/
 *    Служба технической поддержки проекта «Komunikator»: E-mail: support@komunikator.ru
 
 *    В проекте «Komunikator» используются:
 *      исходные коды проекта «YATE», http://yate.null.ro/pmwiki/
 *      исходные коды проекта «FREESENTRAL», http://www.freesentral.com/
 *      библиотеки проекта «Sencha Ext JS», http://www.sencha.com/products/extjs
 
 *    Web-приложение «Komunikator» является свободным и открытым программным обеспечением. Тем самым
 *  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
 *  и иные права) согласно условиям GNU General Public License, опубликованной
 *  Free Software Foundation, версии 3.
 
 *    В случае отсутствия файла «License» (идущего вместе с исходными кодами программного обеспечения)
 *  описывающего условия GNU General Public License версии 3, можно посетить официальный сайт
 *  http://www.gnu.org/licenses/ , где опубликованы условия GNU General Public License
 *  различных версий (в том числе и версии 3).
 
 *  | ENG | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
 *    "Komunikator" is a web interface for IP-PBX "YATE" configuration and management
 *    Copyright (C) 2012-2013, "Telephonnyie sistemy" Ltd.
 
 *    THIS FILE is an integral part of the project "Komunikator"
 
 *    "Komunikator" project site: http://komunikator.ru/
 *    "Komunikator" technical support e-mail: support@komunikator.ru
 
 *    The project "Komunikator" are used:
 *      the source code of "YATE" project, http://yate.null.ro/pmwiki/
 *      the source code of "FREESENTRAL" project, http://www.freesentral.com/
 *      "Sencha Ext JS" project libraries, http://www.sencha.com/products/extjs
 
 *    "Komunikator" web application is a free/libre and open-source software. Therefore it grants user rights
 *  for distribution and (or) modification (including other rights) of this programming solution according
 *  to GNU General Public License terms and conditions published by Free Software Foundation in version 3.
 
 *    In case the file "License" that describes GNU General Public License terms and conditions,
 *  version 3, is missing (initially goes with software source code), you can visit the official site
 *  http://www.gnu.org/licenses/ and find terms specified in appropriate GNU General Public License
 *  version (version 3 as well).
 
 *  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 */

Ext.apply(Ext.form.field.VTypes, {
    // vtype validation function
    file: function(val, field) {
        var fileName = /^.*\.mp3$/i;
        return fileName.test(val);
    },
    // vtype Text property to display error Text
    // when the validation function returns false
    fileText: app.msg.wav_file_type, // формат звукового файла должен быть MP3
    // vtype Mask property for keystroke filter mask
    fileMask: /[a-z_\.]/i
});

Ext.define('app.module.Prompts_Panel', {
    extend: 'Ext.Panel',
    bodyStyle: 'padding: 10px;',
    autoScroll: true,
    // style       : 'padding: 10px;',  // не работает в отличии от bodyStyle (для справки)

    defaults: {
        // minWidth : 320,  // не имеет смысла если width задана напрямую
        style: 'padding: 5px;',
        width: 925
    },
    listeners: {
        activate: function(i) {
            app.active_store = null;
        }
    },
    reset: function() {
        var me = this;
        app.request(
                {
                    action: 'get_prompts'
                },
        function(result) {
            var status = ['online', 'offline']
            me.removeAll(true);
            me.doLayout();
            if (result && result.data)
                for (var i in status) {
                    var file_name = '';
                    for (var p in result.data)
                        if (status[i] == result.data[p][1])
                            file_name = result.data[p][4].replace('wav', 'mp3');
                    var item = me.add(
                            {
                                height: 52,
                                // xtype : 'container',  // !
                                xtype: 'panel',
                                layout: 'column',
                                //bodyStyle : 'padding: 5px;',
                                defaults: {
                                    border: false,
                                    style: 'padding: 10px 5px 10px 5px'
                                },
                                items: [{
                                        width: 120,
                                        //style : 'padding: 5px',
                                        html: '<b style="color:' + (file_name ? 'green"' : 'red"') + '>' + app.msg[status[i]] + '</b>'
                                    }, {
                                        // width : 300,
                                        layout: 'column',
                                        fileUpload: true,
                                        xtype: 'form',
                                        itemId: 'form',
                                        items: [
                                            //{xtype:'textfield',filelabel:'comment'},
                                            {
                                                xtype: 'filefield',
                                                //
                                                buttonText: app.msg.select_prompt,
                                                width: 350,
                                                //buttonOnly : true,
                                                buttonConfig: {
                                                    //width: 110,
                                                    //xtype: 'button'
                                                    //iconCls: 'upload-icon'
                                                },
                                                vtype: 'file', /*
                                                 validator: function(v){
                                                 if(!/\.txt$/.test(v)){
                                                 return 'Only text files allowed';
                                                 }
                                                 return true;
                                                 }, */
                                                name: 'prompt_file',
                                                status_val: status[i]
                                            },
                                            {
                                                //width: 60,
                                                monitorValid: true,
                                                formBind: true,
                                                xtype: 'button',
                                                text: app.msg.upload,
                                                handler: function() {
                                                    var form = this.ownerCt.getForm();
                                                    var f_field = form.findField("prompt_file");
                                                    if (form && form.isValid() && f_field.getValue()) {
                                                        form.submit({
                                                            url: 'data.php?action=load_prompt&status=' + f_field.status_val,
                                                            //waitMsg: 'Wait..',
                                                            onSuccess: function(result, request) {
                                                                app.onSuccessOrFail(result, request, function() {
                                                                    app.msgShow(app.msg.saved ? app.msg.saved : 'Saved', 'info');
                                                                    me.reset();
                                                                })
                                                            },
                                                            onFailure: function(result, request) {
                                                                app.onSuccessOrFail(result, request)
                                                            }
                                                        });
                                                    }
                                                    else
                                                    if (form.isValid())
                                                        app.msgShow(app.msg.choose_file, 'error');
                                                    else
                                                        app.msgShow(form.isValid(), 'error');
                                                }
                                            }
                                        ]
                                    }, {
                                        //columnWidth: .2,
                                        style: Ext.isIE ? '' : 'padding:5px',
                                        'html': (file_name && app.support_audio()) ? '<audio type="audio/wav" ' + (Ext.isIE ? 'style="width: 300px; margin-top:-6px;" ' : '') + ' src="' + file_name + '?dc_=' + new Date().getTime() + '" controls autobuffer>Your browser does not support the audio element.</audio>' : ''
                                    },
                                    {
                                        //style:'padding:5px',
                                        'html': file_name ? '<a TARGET="_blank" href="' + file_name + '">' + app.msg.download + '</a>' : ''
                                    }]
                            });
                    /*
                     if (file_name) item.add({
                     //style:'padding:4px',
                     xtype: 'button',
                     iconCls: 'icon-delete',
                     text: app.msg['delete']?app.msg['delete']:'Delete',
                     itemId: 'delete',
                     status_val : status[i],	
                     handler: function(){ 
                     app.request(
                     {
                     action:'destroy_prompts',
                     status: this.status_val
                     },
                     function(){
                     app.msgShow(app.msg.saved?app.msg.saved:'Saved','info');
                     me.reset();
                     }
                     )
                     }
                     })
                     */
                }
            me.doLayout();
        });
    },
    initComponent: function() {
        this.reset();
        app.Loader.load('js/app/prompts.css');
        var key_info = app.get_array_key(app.msg, this.title);
        if (app.msg[key_info + '_info'])
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
                        bodyStyle: 'padding: 10px 50px;',
                        // style : {
                        //     textAlign  : 'center', 
                        //     padding    : '0px'
                        // }, 
                        html: app.msg[key_info + '_info']
                    }
                ]
            });

        }
        if (this.title)
            this.title = '<center>' + this.title + '</center>';
        this.callParent(arguments);
    }
});