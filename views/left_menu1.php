<?php

$sql="SELECT * FROM app.app_left_menu order by pos asc";
$connnect=app\core\DB::getInstance($data);
$res=app\core\DB::query($sql);
if($res)
{
	while($row=app\core\DB::fetch_object($res))
	{
		echo "<b><a href=/".$row->href.">".$row->href."</a></b><br>";
	}
}