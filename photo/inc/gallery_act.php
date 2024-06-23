<?
if (isset($user) && $user['id'] == $ank['id'])
{
	if (isset($_GET['act']) && $_GET['act']=='create' && isset($_GET['ok']) && isset($_POST['name']) && isset($_POST['opis']))
	{
		$name = my_esc($_POST['name']);
		if (strlen2($name) < 3)$err = '标题太短了！要大于3个字';
		if (strlen2($name) > 32)$err = '标题不得超过32个字';
		@$pass = my_esc($pass);
		$privat = intval($_POST['privat']);
		$privat_komm = intval($_POST['privat_komm']);
		$msg = $_POST['opis'];
		if (strlen2($msg) > 256)$err = '描述长度超过 256 个字的限制';
		$msg = my_esc($msg);
		if (dbresult(dbquery("SELECT COUNT(*) FROM `gallery` WHERE `id_user` = '$ank[id]' AND `name` = '$name'"),0) != 0)
		$err = '相册名字重复';	
		if (!isset($err))
		{
			dbquery("INSERT INTO `gallery` (`opis`, `time_create`, `id_user`, `name`, `time`, `pass`, `privat`, `privat_komm`) values('$msg', '$time', '$ank[id]', '$name', '$time', '$pass', '$privat', '$privat_komm')");
			$gallery_id = dbinsertid();
			$_SESSION['message'] = '相册已成功创建';
			header("Location: /photo/$ank[id]/$gallery_id/");
			exit;
		}
	}
}
?>
