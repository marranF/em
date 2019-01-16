<?php
 function get_kurs_fkopen($file,&$class)
{
    $count=count($file);
    for($i=0;$i<$count;$i++)
    {
        if(strpos($file[$i],'<Томск'))
        {           
            $pos1=$i;     
        }
        if(strpos($file[$i],'</Томск>'))
        {
            $pos2=$i;
            break;
        }
    }
    for($i=$pos1;$i<$pos2;$i++)
    {
        if(strpos($file[$i],'<USD>'))
        $posusd=$i;
        if(strpos($file[$i],'<EUR>'))
        $poseur=$i;
        
    }
    $class->m_eur['buy'] = str_replace(" ","",$file[$poseur+1]);
    $class->m_eur['sell'] = str_replace(" ","",$file[$poseur+2]);
    $class->m_usd['buy'] = str_replace(" ","",$file[$posusd+1]);
    $class->m_usd['sell'] = str_replace(" ","",$file[$posusd+2]);
    $class->m_usd['buy'] = preg_replace('/[^0-9.]/', '', $usdbuy1);
    $class->m_usd['sell'] = preg_replace('/[^0-9.]/', '', $usdsell1);
    $class->m_eur['buy'] = preg_replace('/[^0-9.]/', '', $eurbuy2);
    $class->m_eur['sell'] = preg_replace('/[^0-9.]/', '', $eursell2);
}