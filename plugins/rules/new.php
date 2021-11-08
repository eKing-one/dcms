<?


include_once '../../sys/inc/start.php';


include_once '../../sys/inc/compress.php';


include_once '../../sys/inc/sess.php';


include_once '../../sys/inc/home.php';


include_once '../../sys/inc/settings.php';


include_once '../../sys/inc/db_connect.php';


include_once '../../sys/inc/ipua.php';


include_once '../../sys/inc/fnc.php';


include_once '../../sys/inc/adm_check.php';


include_once '../../sys/inc/user.php';





if (isset($user) && $user['level'] < 3)


header("Location: /");





$set['title']='添加条目';


include_once '../../sys/inc/thead.php';


aut();


title();





if(isset($_GET['post']))


{


	if (isset($_POST['title']))


	{


		$title=esc($_POST['title'],1);


		$msg=esc($_POST['msg'],1);


		$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `rules`"), 0)+1;


		


		if (!isset($err)){


			dbquery("INSERT INTO `rules` (`time`, `msg`, `title`, `id_user`, `pos`) values('$time', '$msg', '$title', '$user[id]', '$pos')");


			dbquery("OPTIMIZE TABLE `rules`");


			


			$_SESSION['message'] = '项目成功添加';


			header("Location: index.php?");


			exit;


		}


	}


	


	err();


	


	echo '<form method="post" action=""new.php?post">';


	echo '名称(连结):<br /><input name="title" size="16" maxlength="32" value="" type="text" /><br />';


	//echo 'Текст (на главной):<br /><textarea name="msg" ></textarea><br />';


	echo '<input value="添加" type="submit" />';


	echo '</form>';


	


}











if(isset($_GET['msg']))


{


	if (isset($_POST['msg']))


	{


		$msg=esc($_POST['msg'],1);


		$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `rules`"), 0)+1;


		if (!isset($err)){


			dbquery("INSERT INTO `rules` (`time`, `msg`, `title`, `id_user`, `pos`) values('$time', '$msg', '$title', '$user[id]', '$pos')");


			dbquery("OPTIMIZE TABLE `rules`");


			


			$_SESSION['message'] = '文本成功添加';


			header("Location: index.php?");


			exit;


		}


	}


	


	err();


	echo '<form method="post" action="new.php?msg">';


	//echo 'Название (ссылка):<br /><input name="title" size="16" maxlength="32" value="" type="text" /><br />';


	echo '文本:<br /><textarea name="msg" ></textarea><br />';


	echo '<input value="添加" type="submit" />';


	echo '</form>';


}











if(isset($_GET['url']))


{


	if (isset($_POST['url']) && isset($_POST['name_url']))


	{


		$url=esc($_POST['url'],1);


		$name_url=esc($_POST['name_url'],1);


		$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `rules`"), 0)+1;


		if (!isset($err)){


			dbquery("INSERT INTO `rules` (`time`, `id_user`, `url`, `name_url`, `pos`) values('$time', '$user[id]', '$url', '$name_url', '$pos')");


			dbquery("OPTIMIZE TABLE `rules`");


			$_SESSION['message'] = '链接成功添加';


			header("Location: index.php?");


			exit;


		}


	}


	


	err();


	echo '<form method="post" action="new.php?url">';


	echo '链接名称:<br /><input name="name_url" size="16" value="" type="text" /><br />';


	echo '连结地址:<br /><input name="url" size="16" value="/" type="text" /><br />';


	echo '<input value="添加" type="submit" />';


	echo '</form>';


}





echo '<div class="foot"><img src="/style/icons/str2.gif" alt="*"/> <a href="index.php">资料</a></div>';


include_once '../../sys/inc/tfoot.php';


?>