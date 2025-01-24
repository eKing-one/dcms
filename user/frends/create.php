<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
only_reg();
if (isset($_GET['no']))
{
	$no = intval($_GET['no']);
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$no' LIMIT 1"),0)==0){header("Location: index.php?");exit;}
	dbquery("DELETE FROM `frends` WHERE `user` = '$user[id]' AND `frend` = '$no' LIMIT 1");
	dbquery("DELETE FROM `frends` WHERE `user` = '$no' AND `frend` = '$user[id]' LIMIT 1");
	dbquery("DELETE FROM `frends_new` WHERE `user` = '$no' AND `to` = '$user[id]' LIMIT 1");
	dbquery("DELETE FROM `frends_new` WHERE `user` = '$user[id]' AND `to` = '$no' LIMIT 1");
	dbquery("OPTIMIZE TABLE `frends`");
	dbquery("OPTIMIZE TABLE `frends_new`");
		/*
		==========================
		Уведомления друзьях
		==========================
		*/
		dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$no', '$user[id]', 'no_frend', '$time')");
	$_SESSION['message']="Заявка отклонена";
	header("Location: new.php?".session_id());
	exit;
}
if (isset($_GET['ok']))
{
	$ok = intval($_GET['ok']);
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$ok' LIMIT 1"),0)==0){header("Location: index.php?");exit;}
    $a = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE `user`='$ok' AND `to`='$user[id]'"),0);
	$as = dbarray(dbquery("SELECT * FROM `user` WHERE `id` = '".$ok."' LIMIT 1"));
	if ($a==0)
	{
	die("错误");
	}
	else{	
	/*----------------------Лента------------------------*/
	$q = dbquery("SELECT * FROM `frends` WHERE `user` = '".$user['id']."' AND `i` = '1'");
	/* Список друзей принимающего заявку */
	while ($f = dbarray($q)){
		$a=user::get_user($f['frend']);
		$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка ленты
		if ($f['lenta_frends']==1 && $lenaSet['lenta_frends']==1) /* Фильтр рассылки */
		{	
			if (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'frends' AND `id_file` = '$ok'"),0)==0)	{
				/* Отправляем друзьям принявшего дружбу в ленту нового друга */		
				dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$user[id]', 'frends', '$time', '$ok', '1')");
			}
		}
	}
		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '$ok' AND `i` = '1'");
			/* Список друзей подавщего заявку */
			while ($f = dbarray($q)){
			$a=user::get_user($f['frend']);
			$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка ленты
				if ($f['lenta_frends']==1 && $lentaSet['lenta_frends']==1) /* Фильтр рассылки */
				{	
					if (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'frends' AND `id_file` = '$user[id]'"),0)==0)
					{ 		
						/* Отправляем друзьям отправившего заявку в ленту нового друга */	
						dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$ok', 'frends', '$time', '$user[id]', '1')");
					}
				}
			}
		/*-------------------alex-borisi--------------------*/	
	if (dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ok') OR (`user` = '$ok' AND `to` = '$user[id]')"),0)==1)
	{
		/*
		==========================
		Уведомления друзьях
		==========================
		*/
		dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ok', '$user[id]', 'ok_frend', '$time')");
	dbquery("INSERT INTO `frends` (`user`, `frend`, `time`, `i`) values('$user[id]', '$ok', '$time', '1')");
    dbquery("INSERT INTO `frends` (`user`, `frend`, `time`, `i`) values('$ok', '$user[id]', '$time', '1')");
    dbquery("DELETE FROM `frends_new` WHERE `user` = '$ok' AND `to` = '$user[id]' LIMIT 1");
	dbquery("DELETE FROM `frends_new` WHERE `user` = '$user[id]' AND `to` = '$ok' LIMIT 1");
    dbquery("OPTIMIZE TABLE `frends`");
    dbquery("OPTIMIZE TABLE `frends_new`");
	}		
	$_SESSION['message']="用户已添加到好友列表";
	header("Location: new.php?".session_id());
	exit;
	}
}
if (isset($_GET['del']))
{
  $no = intval($_GET['del']);
  if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$no' LIMIT 1"),0)==0){header("Location: index.php?");exit;}
  if (dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$no') OR (`user` = '$no' AND `frend` = '$user[id]') LIMIT 1"),0)>0)
  {
  			/*
		==========================
		Уведомления друзьях
		==========================
		*/
		dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$no', '$user[id]', 'del_frend', '$time')");
  dbquery("DELETE FROM `frends` WHERE `user` = '$user[id]' AND `frend` = '$no' LIMIT 1");
  dbquery("DELETE FROM `frends` WHERE `user` = '$no' AND `frend` = '$user[id]' LIMIT 1");
  dbquery("DELETE FROM `frends_new` WHERE `user` = '$no' AND `to` = '$user[id]' LIMIT 1");
  dbquery("DELETE FROM `frends_new` WHERE `user` = '$user[id]' AND `to` = '$no' LIMIT 1");
  dbquery("OPTIMIZE TABLE `frends`");
  dbquery("OPTIMIZE TABLE `frends_new`");
	$_SESSION['message']="已删除此好友";
	header("location:  " . htmlspecialchars($_SERVER['HTTP_REFERER']) . "");
	}
exit;
}
if (isset($_GET['otm']))
{
  $no = intval($_GET['otm']);
  if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$no' LIMIT 1"),0)==0){header("Location: index.php?");exit;}
  if (dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$no') OR (`user` = '$no' AND `to` = '$user[id]') LIMIT 1"),0)>0)
  {
  		/*
		==========================
		Уведомления друзьях
		==========================
		*/
		dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$no', '$user[id]', 'otm_frend', '$time')");
  dbquery("DELETE FROM `frends` WHERE `user` = '$user[id]' AND `frend` = '$no' LIMIT 1");
  dbquery("DELETE FROM `frends` WHERE `user` = '$no' AND `frend` = '$user[id]' LIMIT 1");
  dbquery("DELETE FROM `frends_new` WHERE `user` = '$no' AND `to` = '$user[id]' LIMIT 1");
  dbquery("DELETE FROM `frends_new` WHERE `user` = '$user[id]' AND `to` = '$no' LIMIT 1");
  dbquery("OPTIMIZE TABLE `frends`");
  dbquery("OPTIMIZE TABLE `frends_new`");
	$_SESSION['message']="对方拒绝添加你为好友";
	header("location:  " . htmlspecialchars($_SERVER['HTTP_REFERER']) . "");
	}
exit;
}
if (isset($_GET['add']))
{
$ank['id']=intval($_GET['add']);
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$ank[id]' LIMIT 1"),0)==0){header("Location: index.php?".session_id());exit;}
if (dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"),0)==1){header("Location: index.php?".session_id());exit;}
if (dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"),0)==1){header("Location: index.php?".session_id());exit;}
if ($ank['id']==$user['id']){header("Location: index.php?".session_id());exit;}
dbquery("INSERT INTO `frends_new` (`user`, `to`, `time`) values('$user[id]', '$ank[id]', '$time')");
dbquery("OPTIMIZE TABLE `frends_new`");
$_SESSION['message']="好友申请已发送";
header("location:  " . htmlspecialchars($_SERVER['HTTP_REFERER']) . "");
exit;
}
include_once '../../sys/inc/tfoot.php';
