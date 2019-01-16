<?php
/* Точка входа Подключена автозагрузка и параметры подключения к БД
Если нет Гет с адресом открывает главную страницу
*/
//include "bks_bank_kurs_41.php";
include ('autoload.php'); 
$db=include ('core/dbconnect.php');
session_start();
$data=new app\core\registry();
if(!$db)
{
  $data->set("isset_db","false");

}
else
{
  $data->set('host',$db['host']);
  $data->set('user',$db['user']);
  $data->set('pass',$db['pass']);
  $data->set('db',$db['db']);
  $data->set("isset_db","true");

}
app\core\Router::Run($data);
