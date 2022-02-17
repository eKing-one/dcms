<?
include_once '../../sys/inc/home.php';
include_once START;
include_once COMPRESS;
include_once SESS;
include_once SETTINGS;
include_once DB_CONNECT;
include_once IPUA;
include_once FNC;
include_once SHIF;
include_once USER;
include_once THEAD;
if (isset($user))$ank['id']=$user['id'];
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);if ($ank['id']==0)
{
$ank=get_user($ank['id']);
$set['title']=$ank['nick'].' - анкета '; // заголовок страницы

title();
aut();/*
==================================
Приватность станички пользователя
Запрещаем просмотр анкеты
==================================
*/

	$uSet = mysql_fetch_array(mysql_query("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
	$frend=mysql_result(mysql_query("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"),0);
	$frend_new=mysql_result(mysql_query("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"),0);

if ($ank['id'] != $user['id'] && $user['group_access'] == 0)
{

	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // Начинаем вывод если стр имеет приват настройки
	{
		if ($ank['group_access']>1) echo "<div class='err'>$ank[group_name]</div>";
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
	include_once TFOOT;
	exit;
	}
	
	if ($uSet['privat_str'] == 0) // Если закрыта
	{
		echo '<div class="mess">';
		echo 'Пользователь запретил просматривать его страничку!';
		echo '</div>';
		
	include_once TFOOT;
	exit;
	}

}
	

echo "<div class=\"err\">$ank[group_name]</div>\n";

if ($ank['ank_o_sebe']!=NULL)echo "<span class='ank_n'>О себе:</span> <span class=\"ank_d\">$ank[ank_o_sebe]</span><br />\n";

if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))
echo "<div class='foot'>&laquo;<a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />\n</div>\n";

include_once TFOOT;
exit;
}

$ank=get_user($ank['id']);
if(!$ank){header("Location: /index.php?".SID);exit;}
$timediff=mysql_result(mysql_query("SELECT `time` FROM `user` WHERE `id` = '$ank[id]' LIMIT 1",$db), 0);

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

$set['title']=$ank['nick'].' - анкета '; // заголовок страницы
include_once THEAD;
title();
aut();
if ((!isset($_SESSION['refer']) || $_SESSION['refer']==NULL)
&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL &&
!preg_match('#info\.php#',$_SERVER['HTTP_REFERER']))
$_SESSION['refer']=str_replace('&','&amp;',preg_replace('#^https://[^/]*/#','/', $_SERVER['HTTP_REFERER']));



   if ($user['level']>$ank['level']){
   echo "<div class='nav1'>";
   	if (user_access('user_prof_edit'))
echo "[<a href='/adm_panel/user.php?id=$ank[id]'>Ред.</a>] ";
if ($user['id']!=$ank['id']){
if (user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset'))
echo "[<a href='/adm_panel/ban.php?id=$ank[id]'>Бан</a>] ";

if (user_access('user_delete'))
{
echo "[<a href='/adm_panel/delete_user.php?id=$ank[id]'>Удалить</a>]";
echo "<br />\n";
}

}
echo "</div>";
}

if ($ank['group_access']>1) echo "<div class='err'>$ank[group_name]</div>";
echo "<div class='nav1'>";
echo avatar($ank['id'], true, 128, 128);
echo "<br />\n";

if ($ank['rating']>=0 && $ank['rating']<= 100){

echo "<div class='rating1'>
<div class='rating2' style='width:$ank[rating]%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=100 && $ank['rating']<= 200){
$rat=$ank['rating']-100;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=200 && $ank['rating']<= 300){
$rat=$ank['rating']-200;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=300 && $ank['rating']<= 400){
$rat=$ank['rating']-300;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=400 && $ank['rating']<= 500){
$rat=$ank['rating']-400;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=500 && $ank['rating']<= 600){
$rat=$ank['rating']-500;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=600 && $ank['rating']<= 700){
$rat=$ank['rating']-600;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=700 && $ank['rating']<= 800){
$rat=$ank['rating']-700;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=800 && $ank['rating']<= 900){
$rat=$ank['rating']-800;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";

}elseif ($ank['rating']>=900 && $ank['rating']<= 1000){
$rat=$ank['rating']-900;

echo "<div class='rating1'>
<div class='rating2' style='width:$rat%;'></div>
<span class='rating3'>$ank[rating]%</span>
</div>";
}
echo "</div>";


//-----------------инфо----------------//
echo "<div class='nav2'>";
echo "<span class='ank_n'>ID: $ank[id]</span><br /> \n";
echo "<span class='ank_n'>Баллы:</span> <font color='green'>$ank[balls]</font><br /> \n";
echo '<span class="ank_n">' . $sMonet[2] . ':</span> <span class="ank_d">' . $ank['money'] . '<br />';
echo "</div><div class='nav1'>";
echo "<span class='ank_n'>Посл. посещение:</span> <span class='ank_d'>".vremja($ank['date_last'])."</span><br />\n";
echo "<span class='ank_n'>Время онлайн:</span> <span class='ank_d'>$displaystring</span><br />  \n";

if (mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time'"), 0)!=0)
{
$q=mysql_query("SELECT * FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time' ORDER BY `time` DESC LIMIT 5");
while ($post = mysql_fetch_assoc($q))
{
echo "<span class='ank_n'>Забанен до <span class='ank_d'>".vremja($post['time']).":</span>\n";
echo "".output_text($post['prich'])."<br />\n";
}
}
else
{
$narush=mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]'"), 0);
echo "<span class='ank_n'>Нарушений:</span><span class='ank_d'>".(($narush==0)?" Не обнаружено<br />\n":" $narush<br />\n")."</span>";
}
echo "<span class='ank_n'>Регистрация:</span> <span class='ank_d'>".vremja($ank['date_reg'])."</span><br />\n";
echo "</div><div class='main'><span class='ank_n'>Основное</span></div><div class='nav2'>";

if ($ank['ank_name']!=NULL)
echo "<span class='ank_n'>Имя:</span> <span class='ank_d'>$ank[ank_name]</span><br />\n";
else
echo "<span class='ank_n'>Имя:</span> <span class='ank_d'>Не заполнено</span><br />\n";

echo "<span class='ank_n'>Пол:</span> <span class='ank_d'>".(($ank['pol']==1)?'<img src="/style/icons/pol_1.png"> Парень':'<img src="/style/icons/pol_0.png"> Девушка')."</span><br />\n";

if ($ank['ank_city']!=NULL)
echo "<span class='ank_n'>Город:</span> <span class='ank_d'>".output_text($ank['ank_city'])."</span><br />\n";


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
echo "<span class='ank_n'>Дата рождения:</span> <span class='ank_d'>$ank[ank_d_r] $ank[mes] $ank[ank_g_r]г. </span><br />\n";
$ank['ank_age']=date("Y")-$ank['ank_g_r'];
if (date("n")<$ank['ank_m_r'])$ank['ank_age']=$ank['ank_age']-1;
elseif (date("n")==$ank['ank_m_r']&& date("j")<$ank['ank_d_r'])$ank['ank_age']=$ank['ank_age']-1;
echo "<span class='ank_n'>Возраст:</span> <span class='ank_d'>".ages($ank['ank_age'])."</span><br/><span class='ank_n'>Знак зодиака:</span><span class='ank_d'>";
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
echo "<span class='ank_n'>День рождения:</span> <span class='ank_d'>$ank[ank_d_r] $ank[mes] </span>";
}

if ($ank['ank_d_r']>=19 && $ank['ank_m_r']==1){echo " <img src='/style/icons/zod/1.jpg'> Водолей <br />";}
elseif ($ank['ank_d_r']<=19 && $ank['ank_m_r']==2){echo " <img src='/style/icons/zod/1.jpg'> Водолей<br />";}
elseif ($ank['ank_d_r']>=18 && $ank['ank_m_r']==2){echo " <img src='/style/icons/zod/2.jpg'> Рыбы<br />";}
elseif ($ank['ank_d_r']<=21 && $ank['ank_m_r']==3){echo " <img src='/style/icons/zod/2.jpg'> Рыбы<br />";}
elseif ($ank['ank_d_r']>=20 && $ank['ank_m_r']==3){echo " <img src='/style/icons/zod/3.jpg'> Овен<br />";}
elseif ($ank['ank_d_r']<=21 && $ank['ank_m_r']==4){echo " <img src='/style/icons/zod/3.jpg'> Овен<br />";}
elseif ($ank['ank_d_r']>=20 && $ank['ank_m_r']==4){echo " <img src='/style/icons/zod/4.jpg'> Телец<br />";}
elseif ($ank['ank_d_r']<=21 && $ank['ank_m_r']==5){echo " <img src='/style/icons/zod/4.jpg'> Телец<br />";}
elseif ($ank['ank_d_r']>=20 && $ank['ank_m_r']==5){echo " <img src='/style/icons/zod/5.jpg'> Близнецы<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==6){echo " <img src='/style/icons/zod/5.jpg'> Близнецы<br />";}
elseif ($ank['ank_d_r']>=21 && $ank['ank_m_r']==6){echo " <img src='/style/icons/zod/6.jpg'> Рак<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==7){echo " <img src='/style/icons/zod/6.jpg'> Рак<br />";}
elseif ($ank['ank_d_r']>=23 && $ank['ank_m_r']==7){echo " <img src='/style/icons/zod/7.jpg'> Лев<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==8){echo " <img src='/style/icons/zod/7.jpg'> Лев<br />";}
elseif ($ank['ank_d_r']>=22 && $ank['ank_m_r']==8){echo " <img src='/style/icons/zod/8.jpg'> Дева<br />";}
elseif ($ank['ank_d_r']<=23 && $ank['ank_m_r']==9){echo " <img src='/style/icons/zod/8.jpg'> Дева<br />";}
elseif ($ank['ank_d_r']>=22 && $ank['ank_m_r']==9){echo " <img src='/style/icons/zod/9.jpg'> Весы<br />";}
elseif ($ank['ank_d_r']<=23 && $ank['ank_m_r']==10){echo " <img src='/style/icons/zod/9.jpg'> Весы<br />";}
elseif ($ank['ank_d_r']>=22 && $ank['ank_m_r']==10){echo " <img src='/style/icons/zod/10.jpg'> Скорпион<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==11){echo " <img src='/style/icons/zod/10.jpg'> Скорпион<br />";}
elseif ($ank['ank_d_r']>=21 && $ank['ank_m_r']==11){echo " <img src='/style/icons/zod/11.jpg'> Стрелец<br />";}
elseif ($ank['ank_d_r']<=22 && $ank['ank_m_r']==12){echo " <img src='/style/icons/zod/11.jpg'>Стрелец<br />";}
elseif ($ank['ank_d_r']>=21 && $ank['ank_m_r']==12){echo " <img src='/style/icons/zod/12.jpg'> Козерог<br />";}
elseif ($ank['ank_d_r']<=20 && $ank['ank_m_r']==1){echo " <img src='/style/icons/zod/12.jpg'> Козерог<br />";}

echo "</span></div>";

if ($ank['ank_rost']!=NULL || $ank['ank_ves']!=NULL || $ank['ank_cvet_glas']!=NULL || $ank['ank_volos']!=NULL || $ank['ank_telosl']>0) {
echo "<div class='main'><span class='ank_n'>Типаж</span></div><div class='nav2'>";
}
if ($ank['ank_rost']!=NULL)
echo "<span class='ank_n'>Рост:</span><span class='ank_d'> $ank[ank_rost] см</span><br />\n";

if ($ank['ank_ves']!=NULL)
echo "<span class='ank_n'>Вес:</span> <span class='ank_d'>$ank[ank_ves] кг</span><br />\n";

if ($ank['ank_cvet_glas']!=NULL)
echo "<span class='ank_n'>Цвет глаз:</span> <span class='ank_d'>$ank[ank_cvet_glas]</span><br />\n";

if ($ank['ank_volos']!=NULL)
echo "<span class='ank_n'>Волосы:</span> <span class='ank_d'>$ank[ank_volos]</span><br />\n";

if ($ank['ank_telosl']==0) {
} else {
echo "<span class='ank_n'>Телосложение:</span><span class='ank_d'>";
if ($ank['ank_telosl']==1)
echo " Нет ответа<br />\n";
if ($ank['ank_telosl']==2)
echo " Худощавое<br />\n";
if ($ank['ank_telosl']==3)
echo " Обычное<br />\n";
if ($ank['ank_telosl']==4)
echo " Спортивное<br />\n";
if ($ank['ank_telosl']==5)
echo " Мускулистое<br />\n";
if ($ank['ank_telosl']==6)
echo " Плотное<br />\n";
if ($ank['ank_telosl']==7)
echo " Полное<br />\n";
echo "</span>";
}
if ($ank['ank_rost']!=NULL || $ank['ank_ves']!=NULL || $ank['ank_cvet_glas']!=NULL || $ank['ank_volos']!=NULL || $ank['ank_telosl']>0) {
echo "</div>";
}

if ($ank['ank_o_sebe']!=NULL || $ank['ank_zan']!=NULL || $ank['ank_mat_pol']>0 || $ank['ank_avto_n']>0 || $ank['ank_proj']>0) {
echo "<div class='main'><span class='ank_n'>Общее положение</span></div><div class='nav2'>";
}
if ($ank['ank_o_sebe']!=NULL)
echo "<span class='ank_n'>О себе:</span> <span class='ank_d'>".output_text($ank['ank_o_sebe'])."</span><br />\n";


if ($ank['ank_zan']!=NULL)
echo "<span class='ank_n'>Чем занимаюсь:</span> <span class='ank_d'>".output_text($ank['ank_zan'])."</span><br />\n";

if ($ank['ank_mat_pol']==0) {
} else {
echo "<span class='ank_n'>Материальное положение:</span><span class='ank_d'>";
if ($ank['ank_mat_pol']==1)
echo " Непостоянные заработки<br />\n";
if ($ank['ank_mat_pol']==2)
echo " Постоянный небольшой доход<br />\n";
if ($ank['ank_mat_pol']==3)
echo " Стабильный средний доход<br />\n";
if ($ank['ank_mat_pol']==4)
echo " Хорошо зарабатываю / обеспечен<br />\n";
if ($ank['ank_mat_pol']==5)
echo " Не зарабатываю<br />\n";
echo "</span>";
}
if ($ank['ank_avto_n']==0) {
} else {
echo "<span class='ank_n'>Наличие автомобиля:</span><span class='ank_d'>";
if ($ank['ank_avto_n']==1)
echo " Есть<br />\n";
if ($ank['ank_avto_n']==2)
echo " Нет<br />\n";
if ($ank['ank_avto_n']==3)
echo " Хочу купить<br />\n";
echo "</span>";

if ($ank['ank_avto'] && $ank['ank_avto_n']!=2 && $ank['ank_avto_n']!=0)
   echo "<span class='ank_n'>Название\Марка авто:</span> <span class='ank_d'>".output_text($ank['ank_avto'])."</span><br />";
   }
   
 if ($ank['ank_proj']==0) {
} else {  
echo "<span class='ank_n'>Проживание:</span><span class='ank_d'>";
if ($ank['ank_proj']==1)
echo " Отдельная квартира (снимаю или своя)<br />\n";
if ($ank['ank_proj']==2)
echo " Комната в общежитии, коммуналка<br />\n";
if ($ank['ank_proj']==3)
echo " Живу с родителями<br />\n";
if ($ank['ank_proj']==4)
echo " Живу с приятелем / с подругой<br />\n";
if ($ank['ank_proj']==5)
echo " Живу с партнером или супругом (-ой)<br />\n";
if ($ank['ank_proj']==6)
echo " Нет постоянного жилья<br />\n";
echo "</span>";
}
if ($ank['ank_o_sebe']!=NULL || $ank['ank_zan']!=NULL || $ank['ank_mat_pol']>0 || $ank['ank_avto_n']>0 || $ank['ank_proj']>0) {
echo "</div>";
}

if ($ank['ank_smok']>0 || $ank['ank_alko_n']>0 || $ank['ank_nark']>0) {
echo "<div class='main'><span class='ank_n'>Привычки</span></div><div class='nav2'>";
}
if ($ank['ank_smok']==1)
echo "<span class='ank_n'>Курение:</span> <span class='ank_d'>Не курю</span><br />\n";
if ($ank['ank_smok']==2)
echo "<span class='ank_n'>Курение:</span> <span class='ank_d'>Курю</span><br />\n";
if ($ank['ank_smok']==3)
echo "<span class='ank_n'>Курение:</span> <span class='ank_d'>Редко</span><br />\n";
if ($ank['ank_smok']==4)
echo "<span class='ank_n'>Курение:</span> <span class='ank_d'>Бросаю</span><br />\n";
if ($ank['ank_smok']==5)
echo "<span class='ank_n'>Курение:</span> <span class='ank_d'>Успешно бросил</span><br />\n";

if ($ank['ank_alko_n']==1)
echo "<span class='ank_n'>Алкоголь:</span> <span class='ank_d'>Да, выпиваю</span><br />\n";
if ($ank['ank_alko_n']==2)
echo "<span class='ank_n'>Алкоголь:</span> <span class='ank_d'>Редко, по праздникам</span><br />\n";
if ($ank['ank_alko_n']==3)
echo "<span class='ank_n'>Алкоголь:</span> <span class='ank_d'>Нет, категорически не приемлю</span><br />\n";
if ($ank['ank_alko'] && $ank['ank_alko_n']!=3 && $ank['ank_alko_n']!=0)echo "<span class='ank_n'>Напиток:</span> <span class='ank_d'>".output_text($ank['ank_alko'])."</span><br />";

if ($ank['ank_nark']==1)
echo "<span class='ank_n'>Наркотики:</span> <span class='ank_d'>Да, курю травку</span><br />\n";
if ($ank['ank_nark']==2)
echo "<span class='ank_n'>Наркотики:</span> <span class='ank_d'>Да, люблю любой вид наркотических средств</soan><br />\n";
if ($ank['ank_nark']==3)
echo "<span class='ank_n'>Наркотики:</span> <span class='ank_d'>Бросаю, прохожу реабилитацию</span><br />\n";
if ($ank['ank_nark']==4)
echo "<span class='ank_n'>Наркотики:</span> <span class='ank_d'>Нет, категорически не приемлю</span><br />\n";

if ($ank['ank_smok']>0 || $ank['ank_alko_n']>0 || $ank['ank_nark']>0) {
echo "</div>";
}
if ($ank['ank_lov_1']>0 || $ank['ank_lov_2']>0 || $ank['ank_lov_3']>0 || $ank['ank_lov_4']>0 || $ank['ank_lov_5']>0 || $ank['ank_lov_6']>0 || $ank['ank_lov_7']>0 || $ank['ank_lov_8']>0 || $ank['ank_lov_9']==1 || $ank['ank_lov_10']>0 || $ank['ank_lov_11']>0 || $ank['ank_lov_12']>0 || $ank['ank_lov_13']>0 || $ank['ank_lov_14']>0 || $ank['ank_orien']>0 || $ank['ank_baby']>0 || $ank['ank_o_par']!=NULL) {
echo "<div class='main'><span class='ank_n'>Знакомства</span></div><div class='nav2'>";
}
if ($ank['ank_orien']!=0)
echo "<span class='ank_n'>Ориентация:</span><span class='ank_d'>";
if ($ank['ank_orien']==1)
echo " Гетеро<br />\n";
if ($ank['ank_orien']==2)
echo " Би<br />\n";
if ($ank['ank_orien']==3)
echo " Гей/Лесби<br />\n";
echo "</span>";

if ($ank['ank_lov_1']==0 && $ank['ank_lov_2']==0 && $ank['ank_lov_3']==0 && $ank['ank_lov_4']==0 && $ank['ank_lov_5']==0 && $ank['ank_lov_6']==0 && $ank['ank_lov_7']==0 && $ank['ank_lov_8']==0 && $ank['ank_lov_9']==0 && $ank['ank_lov_10']==0 && $ank['ank_lov_11']==0 && $ank['ank_lov_12']==0 && $ank['ank_lov_13']==0 && $ank['ank_lov_14']==0) {
} else {
echo "<span class='ank_n'>Цели знакомства:</span><span class='ank_d'>";

if ($ank['ank_lov_1']==1)echo " Дружба и общение<br />";
if ($ank['ank_lov_2']==1)echo " Переписка<br />";
if ($ank['ank_lov_3']==1)echo "Любовь, отношения<br />";
if ($ank['ank_lov_4']==1)echo " Регулярный секс вдвоем<br />";
if ($ank['ank_lov_5']==1)echo " Секс на один-два раза<br />";
if ($ank['ank_lov_6']==1)echo " Групповой секс<br />";
if ($ank['ank_lov_7']==1)echo " Виртуальный секс<br />";
if ($ank['ank_lov_8']==1)echo " Предлагаю интим за деньги<br />";
if ($ank['ank_lov_9']==1)echo " Ищу интим за деньги<br />";
if ($ank['ank_lov_10']==1)echo " Брак, создание семьи<br />";
if ($ank['ank_lov_11']==1)echo " Рождение, воспитание ребенка<br />";
if ($ank['ank_lov_12']==1)echo " Брак для вида<br />";
if ($ank['ank_lov_13']==1)echo " Совместная аренда жилья<br />";
if ($ank['ank_lov_14']==1)echo " Занятия спортом<br />";
echo "</span>";
}

if ($ank['ank_baby']==0) {
} else {
echo "<span class='ank_n'>Есть ли дети:</span><span class='ank_d'>";
if ($ank['ank_baby']==1)
echo " Нет<br />\n";
if ($ank['ank_baby']==2)
echo " Нет, но хотелось бы<br />\n";
if ($ank['ank_baby']==3)
echo " Есть, живем вместе<br />\n";
if ($ank['ank_baby']==4)
echo " Есть, живем порознь<br />\n";
echo "</span>";
}

if ($ank['ank_o_par']!=NULL)
echo "<span class='ank_n'>О партнере:</span> <span class='ank_d'>".output_text($ank['ank_o_par'])."</span><br />\n";

if ($ank['ank_lov_1']>0 || $ank['ank_lov_2']>0 || $ank['ank_lov_3']>0 || $ank['ank_lov_4']>0 || $ank['ank_lov_5']>0 || $ank['ank_lov_6']>0 || $ank['ank_lov_7']>0 || $ank['ank_lov_8']>0 || $ank['ank_lov_9']==1 || $ank['ank_lov_10']>0 || $ank['ank_lov_11']>0 || $ank['ank_lov_12']>0 || $ank['ank_lov_13']>0 || $ank['ank_lov_14']>0 || $ank['ank_orien']>0 || $ank['ank_baby']>0 || $ank['ank_o_par']!=NULL) {
echo "</div>";
}

if ($ank['ank_mail']!=NULL || $ank['ank_n_tel']!=NULL || $ank['ank_skype']!=NULL || $ank['ank_icq']!=NULL){
echo "<div class='main'><span class='ank_n'>Контакты</span></div><div class='nav2'>";
}
if ($ank['ank_mail']!=NULL && ($ank['set_show_mail']==1 || isset($user) && ($user['level']>$ank['level'] || $user['level']==4))){
echo "<span class='ank_n'>E-Mail:</span>";
if ($ank['set_show_mail']==0)$hide_mail=' (скрыт)';else $hide_mail=NULL;
if (preg_match("#(@mail\.ru$)|(@bk\.ru$)|(@inbox\.ru$)|(@list\.ru$)#", $ank['ank_mail']))
echo " <span class='ank_d'><a href=\"mailto:$ank[ank_mail]\" title=\"Написать письмо\">$ank[ank_mail]</a>$hide_mail</span><br />\n";
else 
echo " <span class='ank_d'><a href=\"mailto:$ank[ank_mail]\" title=\"Написать письмо\">$ank[ank_mail]</a>$hide_mail</span><br />\n";
}

if ($ank['ank_n_tel']!=NULL)
echo "<span class='ank_n'>Телефон:</span> <span class='ank_d'>$ank[ank_n_tel]</span><br />\n";

if ($ank['ank_skype']!=NULL)
echo "<span class='ank_n'>Skype:</span> <span class='ank_d'>$ank[ank_skype]</span><br />";

if ($ank['ank_icq']!=NULL)
echo "<span class='ank_n'>ICQ:</span> <span class='ank_d'>$ank[ank_icq]</span><br />";

if ($ank['ank_mail']!=NULL || $ank['ank_n_tel']!=NULL || $ank['ank_skype']!=NULL || $ank['ank_icq']!=NULL){
echo "</div>";
}
//------------------------------------------//

if ($user['level']>$ank['level']){
if (isset($_GET['info'])){
echo "<div class='foot'>\n";
echo "<a href='?id=$ank[id]'>Скрыть</a><br />\n";
echo "</div>\n";

echo "<div class='p_t'>";
if ($ank['ip']!=NULL){
if (user_access('user_show_ip') && $ank['ip']!=0){
echo "<span class='ank_n'>IP:</span> <span class=\"ank_d\">".long2ip($ank['ip'])."</span>";
if (user_access('adm_ban_ip'))
echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip]'>Бан</a>]";
echo "<br />\n";
}
}
if ($ank['ip_cl']!=NULL){
if (user_access('user_show_ip') && $ank['ip_cl']!=0){
echo "<span class='ank_n'>IP (CLIENT):</span> <span class=\"ank_d\">".long2ip($ank['ip_cl'])."</span>";
if (user_access('adm_ban_ip'))
echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip_cl]'>Бан</a>]";
echo "<br />\n";
}
}

if ($ank['ip_xff']!=NULL){
if (user_access('user_show_ip') && $ank['ip_xff']!=0){
echo "<span class='ank_n'>IP (XFF):</span> <span class=\"ank_d\">".long2ip($ank['ip_xff'])."</span>";
if (user_access('adm_ban_ip'))
echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip_xff]'>Бан</a>]";
echo "<br />\n";
}
}

if (user_access('user_show_ua') && $ank['ua']!=NULL)
echo "<span class='ank_n'>UA:</span> <span class=\"ank_d\">$ank[ua]</span><br />\n";
if (user_access('user_show_ip') && opsos($ank['ip']))
echo "<span class='ank_n'>Пров:</span> <span class=\"ank_d\">".opsos($ank['ip'])."</span><br />\n";
if (user_access('user_show_ip') && opsos($ank['ip_cl']))
echo "<span class='ank_n'>Пров (CL):</span> <span class=\"ank_d\">".opsos($ank['ip_cl'])."</span><br />\n";
if (user_access('user_show_ip') && opsos($ank['ip_xff']))
echo "<span class='ank_n'>Пров (XFF):</span> <span class=\"ank_d\">".opsos($ank['ip_xff'])."</span><br />\n";

if ($ank['show_url']==1)
{
if (otkuda($ank['url']))echo "<span class='ank_n'>URL:</span> <span class=\"ank_d\"><a href='$ank[url]'>".otkuda($ank['url'])."</a></span><br />\n";
}
if (user_access('user_collisions') && $user['level']>$ank['level'])
{
$mass[0]=$ank['id'];
$collisions=user_collision($mass);if (count($collisions)>1)
{
echo "<span class='ank_n'>Возможные ники:</span> Не заполнено<br />\n";

for ($i=1;$i<count($collisions);$i++)
{
$ank_coll=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = '$collisions[$i]' LIMIT 1"));
echo "\"<a href='/info.php?id=$ank_coll[id]'>$ank_coll[nick]</a>\"<br />\n";
}
}
}
if (user_access('adm_ref') && ($ank['level']<$user['level'] || $user['id']==$ank['id']) && mysql_result(mysql_query("SELECT COUNT(*) FROM `user_ref` WHERE `id_user` = '$ank[id]'"), 0))
{
$q=mysql_query("SELECT * FROM `user_ref` WHERE `id_user` = '$ank[id]' ORDER BY `time` DESC LIMIT $set[p_str]");
echo "Посещаемые сайты:<br />\n";
while ($url=mysql_fetch_assoc($q)) {
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
echo "<div class='foot'><img src='/style/icons/str.gif' alt='*' /> <a href='?id=$ank[id]&amp;info'>Доп. инфо</a></div>\n";
}
}
if (isset($user) && $user['id']==$ank['id'])echo "<div class='foot'><img src='/style/icons/str.gif' alt='*' /> <a href=\"edit.php\">Изменить анкету</a></div>\n";

if (user_access('adm_log_read') && $ank['level']!=0 && ($ank['id']==$user['id'] || $ank['level']<$user['level']))
echo "<div class='foot'><img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/adm_log.php?id=$ank[id]'>Отчет по администрированию</a></div>\n";
include_once TFOOT;
?>