<?php
/*
тип 
0 - init
1 - send message
2 - update messages
3 - like
4 - delete message

[0]

*/
class server_base
{
  public function __construct()
  {
    $data=include "../../core/dbconnect.php";
    include "../../core/DB.php";
    $connnect=\core\DB::getInstance($data);
  }
  public function init()
  {
    $sql="SELECT * FROM (select chat_messages.id_user, chat_messages.id, chat_messages.message,
 chat_messages.likes, chat_messages.data, chat_users.login
      from chat_messages, chat_users where chat_messages.id_user=chat_users.id order by chat_messages.id DESC LIMIT 5) as t
      ORDER BY id";
      $res=\core\DB::query($sql);
      return $res;
  }
  
  public function exe()
  {
  		$res=$this->init();
  	 $row=\core\DB::fetch_object($res);
  	 mysqli_data_seek($res,0);
		 $row2 = \core\DB::fetch_array($res);
  }
  public function add_message($data)
  {
    $date = date('Y-m-d H:i:s');
    $id_user=$data->id;
    $message=$this->check($data->message);
    $res=\core\DB::query("INSERT INTO chat_messages (id_user,message,likes,data) VALUES ('$id_user','$message',0,'$date')");
    $id=\core\DB::insert_id();
    if($res)
    { 
      if(!empty($data->img))
      { 
        foreach($data->img as $val)
          if($val!="")
            $res=\core\DB::query("INSERT INTO chat_additions (id_message,addition,type) VALUES ('$id','$val',0)");
          if(!$res)
          {
            echo array("error"=>"0");
            exit();
          }
      }
      if(count($data->url))
      {
        foreach($data->url as $val)
          if($val!="")
            $res=\core\DB::query("INSERT INTO chat_additions (id_message,addition,type) VALUES ('$id','$val',2)");
          if(!$res)
          {
            echo array("error"=>"0");
            exit();
          }     
      }
      if(count($data->ytb))
      {
        foreach($data->ytb as $val)
          if($val!="")
            $res=\core\DB::query("INSERT INTO chat_additions (id_message,addition,type) VALUES ('$id','$val',1)");
          if(!$res)
          {
            echo array("error"=>"0");
            exit();
          }
      }
    }
    $sql="select chat_messages.id_user, chat_messages.id, chat_messages.message, chat_messages.likes, chat_messages.data, chat_users.login
      from chat_messages, chat_users where chat_messages.id_user=chat_users.id and chat_messages.id=$id order by chat_messages.id";
      $res=\core\DB::query($sql);
      return $res;
  }
  
  public function check($data)
  {
    //$data = @iconv("UTF-8", "windows-1251", $data);
    $data = addslashes($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);
    return $data;
  }
  
  public function output($res)
  {
    $img=$ytb=$http=NULL;
    while($row = \core\DB::fetch_array($res))
    {    
       $res2=\core\DB::query("select addition, type from chat_additions where id_message=".$row['id']." order by type");
       while($row2 = \core\DB::fetch_array($res2))
       {
         if($row2['type']==0)
         {
           $img[]=$row2['addition'];
         }
         if($row2['type']==1)
         {
           $ytb[]=$row2['addition'];
         }
         if($row2['type']==2)
         {
           $http[]=$row2['addition'];
         }
       }   
       $arr[]=array(
            'id'=>$row['id'],
            'user'=>$row['id_user'],
            'login'=>$row['login'],
            'message'=>$row['message'],
            'likes'=>$row['likes'],
            'data'=>$row['data'],
            'img'=>$img,
            'ytb'=>$ytb,
            'http'=>$http
       );
       $img=$ytb=$http=null;
    }
    $arr=json_encode($arr);
    return $arr;
  }
  
  public function delete($data)
  {
    $sql="delete from chat_messages where id=".$data->id;
    $sql2="delete from chat_additions where id_message=".$data->id;
    $res=\core\DB::query($sql2);
    if($res)    
      $res=\core\DB::query($sql);
    if($res)
      return json_encode(array('res'=>'ok','id'=>$data->id));
    return json_encode(array("res"=>"error"));
  }
  
  public function route()
  {
    $data=json_decode($_POST["data"]);
    if($data->action=="init")
    {
      $res=$this->init();
      if($res)
        $arr=$this->output($res);
    }
    if($data->action=="send")
    {
      $res=$this->add_message($data);
      if($res)
        $arr=$this->output($res);
    }
    if($data->action=="delete")
    {
      $arr=$this->delete($data);
    }
    echo $arr;
  }
  
}
  

$sb=new server_base();
$sb->route();
$sb->exe();
//$sb->init();
