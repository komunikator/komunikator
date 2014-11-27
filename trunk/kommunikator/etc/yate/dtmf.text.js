function onText(msg)
{
    //Engine.print_r(msg);
       var m = new Message("database");
       m.account = "kommunikator";//from mysqldb.conf
       m.query = "update call_logs set gateway= '"+msg.text.substr(7)+"' where billid = '"+msg.billid+"'";//without 'Signal='
       m.dispatch();
}

Message.install(onText, "chan.text",10);
