<?php返回<

/*

=======================================

Личные файлы юзеров для Dcms-Social

Автор: Искатель

---------------------------------------

Этот скрипт распостроняется по лицензии

движка Dcms-Social. 

При использовании указывать ссылку на

оф. сайт http://dcms-social.ru

---------------------------------------

Контакты

ICQ: 587863132

http://dcms-social.ru

=======================================

*/





only_reg();



if ($dir['id_dires']=='/')

$id_dires = $dir['id_dires'] . $dir['id'].'/';

else

$id_dires = $dir['id_dires'] . $dir['id'].'/';



if (isset($_POST['name']) && isset($user))

{

$msg=$_POST['msg'];

$name=$_POST['name'];

$pass=$_POST['pass'];

$osn = $dir['osn']+1;



if ($dir['osn']==6)$err[] = 'Нельзя создавать папки в более чем двух поддиректорях';



if (strlen2($msg)>256){$err[]='Длина описания превышает 256 символов';}

if (strlen2($name)>30){$err[]='Длина названия превышает 30 символов';}



if(!isset($err)){

dbquery("INSERT INTO `user_files` (`id_user`, `name`, `msg`,  `time`, `id_dir`, `osn`, `id_dires`, `pass`) values('$user[id]', '".my_esc($name)."','".my_esc($msg)."',  '$time', '$dir[id]', '$osn', '$id_dires', '".my_esc($pass)."')");



$_SESSION['message'] = 'Папка успешно создана';

header("Location: ?");

exit;

}

}



$set['title'] = 'Создание папки';

include_once '../../sys/inc/thead.php';

title();

aut();

err();

echo "<div class='foot'>";

echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'Файлы':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; '.htmlspecialchars($dir['name']))."\n";

echo "</div>";





echo '<form class="mess" name="message" action="?add" method="post">';

echo 'Имя папки:<br/><input type="text" name="name" maxlength="30" value="" /><br />';

	if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))		include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';	else	{		echo $tPanel . '<textarea name="msg"></textarea><br />';	}

echo 'Пароль:<br/><input type="pass" name="pass" maxlength="12" value="" /><br />';

echo '<input type="submit" name="sub" value="Создать"/></form>';





echo "<div class='foot'>";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?'>Назад</a><br />\n";

echo "</div>";

echo "<div class='foot'>";

echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'Файлы':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; '.htmlspecialchars($dir['name']))."\n";

echo "</div>";

include_once ('../../sys/inc/tfoot.php');





?>