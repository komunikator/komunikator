Ext.define('app.Page_Code', {
    extend: 'Ext.window.Window',
    id: 'Page_Code',
    title: app.msg.button_code, //получаем название титула окна
    width: 400,
    height: 400,
    autoHeight: true,
    autoScroll: true, // скроллинг если текст не влезает.
    html: 'Оболочка Windows является  на  сегодня  самым популярным  программным продуктом. ',
    maximizable: true, // значок «раскрыть окно на весь экран»

    layout: 'fit',
    border: false,
    modal: true, //блокирует всё, что на заднем фоне
    //   closable: true, //убирает крестик, закрывающий окно
    //   resizable: false, // нельзя изменить размеры окна
    draggable: true, //перемещение объекта по экрану
 items: [
                     { xtype: 'textfield',
                       //  title: 'Заголовок панели 1',
                     //    html:'Текст 1 текст 1 текст 1'
                     store : 'time_frames'
                    },

                  ],


    initComponent: function() {

        this.callParent(arguments);
    }
}
);