<?php
namespace app\core;
class DB {

        protected static $_instance;  //экземпляр объекта
 
        public static function getInstance($data) 
        { // получить экземпляр данного класса
            if (self::$_instance === null) 
            { // если экземпляр данного класса  не создан
                self::$_instance = new self($data);  // создаем экземпляр данного класса
            }
            return self::$_instance; // возвращаем экземпляр данного класса
        }
   
         private  function __construct($data) { // конструктор отрабатывает один раз при вызове DB::getInstance();
               
                //подключаемся к БД
                if(is_object($data))
                {
                  $this->connect = mysqli_connect($data->get('host'),$data->get('user'),$data->get('pass'),"app") or die("Невозможно установить соединение".mysql_error());
              
                }
                else
                {
                  $this->connect = mysqli_connect($data['host'],$data['user'],$data['pass'],"app") or die("Невозможно установить соединение".mysql_error());
                  
                }
                // выбираем таблицу
                
                $this->query('SET names "utf8"');  
             
       
        }
 
        private function __clone() { //запрещаем клонирование объекта модификатором private
        }
       
        private function __wakeup() {//запрещаем клонирование объекта модификатором private
        }
   
        public static function query($sql) 
        {
       
            $obj=self::$_instance;
       
            if(isset($obj->connect)){
                $obj->count_sql++;
                $result=mysqli_query($obj->connect,$sql)or die(mysql_error());
                return $result;
            }
            return false;
        }  
   
        //возвращает запись в виде объекта
        public static function fetch_object($object)
        {
            return @mysqli_fetch_object($object);
        }

        //возвращает запись в виде массива
        public static function fetch_array($object)
        {
            return @mysqli_fetch_array($object);
        }
       

        public static function num_rows($oblect)
        {
          $res=@mysqli_num_rows($object);
            return $res;
        }
        
        public static function insert_id()
        {
            return @mysqli_insert_id(self::$_instance->connect);
        }
   
}