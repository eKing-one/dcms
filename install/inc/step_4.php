<?
$set['title']='注册管理员';
include_once 'inc/head.php'; // 设计主题的顶部

if (!isset($_SESSION['shif']))$_SESSION['shif']=$passgen;

$set['shif']=$_SESSION['shif'];

$db=mysql_connect($_SESSION['host'], $_SESSION['user'],$_SESSION['pass']);
mysql_select_db($_SESSION['db'],$db);
mysql_query('set charset utf8');
mysql_query('SET names utf8');
mysql_query('set character_set_client="utf8"');
mysql_query('set character_set_connection="utf8"');
mysql_query('set character_set_result="utf8"');



if (isset($_SESSION['adm_reg_ok']) && $_SESSION['adm_reg_ok']==true)
{
if(isset($_GET['step']) && $_GET['step']=='5')
{


$tmp_set['title']=strtoupper($_SERVER['HTTP_HOST']).' - Главная';
$tmp_set['mysql_host']=$_SESSION['host'];
$tmp_set['mysql_user']=$_SESSION['user'];
$tmp_set['mysql_pass']=$_SESSION['pass'];
$tmp_set['mysql_db_name']=$_SESSION['db'];
$tmp_set['shif']=$_SESSION['shif'];

if (save_settings($tmp_set))
{




unset($_SESSION['install_step'],$_SESSION['host'],$_SESSION['user'],$_SESSION['pass'],$_SESSION['db'],$_SESSION['adm_reg_ok'],$_SESSION['mysql_ok']);
if ($_SERVER["SERVER_ADDR"]!='127.0.0.1')delete_dir(H.'install/');
header ("Location: /index.php?".SID);
exit;


}
else $msg['无法保存系统设置'];



}
}
elseif (isset($_POST['reg']))
{

// проверка ника
if( !preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $_POST['nick']))$err[]='В нике присутствуют запрещенные символы';
if (preg_match("#[a-z]+#ui", $_POST['nick']) && preg_match("#[а-я]+#ui", $_POST['nick']))$err[]='Разрешается использовать символы только русского или только английского алфавита';
if (preg_match("#(^\ )|(\ $)#ui", $_POST['nick']))$err[]='Запрещено использовать пробел в начале и конце ника';
else{
if (strlen2($_POST['nick'])<3)$err[]='Ник короче 3-х символов';
elseif (strlen2($_POST['nick'])>16)$err[]='Ник длиннее 16-ти символов';
elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `user` WHERE `nick` = '".mysql_real_escape_string($_POST['nick'])."' LIMIT 1"),0)!=0)
$err[]='Выбранный ник уже занят другим пользователем';
else $nick=$_POST['nick'];
}
// проверка пароля
if (!isset($_POST['password']) || $_POST['password']==null)$err[]='Введите пароль';
else{
if (strlen2($_POST['password'])<6)$err[]='Пароль короче 6-ти символов';
elseif (strlen2($_POST['password'])>16)$err[]='Пароль длиннее 16-ти символов';
elseif (!isset($_POST['password_retry']))$err[]='Введите подтверждение пароля';
elseif ($_POST['password']!==$_POST['password_retry'])$err[]='Пароли не совпадают';
else $password=$_POST['password'];
}


if (!isset($_POST['pol']) || !is_numeric($_POST['pol']) || ($_POST['pol']!=='0' && $_POST['pol']!=='1'))$err[]='Ошибка при выборе пола';
else $pol=intval($_POST['pol']);



if (!isset($err)) // если нет ошибок
{


mysql_query("INSERT INTO `user` (`nick`, `pass`, `date_reg`, `date_aut`, `date_last`, `pol`, `level`, `group_access`, `balls`, `money`)
VALUES('$nick', '".shif($password)."', $time, $time, $time, '$pol', '4', '15', '5000', '500')");



$user=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `nick` = '$nick' AND `pass` = '".shif($password)."' LIMIT 1"));

$q=mysql_query("SELECT `type` FROM `all_accesses`");
while ($ac = mysql_fetch_assoc($q))
{
mysql_query("INSERT INTO `user_acсess` (`id_user`, `type`) VALUES ('$user[id]','$ac[type]')");
}

/*
========================================
Создание настроек юзера
========================================
*/

mysql_query("INSERT INTO `user_set` (`id_user`) VALUES ('$user[id]')");
mysql_query("INSERT INTO `discussions_set` (`id_user`) VALUES ('$user[id]')");
mysql_query("INSERT INTO `tape_set` (`id_user`) VALUES ('$user[id]')");
mysql_query("INSERT INTO `notification_set` (`id_user`) VALUES ('$user[id]')");


$_SESSION['id_user']=$user['id'];
setcookie('id_user', $user['id'], time()+60*60*24*365);
setcookie('pass', cookie_encrypt($password,$user['id']), time()+60*60*24*365);

$_SESSION['adm_reg_ok']=true;
}


}



if (isset($_SESSION['adm_reg_ok']) && $_SESSION['adm_reg_ok']==true)
{
echo "<div class='msg'>管理员注册成功</div>\n";
if (isset($msg))
{
foreach ($msg as $key=>$value) {
echo "<div class='msg'>$value</div>\n";
}
}
echo "<hr />\n";
echo "<form method=\"get\" action=\"index.php\">\n";
echo "<input name='gen' value='$passgen' type='hidden' />\n";
echo "<input name=\"step\" value=\"".($_SESSION['install_step']+1)."\" type=\"hidden\" />\n";
echo "<input value='完成安装' type=\"submit\" />\n";
echo "</form>\n";
echo "* 安装后，请务必删除文件夹 /install/<br />\n";
}
else
{

if (isset($err))
{
foreach ($err as $key=>$value) {
echo "<div class='err'>$value</div>\n";
}
echo "<hr />\n";
}


echo "<form action='index.php?$passgen' method='post'>\n";
echo "登录名 (3-16 字符):<br />\n<input type='text' name='nick'".((isset($nick))?" value='".$nick."'":" value='Admin'")." maxlength='16' /><br />\n";
echo "密码 (6-16 字符):<br />\n<input type='password'".((isset($password))?" value='".$password."'":null)." name='password' maxlength='16' /><br />\n";
echo "* 使用简单的密码使黑客的生活更轻松<br />\n";
echo "确认密码:<br />\n<input type='password'".((isset($password))?" value='".$password."'":null)." name='password_retry' maxlength='16' /><br />\n";


echo "您的性别:<br />\n";
echo "<select name='pol'>\n";
echo "<option value='1'".((isset($pol) && $pol===1)?" selected='selected'":null).">男性的</option>\n";
echo "<option value='0'".((isset($pol) && $pol===0)?" selected='selected'":null).">妇女的</option>\n";
echo "</select><br />\n";


echo "* 所有字段都必须填写<br />\n";
echo "<input type='submit' name='reg' value='注册' /><br />\n";
echo "</form>\n";
}
echo "<hr />\n";
echo "<b>步骤: $_SESSION[install_step]</b>\n";

include_once 'inc/foot.php'; //设计主题的底部
?>