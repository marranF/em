<?php
namespace app\core;
class router
{
	private $contoller;
  private $data;                  
  public function __construct($data)
  {
    $this->data=$data;
  }
  static function Run($data)
  {
  	$url=$_GET['url'];
    $action="Index";
    if(isset($url))
    {
       $exp=explode('/',$url);
       if(file_exists("ctrls/".$exp[0].".php"))
       {
          $controller="app\ctrls\\".$exp[0];
  				if(isset($exp[1]))
        	{
          	if(method_exists($controller,$exp[1]))
            	$action=$exp[1];
          	else
              $action="err_404";
        	}
       }
       else
       {
          $action="err_404";
          $controller="\app\ctrls\\index";
       }
    }
    else
    {
      $controller='\app\ctrls\\index';
    }
    $controller=new $controller($data);
    $controller->$action();
    }
}