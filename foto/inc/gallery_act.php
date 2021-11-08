<?
if (isset($user) && $user['id'] == $ank['id'])
{
	if (isset($_GET['act']) && $_GET['act']=='create' && isset($_GET['ok']) && isset($_POST['name']) && isset($_POST['opis']))
	{
		$name = my_esc($_POST['name']);
		if (strlen2($name) < 3)$err = 'Короткое название';
		if (strlen2($name) > 32)$err = 'Название не должно быть длиннее 32-х символов';
		
		$pass = my_esc($pass);
		
		$privat = intval($_POST['privat']);
		$privat_komm = intval($_POST['privat_komm']);
		
		$msg = $_POST['opis'];
		
		if (strlen2($msg) > 256)$err = 'Длина описания превышает предел в 256 символов';
		$msg = my_esc($msg);
		
		if (dbresult(dbquery("SELECT COUNT(*) FROM `gallery` WHERE `id_user` = '$ank[id]' AND `name` = '$name'"),0) != 0)
		$err = 'Альбом с таким названием уже существует';	
		
		if (!isset($err))
		{
			dbquery("INSERT INTO `gallery` (`opis`, `time_create`, `id_user`, `name`, `time`, `pass`, `privat`, `privat_komm`) values('$msg', '$time', '$ank[id]', '$name', '$time', '$pass', '$privat', '$privat_komm')");
			$gallery_id = mysql_insert_id();
			$_SESSION['message'] = 'Фотоальбом успешно создан';
			header("Location: /foto/$ank[id]/$gallery_id/");
			exit;
		}
	}
}
?>