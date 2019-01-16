<?php
echo "ok";
class Base
{
    public $m_usd;
    public $m_eur;
    private $m_objects;
    private $m_timestamp;
    private $m_times;
    private $m_log_string;
    private $m_log_file;
    private $m_file;
    private $m_config;
    
    public function __construct()
    {   
        $this->m_timestamp=time();
        $this->load_config();
        $this->get_objects();
        $this->get_time();
     //   $this->set_time();
    }
    
    public function init()
    {
        $this->m_usd=Array('buy'=>0,'sell'=>0,'buy_min'=>0,'sell_min'=>0,'buy_max'=>0,'sell_max'=>0);
        $this->m_eur=Array('buy'=>0,'sell'=>0,'buy_min'=>0,'sell_min'=>0,'buy_max'=>0,'sell_max'=>0);
        $this->log("function init success",0);
    }

    public function load_config()
    {
        $this->m_config=parse_ini_file(dirname(__FILE__) . '/config.ini');
        if(count($this->m_config)>0)
            $this->log("config load success");
        else 
            $this->log("error loading config file",2);
    }
    
    public function get_objects()
    {
        $file=file_get_contents(dirname(__FILE__) . '/settings.json');
        $this->m_objects=json_decode($file,true);
        unset($file);
        if($this->m_objects==NULL)
            $this->log('cannot read settings.json file',2);
        else
            $this->log("settings file settings.json loaded success");
            echo "<pre>";
       // var_dump($this->m_objects);
    }
    
    public function get_time()
    {
        $this->m_file=fopen(dirname(__FILE__) . '/kurs.txt','r+');
        if($this->m_file)
        {
            $this->log('file with times kurs.txt has been reed');
            flock($this->m_file, LOCK_EX);
            $content=fread($this->m_file,99);
            fseek($this->m_file,0);
            $this->m_times=explode('/',$content);
            if($this->m_times[0]=='')
            {
                $this->log('times file was empty << wrote new times',1);
                for($i=0;$i<$this->m_settings['count'];$i++)
                    $this->m_times[$i]=$this->m_timestamp;
            }            
        }
    }
    
    public function log($str = false,$type = 0)
    {
        $class = 'ok';
        if($type == 1)
            $class = 'warning';
        if($type == 2)
            $class = 'error';
        $tag_start = "<p class='".$class."'>";
        $tag_end = "</p>";
        
        if(!$str)
            $str=$this->m_log_string;
        if(!$this->m_log_file)
            $this->m_log_file=fopen(dirname(__FILE__) . '/log.html','a+');
        if($this->m_log_file)
        {
            $res=fwrite($this->m_log_file, $tag_start . $class .' '. $str . $tag_end);
        }
    }
    
    public function set_time()
    {
        $str="";
        for($i=0;$i<count($this->m_times);$i++)
        {
            $str.=$this->m_times[$i];
            if($i+1 < count($this->m_times))
                $str.='/';
        }
        fwrite($this->m_file,$str);
        flock($this->m_file, LOCK_UN);
        fclose($this->m_file);
        $this->log('new times was wrote ');    
    }
    
    public function run()
    {
        $this->init();
        foreach($this->m_objects as $key=>$value)
        {
            if($this->m_times[$value['id']]+$value['time'] > $this->m_timestamp)
                continue;
            if($value['load']=='on')
            {
                $res=@include $key.".php";
                if(!$res)
                {
                    $this->log("cannot load file ".$key.".php! $value[name] ",2);
                    continue;
                }
                $this->log("file $key.php, load success for $value[name]");
                
                $data_url=$this->load_data_url($value['url'],$value['format']);
                if($data_url=='' || !$data_url)
                {
                    $this->log("cannot get file data for $value[name] by url $value[url]!",2);
                    continue;
                }
                $exec='get_kurs_'.$key;
                $exec($data_url,$this);
                $this->print_kurs($key,$value);
                $this->m_times[$value['id']] = $this->m_timestamp;
            }
        }
    }
    
    public function print_kurs($key,$value)
    {
        echo "Kurs of bank $value[name] - USD buy: ".$this->m_usd['buy'].", USD sell: ".$this->m_usd['sell'].", EUR buy: ".$this->m_eur['buy'].", EUR sell: ".$this->m_eur['sell']; 
        $this->log("Kurs of bank $value[name] - USD buy: ".$this->m_usd['buy'].", USD sell: ".$this->m_usd['sell'].", EUR buy: ".$this->m_eur['buy'].", EUR sell: ".$this->m_eur['sell']);
        echo "<br>";
    }
    
    public function load_data_url($url,$format)
    {
        if($format=='csv')
        {
            $file=fopen($url, "rt");
            if(!file)
                return false;
        }
        else
        {
            $file=file_get_contents($url);
            if(!$file)
                $file=exec("curl  $url");
        }
        return $file;
    }
};

$base=new Base();
$base->run();
$base->set_time();




/*

array(5) {
  ["load"]=>
  string(2) "on"
  ["url"]=>
  string(41) "http://bcs-bank.com/export/quotes/csv.asp"
  ["time"]=>
  string(3) "900"
  ["format"]=>
  string(3) "csv"
  ["name"]=>
  string(15) "БКС банк"
}
*/