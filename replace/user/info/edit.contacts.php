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
//----------------email------------------//
if (isset($_POST['set_show_mail']) && $_POST['set_show_mail']==1)
{
$user['set_show_mail']=1;
mysql_query("UPDATE `user` SET `set_show_mail` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['set_show_mail']=0;
mysql_query("UPDATE `user` SET `set_show_mail` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (isset($_POST['ank_mail']) && ($_POST['ank_mail']==null || preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui',$_POST['ank_mail'])))
{
$user['ank_mail']=$_POST['ank_mail'];
mysql_query("UPDATE `user` SET `ank_mail` = '$user[ank_mail]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный E-mail';

//--------------icq----------------//
if (isset($_POST['ank_icq']) && (is_numeric($_POST['ank_icq']) && strlen($_POST['ank_icq'])>=5 && strlen($_POST['ank_icq'])<=9 || $_POST['ank_icq']==NULL))
{
$user['ank_icq']=$_POST['ank_icq'];
if ($user['ank_icq']==null)$user['ank_icq']='null';
dbquery("UPDATE `user` SET `ank_icq` = $user[ank_icq] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_icq']=='null')$user['ank_icq']=NULL;
}
else $err[]='Неверный формат ICQ';

if (isset($_POST['ank_skype']) && preg_match('#^([A-z0-9 \-]*)$#ui', $_POST['ank_skype']))
{
$user['ank_skype']=$_POST['ank_skype'];
dbquery("UPDATE `user` SET `ank_skype` = '".my_esc($user['ank_skype'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный логин Skype';

//-----------------------телефон------------------//
if (isset($_POST['ank_n_tel']) && (is_numeric($_POST['ank_n_tel']) && strlen($_POST['ank_n_tel'])>=5 && strlen($_POST['ank_n_tel'])<=11 || $_POST['ank_n_tel']==NULL))
{
$user['ank_n_tel']=$_POST['ank_n_tel'];
mysql_query("UPDATE `user` SET `ank_n_tel` = '$user[ank_n_tel]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат номера телефона';


if (!isset($err)) {
$_SESSION['message'] = 'Изменения успешно приняты';

	mysql_query("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");
		
		if (isset($_GET['act']) && $_GET['act']=='ank')
			header("Location: /user/info/anketa.php?".SID);			
		else
			header("Location: /user/info/edit.contacts.php?".SID);
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

echo "<div class='webmenu'>";
echo "<a href='/user/info/edit.contacts.php' class='activ'>Контакты</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/info/edit.habits.php'>Привычки</a>";
echo "</div>";
echo "</div>";

echo "<form method='post' action=''>";	
echo "<div class='nav2'>";
echo "<b>Номер телефона:</b><br /><input type='text' name='ank_n_tel' value='$user[ank_n_tel]' maxlength='11' /></div>";

echo "<div class='nav1'>";
echo "<b>E-mail:</b><br />
		<input type='text' name='ank_mail' value='$user[ank_mail]' maxlength='32' /><br />
		<label><input type='checkbox' name='set_show_mail'".($user['set_show_mail']==1?' checked="checked"':null)." value='1' /> Показывать E-mail в анкете</label></div>";

echo "<div class='nav2'>";		
		echo "<b>Skype логин:</b><br /><input type='text' name='ank_skype' value='$user[ank_skype]' maxlength='16' /></div>";
		
		echo "<div class='nav1'>";
	echo "<b>ICQ:</b><br /><input type='text' name='ank_icq' value='$user[ank_icq]' maxlength='9' /></div>";

echo "<div class='nav2'>";	
echo "<input type='submit' name='save' value='Сохранить' /></div></form>\n";

echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> <a href='anketa.php'>Посмотреть анкету</a></div>";

include_once TFOOT;
?>