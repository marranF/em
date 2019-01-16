<?php
namespace app\ctrls;
class index extends \app\core\ctrl
{
  public $data;
  public function __construct($data)
  {
    $this->data=$data;
    
  }
  public function index()
  {
 //   $view=new \views\index();
    \app\core\view::load("get_kurs/index","template",$this->data);
  }
  public function about()
  {
		\app\core\view::load("about","template",$this->data);
  }
  public function err_404()
  {
		\app\core\view::load("404","template",$this->data);
  }
}
