<?	
	
	define('USIDE',true);
	$id = 1;
	setvar('e', 'n');
	define('MODULE', 'kurs_book');
	
	include ADMINPATH . MODULE . '/db.php';
	$t->work();
	
	$pg = new paging_();
		$pg->name = 'nbpgr_nc';
		if (!vars($pg->name)) {
			$pg->reset();
		}
		$pg->skin = fread(fopen($_1 = CODEPATH . 'newpaging41.htm', 'rb'), filesize($_1));
		$pg->sql = 'SELECT COUNT(*) FROM `' . TBL . 'kurs_book` WHERE pageid = 0 and `display`=1';
		$pg->url = '&res#msg';
		$pg->page_size = 10;
		$pg->work();


	class filter_direct {
		var $name;
		var $list = array('asc','desc');
		var $title = array('По возрастанию','По убыванию');
		var $img = array('u_02.png','u_01.png'); 
		var $default = 0;
		var $cookie;
		
		var $temple = '
		<img style="border:none" src="{IMG}" />
		';
		//
		function display(){		
			$s = $this->temple;	
			$s = str_replace("{IMG}",ROOT.'images/kew/'.$this->img[(vars($this->name)?vars($this->name):$this->default)],$s);
			$s = str_replace("{TXT}",$text,$s);
			return $s;		
		}
		function href(){		
			$s = (vars($this->name)?0:1);
			return '&'.$this->name.'='.$s;		
		}
		function titletext(){
			return $this->title[vars($this->name)];
		}
		//
		function work(){
			if(vars($this->name)!='') 
				setcooa($this->cookie,$this->name,vars($this->name));
			else if(cooa($this->cookie,$this->name)!='') 
				setvar($this->name,cooa($this->cookie,$this->name));
			if(vars($this->name)=='') setvar($this->name,$this->default);
		}
		function sql(){
			if(vars($this->name)==''||vars($this->name)==$this->default) 
				return $this->list[vars($this->name)];
			else
				return $this->list[vars($this->name)];
		}		
	}	
	
	class filter_sorting {
		var $name;
		var $list = array('default');
		var $default = 0;
		var $cookie;
		var $direct;
		
		var $temple = '
		<a href="{LINK}" title="{TITLE}">
		<div>{DIR}</div>{TXT}
		</a>
		';
		//
		function display($f, $text){		
			$s = $this->temple;	
			$s = str_replace("{LINKONLY}","?{$this->name}=".$f,$s);
			$s = str_replace("{SPACE}",spacer((strlen($text)*5),1),$s);
			if(vars($this->name) == $f)
			{
				$s = str_replace("{LINK}","?{$this->name}=".$f.$this->direct->href(),$s);
				$s = str_replace("{DIR}",$this->direct->display(),$s);
				$s = str_replace("{TITLE}",$this->direct->titletext(),$s);
				$s = str_replace("{BTXT}","<b>$text</b>",$s);
			}else{
				$s = str_replace("{LINK}","?{$this->name}=".$f,$s);
				$s = str_replace("{DIR}","",$s);
				$s = str_replace("{TITLE}","Сортировать по...",$s);
				$s = str_replace("{BTXT}",$text,$s);
			}	
			
				$s = str_replace("{TXT}",$text,$s);
				return $s;		
		}
		
		//
		function work(){
			if(vars($this->name)!='') 
				setcooa($this->cookie,$this->name,vars($this->name));
			else if(cooa($this->cookie,$this->name)!='') 
				setvar($this->name,cooa($this->cookie,$this->name));
			if(vars($this->name)=='') setvar($this->name,$this->default);
		}
		function sql(){
			if(vars($this->name)==''||vars($this->name)==$this->default) 
				return $this->list[vars($this->name)];
			else
				return $this->list[vars($this->name)];
		}		
	}
	function addzero($f,$n=2)
	{
		$res=(string)round($f,$n);
		$s=explode('.',$res);
		if (!strlen($s[1])) $res.='.';
		$res.=str_repeat('0',$n-strlen($s[1]));
		
		
		if($f==0.0)
			return '-';
		return $res;
	}

	function kurs_new_display()
	{
	
		global $t,$e1,$w1,$t1,$l1,$p,$acinfo,$id,$pg;
		setlocale(LC_ALL,NULL);
		$last_win_kurs = mysql_fetch_array(mysql_query("select w.*, v.literal, v.name from bank_voting_kurs_win as w, bank_exchange_rates as v where w.kursid = v.id order by w.id DESC limit 0,1"));
		$filter_ = '<form name="one" method="POST" style="padding:0;margin:0;">';
		$db=mysql_query("SELECT DISTINCT posted FROM ".TBL."kurs WHERE `display`='1' ORDER BY posted DESC LIMIT 0,30");
		$filter_ .= '<div style="margin:10 0;"> Курсы валют Томских банков на ';
		$filter_ .= '<select name="date" onchange="one.submit();">';
		$row=mysql_fetch_assoc($db);
		if (!vars('date')) setvar('date',$row['posted']);
		do
		{
			if (vars('date')==$row['posted']) $sel=" selected"; else $sel="";
			$filter_ .= '<option value="'.$row['posted'].'" '.$sel.'>'.hrdate4($row['posted']).'</option>';
		}
		
		while ($row=mysql_fetch_assoc($db));
		$filter_ .= '</select>';
		$filter_ .= '</div>';	
		$filter_ .= '</form>';	
		
		
		$usd_last = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='104' ORDER BY posted DESC LIMIT 1"));
		$udate_last = strtotime($usd_last[posted]);
		$dat = mktime(0,0,0,date("m",$udate_last),date("d",$udate_last)-1,date("Y",$udate_last));
		$usd_back = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='104' AND posted < '".$usd_last[posted]."' AND sell!='' ORDER BY posted DESC LIMIT 1"));
		$udate_back = strtotime($usd_back[posted]);
		
		
		$e_last = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='105' ORDER BY posted DESC LIMIT 1"));
		$edate_last = strtotime($e_last[posted]);
		$dat = mktime(0,0,0,date("m",$edate_last),date("d",$edate_last)-1,date("Y",$edate_last));
		$e_back = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='105' AND posted < '".$e_last[posted]."' AND sell!='' ORDER BY posted DESC LIMIT 1"));
		$edate_back = strtotime($e_back[posted]);
		
		$g_last = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='97' ORDER BY posted DESC LIMIT 1"));
		$gdate_last = strtotime($g_last[posted]);
		$dat = mktime(0,0,0,date("m",$gdate_last),date("d",$gdate_last)-1,date("Y",$gdate_last));
		$g_back = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='97' AND posted < '".$g_last[posted]."' AND sell!='' ORDER BY posted DESC LIMIT 1"));
		$gdate_back = strtotime($g_back[posted]);
		
		if($udate_last<$edate_last)
		{
			$udate_last	= $edate_last;
			$udate_back	= $edate_back;
		}
		
		$u_last_value = $usd_last[sell];
		$u_back_value = $usd_back[sell];
		$u_dist1 = $u_last_value - $u_back_value;
		//var_dump($u_dist);die;
		
		$e_last_value = $e_last[sell];
		$e_back_value = $e_back[sell];
		$e_dist = $e_last_value - $e_back_value;
		
		$g_last_value = $g_last[sell];
		$g_back_value = $g_back[sell];
		$g_dist = $g_last_value - $g_back_value;
		
		$bvk_last = ($u_last_value * 0.55) + ($e_last_value * 0.45);
		$bvk_back = ($u_back_value * 0.55) + ($e_back_value * 0.45);
		$bvk_dist = $bvk_last - $bvk_back;
		
		$redcolor = "#ae0005";
		$greencolor = "#4d9200";
		$greycolor = "#777";
		
		if ($u_dist1<0) {$color1=$redcolor;$pl1='';}
		elseif($u_dist1==0) {$color1=$greycolor;$pl1='';}
		else {$color1=$greencolor;$pl1='+';}
		
		if ($e_dist<0) {$color2=$redcolor;$pl2='';}
		elseif($e_dist==0) {$color2=$greycolor;$pl2='';}
		else {$color2=$greencolor;$pl2='+';}
				
		if ($bvk_dist<0) {$color3=$redcolor;$pl3='';}
		elseif($bvk_dist==0) {$color3=$greycolor;$pl3='';}
		else {$color3=$greencolor;$pl3='+';}
		
		if ($g_dist<0) {$color4=$redcolor;$pl4='';}
		elseif($g_dist==0) {$color4=$greycolor;$pl4='';}
		else {$color4=$greencolor;$pl4='+';}
		
		if(date('w')==1) $udate_last = time();
		
		?>
<style>
.cbr-kurs {
	color: #555;
	padding-left: 10px;
	font-size: 8pt;
	font-weight: bold;
}
.cbr-kurs-dop {
	color: #555;
	font-size: 8pt;
	font-weight: bold;
}
.cbr-header {
	color: #5ba1d8;
	font-weight: bold;
	font-size: 10pt;
}
.prognoz_blue {
	color: #5ba1d8;
	font-size: 10pt;
	font-weight:normal;
}
.cbr-data {
	color: #777;
	font-size: 10pt;
	font-weight: normal;
	padding-left: 5px;
}
.block-top {
	border-bottom: 1px solid #488cc9;
	margin-top: 15px;
	padding-bottom: 2px;
}
.block-top .block-header {
	position: relative; 
	left: 5px; 
	color: #5ba1d8;
	font-weight: bold;
	font-size: 10pt;
}
.block-body {
	border-bottom: 1px solid #488cc9;
	margin-left: 15px;
	margin-right: 15px;
}
.other-currency {
	color: #777;
	font-weight: bold;
	font-size: 8pt;
}
.other-header {
	background-color: #5BA1D8;
	color:white;
	line-height: 25px;
}
.other-header td:FIRST-CHILD {
	padding-left: 15px;
}
.other-row {
	background-color: #f0f5f9;
}
.other-row td:FIRST-CHILD {
	padding-left: 15px;
}
.tomskcur {
	color: #777;
	font-weight: bold;
	font-size: 8pt;
}
.tomskcur tr td{
	text-align: center;
	font-size: 8pt;
}
.tomskcur tr td:FIRST-CHILD{
	text-align: left; 
	padding-left: 15px;
	font-size: 8pt;
}

.tomskcur-header1 {
	background-color: #f0f5f9;
	
}
.tomskcur-header1 td{
	border-top: 1px solid #488cc9;
	height: 18px;
}
.tomskcur-header2 {
	background-color: #d9e9f6;
	font-size: 1px;
	height: 10px;
}

.tomskcur-filter{
	width: 100%;
	color: #777;
	font-size: 10pt;
	margin-top: 10px;
	
}
input, select {
	color: #488cc9;
	border: 1px solid #777;
	background-color: #eee;
}
.curbody {
	height: 18px;
}
.curbody td a img{
	border: none;
	position: relative; left: 5px; top: 2px;
}
.curplus {
	color: #488cc9;
}
.metal-head {
	height: 22px;
}

.metal-head td{
	border-bottom: 1px solid #488cc9;
	color: #ccc;
	font-weight: bold;
}
.metal-head td:FIRST-CHILD{
	color: #5ba1d8;
	font-weight: bold;
	font-size: 10pt;
}
.other_vals {
display:none;
}
.winner td:first-child{
	color:white;
}
.winner {
	background:#009EB7;
	color:white;
}
td.winner {
	color:white;
}
.box-val-field td.winner {
	color:white;
}
</style>
<script type="text/javascript">
<!--
$(document).ready(function(){ 
	//
	$('#game_valut').blur(function (){
		var valut = $(this).val();
		
		if(valut == '') $(this).attr('value','0.0000');
	})
	
	$('.block-header').toggle(function (){
			var $b = $(this).parent();
			var $v = $b.parent().find('.block-body');
			$v.slideDown('100');		
			$b.find('img').eq(0).hide();
			$b.find('img').eq(1).show();	
		},function (){
			var $b = $(this).parent();
			var $v = $b.parent().find('.block-body');
			$v.slideUp('100');		
			$b.find('img').eq(1).hide();
			$b.find('img').eq(0).show();	
		}
	);
	$('.tomskcur tr.curbody').hover(function(){
		$(this)
		.find('td').stop().animate({"background-color":"#d9e9f6"}, 100);
			}, function(){			
		$(this)
		.find('td').stop().animate({"background-color":"#fff"}, 100);		
	});
	
	$('.other-currency tr.other-row').hover(function(){
		$(this)
		.find('td').stop().animate({"background-color":"#d9e9f6"}, 100);
			}, function(){			
		$(this)
		.find('td').stop().animate({"background-color":"#f0f5f9"}, 100);		
	});
});
jQuery.fn.scroll_to_anchor = function(){
			this.stop( false , false ) // останавливает анимацию если уже идет
			$('html,body').animate({scrollTop: this.offset().top},'slow');
			return this;
}
function show_info(id) {
	$('.other_vals').hide();
	$('.other_vals_'+id).show();
	$( '#other_vals_info' ).scroll_to_anchor();
}


//-->
</script>
<?
if(isset($_GET['res'])) {
	$act1 = '';
	$act2 = 'active';
	$stl1 = '';
	$stl2 = 'style="display:block"';
} else {
	$act1 = 'active';
	$act2 = '';
	$stl1 = 'style="display:block"';
	$stl2 = '';
}?>
			<ul id="MenuSwitcher" class="vkladki_menutabsSwitcher">
				<li class="item <?=$act1;?>">
					<div id="valut"><span class="itemLabel">Котировки</span></div>
				</li>
				<li class="item">
					<div id="drug_val"><span class="itemLabel">Другие валюты</span></div>
				</li>
				<li class="item">
					<div id="vse_val"><span class="itemLabel">Все о валюте</span></div>
				</li>				
				<li class="item <?=$act2;?>">
					<div id="prognoz" onclick="history.pushState(null, null, '?res');"><span class="itemLabel itemred">Конкурс для любителей валют</span></div>
				</li>
			</ul>
		<div class="vkladki_divSwitcher" id="drug_val_div">
		<?
		$q="SELECT * FROM ".TBL."exchange_rates WHERE display='1' ORDER BY name";
		$dbb=mysql_query($q);
		?>
		<table class="other-currency" cellpadding="2" cellspacing="0" width="100%" border="0">
		<tr class="other-header">
			<td>Название</td>
			<td align="center">Тикер</td>
			<td align="center">Единиц</td>
			<td align="center">Текущая цена</td>
			<td colspan="2" align="center">Изменение</td>
		</tr>
		<?
		while ($row = mysql_fetch_array($dbb)) {
		$last = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='{$row[id]}' ORDER BY posted DESC LIMIT 1"));
		$date_last = strtotime($last[posted]);
		$dat = mktime(0,0,0,date("m",$date_last),date("d",$date_last)-1,date("Y",$date_last));
		//$back = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='{$row[id]}' AND posted <= '".date('Y-m-d H:i:s',$dat)."' AND sell!='' ORDER BY posted DESC LIMIT 1"));
		$back = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='{$row[id]}' AND id != {$last[id]} AND sell!='' ORDER BY posted DESC LIMIT 0,1"));
		$date_back = strtotime($back[posted]);		

		$last_value = $last[sell];
		$back_value = $back[sell];
		$dist = $last_value - $back_value;
		mysql_error();	
		?>
		
		<tr class="other-row">
			<td title="Данные от <?=mydate($last[posted])?>"><a href="#<?=$row['id']?>" onclick="show_info(<?=$row['id']?>)"><?=$row['name']?></a></td>
			<td align="center"><?=$row['literal']?></td>
			<td align="center"><?=$row['units']?></td>
			<td align="center"><?=number_format($last_value,4,",",".")?></td>
			<td align="center">
			<?	if ($dist<0) {$color=$redcolor;$pl='';}
				elseif($dist==0) {$color=$greycolor;$pl='';}
				else {$color=$greencolor;$pl='+';}	?>
			</td>
			<td align="center">
			<label style="color:<?=$color?>"><?=$pl.number_format(round($dist,4),4,",",".")?></label>
			</td>
		</tr>
		<?				
		}
		?>
		</table>
		<a id="other_vals_info"></a>
		<?display_content(583);?>
		</div>
				<div class="vkladki_divSwitcher" id="prognoz_div" <?=$stl2;?>>
		
		
		<?
			$date_time_array = getdate( time() );
			if($date_time_array['wday']==0) $date_time_array['wday']=7;
			
			
			if(($date_time_array['wday']>=1 && $date_time_array['wday']<5) || ($date_time_array['hours']<13 && $date_time_array['wday']==5)) {
				$days = 8 - $date_time_array['wday'];
			} else {
				$days = 15 - $date_time_array['wday'];
			}
			$date = date('d.m.Y',strtotime('+'.$days.' days'));
			//print_r($date_time_array);
			//if($date_time_array['wday']>=1 && $date_time_array['wday']<=4) {
			//if(isset($_GET['test'])) {
			//	echo $date_time_array['hours'];
			//}
			//if(($date_time_array['wday']>=1 && $date_time_array['wday']<5) || ($date_time_array['hours']<12 && $date_time_array['wday']==5)) {
			  	$dis = '';
			//} else {
			 //	$dis = 'disabled';
			//}
			$auth=new auth();
			$auth->name='last_name';
			$auth->table=TBL.'account';
			$auth->user='login';
			$auth->password='password';
			// регистрационная информация (логин, пароль #1) 
			// registration info (login, password #1) 
			$game_valut = 1;
			if ($auth->logged_id()){
				
				$sql="SELECT mobile,login,email FROM ".TBL."account WHERE id='".$auth->logged_id()."'";
				$db=mysql_fetch_array(mysql_query($sql));
				
				$sql2="SELECT * FROM ".TBL."game_kurs WHERE login='".$db['login']."' AND stat_game='0' AND stav_vid='1' and DATE_FORMAT(date,'%Y-%m-%d') = curdate()";
				//bags 20.08.2014 EEK
				$sql20="SELECT * FROM ".TBL."game_kurs WHERE login='".$db['login']."' AND stat_game='0' AND stav_vid='1' AND eek = '0' and DATE_FORMAT(date,'%Y-%m-%d') = '2014-08-16'";
				$db20 = mysql_fetch_array(mysql_query($sql20));
				
				$sql21="SELECT * FROM ".TBL."game_kurs WHERE login='".$db['login']."' AND stat_game='0' AND stav_vid='1' AND eek = '0' and DATE_FORMAT(date,'%Y-%m-%d') = '2014-08-17'";
				$db21 = mysql_fetch_array(mysql_query($sql21));
				
				$sql22="SELECT * FROM ".TBL."game_kurs WHERE login='".$db['login']."' AND stat_game='0' AND stav_vid='1' AND eek = '0' and DATE_FORMAT(date,'%Y-%m-%d') = '2014-08-18'";
				$db22 = mysql_fetch_array(mysql_query($sql22));
				
				$sql23="SELECT * FROM ".TBL."game_kurs WHERE login='".$db['login']."' AND stat_game='0' AND stav_vid='1' AND eek = '0' and DATE_FORMAT(date,'%Y-%m-%d') = '2014-08-19'";
				$db23 = mysql_fetch_array(mysql_query($sql23));
				//bags 20.08.2014 EEK
				$sql3="SELECT * FROM ".TBL."game_kurs WHERE login='".$db['login']."' AND stat_game='0' AND stav_vid='2' AND DATE_FORMAT(date,'%Y-%m-%d') = curdate()";
				$db2=mysql_fetch_array(mysql_query($sql2));
				$db3=mysql_fetch_array(mysql_query($sql3));
				
				if(isset($_POST['game_id_stav'])) {
				$game_id_stav = $_POST['game_id_stav'];
				$count_change = $_POST['count_change'];
				$stav_vid = $_POST['stav_vid'];
				$game_usd = str_replace(',','.',$_POST['game_usd']);
				$game_eur = str_replace(',','.',$_POST['game_eur']);
				$game_gbp = str_replace(',','.',$_POST['game_gbp']);
				
				$game_id_stav = preg_replace('/\D/','',$game_id_stav);
				$count_change = preg_replace('/\D/','',$count_change);
				$game_usd = preg_replace('/[^.\d]/','',$game_usd);
				$game_eur = preg_replace('/[^.\d]/','',$game_eur);
				$game_gbp = preg_replace('/[^.\d]/','',$game_gbp);
				
				if($game_id_stav=='') $game_id_stav='0';
				if($count_change=='') $count_change=0;
				$now_text = 'NOW()';
				if($date_time_array['hours']>=12 && $date_time_array['wday']==5) {
					$now_text = 'NOW() + INTERVAL 1 DAY';
				}

				if($dis == 'disabled') {
					$res = 3;
				} else if($game_id_stav!=='0') {
					$res = 2;
					if(isset($_POST['eek']))
						mysql_query($qqq = "UPDATE ".TBL."game_kurs SET eek='1',usd_stav='{$game_usd}' , eur_stav='{$game_eur}', gbp_stav='{$game_gbp}', count_change = '{$count_change}'  WHERE id ='{$game_id_stav}' AND stav_vid='{$stav_vid}' AND stat_game=0 AND login = '{$db['login']}'");
					else
						mysql_query($qqq = "UPDATE ".TBL."game_kurs SET date={$now_text},usd_stav='{$game_usd}' , eur_stav='{$game_eur}', gbp_stav='{$game_gbp}', count_change = '{$count_change}'  WHERE id ='{$game_id_stav}' AND stav_vid='{$stav_vid}' AND stat_game=0 AND login = '{$db['login']}'");
				 } else {
					$res = 1;
					mysql_query($qqq = "INSERT INTO ".TBL."game_kurs (date,login,stat_game,usd_stav,eur_stav,gbp_stav,stav_vid) VALUES ({$now_text},'{$db['login']}',0,'{$game_usd}','{$game_eur}','{$game_gbp}','{$stav_vid}')");
				 }
				//print_r(array($game_id_stav,$game_usd,$game_eur));
				echo '<SCRIPT LANGUAGE="JavaScript"> 
					<!--
					window.location = "/pages/41/?res='.$res.'";
					//-->
				</script>';
				//header( 'Location: ' );exit();
				}
			
				//$sql="SELECT mobile,login,email FROM ".TBL."account WHERE id='".$auth->logged_id()."'";
				//$db=mysql_fetch_array(mysql_query($sql));
				
				//$sql2="SELECT * FROM ".TBL."game_kurs WHERE login='".$db['login']."' AND stat_game='0'";
				//$db2=mysql_fetch_array(mysql_query($sql2));
				if(isset($db2['id'])) {
				$game_id_stav = $db2['id'];//usd_stav
				$game_usd = $db2['usd_stav'];
				$game_eur = $db2['eur_stav'];
				$count_change = $db2['count_change']+1;
				} else {
				$game_id_stav = '0';
				$game_usd = '0,0000';
				$game_eur = '0,0000';
				$count_change = 0;
				}
				
				if(isset($db3['id'])) {
				$game_id_stav_gbp = $db3['id'];//usd_stav
				$game_gbp = $db3['gbp_stav'];
				$count_change_gbp = $db3['count_change']+1;
				} else {
				$game_id_stav_gbp = '0';
				$game_gbp = '0,0000';
				$count_change_gbp = 0;
				}
				/*<p>Вы вошли в сисему</p>
				<p>Ваш ID&nbsp;<?=$auth->logged_id()?></p>
				<p>Ваш логин&nbsp;<?=$db['login']?></p>
				<p>Ваш email&nbsp;<?=$db['email']?></p>
				<p>Ваш телефон, на который прийдет выйгрыш &nbsp;<?=$db['mobile']?></p>
				
				<form method=POST>
				<table>
					<tr>
						<td>Прогноз USD</td><td>Прогноз EUR</td>
					</tr>
					<tr>
						<td><input type="text" name="game_usd" value="<?=$game_usd?>"></td><td><input type="text" name="game_eur" value="<?=$game_eur?>"></td>
					</tr>
					<tr>
						<td colspan="2"><input type="hidden" name="count_change" value="<?=$count_change?>"><input type="hidden" name="game_id_stav" value="<?=$game_id_stav?>"><input type="submit" value="оставить прогноз"></td>
					</tr>
				</table>
				</form>*/
				//usd = 1; eur = 2;
				
				?>
				<div align="left">
				
				
				<div>
				<div style="float:right;font-size:10pt;font-weight:bold;margin-right:20px;">
				<a href="/pages/48/?id=20"><img src="../../files/303.jpg" class="tarif-logo" ></a>
				<div>Спонсор конкурса</div>
				</div>
				<? if($dis == '') {?>
				<span style="color:red;font-size:12px;font-weight:bold;">Оставь прогноз курса валюты на следующий понедельник (<?=$date?>) и получи 500 рублей на телефон! Валюта этой недели - <?=$last_win_kurs['name']?>.</span>				
				<?} else {?>
				<span style="color:red;font-size:12px;font-weight:bold;"> Сегодня ставки не принимаются. Ставки будут приниматься с 5.01.2019</span>
				<? //<span style="color:red;font-size:12px;font-weight:bold;"> Конкурс приостоновлен! Мы возобновим игру после новогодних праздников в 2016 году.</span>?>
				<?}?>
				<form method=POST align="center">
				<table width="288px" style="margin-top:10px">
					<tr>
						<td><span style="color:#333333;font-size:12px;font-weight:bold;"><?php echo $last_win_kurs['literal'];?>:</span></td>
						<td><input id="game_valut" type="text" name="game_usd" value="<?=$game_usd?>" onfocus="this.value='';" onclick="this.value='';" <?=$dis;?>></td>
						<td><input type="hidden" name="count_change" value="<?=$count_change?>" <?=$dis;?>><input type="hidden" name="stav_vid" value="1" <?=$dis;?>><input type="hidden" name="game_id_stav" value="<?=$game_id_stav?>" <?=$dis;?>><input type="submit" value="оставить прогноз" <?=$dis;?>></td>
					</tr>
				</table>
				</form>
				
				<? 
               
                /* Убрал GBP
				<form method=POST align="center">
				<table width="288px" style="margin-top:10px">
					<tr>
						<td><span style="color:#333333;font-size:12px;font-weight:bold;">GBP:</span></td>
						<td><input id="game_valut" type="text" name="game_gbp" value="<?=$game_gbp?>" onfocus="this.value='';" onclick="this.value='';" <?=$dis;?>></td>
						<td><input type="hidden" name="count_change" value="<?=$count_change_gbp?>" <?=$dis;?>><input type="hidden" name="stav_vid" value="2" <?=$dis;?>><input type="hidden" name="game_id_stav" value="<?=$game_id_stav_gbp?>" <?=$dis;?>><input type="submit" value="оставить прогноз" <?=$dis;?>></td>
					</tr>
				</table>
				</form> */ ?>
				
					<?if(isset($_GET['res'])) {
					switch ($_GET['res']) {
						case 1:$res='Ваш прогноз принят';break;
						case 2:$res='Ваш прогноз обновлен';break;
						case 3:$res='Сегодня ставки не принимаются';break;
						default:$res='';
					}?>
					<div align="center" id="redd" style="color:green;font-size:12px;margin-top:15px;"><?=$res?></div>
					
					<?}?>
				
				
				<? if($dis == '') {?>
				</form>
				<?}?>
				</div>
				<div style="clear:both"></div>
				
				</div>
				<?} else {?>
				<div align="left">
				
				
				<div style="">
				<div style="float:right;font-size:10pt;font-weight:bold;margin-right:20px;">
				<a href="/pages/48/?id=20"><img src="../../files/303.jpg" class="tarif-logo" ></a>
				<div>Спонсор конкурса</div>
				</div>
				<? if($dis == '') {?>
					<span style="color:red;font-size:12px;font-weight:bold;">Оставь прогноз курса валюты на следующий понедельник (<?=$date?>) и получи 500 рублей на телефон! Валюта этой недели - <?=$last_win_kurs['name']?>.</span>
				<?} else {?>
					<span style="color:red;font-size:12px;font-weight:bold;"> Ставки не принимаются. Ставки будут приниматься с 5.01.2019</span> 
					<? //<span style="color:red;font-size:12px;font-weight:bold;">Конкурс приостоновлен! Мы возобновим игру после новогодних праздников в 2016 году.</span>?>
				<?}?>
				<?
				
				?>
				<!--kilop-->
				<table width="288px" style="margin-top:10px">
					<tr>
						<td><span style="color:#333333;font-size:12px;font-weight:bold;"><?echo $last_win_kurs['literal'];?>:</span></td>
						<td><input id="game_valut" type="text" name="" value="0.0000" onfocus="this.value='';" onclick="this.value='';" <?=$dis;?>></td>
						<td><input id="game_valut" onclick="$('#redd').show();" type="submit" value="оставить прогноз" <?=$dis;?>></td>
					</tr>
				</table>
				<? /* УБРАЛ GBP
				<table width="288px" style="margin-top:10px">
					<tr>
						<td><span style="color:#333333;font-size:12px;font-weight:bold;">GBP:</span></td>
						<td><input id="game_valut" type="text" name="" value="0.0000" onfocus="this.value='';" onclick="this.value='';" <?=$dis;?>></td>
						<td><input id="game_valut" onclick="$('#redd').show();" type="submit" value="оставить прогноз" <?=$dis;?>></td>
					</tr>
					
				</table>*/ ?>
				
					<?if (!$auth->logged_id()){?>
							<div align="center" id="redd" style="color:#333333;font-size:12px;margin-top:15px;">Для участия в конкурсе нужно <a href="#auth_modal" name="auth_modal" rel="leanModal">войти</a> или <a href="http://banki.tomsk.ru/pages/account/">зарегистрироваться</a></div>
						<?}?>
				
				<?/*<p><b><a href="/pages/275">Правила</a>&nbsp;<span style="color:#5BA1D8;">/</span>&nbsp;<a href="/pages/451">Таблица результатов</a>&nbsp;<span style="color:#5BA1D8;">/</span>&nbsp;<a href="/pages/451">Обсуждение на форуме</a></b></p>*/?>
				</div>
				<div style="clear:both"></div>
			
				</div>
				<?}?>
				
				
				
			<?/*} else if(!$auth->logged_id()) {?>
				<p>Вы не вошли в сисему</p>
			<?} else if($auth->user_act()==false) {?>
				<p>Вы не аутентифицированы</p>
			<?} else {?>
				<p>Не получилось</p>
			<?}*/?>
		
		<?
		/*
		$db_g = mysql_query("SELECT * FROM ".TBL."game_kurs WHERE stat_game='0'");
		$text = '<table width="60%">';
		$text_up = '';
		$text_in = '';
		//$text_up.= '<tr><td colspan="5">Розыгрыш будет происходить в эту пятницу</td></tr>
		$text_up.= '
								<tr><td>Логин</td><td>Последняя дата и время ставки</td><td>Количество изменений</td><td>USD</td><td>EUR</td></tr>';
		while ($row_g = mysql_fetch_array($db_g)) {
			
				
			
			if($row_g['usd_win_state']=='1') $row_g['usd_stav'] = "<span style='color:blue'>".$row_g['usd_stav']."(WIN)</span>";
			if($row_g['eur_win_state']=='1') $row_g['eur_stav'] = "<span style='color:blue'>".$row_g['eur_stav']."(WIN)</span>";
			
				$text_in.= "<tr><td>{$row_g['login']}</td><td>{$row_g['date']}</td><td>{$row_g['count_change']}</td><td>{$row_g['usd_stav']}</td><td>{$row_g['eur_stav']}</td></tr>";
			
		}
		$text.=$text_up.$text_in.'</table>';
		echo '<p>'.$text.'</p>';
		
		$db_g = mysql_query("SELECT * FROM ".TBL."game_kurs WHERE stat_game='1'");
		$text = '<table width="60%">';
		$text_up = '';
		$text_in = '';
		while ($row_g = mysql_fetch_array($db_g)) {
			if($row_g['login']=='game_cbr' && $row_g['usd_win_state']=='12') {
				$text_up.= '<tr><td colspan="5">Розыгрыш произошел '.$row_g['date'].', показатели:(USD:'.$row_g['usd_stav'].';EUR:'.$row_g['eur_stav'].')</td></tr>
								<tr><td>Логин</td><td>Дата и время ставки</td><td>Количество изменений</td><td>USD</td><td>EUR</td></tr>';
			} else {
			if($row_g['usd_win_state']=='1') $row_g['usd_stav'] = "<span style='color:blue'>".$row_g['usd_stav']."(WIN)</span>";
			if($row_g['eur_win_state']=='1') $row_g['eur_stav'] = "<span style='color:blue'>".$row_g['eur_stav']."(WIN)</span>";
			
				$text_in.= "<tr><td>{$row_g['login']}</td><td>{$row_g['date']}</td><td>{$row_g['count_change']}</td><td>{$row_g['usd_stav']}</td><td>{$row_g['eur_stav']}</td></tr>";
			}
		}
		$text.=$text_up.$text_in.'</table>';
		echo $text;*/?>
		
		
		
		<div style="font-size:14px;font-weight:bold;background:#009EB7;color:white;padding:5px 13px;" align="center">
		<a href="javascript:void()" onclick="$('#pravila').slideToggle();" style="color:white">Правила конкурса</a>
		</div>
		
		<div id="pravila" style="display:none;margin-top:10px">
			<? display_content(585);?>
		</div>
		<p class="prognoz_blue">Для справки</p>
		<table class="cbr-kurs-dop" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>	
				<?php
				$valute_last = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='{$last_win_kurs['kursid']}' ORDER BY posted DESC LIMIT 1"));
				$udate_last = strtotime($valute_last[posted]);
				$valute_back = mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."exchange WHERE parent='{$last_win_kurs['kursid']}' AND posted < '".$valute_last[posted]."' AND sell!='' ORDER BY posted DESC LIMIT 1"));
				$v_last_value = $valute_last[sell];
				$v_back_value = $valute_back[sell];
				$u_dist = $v_last_value - $v_back_value;
				//var_dump($u_dist);die;
				if ($u_dist<0) {$color1=$redcolor;$pl1='';}
				elseif($u_dist==0) {$color1=$greycolor;$pl1='';}
				else {$color1=$greencolor;$pl1='+';}
				?>			
				<td valign="middle">
					<div class="prognoz_blue"><nobr>Курс ЦБ РФ на сегодня&nbsp;(<?=date('d.m.Y')?>)</nobr></div>
				</td>
				
				<td><nobr><span style="position: relative; bottom: 8px; left: 2px;"><?echo $last_win_kurs['literal'];?> - <?=number_format($v_last_value,4,",",".")?> <span title="<?=date('d.m.Y',strtotime($valute_back[posted])).' '.$last_win_kurs['literal'].' '.number_format($valute_back[sell],4,",",".")?>" style="color:<?=$color1?>">(<?=$pl1.number_format($u_dist,4,",",".")?>)</span></span></nobr></td>
				<? /* Убрал GPB
				<td><nobr><img src="../../files/File/sberbank/funt-sterlingov.png"><span style="position: relative; bottom: 8px; left: 2px;"><?=number_format($g_last_value,4,",",".")?> <span title="<?=date('d.m.Y',strtotime($g_back[posted])).' GBP '.number_format($g_back[sell],4,",",".")?>" style="color:<?=$color4?>">(<?=$pl4.number_format($g_dist,4,",",".")?>)</span></span></nobr></td>
				*/ ?>
			</tr>		
		</table>
		<div style="margin-top:20px;">
		<?
		include(ROOT.'kernel/code/game_prognoz.php');
						game_prognoz_display();
		?>
		</div>
		
	<?	site_h1('Комментарии');?>
	<br />
	<span><?php echo u_message($t) ?> </span>
		<?php
			$pageid = 0;
			include $t->display();
			
		?>
		<div style="clear:both;height:20px;"></div>
		
			
			<div id="auth_modal">
				<?
					include(ROOT.'kernel/code/auth_modal.php');
					auth_modal_display();
				?>
			</div>
		
		</div>
		<div class="vkladki_divSwitcher" id="vse_val_div">
		<?display_content(601);?>
		</div>
		
		
		<div class="vkladki_divSwitcher" id="valut_div" <?=$stl1;?>>
		<table class="cbr-kurs" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>				
				<td valign="top">
					<div class="cbr-header"><nobr>Курс ЦБ РФ</nobr></div>
					<div class="cbr-data"><?=date('d.m.Y',$udate_last)?></div>
				</td>
				<td><nobr><img src="../../images/design/cbr_03.png"><span style="position: relative; bottom: 8px; left: 2px;"><?=number_format($u_last_value,4,",",".")?> <span title="<?=date('d.m.Y',strtotime($usd_back[posted])).' USD '.number_format($usd_back[sell],4,",",".")?>" style="color:<?=$color1?>">(<?=$pl1.number_format($u_dist1,4,",",".")?>)</span></span></nobr></td>
				<td><nobr><img src="../../images/design/cbr_05.png"><span style="position: relative; bottom: 8px; left: 2px;"><?=number_format($e_last_value,4,",",".")?> <span title="<?=date('d.m.Y',strtotime($e_back[posted])).' EURO '.number_format($e_back[sell],4,",",".")?>" style="color:<?=$color2?>">(<?=$pl2.number_format($e_dist,4,",",".")?>)</span></span></nobr></td>
				<td><nobr><img src="../../images/design/cbr_07.png"><span style="position: relative; bottom: 8px; left: 2px;"><?=number_format(round($bvk_back,4),4,",",".")?> <span style="color:<?=$color3?>">(<?=$pl3.number_format($bvk_dist,4,",",".")?>)</span></span></nobr></td>
			</tr>		
		</table>
		
		<table class="tomskcur-filter" cellpadding="5" cellspacing="0" border="0">
		<tr>
			<td align="left"><?=$filter_?></td>
			<!--td align="right">Курсы томских банков на <?//=hrdate4(vars('date'))?></td-->
		</tr>
		</table>
		
		<?
		$fd = new filter_direct();
		$fd->name = 'kursdir';
		$fd->default = 0;
		
		$fs = new filter_sorting();
		$fs->name = 'kurssort';
		$fs->list = array('bankname','ubuy','usell','ebuy','esell');
		$fs->default = 0;
		$fs->direct = $fd; 
		$fs->temple = '
		<nobr>
			<a title="По убыванию" href="{LINKONLY}&'.$fd->name.'=1">
				<IMG style="border:none" SRC="../../images/kew/u_02.png">
			</a>
			<a title="По возрастанию" href="{LINKONLY}&'.$fd->name.'=0">
				<IMG style="border:none" SRC="../../images/kew/u_01.png">
			</a>
		</nobr>
		';		
		
		?>
		<table class="tomskcur" border=0 cellpadding=0 cellspacing=0 width="100%">
			<tr>
				<td></td>
				<td align=center style="" colspan="2"><img src="../../images/design/cbr_03.png" /></td>
				<td align=center style="" colspan="2"><img src="../../images/design/cbr_05.png" /></td>
			</tr>
			<tr class="tomskcur-header1">
				<td align="center">Банк</td>
				<td align="center">Покупка</td>
				<td align="center">Продажа</td>
				<td align="center">Покупка</td>
				<td align="center">Продажа</td>
			</tr>
			<tr class="tomskcur-header2">
				<td align="center"><?=$fs->display(0,'Банк')?></td>
				<td align="center"><?=$fs->display(1,'Покупка')?></td>
				<td align="center"><?=$fs->display(2,'Продажа')?></td>
				<td align="center"><?=$fs->display(3,'Покупка')?></td>
				<td align="center"><?=$fs->display(4,'Продажа')?></td>
			</tr>				
		<?

				
				
				
	//####################################################
		//#####################################################
		//#####################################################	
	//  if($auth->logged_id()==16740)
				 include "bks_bank_kurs_41.php";
         include "rosbank_kurs.php";
			//	 include "enisei_kurs.php";
		//######################################################
		//######################################################
		//######################################################
		
		
		
		
		//$usdmaxbuy=mysql_fetch_array(mysql_query($q="SELECT MAX(usdbuy) FROM ".TBL."kurs WHERE bank_id!=-1 AND usdbuy!='0.0' AND posted='".vars('date')."' ORDER BY posted"));
		$usdmaxbuy=mysql_fetch_array(mysql_query($q="SELECT MAX(t5.usdbuy) FROM (
														SELECT t1.bank_id,t2.usdbuy,t2.id FROM bank_kurs as t1
															LEFT JOIN (
															SELECT bank_id,usdbuy,id FROM bank_kurs
															WHERE usdbuy!='0.0' AND posted='".vars('date')."' ORDER BY id DESC
															) as t2 ON t1.bank_id = t2.bank_id
														WHERE t1.usdbuy!='0.0' AND t1.bank_id!=-1 AND t1.bank_id!=84 AND t1.bank_id!=23111 AND t1.bank_id!=59 AND t1.posted='".vars('date')."' GROUP BY t1.bank_id
													) as t5"));
		//echo $q;
		$eurmaxbuy=mysql_fetch_array(mysql_query($q="SELECT MAX(t5.eurobuy) FROM (SELECT t1.bank_id,t2.eurobuy,t2.id FROM bank_kurs as t1
															LEFT JOIN (
															SELECT bank_id,eurobuy,id FROM bank_kurs
															WHERE eurobuy!='0.0' AND posted='".vars('date')."' ORDER BY id DESC
															) as t2 ON t1.bank_id = t2.bank_id
														WHERE t1.eurobuy!='0.0' AND t1.bank_id!=-1 AND t1.bank_id!=84 AND t1.bank_id!=23111 AND t1.bank_id!=59 AND t1.posted='".vars('date')."' GROUP BY t1.bank_id
													) as t5"));
		
		$usdminsell=mysql_fetch_array(mysql_query($q="SELECT MIN(t5.usdsell) FROM (SELECT t1.bank_id,t2.usdsell,t2.id FROM bank_kurs as t1
															LEFT JOIN (
															SELECT bank_id,usdsell,id FROM bank_kurs
															WHERE usdsell!='0.0' AND posted='".vars('date')."' ORDER BY id DESC
															) as t2 ON t1.bank_id = t2.bank_id
														WHERE t1.usdsell!='0.0' AND t1.bank_id!=-1 AND t1.bank_id!=84 AND t1.bank_id!=23111 AND t1.bank_id!=59 AND t1.posted='".vars('date')."' GROUP BY t1.bank_id
													) as t5"));
		//echo $q;
		$eurminsell=mysql_fetch_array(mysql_query($q="SELECT MIN(t5.eurosell) FROM (SELECT t1.bank_id,t2.eurosell,t2.id FROM bank_kurs as t1
															LEFT JOIN (
															SELECT bank_id,eurosell,id FROM bank_kurs
															WHERE eurosell!='0.0' AND posted='".vars('date')."' ORDER BY id DESC
															) as t2 ON t1.bank_id = t2.bank_id
														WHERE t1.eurosell!='0.0' AND t1.bank_id!=-1 AND t1.bank_id!=84 AND t1.bank_id!=23111 AND t1.bank_id!=59 AND t1.posted='".vars('date')."' GROUP BY t1.bank_id
													) as t5"));
		
		$sql = "SELECT *,t2.name as bankname FROM 
		(SELECT posted,display,id,bank_id, usdbuy as ubuy, usdsell as usell, eurobuy as ebuy, eurosell as esell, usdbuy_max, usdsell_max, eurobuy_max, eurosell_max, chfbuy as time FROM ".TBL."kurs 
		WHERE posted='".vars('date')."' ORDER BY id DESC) as t1
		LEFT JOIN bank_banki as t2 ON t2.id=t1.bank_id 
		WHERE t1.posted='".vars('date')."' AND t1.bank_id!='-1' AND t1.bank_id!='23111' AND t1.display='1'
		GROUP BY t1.bank_id ORDER BY ".$fs->sql()." ".$fd->sql()."	
		"; 
/*SELECT *, t2.name as bankname FROM (

SELECT posted,display,id,bank_id, usdbuy as ubuy, usdsell as usell, eurobuy as ebuy, eurosell as esell FROM bank_kurs 
WHERE posted='2012-04-16' ORDER BY id DESC
)  as t1

LEFT JOIN bank_banki as t2 ON t2.id=t1.bank_id 
WHERE t1.posted='2012-04-16' AND t1.bank_id='70' AND t1.display='1' 
GROUP BY t1.bank_id ORDER BY bankname asc*/
		$db=mysql_query($sql);
		$f=true;
		$cc = array();
		$ct = array();
		$znak = array();
		//$znak [56] = '/pages/48/?id=14';
		$znak [31] = '/pages/48/?id=30';
		$znak [18] = '/pages/1307';
		$znak [33] = '/pages/954';
		$znak [6] = '/pages/113/?idp=142';
		$znak [70] = '/pages/48/?id=48';
		$znak [23] = '/pages/48/?id=37';
		$znak [28] = '/pages/1288';
		$znak [9] = '/pages/1132';
		//

						
		ob_start();
        $c_arr=array();
		while ($row=mysql_fetch_assoc($db))
		{
			/*if($row['usell']=='0.0') $row['usell']='';
			if($row['ubuy']=='0.0') $row['ubuy']='';
			if($row['esell']=='0.0') $row['esell']='';
			if($row['ebuy']=='0.0') $row['ebuy']='';
			print_r($row);*/
			$time=$row['time'];
			if($time!="" && $time!=NULL)
			{
				$time=$time*10000;
				$time=date( "Y-m-d H:i:s" , $time);
			}
			$c=array();
			if ($row[ubuy]==$usdmaxbuy[0] && $row['bank_id']!='84') $c[0]='curplus';
			if ($row[usell]==$usdminsell[0] && $row['bank_id']!='84') $c[1]='curplus';
			if ($row[ebuy]==$eurmaxbuy[0] && $row['bank_id']!='84') $c[2]='curplus';
			if ($row[esell]==$eurminsell[0] && $row['bank_id']!='84') $c[3]='curplus';
			
          /*  if($c_arr[0]=='')
                $c_arr[0]=$c[0];
            else
                $c[0]='';
            if($c_arr[1]=='')
                $c_arr[1]=$c[1];
            else
                $c[1]='';    
            if($c_arr[2]=='')
                $c_arr[2]=$c[2];
            else
                $c[2]='';    
            if($c_arr[3]=='')
                $c_arr[3]=$c[3];
            else
                $c[3]='';    
                
            */    
			$rt=mysql_fetch_array(mysql_query($q="SELECT id,name,ppage,ppattern FROM ".TBL."banki WHERE id='{$row['bank_id']}'"));
			
			$pers=mysql_fetch_assoc(mysql_query("SELECT id FROM ".TBL."bank_personal WHERE display='1' AND bank='{$rt['id']}'"));

            ///////////////////////////////////////////////////////
			if ($rt['name'] == "Россельхозбанк")
            {
                $rt['name']= 'АО «Россельхозбанк» от 1000 у.е.';
                if ($rt['ppage']) $bank=link_(sitemap_path($rt['ppage']),$rt['name'],'bank','target="_blank"');
                elseif ($rt['ppattern']) $bank=link_('/show.php?id='.$rt['ppattern'],$rt['name'],'bank','target="_self"');
                elseif ($pers[id]) $bank=link_('/pages/48/?id='.$pers[id],$rt['name'],'bank','target="_self"');
                else $bank=$rt['name'];
                
                if(in_array($row['bank_id'],array_keys($znak)))
                {

                    $bank = $znak[$row['bank_id']]; 
                    $bank = "<a href=\"$bank\">$rt[name]<img src=\"../../images/design/znak.png\"></a>";
                
                }

                echo "    <tr class=\"curbody\" title='Курс обновлен: ".$time."'>
                        <td class='bank' width=50%>{$bank}</td>
                        <td class='{$c[0]}'>",$row[usdbuy_max]>0?number_format(addzero($row[usdbuy_max]),2,",","."):'-',"</td>
                        <td class='{$c[1]}'>",$row[usdsell_max]>0?number_format(addzero($row[usdsell_max]),2,",","."):'-',"</td>
                        <td class='{$c[2]}'>",$row[eurobuy_max]>0?number_format(addzero($row[eurobuy_max]),2,",","."):'-',"</td>
                        <td class='{$c[3]}'>",$row[eurosell_max]>0?number_format(addzero($row[eurosell_max]),2,",","."):'-',"</td>
                    </tr>";
            
                $cc[0] += $row[usdbuy_max];
                $cc[1] += $row[usdsell_max];
                $cc[2] += $row[eurobuy_max];
                $cc[3] += $row[eurosell_max];
            
                $ct[0] += $row[usdbuy_max]>0?1:0;
                $ct[1] += $row[usdsell_max]>0?1:0;
                $ct[2] += $row[eurobuy_max]>0?1:0;
                $ct[3] += $row[eurosell_max]>0?1:0;
                $rt['name']= 'АО «Россельхозбанк» до 1000 у.е.';
            } 
//////////////////////////////////////////////////////////////////////
			if ($rt['name'] == "Банк Взаимодействие") $rt['name']= 'Банк Взаимодействие (г.Северск)';
		//	if($rt['name']=="ббОперкасса2 Томскпромстройбанк в КПК «Содействие»") $rt['name']="Томскпромстройбанк (филиал г.Северск)";
						
						
			if ($rt['ppage']) $bank=link_(sitemap_path($rt['ppage']),$rt['name'],'bank','target="_blank"');
			elseif ($rt['ppattern']) $bank=link_('/show.php?id='.$rt['ppattern'],$rt['name'],'bank','target="_self"');
			elseif ($pers[id]) $bank=link_('/pages/48/?id='.$pers[id],$rt['name'],'bank','target="_self"');
			else $bank=$rt['name'];
			
			if(in_array($row['bank_id'],array_keys($znak)))
			{
				/*if ($rt['ppage']) $bank=sitemap_path($rt['ppage']);
				elseif ($rt['ppattern']) $bank='/show.php?id='.$rt['ppattern'];
				elseif ($pers[id]) $bank='/pages/48/?id='.$pers[id];*/
				$bank = $znak[$row['bank_id']]; 
				$bank = "<a href=\"$bank\">$rt[name]<img src=\"../../images/design/znak.png\"></a>";
				
			}
							if($row['bank_id']==33)
								$bank = '<a href="http://banki.tomsk.ru/pages/48/?id=59">Сбербанк России<img src=../../images/design/znak.png></a>';
							if($row['bank_id']==87)
								if( (int)date('H')<9)
									continue;
							if($row['bank_id']==59)
								continue;
                                    
                           if($row['bank_id']==84 || $row['bank_id']==82)
                                if((date('w'))==0 || (date('w'))==6)
                                    continue;
                                    //id ставить на томпростройбанк tspb_kurs
			if($row['bank_id']=='27') $bank = '<a href="http://banki.tomsk.ru/pages/48/?id=54">МТС Банк</a>';
						if($row['bank_id']=='82') $bank = '<a href="http://banki.tomsk.ru/pages/48/?id=63">БКС Банк</a>';
						if($row['bank_id']=='84') $bank = '<a href="https://valuta.bcs-bank.com/?utm_source=banki.tomsk.ru&utm_medium=line&utm_campaign=line-1-banki-tomsk-ru" target="_blank">БКС Банк. Обмен валюты онлайн</a>';
            if($row['bank_id']=='88')  $bank = '<a href="http://banki.tomsk.ru/pages/420/index.php?id=226" target="_self">Томскпромстройбанк (Валютная касса на К.Маркса)</a>';
            if($row['bank_id']==89) $bank = '<a href="http://banki.tomsk.ru/pages/113/?idp=142#irkutskii" target="_self">Томскпромстройбанк (Касса на Иркутском тракте)<img src=../../images/design/znak.png></a>';
			echo "	<tr class=\"curbody\"  title='Курс обновлен: ".$time."'>
						<td class='bank' width=50% >{$bank}</td>
						<td class='{$c[0]}'>",$row[ubuy]>0?number_format(addzero($row[ubuy]),2,",","."):'-',"</td>
						<td class='{$c[1]}'>",$row[usell]>0?number_format(addzero($row[usell]),2,",","."):'-',"</td>
						<td class='{$c[2]}'>",$row[ebuy]>0?number_format(addzero($row[ebuy]),2,",","."):'-',"</td>
						<td class='{$c[3]}'>",$row[esell]>0?number_format(addzero($row[esell]),2,",","."):'-',"</td>
					</tr>";
			
			$cc[0] += $row[ubuy];
			$cc[1] += $row[usell];
			$cc[2] += $row[ebuy];
			$cc[3] += $row[esell];
			
			$ct[0] += $row[ubuy]>0?1:0;
			$ct[1] += $row[usell]>0?1:0;
			$ct[2] += $row[ebuy]>0?1:0;
			$ct[3] += $row[esell]>0?1:0;
		}
		echo '
			<tr height="20px">
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="left" valign="top">Среднее значение</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{USDBUY}</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{USDSELL}</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{EURBUY}</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{EURSELL}</td>
			</tr>
			<tr><td colspan="5">',spacer(1,5),'</td></tr>
		';
		
		$cont = ob_get_contents();
		ob_end_clean();
			$ct[0] = $ct[0]==0?1:$ct[0];
			$ct[1] = $ct[1]==0?1:$ct[1];
			$ct[2] = $ct[2]==0?1:$ct[2];
			$ct[3] = $ct[3]==0?1:$ct[3];
		$cont = str_replace("{USDBUY}",number_format(round($cc[0]/$ct[0],2),2,",","."),$cont);
		$cont = str_replace("{USDSELL}",number_format(round($cc[1]/$ct[1],2),2,",","."),$cont);
		$cont = str_replace("{EURBUY}",number_format(round($cc[2]/$ct[2],2),2,",","."),$cont);
		$cont = str_replace("{EURSELL}",number_format(round($cc[3]/$ct[3],2),2,",","."),$cont);
		echo $cont;
		echo '</table>';
		?>		
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td><img src="../../images/design/znak.png"></td>
				<td>- показывает наличие дополнительной информации о курсах валют на персональной странице банка.</td>
				 
			</tr>
		</table>
		
	<?/*
	echo spacer(5,15);
	site_h1('Курсы обмена безналичной валюты для юридических лиц');?>
			<table class="tomskcur" border=0 cellpadding=0 cellspacing=0 width="100%">
			<tr>
				<td></td>
				<td align=center style="" colspan="2"><img src="../../images/design/cbr_03.png" /></td>
				<td align=center style="" colspan="2"><img src="../../images/design/cbr_05.png" /></td>
			</tr>
			<tr class="tomskcur-header1">
				<td align="center">Банк</td>
				<td align="center">Покупка</td>
				<td align="center">Продажа</td>
				<td align="center">Покупка</td>
				<td align="center">Продажа</td>
			</tr>
			<tr class="tomskcur-header2">
				<td align="center"><?=$fs->display(0,'Банк')?></td>
				<td align="center"><?=$fs->display(1,'Покупка')?></td>
				<td align="center"><?=$fs->display(2,'Продажа')?></td>
				<td align="center"><?=$fs->display(3,'Покупка')?></td>
				<td align="center"><?=$fs->display(4,'Продажа')?></td>
			</tr>				
		<?

		
		
		$usdmaxbuy=mysql_fetch_array(mysql_query($q="SELECT MAX(usdbuy) FROM ".TBL."kurs_uriki WHERE bank_id!=-1 AND usdbuy!='0.0' AND posted='".vars('date')."' ORDER BY posted"));
		$eurmaxbuy=mysql_fetch_array(mysql_query($q="SELECT MAX(eurobuy) FROM ".TBL."kurs_uriki WHERE bank_id!=-1 AND eurobuy!='0.0' AND posted='".vars('date')."' ORDER BY posted"));
		$usdminsell=mysql_fetch_array(mysql_query($q="SELECT MIN(usdsell) FROM ".TBL."kurs_uriki WHERE bank_id!=-1 AND usdsell!='0.0' AND posted='".vars('date')."' ORDER BY posted"));
		$eurminsell=mysql_fetch_array(mysql_query($q="SELECT MIN(eurosell) FROM ".TBL."kurs_uriki WHERE bank_id!=-1 AND eurosell!='0.0' AND posted='".vars('date')."' ORDER BY posted"));
		$sql = "SELECT DISTINCT (bank_id),
		(SELECT name FROM ".TBL."banki WHERE id=t1.bank_id) as bankname,
		usdbuy as ubuy,
		usdsell as usell,
		eurobuy as ebuy,
		eurosell as esell
		FROM ".TBL."kurs_uriki as t1 WHERE t1.posted='".vars('date')."' AND t1.bank_id!='-1' AND display='1'
		GROUP BY t1.bank_id ORDER BY ".$fs->sql()." ".$fd->sql().", t1.id DESC 	
		"; 
		$db=mysql_query($sql);
		$f=true;
		$cc = array();
		$ct = array();
		$znak = array();
		//$znak [56] = '/pages/48/?id=14';
		$znak [18] = '/pages/48/?id=10';
		$znak [33] = '/pages/954';
		$znak [6] = '/pages/113/?idp=142';
		$znak [70] = '/pages/48/?id=48&ur=1';
		$znak [23] = '/pages/48/?id=37';
		$znak [9] = '/pages/1132';
		//
		
		ob_start();
		while ($row=mysql_fetch_assoc($db))
		{
			/*if($row['usell']=='0.0') $row['usell']='';
			if($row['ubuy']=='0.0') $row['ubuy']='';
			if($row['esell']=='0.0') $row['esell']='';
			if($row['ebuy']=='0.0') $row['ebuy']='';
			print_r($row);
			$c=array();
			if ($row[ubuy]==$usdmaxbuy[0]) $c[0]='curplus';
			if ($row[usell]==$usdminsell[0]) $c[1]='curplus';
			if ($row[ebuy]==$eurmaxbuy[0]) $c[2]='curplus';
			if ($row[esell]==$eurminsell[0]) $c[3]='curplus';
			
			$rt=mysql_fetch_array(mysql_query($q="SELECT id,name,ppage,ppattern FROM ".TBL."banki WHERE id='{$row['bank_id']}'"));
			
			$pers=mysql_fetch_assoc(mysql_query("SELECT id FROM ".TBL."bank_personal WHERE display='1' AND bank='{$rt['id']}'"));

			if ($rt['name'] == "Россельхозбанк") $rt['name']= 'АО «Россельхозбанк»';
			
			if ($rt['ppage']) $bank=link_(sitemap_path($rt['ppage']),$rt['name'],'bank','target="_blank"');
			elseif ($rt['ppattern']) $bank=link_('/show.php?id='.$rt['ppattern'],$rt['name'],'bank','target="_self"');
			elseif ($pers[id]) $bank=link_('/pages/48/?id='.$pers[id],$rt['name'],'bank','target="_self"');
			else $bank=$rt['name'];
			
			if(in_array($row['bank_id'],array_keys($znak)))
			{
				/*if ($rt['ppage']) $bank=sitemap_path($rt['ppage']);
				elseif ($rt['ppattern']) $bank='/show.php?id='.$rt['ppattern'];
				elseif ($pers[id]) $bank='/pages/48/?id='.$pers[id];
				$bank = $znak[$row['bank_id']]; 
				$bank = "<a href=\"$bank\">$rt[name]</a>";
				
			}
						

			echo "	<tr class=\"curbody\">
						<td class='bank' width=50%>{$bank}</td>
						<td class='{$c[0]}'>",$row[ubuy]>0?number_format(addzero($row[ubuy]),2,",","."):'-',"</td>
						<td class='{$c[1]}'>",$row[usell]>0?number_format(addzero($row[usell]),2,",","."):'-',"</td>
						<td class='{$c[2]}'>",$row[ebuy]>0?number_format(addzero($row[ebuy]),2,",","."):'-',"</td>
						<td class='{$c[3]}'>",$row[esell]>0?number_format(addzero($row[esell]),2,",","."):'-',"</td>
					</tr>";
			
			$cc[0] += $row[ubuy];
			$cc[1] += $row[usell];
			$cc[2] += $row[ebuy];
			$cc[3] += $row[esell];
			
			$ct[0] += $row[ubuy]>0?1:0;
			$ct[1] += $row[usell]>0?1:0;
			$ct[2] += $row[ebuy]>0?1:0;
			$ct[3] += $row[esell]>0?1:0;
		}
		echo '
			<tr height="20px">
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="left" valign="top">Среднее значение</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{USDBUY}</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{USDSELL}</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{EURBUY}</td>
				<td style="border-top: 1px solid #eee;border-bottom: 1px solid #488cc9;" align="center">{EURSELL}</td>
			</tr>
			<tr><td colspan="5">',spacer(1,5),'</td></tr>
		';
		
		$cont = ob_get_contents();
		ob_end_clean();
			$ct[0] = $ct[0]==0?1:$ct[0];
			$ct[1] = $ct[1]==0?1:$ct[1];
			$ct[2] = $ct[2]==0?1:$ct[2];
			$ct[3] = $ct[3]==0?1:$ct[3];
		$cont = str_replace("{USDBUY}",number_format(round($cc[0]/$ct[0],2),2,",","."),$cont);
		$cont = str_replace("{USDSELL}",number_format(round($cc[1]/$ct[1],2),2,",","."),$cont);
		$cont = str_replace("{EURBUY}",number_format(round($cc[2]/$ct[2],2),2,",","."),$cont);
		$cont = str_replace("{EURSELL}",number_format(round($cc[3]/$ct[3],2),2,",","."),$cont);
		echo $cont;
		echo '</table>';*/
		?>		
		
		<div class="block">
		<table width="100%">
	
		<tr class="metal-head">
					<td align="left" colspan="4">Графические данные</td>
					<td align="right" colspan="4"></td>
				</tr>
				<tr>	
					<td colspan="8">
						<div id="block-body2" class="block-body" style="padding-top: 5px; display: block;">
						<?
						include(ROOT.'kernel/code/grafik_new.php');
						grafik_new_display();	
						?>
						</div>
					</td>
				</tr>
		</table>
		
		
		</div>
		
		<?
		
		
		$rw=mysql_fetch_array(mysql_query("SELECT * FROM ".TBL."texts WHERE id=394"));
		if($rw['text'])	display_content(394);
		
		echo spacer(15,1);
		
		site_h1('Аналитика');
		$db=mysql_query($q="SELECT *,DATE_FORMAT(posted,'%d.%m.%Y') as data FROM ".TBL."pressreliz_analitic WHERE display='1' ORDER BY posted DESC LIMIT 0,5");
		$num = mysql_num_rows($db);
			$bank60 = '';
			while ($row=mysql_fetch_array($db)) {
			
				$bank60.='<span>'.$row['data'].'&nbsp;<a href="/pages/903?id='.$row['id'].'">'.$row['name'].'</a></span><br />';
			}
			$bank60.='<p align="right"><a href="/pages/449/?from=kurs" style="color:#488CC9;font-weight:bold;border-bottom: 1px dashed;">Все новости</a><p>';
		$r_array = array('kurs_vpres'=>$bank60);
		list($name,$txt,$d)=mysql_fetch_array(mysql_query("SELECT name,text,display FROM ".TBL."texts WHERE id='461'"));
		if($d)
		{
			$patterns = array();
			$replacements = array();
			foreach($r_array as $k=>$v)
			{
				$patterns[] = '/{'.$k.'}/';
				$replacements[] = $v;
			}
			$txt = preg_replace($patterns, $replacements, $txt);
			echo $txt;
		}
		
		
		
		$link='<!--BANKI.TOMSK.RU informer begin--><a href="http://banki.tomsk.ru/pages/41/" title="Все банки Томска" target="_blank"><img src="http://banki.tomsk.ru/info.php?a=#" width="120" height="100" border="0" alt="Лучшие курсы валют Томских банков от banki.tomsk.ru"></a><!--BANKI.TOMSK.RU informer end-->';
		site_h1('Информеры от <span style="text-decoration:underline;">banki.tomsk.ru</span>');

		echo '<table width="100%"><tr>';
		
		for ($i=1;$i<=5;$i++)
		{
			$a=str_replace('#',$i,$link);
			echo '
					<td width="20%" align="center"><div id="i',$i,'" class="informers">
						
							',$a,'
							<p><a onclick="$(\'#inform_text\').html(\'',htmlspecialchars($a),'\');$(\'#inform_text\').show()" href="javascript: void(0)" style="font-weight:bold">Получить код</a></p>
						</div>
						
					</td>
				';
		}
		?></tr><tr><td colspan="5" width="100%"><textarea style="width:100%;display:none" id="inform_text"></textarea></td>
		</tr></table>
	
		</div><?

	}
?>