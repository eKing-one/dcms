<?
include_once '../../sys//inc/start.php';
include_once '../../sys//inc/compress.php';
include_once '../../sys//inc/sess.php';
include_once '../../sys//inc/home.php';
include_once '../../sys//inc/settings.php';
include_once '../../sys//inc/db_connect.php';
include_once '../../sys//inc/ipua.php';
include_once '../../sys//inc/fnc.php';
include_once '../../sys//inc/user.php';

if (isset($user))$ank['id']=$user['id'];
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);if ($ank['id']==0)
{
$ank=get_user($ank['id']);
$set['title']=$ank['nick'].' - анкета '; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();/*
==================================
Приватность станички пользователя
Запрещаем просмотр анкеты
==================================
*/

	$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
	$frend=dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"),0);
	$frend_new=dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"),0);

if ($ank['id'] != $user['id'] && $user['group_access'] == 0)
{

	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // Начинаем вывод если стр имеет приват настройки
	{
		if ($ank['group_access']>1)echo "<div class='err'>$ank[group_name]</div>\n";
		echo "<div class='nav1'>";
		echo group($ank['id'])." $ank[nick] ";
		echo medal($ank['id'])." ".online($ank['id'])." ";
		echo "</div>";

		echo "<div class='nav2'>";
		echo avatar($ank['id'], true, 128, 128);
		echo "<br />";

	}
	
	
	if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo 'Просматривать страничку пользователя могут только его друзья!';
		echo '</div>';
		
		// В друзья
		if (isset($user))
		{
			echo '<div class="nav1">';
			if ($frend_new == 0 && $frend==0){
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=".$ank['id']."'>Добавить в друзья</a><br />\n";
			}elseif ($frend_new == 1){
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>Отклонить заявку</a><br />\n";
			}elseif ($frend == 2){
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>Удалить из друзей</a><br />\n";
			}
			echo "</div>";
		}
	include_once '../../sys/inc/tfoot.php';
	exit;
	}
	
	if ($uSet['privat_str'] == 0) // Если закрыта
	{
		echo '<div class="mess">';
		echo 'Пользователь запретил просматривать его страничку!';
		echo '</div>';
		
	include_once '../../sys/inc/tfoot.php';
	exit;
	}

}
	

echo "<span class=\"err\">$ank[group_name]</span><br />\n";

if ($ank['ank_o_sebe']!=NULL)echo "<span class=\"ank_n\">О себе:</span> <span class=\"ank_d\">$ank[ank_o_sebe]</span><br />\n";

if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))
echo "<div class='foot'>&laquo;<a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />\n</div>\n";

include_once '../../sys//inc/tfoot.php';
exit;
}

$ank=get_user($ank['id']);
if(!$ank){header("Location: /index.php?".SID);exit;}
$timediff=dbresult(dbquery("SELECT `time` FROM `user` WHERE `id` = '$ank[id]' LIMIT 1",$db), 0);

$oneMinute=60; 
$oneHour=60*60; 
$hourfield=floor(($timediff)/$oneHour); 
$minutefield=floor(($timediff-$hourfield*$oneHour)/$oneMinute); 
$secondfield=floor(($timediff-$hourfield*$oneHour-$minutefield*$oneMinute)); 

$sHoursLeft=$hourfield; 
$sHoursText = "часов"; 
$nHoursLeftLength = strlen($sHoursLeft); 
$h_1=substr($sHoursLeft,-1,1); 
if (substr($sHoursLeft,-2,1) != 1 && $nHoursLeftLength>1) 
{ 
    if ($h_1== 2 || $h_1== 3 || $h_1== 4) 
    { 
        $sHoursText = "часа"; 
    } 
    elseif ($h_1== 1) 
    { 
        $sHoursText = "час"; 
    } 
} 

if ($nHoursLeftLength==1) 
{ 
    if ($h_1== 2 || $h_1== 3 || $h_1== 4) 
    { 
        $sHoursText = "часа"; 
    } 
    elseif ($h_1== 1) 
    { 
        $sHoursText = "час"; 
    } 
} 

$sMinsLeft =$minutefield; 
$sMinsText = "минут"; 
$nMinsLeftLength = strlen($sMinsLeft); 
$m_1=substr($sMinsLeft,-1,1); 

if ($nMinsLeftLength>1 && substr($sMinsLeft,-2,1) != 1) 
{ 
    if ($m_1== 2 || $m_1== 3 || $m_1== 4) 
    { 
        $sMinsText = "минуты"; 
    } 
    else if ($m_1== 1) 
    { 
        $sMinsText = "минута"; 
    } 
} 

if ($nMinsLeftLength==1) 
{ 
    if ($m_1== 2 || $m_1==3 || $m_1== 4) 
    { 
        $sMinsText = "минуты"; 
    } 
    elseif ($m_1== "1") 
    { 
        $sMinsText = "минута"; 
    } 
} $displaystring="". 
$sHoursLeft." ". 
$sHoursText." ". 
$sMinsLeft." ". 
$sMinsText." ";
if ($timediff<0) $displaystring='дата уже наступила'; 

$set['title']=$ank['nick'].' - анкета '; //网页标题
include_once '../../sys/inc/thead.php';
title();

if ((!isset($_SESSION['refer']) || $_SESSION['refer']==NULL)
&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL &&
!preg_match('#info\.php#',$_SERVER['HTTP_REFERER']))
$_SESSION['refer']=str_replace('&','&amp;',preg_replace('#^http://[^/]*/#','/', $_SERVER['HTTP_REFERER']));aut();

if (isset($user) && $ank['id']==$user['id'])
{
$name = "<a href='/user/info/edit.php?act=ank&amp;set=name'>";
$date = "<a href='/user/info/edit.php?act=ank&amp;set=date'>";
$gorod = "<a href='/user/info/edit.php?act=ank&amp;set=gorod'>";

$orien = "<a href='/user/info/edit.php?act=ank&amp;set=orien'>";
$loves = "<a href='/user/info/edit.php?act=ank&amp;set=loves'>";
$opar = "<a href='/user/info/edit.php?act=ank&amp;set=opar'>";

$volos = "<a href='/user/info/edit.php?act=ank&amp;set=volos'>";
$ves = "<a href='/user/info/edit.php?act=ank&amp;set=ves'>";
$glaza = "<a href='/user/info/edit.php?act=ank&amp;set=glaza'>";
$rost = "<a href='/user/info/edit.php?act=ank&amp;set=rost'>";
$osebe = "<a href='/user/info/edit.php?act=ank&amp;set=osebe'>";
$pol = "<a href='/user/info/edit.php?act=ank&amp;set=pol'>";
$telo = "<a href='/user/info/edit.php?act=ank&amp;set=telo'>";

$avto = "<a href='/user/info/edit.php?act=ank&amp;set=avto'>";
$baby = "<a href='/user/info/edit.php?act=ank&amp;set=baby'>";
$proj = "<a href='/user/info/edit.php?act=ank&amp;set=proj'>";
$zan = "<a href='/user/info/edit.php?act=ank&amp;set=zan'>";
$smok = "<a href='/user/info/edit.php?act=ank&amp;set=smok'>";
$mat_pol = "<a href='/user/info/edit.php?act=ank&amp;set=mat_pol'>";

$mail = "<a href='/user/info/edit.php?act=ank&amp;set=mail'>";
$icq = "<a href='/user/info/edit.php?act=ank&amp;set=icq'>";
$skype = "<a href='/user/info/edit.php?act=ank&amp;set=skype'>";
$mobile = "<a href='/user/info/edit.php?act=ank&amp;set=mobile'>";

$a = "</a>";

}else{
$name = "<font style='color : #005ba8; padding:1px;'>";
$date =  "<font style='color : #005ba8; padding:1px;'>";
$gorod =  "<font style='color : #005ba8; padding:1px;'>";

$orien = "<font style='color : #005ba8; padding:1px;'>";
$loves = "<font style='color : #005ba8; padding:1px;'>";
$opar = "<font style='color : #005ba8; padding:1px;'>";

$avto = "<font style='color : #005ba8; padding:1px;'>";
$baby =  "<font style='color : #005ba8; padding:1px;'>";
$zan = "<font style='color : #005ba8; padding:1px;'>";
$smok = "<font style='color : #005ba8; padding:1px;'>";
$mat_pol =  "<font style='color : #005ba8; padding:1px;'>";
$proj =  "<font style='color : #005ba8; padding:1px;'>";

$telo =  "<font style='color : #005ba8; padding:1px;'>";
$volos = "<font style='color : #005ba8; padding:1px;'>";
$ves =  "<font style='color : #005ba8; padding:1px;'>";
$glaza =  "<font style='color : #005ba8; padding:1px;'>";
$rost =  "<font style='color : #005ba8; padding:1px;'>";
$osebe =   "<font style='color : #005ba8; padding:1px;'>";
$pol =   "<font style='color : #005ba8; padding:1px;'>";

$mail =   "<font style='color : #005ba8; padding:1px;'>";
$icq =   "<font style='color : #005ba8; padding:1px;'>";
$skype =   "<font style='color : #005ba8; padding:1px;'>";
$mobile =   "<font style='color : #005ba8; padding:1px;'>";
$a = "</font>";

}
if ($ank['group_access']>1)echo "<div class='err'>$ank[group_name]</div>\n";
echo "<div class='nav2'>";
echo "<span class=\"ank_n\">Посл. посещение:</span> <span class=\"ank_d\">".vremja($ank['date_last'])."</span><br />\n";
echo "</div>\n";

echo "<div class='nav1'>";
echo avatar($ank['id'], true, 128, 128);
echo "</div>\n";

//-------------alex-borisi---------------//
if ($ank['rating']>=0 && $ank['rating']<= 100){
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$ank[rating]%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=100 && $ank['rating']<= 200){
$rat=$ank['rating']-100;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=200 && $ank['rating']<= 300){
$rat=$ank['rating']-200;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=300 && $ank['rating']<= 400){
$rat=$ank['rating']-300;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=400 && $ank['rating']<= 500){
$rat=$ank['rating']-400;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=500 && $ank['rating']<= 600){
$rat=$ank['rating']-500;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=600 && $ank['rating']<= 700){
$rat=$ank['rating']-600;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=700 && $ank['rating']<= 800){
$rat=$ank['rating']-700;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=800 && $ank['rating']<= 900){
$rat=$ank['rating']-800;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}elseif ($ank['rating']>=900 && $ank['rating']<= 1000){
$rat=$ank['rating']-900;
echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}

//-------------alex-borisi---------------//

if (isset($user) && $user['id']!=$ank['id']){
echo "<div class='nav2'>";
echo "<img src='/style/icons/pochta.gif' alt='*' /> <a href=\"/mail.php?id=$ank[id]\"><b>Написать в приват</b></a>\n";
echo "</div>\n";
}

echo "<div class='nav2'>";
echo "<img src='/style/icons/foto.png' alt='*' /> <a href='/foto/$ank[id]/'><b>相片册</b></a><br />\n";
echo "</div>\n";

//-----------------инфо----------------//
echo "<div class='nav2'>";
echo "<b>ID: $ank[id]</b><br /> \n";
echo "Баллы (";
echo "<font color='green'>$ank[balls]</font>)<br /> \n";echo $sMonet[2] . ' (' . $ank['money'] . ')<br />';
echo "<img src='/style/icons/time.png' alt='*' width='14'/> ($displaystring)<br />  \n";
echo "</div>\n";

//-------------------------------------------------------//

//------------------основное-------------------//
echo "<div class='nav1'>";
if ($ank['ank_name']!=NULL)
echo "$name<span class=\"ank_n\">Имя:</span>$a <span class=\"ank_d\">$ank[ank_name]</span><br />\n";
else
echo "$name<span class=\"ank_n\">Имя:</span>$a<br />\n";

echo "$pol<span class=\"ank_n\">Пол:</span>$a <span class=\"ank_d\">".(($ank['pol']==1)?'Мужской':'Женский')."</span><br />\n";

if ($ank['ank_city']!=NULL)
echo "$gorod<span class=\"ank_n\">Город:</span>$a <span class=\"ank_d\">".output_text($ank['ank_city'])."</span><br />\n";
else
echo "$gorod<span class=\"ank_n\">Город:</span>$a<br />\n";

if ($ank['ank_d_r']!=NULL && $ank['ank_m_r']!=NULL && $ank['ank_g_r']!=NULL){
if ($ank['ank_m_r']==1)$ank['mes']='Января';
elseif ($ank['ank_m_r']==2)$ank['mes']='Февраля';
elseif ($ank['ank_m_r']==3)$ank['mes']='Марта';
elseif ($ank['ank_m_r']==4)$ank['mes']='Апреля';
elseif ($ank['ank_m_r']==5)$ank['mes']='Мая';
elseif ($ank['ank_m_r']==6)$ank['mes']='Июня';
elseif ($ank['ank_m_r']==7)$ank['mes']='Июля';
elseif ($ank['ank_m_r']==8)$ank['mes']='Августа';
elseif ($ank['ank_m_r']==9)$ank['mes']='Сентября';
elseif ($ank['ank_m_r']==10)$ank['mes']='Октября';
elseif ($ank['ank_m_r']==11)$ank['mes']='Ноября';
else $ank['mes']='Декабря';
echo "$date<span class=\"ank_n\">Дата рождения:</span>$a $ank[ank_d_r] $ank[mes] $ank[ank_g_r]г. <br />\n";
$ank['ank_age']=date("Y")-$ank['ank_g_r'];
if (date("n")<$ank['ank_m_r'])$ank['ank_age']=$ank['ank_age']-1;
elseif (date("n")==$ank['ank_m_r']&& date("j")<$ank['ank_d_r'])$ank['ank_age']=$ank['ank_age']-1;
echo "<span class=\"ank_n\">Возраст:</span> $ank[ank_age] \n";
}
elseif($ank['ank_d_r']!=NULL && $ank['ank_m_r']!=NULL)
{
if ($ank['ank_m_r']==1)$ank['mes']='Января';
elseif ($ank['ank_m_r']==2)$ank['mes']='Февраля';
elseif ($ank['ank_m_r']==3)$ank['mes']='Марта';
elseif ($ank['ank_m_r']==4)$ank['mes']='Апреля';
elseif ($ank['ank_m_r']==5)$ank['mes']='Мая';
elseif ($ank['ank_m_r']==6)$ank['mes']='Июня';
elseif ($ank['ank_m_r']==7)$ank['mes']='Июля';
elseif ($ank['ank_m_r']==8)$ank['mes']='Августа';
elseif ($ank['ank_m_r']==9)$ank['mes']='Сентября';
elseif ($ank['ank_m_r']==10)$ank['mes']='Октября';
elseif ($ank['ank_m_r']==11)$ank['mes']='Ноября';
else $ank['mes']='Декабря';
echo "$date<span class=\"ank_n\">День рождения:</span>$a $ank[ank_d_r] $ank[mes] \n";
}else{echo "$date<span class=\"ank_n\">Дата рождения:</span>$a\n";}

if ($ank['ank_d_r']>=19 && $ank['ank_m_r']==1){echo "| Водолей<br />";}
elseif ($ank['ank_d_r']<=19 && $ank['ank_m_r']==2){echo "| Водолей<br />";}
elseif ($ank['ank_d_r']>=18 && $ank['ank_m_r']==2){echo "| Рыбы<br />";}
elseif ($ank['ank_d_r']<=21 && $ank['ank_m_r']==3){echo "| Рыбы<br />";}
elseif ($ank['ank_d_r']>=20 && $ank['ank_m_r']==3){echo "| Овен<br />";}
elseif ($ank['ank_d_r']<=21 && $ank['ank_m_r']==4){echo "| Овен<br />";}
elseif ($ank['ank_d_r']>=20 && $ank['ank_m_r']==4){echo "| Телец<br />";}
elseif ($ank['ank_d_r']<=21 && $ank['ank_m_r']==5){echo "| Телец<br />";}
elseif ($ank['ank_d_r']>=20 && $ank['ank_m_r']==5){echo "| Близнецы<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==6){echo "| Близнецы<br />";}
elseif ($ank['ank_d_r']>=21 && $ank['ank_m_r']==6){echo "| Рак<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==7){echo "| Рак<br />";}
elseif ($ank['ank_d_r']>=23 && $ank['ank_m_r']==7){echo "| Лев<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==8){echo "| Лев<br />";}
elseif ($ank['ank_d_r']>=22 && $ank['ank_m_r']==8){echo "| Дева<br />";}
elseif ($ank['ank_d_r']<=23 && $ank['ank_m_r']==9){echo "| Дева<br />";}
elseif ($ank['ank_d_r']>=22 && $ank['ank_m_r']==9){echo "| Весы<br />";}
elseif ($ank['ank_d_r']<=23 && $ank['ank_m_r']==10){echo "| Весы<br />";}
elseif ($ank['ank_d_r']>=22 && $ank['ank_m_r']==10){echo "| Скорпион<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==11){echo "| Скорпион<br />";}
elseif ($ank['ank_d_r']>=21 && $ank['ank_m_r']==11){echo "| Стрелец<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==12){echo "| Стрелец<br />";}
elseif ($ank['ank_d_r']>=21 && $ank['ank_m_r']==12){echo "| Козерог<br />";}
elseif ($ank['ank_d_r']<=20 && $ank['ank_m_r']==1){echo "| Козерог<br />";}

echo "</div>\n";
//--------------------------------------------------//


//--------------внешность---------------//
echo "<div class='nav2'>";
if ($ank['ank_rost']!=NULL)
echo "$rost<span class=\"ank_n\">Рост:</span>$a <span class=\"ank_d\">$ank[ank_rost]</span><br />\n";
else
echo "$rost<span class=\"ank_n\">Рост:</span>$a<br />\n";if ($ank['ank_ves']!=NULL)
echo "$ves<span class=\"ank_n\">Вес:</span>$a <span class=\"ank_d\">$ank[ank_ves]</span><br />\n";
else
echo "$ves<span class=\"ank_n\">Вес:</span>$a<br />\n";

if ($ank['ank_cvet_glas']!=NULL)
echo "$glaza<span class=\"ank_n\">Цвет глаз:</span>$a <span class=\"ank_d\">$ank[ank_cvet_glas]</span><br />\n";
else
echo "$glaza<span class=\"ank_n\">Цвет глаз:</span>$a<br />\n";if ($ank['ank_volos']!=NULL)
echo "$volos<span class=\"ank_n\">Волосы:</span>$a <span class=\"ank_d\">$ank[ank_volos]</span><br />\n";
else
echo "$volos<span class=\"ank_n\">Волосы:</span>$a<br />\n";

echo "$telo<span class=\"ank_n\">Телосложение:</span>$a";
if ($ank['ank_telosl']==1)
echo " <span class=\"ank_d\">Нет ответа</span><br />\n";
if ($ank['ank_telosl']==2)
echo " <span class=\"ank_d\">Худощавое</span><br />\n";
if ($ank['ank_telosl']==3)
echo " <span class=\"ank_d\">Обычное</span><br />\n";
if ($ank['ank_telosl']==4)
echo " <span class=\"ank_d\">Спортивное</span><br />\n";
if ($ank['ank_telosl']==5)
echo " <span class=\"ank_d\">Мускулистое</span><br />\n";
if ($ank['ank_telosl']==6)
echo " <span class=\"ank_d\">Плотное</span><br />\n";
if ($ank['ank_telosl']==7)
echo " <span class=\"ank_d\">Полное</span><br />\n";
if ($ank['ank_telosl']==0)
echo "<br />\n";
echo "</div>\n";
//-----------------------------------------------------//


//--------------Знакомства---------------//
echo "<div class='nav1'>";

echo "$orien<span class=\"ank_n\">Ориентация:</span>$a";
if ($ank['ank_orien']==0)
echo "<br />\n";
if ($ank['ank_orien']==1)
echo " <span class=\"ank_d\">Гетеро</span><br />\n";
if ($ank['ank_orien']==2)
echo " <span class=\"ank_d\">Би</span><br />\n";
if ($ank['ank_orien']==3)
echo " <span class=\"ank_d\">Гей/Лесби</span><br />\n";

echo "$loves<span class=\"ank_n\">Цели знакомства:</span>$a<br />";

if ($ank['ank_lov_1']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Дружба и общение<br />";
if ($ank['ank_lov_2']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Переписка<br />";
if ($ank['ank_lov_3']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Любовь, отношения<br />";
if ($ank['ank_lov_4']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Регулярный секс вдвоем<br />";
if ($ank['ank_lov_5']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Секс на один-два раза<br />";
if ($ank['ank_lov_6']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Групповой секс<br />";
if ($ank['ank_lov_7']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Виртуальный секс<br />";
if ($ank['ank_lov_8']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Предлагаю интим за деньги<br />";
if ($ank['ank_lov_9']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Ищу интим за деньги<br />";
if ($ank['ank_lov_10']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Брак, создание семьи<br />";
if ($ank['ank_lov_11']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Рождение, воспитание ребенка<br />";
if ($ank['ank_lov_12']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Брак для ви是的<br />";
if ($ank['ank_lov_13']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Совместная аренда жилья<br />";
if ($ank['ank_lov_14']==1)echo "<img src='/style/icons/str.gif' alt='*' />  Занятия спортом<br />";if ($ank['ank_o_par']!=NULL)
echo "$opar<span class=\"ank_n\">О партнере:</span>$a <span class=\"ank_d\">".output_text($ank['ank_o_par'])."</span><br />\n";
else
echo "$opar<span class=\"ank_n\">О партнере:</span>$a<br />\n";

if ($ank['ank_o_sebe']!=NULL)
echo "$osebe<span class=\"ank_n\">О себе:</span>$a <span class=\"ank_d\">".output_text($ank['ank_o_sebe'])."</span><br />\n";
else
echo "$osebe<span class=\"ank_n\">О себе:</span>$a<br />\n";
echo "</div>\n";
//-----------------------------------------------------//


//--------------о себе------------------//
echo "<div class='nav2'>";
if ($ank['ank_zan']!=NULL)
echo "$zan<span class=\"ank_n\">Чем занимаюсь:</span>$a <span class=\"ank_d\">".output_text($ank['ank_zan'])."</span><br />\n";
else
echo "$zan<span class=\"ank_n\">Чем занимаюсь:</span>$a<br />\n";

echo "$smok<span class=\"ank_n\">Курение:</span>$a";
if ($ank['ank_smok']==1)
echo " <span class=\"ank_d\">Не курю</span><br />\n";
if ($ank['ank_smok']==2)
echo " <span class=\"ank_d\">Курю</span><br />\n";
if ($ank['ank_smok']==3)
echo " <span class=\"ank_d\">Редко</span><br />\n";
if ($ank['ank_smok']==4)
echo " <span class=\"ank_d\">Бросаю</span><br />\n";
if ($ank['ank_smok']==5)
echo " <span class=\"ank_d\">Успешно бросил</span><br />\n";
if ($ank['ank_smok']==0)
echo "<br />\n";echo "$mat_pol<span class=\"ank_n\">Материальное положение:</span>$a";
if ($ank['ank_mat_pol']==1)
echo " <span class=\"ank_d\">Непостоянные заработки</span><br />\n";
if ($ank['ank_mat_pol']==2)
echo " <span class=\"ank_d\">Постоянный небольшой доход</span><br />\n";
if ($ank['ank_mat_pol']==3)
echo " <span class=\"ank_d\">Стабильный средний доход</span><br />\n";
if ($ank['ank_mat_pol']==4)
echo " <span class=\"ank_d\">Хорошо зарабатываю / обеспечен</span><br />\n";
if ($ank['ank_mat_pol']==5)
echo " <span class=\"ank_d\">Не зарабатываю</span><br />\n";
if ($ank['ank_mat_pol']==0)
echo "<br />\n";

echo "$avto<span class=\"ank_n\">Наличие автомобиля:</span>$a";
if ($ank['ank_avto_n']==1)
echo " <span class=\"ank_d\">Есть</span><br />\n";
if ($ank['ank_avto_n']==2)
echo " <span class=\"ank_d\">取消</span><br />\n";
if ($ank['ank_avto_n']==3)
echo " <span class=\"ank_d\">Хочу купить</span><br />\n";
if ($ank['ank_avto_n']==0)
echo "<br />\n";
if ($ank['ank_avto'] && $ank['ank_avto_n']!=2 && $ank['ank_avto_n']!=0)
   echo "<img src='/style/icons/str.gif' alt='*' />  <span class=\"ank_d\">".output_text($ank['ank_avto'])."</span><br />";
echo "$proj<span class=\"ank_n\">Проживание:</span>$a";
if ($ank['ank_proj']==1)
echo " <span class=\"ank_d\">Отдельная квартира (снимаю или своя)</span><br />\n";
if ($ank['ank_proj']==2)
echo " <span class=\"ank_d\">Комната в общежитии, коммуналка</span><br />\n";
if ($ank['ank_proj']==3)
echo " <span class=\"ank_d\">Живу с родителями</span><br />\n";
if ($ank['ank_proj']==4)
echo " <span class=\"ank_d\">Живу с приятелем / с подругой</span><br />\n";
if ($ank['ank_proj']==5)
echo " <span class=\"ank_d\">Живу с партнером или супругом (-ой)</span><br />\n";
if ($ank['ank_proj']==6)
echo " <span class=\"ank_d\">Нет постоянного жилья</span><br />\n";
if ($ank['ank_proj']==0)
echo "<br />\n";echo "$baby<span class=\"ank_n\">Есть ли дети:</span>$a";
if ($ank['ank_baby']==1)
echo " <span class=\"ank_d\">取消</span><br />\n";
if ($ank['ank_baby']==2)
echo " <span class=\"ank_d\">Нет, но хотелось бы</span><br />\n";
if ($ank['ank_baby']==3)
echo " <span class=\"ank_d\">Есть, живем вместе</span><br />\n";
if ($ank['ank_baby']==4)
echo " <span class=\"ank_d\">Есть, живем порознь</span><br />\n";
if ($ank['ank_baby']==0)
echo "<br />\n";
echo "</div>\n";
//-------------------------------------------//

if (isset($user) && $ank['id']==$user['id'])
{
$alko = "<a href='/user/info/edit.php?act=ank&amp;set=alko'>";
$nark = "<a href='/user/info/edit.php?act=ank&amp;set=nark'>";

}else{

$alko = null;
$nark = null;
}

//---------------------дополнительно--------------------//

echo "<div class='nav1'>";

echo "$alko<span class=\"ank_n\">Алкоголь:</span>$a";
if ($ank['ank_alko_n']==1)
echo " <span class=\"ank_d\">Да, выпиваю</span><br />\n";
if ($ank['ank_alko_n']==2)
echo " <span class=\"ank_d\">Редко, по праздникам</span><br />\n";
if ($ank['ank_alko_n']==3)
echo " <span class=\"ank_d\">Нет, категорически не приемлю</span><br />\n";
if ($ank['ank_alko_n']==0)
echo "<br />\n";
if ($ank['ank_alko'] && $ank['ank_alko_n']!=3 && $ank['ank_alko_n']!=0)echo "<img src='/style/icons/str.gif' alt='*' />  <span class=\"ank_d\">".output_text($ank['ank_alko'])."</span><br />";
echo "$nark<span class=\"ank_n\">Наркотики:</span>$a";
if ($ank['ank_nark']==1)
echo " <span class=\"ank_d\">Да, курю травку</span><br />\n";
if ($ank['ank_nark']==2)
echo " <span class=\"ank_d\">Да, люблю любой вид наркотических средств</span><br />\n";
if ($ank['ank_nark']==3)
echo " <span class=\"ank_d\">Бросаю, прохожу реабилитацию</span><br />\n";
if ($ank['ank_nark']==4)
echo " <span class=\"ank_d\">Нет, категорически не приемлю</span><br />\n";
if ($ank['ank_nark']==0)
echo "<br />\n";echo "</div>\n";
//----------------------------------------------------------//

//-------------контакты----------------//
echo "<div class='nav2'>";
if ($ank['ank_icq']!=NULL && $ank['ank_icq']!=0)
echo "$icq<span class=\"ank_n\">ICQ:</span>$a <span class=\"ank_d\">$ank[ank_icq]</span><br />\n";
else
echo "$icq<span class=\"ank_n\">ICQ:</span>$a<br />\n";

echo "$mail E-Mail:$a";
if ($ank['ank_mail']!=NULL && ($ank['set_show_mail']==1 || isset($user) && ($user['level']>$ank['level'] || $user['level']==4))){
if ($ank['set_show_mail']==0)$hide_mail=' (скрыт)';else $hide_mail=NULL;
if (preg_match("#(@mail\.ru$)|(@bk\.ru$)|(@inbox\.ru$)|(@list\.ru$)#", $ank['ank_mail']))
echo " <a href=\"mailto:$ank[ank_mail]\" title=\"Написать письмо\" class=\"ank_d\">$ank[ank_mail]</a>$hide_mail<br />\n";
else 
echo " <a href=\"mailto:$ank[ank_mail]\" title=\"Написать письмо\" class=\"ank_d\">$ank[ank_mail]</a>$hide_mail<br />\n";
}
else
{
echo "<br />";
}
if ($ank['ank_n_tel']!=NULL)
echo "$mobile<span class=\"ank_n\">Телефон:</span>$a <span class=\"ank_d\">$ank[ank_n_tel]</span><br />\n";
else
echo "$mobile<span class=\"ank_n\">Телефон:</span>$a<br />\n";
if ($ank['ank_skype']!=NULL)
echo "$skype<span class=\"ank_n\">Skype:</span>$a <span class=\"ank_d\">$ank[ank_skype]</span><br />\n";
else
echo "$skype<span class=\"ank_n\">Skype:</span>$a<br />\n";
echo "</div>\n";
//------------------------------------------//

echo "<div class='nav1'>";
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time'"), 0)!=0)
{
$q=dbquery("SELECT * FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time' ORDER BY `time` DESC LIMIT 5");
while ($post = dbassoc($q))
{
echo "<span class='ank_n'>Забанен до ".vremja($post['time']).":</span>\n";
echo "<span class='ank_d'>".output_text($post['prich'])."</span><br />\n";
}
}
else
{
$narush=dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]'"), 0);
echo "<span class='ank_n'>Нарушений:</span>".(($narush==0)?" <span class='ank_d'>取消</span><br />\n":" <span class=\"ank_d\">$narush</span><br />\n");
}echo "<span class=\"ank_n\">Регистрация:</span> <span class=\"ank_d\">".vremja($ank['date_reg'])."</span><br />\n";

echo "</div>\n";

if ($user['level']>$ank['level']){
if (isset($_GET['info'])){
echo "<div class='foot'>\n";
echo "<img src='/style/icons/str.gif' alt='*' /> <a href='?id=$ank[id]'>Скрыть</a><br />\n";
echo "</div>\n";

echo "<div class='p_t'>";
if ($ank['ip']!=NULL){
if (user_access('user_show_ip') && $ank['ip']!=0){
echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">".long2ip($ank['ip'])."</span>";
if (user_access('adm_ban_ip'))
echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip]'>Бан</a>]";
echo "<br />\n";
}
}
if ($ank['ip_cl']!=NULL){
if (user_access('user_show_ip') && $ank['ip_cl']!=0){
echo "<span class=\"ank_n\">IP (CLIENT):</span> <span class=\"ank_d\">".long2ip($ank['ip_cl'])."</span>";
if (user_access('adm_ban_ip'))
echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip_cl]'>Бан</a>]";
echo "<br />\n";
}
}

if ($ank['ip_xff']!=NULL){
if (user_access('user_show_ip') && $ank['ip_xff']!=0){
echo "<span class=\"ank_n\">IP (XFF):</span> <span class=\"ank_d\">".long2ip($ank['ip_xff'])."</span>";
if (user_access('adm_ban_ip'))
echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip_xff]'>Бан</a>]";
echo "<br />\n";
}
}

if (user_access('user_show_ua') && $ank['ua']!=NULL)
echo "<span class=\"ank_n\">UA:</span> <span class=\"ank_d\">$ank[ua]</span><br />\n";
if (user_access('user_show_ip') && opsos($ank['ip']))
echo "<span class=\"ank_n\">Пров:</span> <span class=\"ank_d\">".opsos($ank['ip'])."</span><br />\n";
if (user_access('user_show_ip') && opsos($ank['ip_cl']))
echo "<span class=\"ank_n\">Пров (CL):</span> <span class=\"ank_d\">".opsos($ank['ip_cl'])."</span><br />\n";
if (user_access('user_show_ip') && opsos($ank['ip_xff']))
echo "<span class=\"ank_n\">Пров (XFF):</span> <span class=\"ank_d\">".opsos($ank['ip_xff'])."</span><br />\n";

if ($ank['show_url']==1)
{
if (otkuda($ank['url']))echo "<span class=\"ank_n\">URL:</span> <span class=\"ank_d\"><a href='$ank[url]'>".otkuda($ank['url'])."</a></span><br />\n";
}
if (user_access('user_collisions') && $user['level']>$ank['level'])
{
$mass[0]=$ank['id'];
$collisions=user_collision($mass);if (count($collisions)>1)
{
echo "<span class=\"ank_n\">Возможные ники:</span><br />\n";
echo "<span class=\"ank_d\">\n";

for ($i=1;$i<count($collisions);$i++)
{
$ank_coll=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$collisions[$i]' LIMIT 1"));
echo "\"<a href='/info.php?id=$ank_coll[id]'>$ank_coll[nick]</a>\"<br />\n";
}

echo "</span>\n";
}
}
if (user_access('adm_ref') && ($ank['level']<$user['level'] || $user['id']==$ank['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `user_ref` WHERE `id_user` = '$ank[id]'"), 0))
{
$q=dbquery("SELECT * FROM `user_ref` WHERE `id_user` = '$ank[id]' ORDER BY `time` DESC LIMIT $set[p_str]");
echo "Посещаемые сайты:<br />\n";
while ($url=dbassoc($q)) {
$site=htmlentities($url['url'], ENT_QUOTES, 'UTF-8');
echo "<a".($set['web']?" target='_blank'":null)." href='/go.php?go=".base64_encode("http://$site")."'>$site</a> (".vremja($url['time']).")<br />\n";
}
}
if (user_access('user_delete'))
{

if (count(user_collision($mass,1))>1)
echo "Удаление (<a href='/adm_panel/delete_user.php?id=$ank[id]&amp;all'>Все ники</a>)";
echo "<br />\n";

}
echo "</div>\n";
}else{
echo "<div class='foot'>\n";
echo "<img src='/style/icons/str.gif' alt='*' /> <a href='?id=$ank[id]&amp;info'>Доп. инфо</a><br />\n";
echo "</div>\n";
}
}

echo "<div class='foot'>\n";

if (isset($user) && $user['id']==$ank['id'])echo "<img src='/style/icons/str.gif' alt='*' /> <a href=\"edit.php\">Изменить анкету</a><br />\n";
   if ($user['level']>$ank['level']){
   	if (user_access('user_prof_edit'))
echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/user.php?id=$ank[id]'>Редактировать профиль</a><br />\n";
if ($user['id']!=$ank['id']){
if (user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset'))
echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/ban.php?id=$ank[id]'>Нарушения (бан)</a><br />\n";

if (user_access('user_delete'))
{

echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/delete_user.php?id=$ank[id]'>Удалить пользователя</a>";
echo "<br />\n";

}
}
}

if (user_access('adm_log_read') && $ank['level']!=0 && ($ank['id']==$user['id'] || $ank['level']<$user['level']))
echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/adm_log.php?id=$ank[id]'>Отчет по администрированию</a><br />\n";

echo "</div>\n";
include_once '../../sys//inc/tfoot.php';
?>