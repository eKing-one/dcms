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

only_reg();
$set['title']='Редактирование анкеты';
title();
aut();

if (isset($_POST['save'])){
/* дети */
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==1)
{
$user['ank_baby']=1;
dbquery("UPDATE `user` SET `ank_baby` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==0)
{
$user['ank_baby']=0;
dbquery("UPDATE `user` SET `ank_baby` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==2)
{
$user['ank_baby']=2;
dbquery("UPDATE `user` SET `ank_baby` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==3)
{
$user['ank_baby']=3;
dbquery("UPDATE `user` SET `ank_baby` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==4)
{
$user['ank_baby']=4;
dbquery("UPDATE `user` SET `ank_baby` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}

/* цель знакомства */

if (isset($_POST['ank_lov_1']) && $_POST['ank_lov_1']==1)
{
$user['ank_lov_1']=1;
dbquery("UPDATE `user` SET `ank_lov_1` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_1']=0;
dbquery("UPDATE `user` SET `ank_lov_1` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_2']) && $_POST['ank_lov_2']==1)
{
$user['ank_lov_2']=1;
dbquery("UPDATE `user` SET `ank_lov_2` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_2']=0;
dbquery("UPDATE `user` SET `ank_lov_2` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_3']) && $_POST['ank_lov_1']==1)
{
$user['ank_lov_3']=1;
dbquery("UPDATE `user` SET `ank_lov_3` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_3']=0;
dbquery("UPDATE `user` SET `ank_lov_3` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_4']) && $_POST['ank_lov_4']==1)
{
$user['ank_lov_4']=1;
dbquery("UPDATE `user` SET `ank_lov_4` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_4']=0;
dbquery("UPDATE `user` SET `ank_lov_4` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_5']) && $_POST['ank_lov_5']==1)
{
$user['ank_lov_5']=1;
dbquery("UPDATE `user` SET `ank_lov_5` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_5']=0;
dbquery("UPDATE `user` SET `ank_lov_5` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_6']) && $_POST['ank_lov_6']==1)
{
$user['ank_lov_6']=1;
dbquery("UPDATE `user` SET `ank_lov_6` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_6']=0;
dbquery("UPDATE `user` SET `ank_lov_6` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_7']) && $_POST['ank_lov_7']==1)
{
$user['ank_lov_7']=1;
dbquery("UPDATE `user` SET `ank_lov_7` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_7']=0;
dbquery("UPDATE `user` SET `ank_lov_7` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_8']) && $_POST['ank_lov_8']==1)
{
$user['ank_lov_8']=1;
dbquery("UPDATE `user` SET `ank_lov_8` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_8']=0;
dbquery("UPDATE `user` SET `ank_lov_8` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_9']) && $_POST['ank_lov_9']==1)
{
$user['ank_lov_9']=1;
dbquery("UPDATE `user` SET `ank_lov_9` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_9']=0;
dbquery("UPDATE `user` SET `ank_lov_9` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_10']) && $_POST['ank_lov_10']==1)
{
$user['ank_lov_10']=1;
dbquery("UPDATE `user` SET `ank_lov_10` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_10']=0;
dbquery("UPDATE `user` SET `ank_lov_10` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_11']) && $_POST['ank_lov_11']==1)
{
$user['ank_lov_11']=1;
dbquery("UPDATE `user` SET `ank_lov_11` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_11']=0;
dbquery("UPDATE `user` SET `ank_lov_11` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_12']) && $_POST['ank_lov_12']==1)
{
$user['ank_lov_12']=1;
dbquery("UPDATE `user` SET `ank_lov_12` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_12']=0;
dbquery("UPDATE `user` SET `ank_lov_12` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_13']) && $_POST['ank_lov_13']==1)
{
$user['ank_lov_13']=1;
dbquery("UPDATE `user` SET `ank_lov_13` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_13']=0;
dbquery("UPDATE `user` SET `ank_lov_13` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_14']) && $_POST['ank_lov_14']==1)
{
$user['ank_lov_14']=1;
dbquery("UPDATE `user` SET `ank_lov_14` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_14']=0;
dbquery("UPDATE `user` SET `ank_lov_14` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

//-----------------Ориентация-----------------//

if (isset($_POST['ank_orien']) && $_POST['ank_orien']==1)
{
$user['ank_orien']=1;
mysql_query("UPDATE `user` SET `ank_orien` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_orien']) && $_POST['ank_orien']==0)
{
$user['ank_orien']=0;
mysql_query("UPDATE `user` SET `ank_orien` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_orien']) && $_POST['ank_orien']==2)
{
$user['ank_orien']=2;
mysql_query("UPDATE `user` SET `ank_orien` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_orien']) && $_POST['ank_orien']==3)
{
$user['ank_orien']=3;
mysql_query("UPDATE `user` SET `ank_orien` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
//----------------о партнере-------------//
if (isset($_POST['ank_o_par']) && strlen2($_POST['ank_o_par'])<=215)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_o_par']))$err[]='В поле "О партнере" используются запрещенные символы';
else {
$user['ank_o_par']=$_POST['ank_o_par'];
mysql_query("UPDATE `user` SET `ank_o_par` = '".my_esc($user['ank_o_par'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='О партнере нужно писать меньше :)';

if (!isset($err)) {
$_SESSION['message'] = 'Изменения успешно приняты';

	mysql_query("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");
		
		if (isset($_GET['act']) && $_GET['act']=='ank')
			header("Location: /user/info/anketa.php?".SID);			
		else
			header("Location: /user/info/edit.meet.php?".SID);
			exit;
}
}
err();
echo "<div id='comments' class='menus'>";

echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.php'>Основное</a>";
echo "</div>"; 

echo "<div class='webmenu'>";
echo "<a href='/user/info/edit.meet.php' class='activ'>Знакомства</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.contacts.php'>Контакты</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.habits.php'>Привычки</a>";
echo "</div>";
echo "</div>";

echo "<form method='post' action=''>";	
echo "<div class='nav2'>";
echo "<b>Ориентация:</b><br /> 
	<label><input name='ank_orien' type='radio' ".($user['ank_orien']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_orien' type='radio' ".($user['ank_orien']==1?' checked="checked"':null)." value='1' />Гетеро</label><br />
	<label><input name='ank_orien' type='radio' ".($user['ank_orien']==2?' checked="checked"':null)." value='2' />Би</label><br />
	<label><input name='ank_orien' type='radio' ".($user['ank_orien']==3?' checked="checked"':null)." value='3' />Гей/Лесби</label><br />";
	echo "</div>";
	echo "<div class='nav1'>";
	echo "<b>Цели знакомства:</b><br />
		<label><input type='checkbox' name='ank_lov_1'".($user['ank_lov_1']==1?' checked="checked"':null)." value='1' /> Дружба и общение</label><br />
		<label><input type='checkbox' name='ank_lov_2'".($user['ank_lov_2']==1?' checked="checked"':null)." value='1' /> Переписка</label><br />
		<label><input type='checkbox' name='ank_lov_3'".($user['ank_lov_3']==1?' checked="checked"':null)." value='1' /> Любовь, отношения</label><br />
		<label><input type='checkbox' name='ank_lov_4'".($user['ank_lov_4']==1?' checked="checked"':null)." value='1' /> Регулярный секс вдвоем</label><br />
		<label><input type='checkbox' name='ank_lov_5'".($user['ank_lov_5']==1?' checked="checked"':null)." value='1' /> Секс на один-два раза</label><br />
		<label><input type='checkbox' name='ank_lov_6'".($user['ank_lov_6']==1?' checked="checked"':null)." value='1' /> Групповой секс</label><br />
		<label><input type='checkbox' name='ank_lov_7'".($user['ank_lov_7']==1?' checked="checked"':null)." value='1' /> Виртуальный секс</label><br />
		<label><input type='checkbox' name='ank_lov_8'".($user['ank_lov_8']==1?' checked="checked"':null)." value='1' /> Предлагаю интим за деньги</label><br />
		<label><input type='checkbox' name='ank_lov_9'".($user['ank_lov_9']==1?' checked="checked"':null)." value='1' /> Ищу интим за деньги</label><br />
		<label><input type='checkbox' name='ank_lov_10'".($user['ank_lov_10']==1?' checked="checked"':null)." value='1' /> Брак, создание семьи</label><br />
		<label><input type='checkbox' name='ank_lov_11'".($user['ank_lov_11']==1?' checked="checked"':null)." value='1' /> Рождение, воспитание ребенка</label><br />
		<label><input type='checkbox' name='ank_lov_12'".($user['ank_lov_12']==1?' checked="checked"':null)." value='1' /> Брак для вида</label><br />
		<label><input type='checkbox' name='ank_lov_13'".($user['ank_lov_13']==1?' checked="checked"':null)." value='1' /> Совместная аренда жилья</label><br />
		<label><input type='checkbox' name='ank_lov_14'".($user['ank_lov_14']==1?' checked="checked"':null)." value='1' /> Занятия спортом</label><br />
		
		</div>";
		echo "<div class='nav2'>";
		echo "<b>Есть ли дети:</b><br /> 
	<label><input name='ank_baby' type='radio' ".($user['ank_baby']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_baby' type='radio' ".($user['ank_baby']==1?' checked="checked"':null)." value='1' />Нет</label><br />
	<label><input name='ank_baby' type='radio' ".($user['ank_baby']==2?' checked="checked"':null)." value='2' />Нет, но хотелось бы</label><br />
	<label><input name='ank_baby' type='radio' ".($user['ank_baby']==3?' checked="checked"':null)." value='3' />Есть, живем вместе</label><br />
	<label><input name='ank_baby' type='radio' ".($user['ank_baby']==4?' checked="checked"':null)." value='4' />Есть, живем порознь</label><br />";
echo "</div>";

echo "<div class='nav1'>";
echo "<b>О партнере:</b><br /><input type='text' name='ank_o_par' value='$user[ank_o_par]' maxlength='215' /></div>";
echo "<div class='nav2'>";
echo "<input type='submit' name='save' value='Сохранить' /></div></form>\n";

echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> <a href='anketa.php'>Посмотреть анкету</a></div>";

include_once TFOOT;
?>