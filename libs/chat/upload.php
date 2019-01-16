
<?php
if(isset($_POST['upload'])){
    //Список разрешенных файлов
    $whitelist = array("gif", "jpeg", "png","jpg");         
    $data = array();
    $error = true;
    
    //Проверяем разрешение файла
	   for($i=0;$i<count($whitelist);$i++)
	   {
	   	 $ext=pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
        if($ext==$whitelist[$i])
        {
        	 $_FILES['userfile']['name']=$id_2 = uniqid("img_").".".$whitelist[$i];
        	$error=false;
				}
    }

    //если нет ошибок, грузим файл
    if(!$error) { 
                 
        $folder =  'img/';//директория в которую будет загружен файл
       
        $uploadedFile =  $folder.basename($_FILES['userfile']['name']);
        
                
        if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
        
            if(move_uploaded_file($_FILES['userfile']['tmp_name'],$uploadedFile)){
        
                $data = $_FILES['userfile'];
            }
            else {    
                $data['errors'] = "Во время загрузки файла произошла ошибка";
            }
        }
        else {    
            $data['errors'] = "Файл не  загружен";
        }
    }
    else{
        
        $data['errors'] = 'Вы загружаете запрещенный тип файла';
    }
    
    
    //Формируем js-файл    
    $res = '<script type="text/javascript">';
    $res .= "var data = new Object;";
    foreach($data as $key => $value){
        $res .= 'data.'.$key.' = "'.$value.'";';
    }
    $res .= 'window.parent.handleResponse(data);';
    $res .= "</script>";
    
    echo $res;

}
else{
    die("ERROR");
}

?> 