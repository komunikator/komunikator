#!/usr/bin/env node

var fs = require('fs');
var Nodeyate = require('./Nodeyate.js');
var path = require('path');
var mysql = require('mysql');
var _billid = {};
process.stdin.resume();

process.stdin.on('data', function(data) {
  
  //fs.appendFile("/tmp/msg", data);
  // анализируем данные  - ми запускаем обработку - если это сообщение
  var tex = data.toString();
  var ar = tex.split(':');
   // проверка условия того что это сообщение
  var mes = ar[0].split('%%');
   
  //проверка условия считывания данных message
  if((mes[mes.length - 1] == '>message') || (mes[mes.length - 1] == '<message')){

     //создание конструктора класса
     var f = ''; 
     var array = {};
     var Nod = new Nodeyate(f,f,f,f,f,f,f);
     Nod.param = {};

     //метод объекта  - определени вида сообщения
     Nod.parse(data);     
    //fs.appendFile('/tmp/file', JSON.stringify(Nod)+  '\n');

     // тут основной цикл по обработке 
     switch(Nod.type){
       case 'incoming': 
         switch (Nod.name){

           //  пришло сообщени об ответе
           case 'call.answered':

            
            var temp_id = Nod.param['id'];
            var tra = temp_id.split('/');
		  
	    if (Nod.param['billid'])
              _billid[Nod.param['targetid']] = Nod.param['billid'];


            // условия канала 
            if (Nod.param['answered'] == 'true'){
               // канал
               var id_in = Nod.param['id'];    
                 
               var billid = Nod.param['billid']; 
	 	if (!billid) {
                   billid = _billid[Nod.param['targetid']];
                   delete _billid[Nod.param['targetid']]  
               };

               if (billid){
                 // формирование названия из billida
               var temp_file = billid.split('-');
               var file_name = '';
               for (var t = 0; t < temp_file.length; t++){
                 file_name += temp_file[t];
               }
              
               // различаем названия 1 и 12 ноги звонка
               var file_1  =  file_name + '_1.au';
               var file_2  =  file_name + '_2.au';

               // формирования пути для файла
               var path_1 = path.join(dirr.path_dest_2, file_1);
               var path_2 = path.join(dirr.path_dest_2, file_2);


               //нужно передать функции записи для каждого канала
               var n = new Nodeyate(f,f,f,f,f,f, array);
               n.yate('chan.masquerade');  
               n.param['id'] = id_in;
               n.param['message'] = 'chan.record';
               n.param['call'] = 'wave/record/' + path_1;
               n.param['peer'] = 'wave/record/' + path_2;
               n.Dispatch();
                            
 
               //fs.appendFile('/tmp/answered',' - ' + path_1 + ' --' +   path_2 + ' - '+  ' - ' + billid  + ' - ' +  '\n');
               }
            }
            // оветим на это сообщение
            Nod.handled = 'false';
            Nod.Acknowledge();
            break;

        default:
         Nod.handled = false;
         Nod.Acknowledge();
         break;
       } // второй switc
       break;
   
      case 'answer':
        break;
     } // первый switch
   }
});


// скасс создания папок данного номера (пользователя )
function Directory (path_1, path_2, path_3, path_dest, path_dest_2){
  this.path_1 = path_1;
  this.path_2 = path_2;
  this.path_3 = path_3;
  this.path_dest = '';
  this.path_dest_2 = '';
};
// функция создания подпапок
Directory.prototype.path = function( ) {

   // соединение путей - где хранятся mp3 файлы
   var path_des_1 = path.join(this.path_1, this.path_2);

   var path_des_1  = path_des_1 + '/';

   // путь где хранятся ноги звонка
   var  path_des_2 = path.join(path_des_1, this.path_3 );

   path_des_2 = path_des_2;

   var t_1 = fs.existsSync(path_des_1);
   var t_2 = fs.existsSync(path_des_2);   

   if (t_1 == true){}
   else{
     fs.mkdirSync(path_des_1,0777)
   }
   if (t_2 == true){}
   else {
     fs.mkdirSync(path_des_2, 0777);
   }

   var ret = new Array();

   this.path_dest = path_des_1;
   this.path_dest_2 = path_des_2;

};



//  етого куда сохранять
var path_pa = '/var/lib/misc'; // папка назвачяенияч
var path_ch = '/records/'; //  подпапка в папке назвачения
var path_des = ''; // переменная куда будут доставляться результируюзие файлы

var path_leg ='leg/'; // подпапка в которой будут хранится ноги звонка
var path_rez = '';
 
var dirr = new Directory(path_pa, path_ch, path_leg, path_des, path_rez); 
dirr.path(); 

//fs.appendFile('/tmp/file', dirr.path_dest + " -  " + dirr.path_dest_2);

var Node = new Nodeyate();

Node.Install('call.answered', 80);
Node.SetLocal('restart',true);