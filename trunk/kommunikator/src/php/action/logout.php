<?
session_start();
echo (out(array("success"=>session_destroy())));
?>