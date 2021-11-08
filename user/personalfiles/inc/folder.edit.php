<?PHP
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


if (isset($_GET['edit_folder']))
{
	$folder = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id` = '".intval($_GET['edit_folder'])."' LIMIT 1"));

	if ($folder['id_user'] != $user['id'] && !user_access('obmen_dir_edit'))
	{

		header("Location: /?".SID);	
		exit;

	}

	if (isset($_POST['name']) && isset($user))
	{
		$msg=$_POST['msg'];
		$name=$_POST['name'];
		$pass=$_POST['pass'];

		if (strlen2($msg) > 256){ $err[] = 'Длина описания превышает 256 символов';}
		if (strlen2($name) > 30){ $err[] = 'Длина названия превышает 30 символов'; }
		if (strlen2($pass) > 13){ $err[] = 'Длина пароля превышает 12 символов'; }
		if (strlen2($name) < 3) { $err[] = 'Длина названия должна быть не менее 3 символов'; }

		if(!isset($err))
		{
			dbquery("UPDATE `user_files` SET `name` = '" . my_esc($name) . "',  `pass` = '" . my_esc($pass) . "', `msg` = '" . my_esc($msg) . "' WHERE `id` = '$folder[id]' LIMIT 1");
			$_SESSION['message'] = 'Изменения приняты';
			header("Location: ?".SID);
			exit;
		}
	}
	err();
	
	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn'] == 1 ? '<a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">Файлы</a>' : '')." ".user_files($dir['id_dires'])." ".($dir['osn'] == 1 ? '' : '&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">'.text($dir['name']).'</a>')."\n";
	echo "</div>";

	echo '<form action="?edit_folder=' . $folder['id'] . '" method="post">';
	echo 'Название:<br/><input type="text" name="name" maxlength="55" value="' . text($folder['name']) . '" /><br />';
	echo 'Описание:<br /><textarea name="msg">' . text($folder['msg']) . '</textarea><br />'; 
	echo 'Пароль:<br/><input type="pass" name="pass" maxlength="12" value="' . text($folder['pass']) . '" /><br />';
	echo '<input type="submit" name="sub" value="Сохранить"/></form>';

	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn'] == 1 ? '<a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">Файлы</a>' : '')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">' . text($dir['name']) . '</a>')."\n";
	echo "</div>";

	include_once '../../sys/inc/tfoot.php';
	exit;
}
?>