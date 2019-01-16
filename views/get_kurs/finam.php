<?php
function get_kurs_finam($file,&$class)
{
    $dom = new DOMDocument;
    $json=json_decode($file,true);
    $class->m_usd['buy']=$json['РўРѕРјСЃРє']['USD']['buy'];
    $class->m_usd['sell']=$json['РўРѕРјСЃРє']['USD']['sell'];
    $class->m_eur['buy']=$json['РўРѕРјСЃРє']['EUR']['buy'];
    $class->m_eur['sell']=$json['РўРѕРјСЃРє']['EUR']['sell'];
}