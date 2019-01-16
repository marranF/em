<?php
namespace app\core;
class view
{
  
  static function load($content_view,$template_view="template",$data=NULL)
  {
  	$menu="views/left_menu1.php";
    $view= "views/".$content_view.".php";      //подключаемый файл в шаблоне страниц
    $message =$data->get("isset_db");
    include_once("views/templates/".$template_view.".php");      //шаблон страниц
  }

}