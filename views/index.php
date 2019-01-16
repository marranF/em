<?php

class Base
{
    public $m_usdbuy;
    public $m_usdsell;
    public $m_eurbue;
    public $m_eurSell;
    public $m_usdbuy2;
    public $m_usdsell2;
    public $m_eurbuy2;
    public $m_eursell2;
    
    public function __construct()
    {
       
    }
    public function get_time()
    { 
        $file=fopen(dirname(__FILE__) . '/kurs.txt','r+');
        if($file)
        {
            flock($file, LOCK_EX);
            $content=fread($file,99);
            fseek($file,0);
            $s=split('/',$content);
            $time1=$time2=$time3=time(); 
            fwrite($file,$time1.'/'.$time2.'/'.$time3);
            flock($file, LOCK_UN);
            fclose($file);
           
        }
    }
};echo "sss";
$base=new Base();
$base->get_time();

