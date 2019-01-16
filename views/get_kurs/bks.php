<?php
function get_kurs_bks($file,&$class)
{
    
    for ($i=0; $data=fgetcsv($file,1000,";"); $i++) 
    {
        $num = count($data);
        if($data[0]=='USD' && $data[4]=='ÒÎÌÑÊ')
        {
            $class->m_usd['buy']=number_format($data[1],2);
            $class->m_usd['sell']=number_format($data[2],2);
        }
        if($data[0]=='EUR' && $data[4]=='ÒÎÌÑÊ')
        {
            $class->m_eur['buy']=number_format($data[1],2);
            $class->m_eur['sell']=number_format($data[2],2);
        }
    }
}