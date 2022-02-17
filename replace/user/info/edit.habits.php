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
//---------------курение------------------//
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==1)
{
$user['ank_smok']=1;
dbquery("UPDATE `user` SET `ank_smok` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==0)
{
$user['ank_smok']=0;
dbquery("UPDATE `user` SET `ank_smok` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==2)
{
$user['ank_smok']=2;
dbquery("UPDATE `user` SET `ank_smok` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==3)
{
$user['ank_smok']=3;
dbquery("UPDATE `user` SET `ank_smok` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==4)
{
$user['ank_smok']=4;
dbquery("UPDATE `user` SET `ank_smok` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==5)
{
$user['ank_smok']=5;
dbquery("UPDATE `user` SET `ank_smok` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}


//---------------алкоголь------------------//
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==3)
{
$user['ank_alko_n']=3;
dbquery("UPDATE `user` SET `ank_alko_n` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==2)
{
$user['ank_alko_n']=2;
dbquery("UPDATE `user` SET `ank_alko_n` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==1)
{
$user['ank_alko_n']=1;
dbquery("UPDATE `user` SET `ank_alko_n` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==0)
{
$user['ank_alko_n']=0;
dbquery("UPDATE `user` SET `ank_alko_n` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (isset($_POST['ank_alko']) && strlen2($_POST['ank_alko'])<=215)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_alko']))$err[]='В поле "Нанпиток" используются запрещенные символы';
else {
$user['ank_alko']=$_POST['ank_alko'];
dbquery("UPDATE `user` SET `ank_alko` = '".my_esc($user['ank_alko'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='О любимом напитке нужно писать меньше :)';

//---------------наркотики------------------//
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==4)
{
$user['ank_nark']=4;
dbquery("UPDATE `user` SET `ank_nark` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==3)
{
$user['ank_nark']=3;
dbquery("UPDATE `user` SET `ank_nark` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==2)
{
$user['ank_nark']=2;
dbquery("UPDATE `user` SET `ank_nark` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==1)
{
$user['ank_nark']=1;
dbquery("UPDATE `user` SET `ank_nark` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==0)
{
$user['ank_nark']=0;
dbquery("UPDATE `user` SET `ank_nark` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (!isset($err)) {
$_SESSION['message'] = 'Изменения успешно приняты';

	mysql_query("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");
		
		if (isset($_GET['act']) && $_GET['act']=='ank')
			header("Location: /user/info/anketa.php?".SID);			
		else
			header("Location: /user/info/edit.habits.php?".SID);
			exit;
}
}
err();
echo "<div id='comments' class='menus'>";

echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.php'>Основное</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.meet.php'>Знакомства</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.contacts.php'>Контакты</a>";
echo "</div>";
echo "<div class='webmenu'>";
echo "<a href='/user/info/edit.habits.php' class='activ'>Привычки</a>";
echo "</div>";
echo "</div>";

echo "<form method='post' action=''>";	
echo "<div class='nav1'>";
echo "<b>Курение:</b><br /> 
	<label><input name='ank_smok' type='radio' ".($user['ank_smok']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_smok' type='radio' ".($user['ank_smok']==1?' checked="checked"':null)." value='1' />Не курю</label><br />
	<label><input name='ank_smok' type='radio' ".($user['ank_smok']==2?' checked="checked"':null)." value='2' />Курю</label><br />
	<label><input name='ank_smok' type='radio' ".($user['ank_smok']==3?' checked="checked"':null)." value='3' />Редко</label><br />
	<label><input name='ank_smok' type='radio' ".($user['ank_smok']==4?' checked="checked"':null)." value='4' />Бросаю</label><br />
	<label><input name='ank_smok' type='radio' ".($user['ank_smok']==5?' checked="checked"':null)." value='5' />Успешно бросил</label><br />";
	echo "</div>";
	
	echo "<div class='nav2'>";
	echo "<b>Алкоголь:</b><br /> 
	<label><input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==1?' checked="checked"':null)." value='1' />Да, выпиваю</label><br />
	<label><input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==2?' checked="checked"':null)." value='2' />Редко, по праздникам</label><br />
	<label><input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==3?' checked="checked"':null)." value='3' />Нет, категорически не приемлю</label><br />";
	echo "<br/><b>Напиток:</b><br /><input type='text' name='ank_alko' value='".htmlspecialchars($user['ank_alko'],false)."' maxlength='215' /></div>";

echo "<div class='nav1'>";
	echo "<b>Наркотики:</b><br /> 
	<label><input name='ank_nark' type='radio' ".($user['ank_nark']==0?' checked="checked"':null)." value='0' />Не указано</label><br />
	<label><input name='ank_nark' type='radio' ".($user['ank_nark']==1?' checked="checked"':null)." value='1' />Да, курю травку</label><br />
	<label><input name='ank_nark' type='radio' ".($user['ank_nark']==2?' checked="checked"':null)." value='2' />Да, люблю любой вид наркотических средств</label><br />
	<label><input name='ank_nark' type='radio' ".($user['ank_nark']==3?' checked="checked"':null)." value='3' />Бросаю, прохожу реабилитацию</label><br />
	<label><input name='ank_nark' type='radio' ".($user['ank_nark']==4?' checked="checked"':null)." value='4' />Нет, категорически не приемлю</label><br />";
echo "</div>";

echo "<div class='nav2'>";
echo "<input type='submit' name='save' value='Сохранить' /></div></form>\n";

echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> <a href='anketa.php'>Посмотреть анкету</a></div>";

include_once TFOOT;
?>