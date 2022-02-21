<?
if (user_access('obmen_dir_delete') && isset($_GET['act']) && $_GET['act']=='delete' && isset($_GET['ok']) && $l!='/') {
	if ($dir_id['my'] == 1) {
		echo "无法删除“个人文件”文件夹！";
		exit;
	}
	$q=dbquery("SELECT * FROM `obmennik_dir` WHERE `dir_osn` like '$l%'");
	while ($post = dbassoc($q)) {
		$q2=dbquery("SELECT * FROM `obmennik_files` WHERE `id_dir` = '$post[id]'");
		while ($post2 = dbassoc($q2)) {
			if (!@unlink(H.'sys/obmen/files/'.$post2['id'].'.dat'))$err[]='删除文件时出错 '.$post2['id'].'.dat';
			@unlink(H.'sys/obmen/files/'.$post2['id'].'.dat.GIF');
			@unlink(H.'sys/obmen/files/'.$post2['id'].'.dat.JPG');
			@unlink(H.'sys/obmen/files/'.$post2['id'].'.dat.PNG');
			dbquery("DELETE FROM `user_music` WHERE `id_file` = '$post2[id]' AND `dir` = 'obmen'");
		}
		dbquery("DELETE FROM `obmennik_files` WHERE `id_dir` = '$post[id]'");
		dbquery("DELETE FROM `obmennik_dir` WHERE `id` = '$post[id]' LIMIT 1");
	}
	$q2=dbquery("SELECT * FROM `obmennik_files` WHERE `id_dir` = '$dir_id[id]'");
	while ($post = dbassoc($q2)) {
		unlink(H.'sys/obmen/files/'.$post['id'].'.dat');
		@unlink(H.'sys/obmen/files/'.$post['id'].'.dat.GIF');
		@unlink(H.'sys/obmen/files/'.$post['id'].'.dat.JPG');
		@unlink(H.'sys/obmen/files/'.$post['id'].'.dat.PNG');
		dbquery("DELETE FROM `user_music` WHERE `id_file` = '$post[id]' AND `dir` = 'obmen'");
	}
	dbquery("DELETE FROM `obmennik_files` WHERE `id_dir` = '$dir_id[id]'");
	dbquery("DELETE FROM `obmennik_dir` WHERE `id` = '$dir_id[id]' LIMIT 1");
	$l=$dir_id['dir_osn'];
	msg('文件夹已成功删除');
	admin_log('下载中心','正在删除文件夹',"文件夹 '$dir_id[name]' 删除");
	$dir_id=dbassoc(dbquery("SELECT * FROM `obmennik_dir` WHERE `dir` = '/$l' OR `dir` = '$l/' OR `dir` = '$l' LIMIT 1"));
	if (isset($dir_id['id'])) $id_dir=$dir_id['id'];
}
if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='mesto' && isset($_GET['ok']) && isset($_POST['dir_osn']) && $l!='/') {
	if ($_POST['dir_osn']==NULL)
	$err= "未选定的路径"; else {
		$q=dbquery("SELECT * FROM `obmennik_dir` WHERE `dir_osn` like '$l%'");
		while ($post = dbassoc($q)) {
			$new_dir_osn=preg_replace("#^$l/#",$_POST['dir_osn'],$post['dir_osn']).$dir_id['name'].'/';
			$new_dir=$new_dir_osn.$post['name'];
			dbquery("UPDATE `obmennik_dir` SET `dir`='$new_dir/', `dir_osn`='$new_dir_osn' WHERE `id` = '$post[id]' LIMIT 1");
		}
		$l=$_POST['dir_osn'];
		dbquery("UPDATE `obmennik_dir` SET `dir`='".$l."$dir_id[name]/', `dir_osn`='".$l."' WHERE `id` = '$dir_id[id]' LIMIT 1");
		admin_log('下载中心','编辑文件夹',"文件夹 '$dir_id[name]' 移动");
		msg('文件夹已成功移动');
		$dir_id=dbassoc(dbquery("SELECT * FROM `obmennik_dir` WHERE `id` = '$dir_id[id]' LIMIT 1"));
		$id_dir=$dir_id['id'];
	}
}
if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='rename' && isset($_GET['ok']) && isset($_POST['name']) && $l!='/') {
	if ($_POST['name']==NULL)
	$err= "输入文件夹名称";
	// ShaMan elseif( !preg_match("#^([A-zА-я0-9\-\_\(\)\ ])+$#ui", $_POST['name']))$err[]='В названии присутствуют запрещенные символы';
	// Тут конец моих дум else {
		$newdir=retranslit($_POST['name'],1);
		if (!isset($err)) {
			if ($l!='/')$l.='/';
			$downpath=preg_replace('#[^/]*/$#', NULL, $l);
			dbquery("UPDATE `obmennik_dir` SET `name`='".esc($_POST['name'],1)."' WHERE `dir` = '/$l' OR `dir` = '$l/' OR `dir` = '$l' LIMIT 1");
			msg('文件夹已成功重命名');
			admin_log('下载中心','编辑文件夹',"文件夹 '$dir_id[name]' 改名为 '".esc($_POST['name'],1)."'");
			$l=$downpath.$newdir;
			$dir_id=dbassoc(dbquery("SELECT * FROM `obmennik_dir` WHERE `dir` = '/$l' OR `dir` = '$l/' OR `dir` = '$l' LIMIT 1"));
			if (isset($dir_id['id']))
			$id_dir=(int) $dir_id['id']; else $id_dir = 0;
		}
	}
if (user_access('obmen_dir_create') && isset($_GET['act']) && $_GET['act']=='mkdir' && isset($_GET['ok']) && isset($_POST['name'])) {
	if ($_POST['name']==NULL){
	$err= "输入文件夹名称";  }else {
		$newdir=retranslit(intval($_POST['name']),1);
		if (isset($_POST['upload']) && $_POST['upload']=='1')$upload=1; else $upload=0;
		if (!isset($_POST['ras']) || $_POST['ras']==NULL) {
			$upload=0;
		}
		$size=0;
		if ($upload==1 && isset($_POST['size']) && isset($_POST['mn'])) {
			$size=intval($_POST['size'])*intval($_POST['mn']);
			if ($upload_max_filesize<$size)$size=$upload_max_filesize;
		} else $upload=0;
		// ShaMan
		$ras=esc(stripcslashes(htmlspecialchars($_POST['ras'],1)));
		// Тут конец моих дум
		if (!isset($err)) {
			if ($l!='/')$l.='/';
			dbquery("INSERT INTO `obmennik_dir` (`name` , `ras` , `maxfilesize` , `dir` , `dir_osn` , `upload` ) 
VALUES ('".esc($_POST['name'],1)."', '$ras', '$size', '".$l."$newdir/', '".$l."', '$upload')");
			msg('文件夹 "'.esc($_POST['name'],1).'" 成功创建');
			admin_log('下载中心','正在创建文件夹',"已创建文件夹 '".esc($_POST['name'],1)."'");
		}
	}
}
if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='set' && isset($_GET['ok'])) {
	if (isset($_POST['upload']) && $_POST['upload']=='1')$upload=1; else $upload=0;
	if (!isset($_POST['ras']) || $_POST['ras']==NULL) {
		$upload=0;
	}
	$size=0;
	if ($upload==1 && isset($_POST['size']) && isset($_POST['mn'])) {
		$size=intval($_POST['size'])*intval($_POST['mn']);
		if ($upload_max_filesize<$size)$size=$upload_max_filesize;
	} else $upload=0;
	// ShaMan
	$ras=esc(stripcslashes(htmlspecialchars($_POST['ras'],1)));
	// Тут конец моих дум
	if (!isset($err)) {
		if ($l!='/')$l.='/';
		dbquery("UPDATE `obmennik_dir` SET `ras`='$ras', `maxfilesize`='$size', `upload`='$upload' WHERE `id` = '$dir_id[id]'");
		msg('文件夹设置已成功更改');
		admin_log('下载中心','编辑文件夹',"更改文件夹选项 '$dir_id[name]'");
		$dir_id=dbassoc(dbquery("SELECT * FROM `obmennik_dir` WHERE `id` = '$dir_id[id]' LIMIT 1"));
		$id_dir=$dir_id['id'];
	}
}
?>