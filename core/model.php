<?php
namespace app\core;
class model
{
  
  public $data;
  public function __construct($data=null)
  {
    
    $this->data=$data;
  }
  public function get_data()
  {
    return $this->data;
  }
  
};