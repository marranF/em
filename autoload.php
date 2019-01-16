<?php
define('ROOT_DIRECTORY', realpath(dirname(__FILE__)));
define('INCLUDE_DIRECTORY', ROOT_DIRECTORY);
$file=(get_include_path() . PATH_SEPARATOR . INCLUDE_DIRECTORY);
$file2=set_include_path(".;c:/ospanel/modules/php/PHP-5.5;c:/ospanel/modules/php/PHP-5.5/PEAR/pear;C:\\OSPanel\\domains\\localhost");
$file3=get_include_path();
//- только файлы с расширением php
spl_autoload_extensions(".php");
//- автозагрузка классов
spl_autoload_register();    //namespace + class name = автозагрузка

?>
