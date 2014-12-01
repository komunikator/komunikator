#!/usr/bin/env node

var fs = require('fs');
var Nodeyate = require('./Nodeyate.js');
var path = require('path');
var mysql = require('mysql');
var child = require('child_process');

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

     // тут основной цикл по обработке 
     switch(Nod.type){
       case 'incoming': 
         switch (Nod.name){
           
           case 'chan.disconnected':
               // оветим на это сообщение
             Nod.handled = 'false';
             Nod.Acknowledge();

            if (Nod.param['answered'] == 'true'){
               // имена файлов ног - такие же как и у первого процесса
               var billid = Nod.param['billid'];
               var temp_file = billid.split('-');
               var file_name = '';
               for (var t = 0; t < temp_file.length; t++ ){
                 file_name += temp_file[t];
               }
               var file_1 = file_name + '_1.au';
               var file_2 = file_name + '_2.au';

               // полнй путь к файлам 
               var path_1 = path.join(way_leg, file_1);
               var path_2 = path.join(way_leg, file_2);

               // запустим функцию 
               //temp_arr.call(billid, path_1, path_2);
               setTimeout(function(){ set_timer(billid, path_1, path_2);}, 8000);
            }
            

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

// фуннкция запроса и запуска скриптов
function set_timer(billid, file_1, file_2){

   var query = 'select' 

+ ' tab.time, '
+ ' tab.caller, '
+ ' tab.called, '
+  ' rt.call_records_id '
 
+ ' from'
+ '(select '
+       ' case' 
+       ' when x.extension is not null and x2.extension is not null then 3 '
+       ' when x.extension is not null then 1 '
+       '  else 2 '
+       ' end type,'
+	' a.caller caller, '
+	' b.called called, '
+ ' date_format( FROM_UNIXTIME (b.time), \'%d_%m_%Y_%H_%i_%s\' ) as time, '
+	' g.gateway_id, '
+	' gm.group_id as caller_group_id, '
+	' gm2.group_id as called_group_id '
+ ' from call_logs a '  
+ ' join call_logs b on b.billid=a.billid and b.ended=1 and b.direction=\'outgoing\' and b.status!=\'unknown\' '
+ ' left join extensions x on x.extension=a.caller '
+ ' left join extensions x2 on x2.extension=b.called '
+  ' left join gateways g  on g.authname=a.called or g.authname=b.caller '
+  ' left join group_members gm on x.extension_id = gm.extension_id '
+  ' left join group_members gm2 on x2.extension_id = gm2.extension_id '
+  ' where a.ended=1 and a.direction=\'incoming\' and a.status!=\'unknown\' and a.billid = \'' + billid + '\''
+ ' ) as tab '

+ ' join call_records rt on rt.enabled=1 and '
+      ' case '
+        ' when (rt.caller_number=\'*\' ) then true '
+       ' else(rt.caller_number=tab.caller or rt.caller_group=tab.caller_group_id) '      
+      ' end and '

+     ' case '
+     '  when ( rt.called_number=\'*\') then true '
+      ' else(rt.called_number=tab.called or rt.called_group=tab.called_group_id) '      
+     ' end and '   

+     ' case ' 
+       ' when rt.gateway=\'*\' then true '
+         ' else(rt.gateway=tab.gateway_id) '      
+     ' end and' 

+      ' case ' 
+       ' when (rt.type=\'*\' ) then true '    
+         ' else(rt.type=tab.type) '
+       ' end '  +  '\n';
           
           
   //fs.appendFile('/tmp/select', query + '\n');

   var qu = connection.query(query, function(err,rezult){
      if (err){//fs.appendFile('/tmp/mysql_err', err + '\n');
      } 

      //fs.appendFile('/tmp/mysql_rez', rezult.length +  '\n');


      if ( rezult.length == 0) {

        var command = '/del_file.sh ' +  file_1 + ' ' +  file_2 + '\n';
        //fs.appendFile('/tmp/command', command);
      
        // запуск bash скриптов
        var rt =  child.exec(__dirname + command, function (error){
         if (error){//fs.appendFile('/tmp/exec', error + '\n'); 
            }
        });   
      }

      else{

        for(i = 0 ; i < rezult.length; i++ ){

          var name = way + ''; // параметры звонка
          time = rezult[0].time;
          caler = rezult[0].caller;
          caled = rezult[0].called;
          rule = rezult[0].call_records_id;

          // временное название результирующего файла
          var file_3 = path.join( way , time + '#' + caler + '_' + caled + '.wav');

          // строка содержащая суть команды
          var command = '/wav_file.sh ' +  file_1 + ' ' +  file_2 + ' ' + file_3 + '\n';
          //fs.appendFile('/tmp/command', command);
       
          // запуск bash скриптов
          var rt =  child.exec(__dirname + command, function (error){
            if (error){//fs.appendFile('/tmp/exec', error + '\n'); 
            }
          });      

           // загрузка в логи яти какое правило сраьотало
           Node.Output( 'CALL FROM ' + caler + ' TO ' + ' RECORDED ACCORDING BY A RULE ' + rule + '\n' );
        
          break;
        }
      }   
   });
};



// переменные - праюочие
var Node = new Nodeyate();

// подключение соединения с SQL
var tt = 0;
var connection = mysql.createConnection({
   host:'localhost',
   user: 'root',
   password:'root'
});
connection.connect(function(err){
   if (err){
   }
   else{
    //fs.appendFile( '/tmp/mysql', connection.threadId);
   }
});

var q ='use kommunikator'; //+ connection.escapeId;
connection.query(q, function(err,rezult){
     if (err) { console.log(err); }
     else { 
    // console.log(rezult);
    }
});




var way = '/var/lib/misc/records/';

var way_leg = '/var/lib/misc/records/leg';
// массив содержащий звонки
var arr = new Array();


// переменная определяющая что нет никакой разницы в правилах звонка
var all = 'all';


// запсук команд yate 
Node.Install('chan.disconnected', 80);
Node.SetLocal('restart',true);