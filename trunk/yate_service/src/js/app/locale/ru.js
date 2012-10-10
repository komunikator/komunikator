Ext.onReady(function() { 
	if (Ext.grid.RowEditor) {
		Ext.apply(Ext.grid.RowEditor.prototype, {
			saveBtnText : "Сохранить",
			cancelBtnText : "Отмена",
			errorsText : "Ошибка",
			dirtyText : "Вы должны сохранить или отменить изменения"
		});
	}
});

if (window['app']== undefined) app = {}; 
app.msg = {
auth_title : 'DIGT PBX авторизация',
login	   : 'Логин',
password   : 'Пароль',
error	   : 'Ошибка',
fail_load  : 'Сбой загрузки',
OK	   : 'ОК',
logout	   : 'Выход',
home	   : 'Главная',
attendant  : 'Автосекретарь',
call_logs  : 'История звонков',
active_calls : 'Активные звонки',
extensions : 'Внутренние номера',
dial_plans : 'Правила набора номера',
users      : 'Управление доступом',
settings   : 'Параметры',
time 	   : 'Время',
status	   : 'Статус',
duration   : 'Длительность',
groups	   : 'Группы',
user	   : 'Пользователь',
total	   : 'Всего',
gateways   : 'Провайдеры',
changepassword: 'Сменить пароль',
add	   : 'Добавить',
'delete'   : 'Удалить',
refresh	   : 'Обновить',
online     : 'Рабочее время',
offline    : 'Нерабочее время',
previous   : 'Предыдущий',
next	   : 'Следующий',
routing_rules: 'Правила маршрутизации',	
conferences: 'Конференции',
param	   : 'Параметр',
value	   : 'Значание',
description: 'Описание',
music_on_hold: 'Музыка на удержании',
playlist   : 'Плейлист',
file	   : 'Файл',
first_step : "<div></div><p><b>The first step for setting it is to upload the two prompts for online/offline mode. The prompts may vary depending on your company's business hours.</b></p>"
}
