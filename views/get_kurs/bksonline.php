<?php
function get_kurs_bksonline($file,&$class)
{
  $dom = new DOMDocument;
  $val=$dom->loadXML($file);
  if(!$val)
     return false;
  $uss=$dom->getElementsByTagName('USDBuy');
  foreach ($uss as $s) 
  {
    $class->m_usd['buy']=$s->nodeValue;
  }
  $uss=$dom->getElementsByTagName('USDSell');
    foreach ($uss as $s) 
  {
     $class->m_usd['sell']=$s->nodeValue;
  }
  $uss=$dom->getElementsByTagName('EurBuy');
  foreach ($uss as $s) 
  {
     $class->m_eur['buy']=$s->nodeValue;
  }
  $uss=$dom->getElementsByTagName('EurSell');
  foreach ($uss as $s) 
  {
     $class->m_eur['sell']=$s->nodeValue;
  }
}