<?
/**
 * & CMS Name :: DCMS-Social
 * & Author   :: Alexandr Andrushkin
 * & Contacts :: ICQ 587863132
 * & Site     :: http://dcms-social.ru
 */
include_once '../sys/inc/home.php';
include_once H.'sys/inc/start.php';
include_once H.'sys/inc/compress.php';
include_once H.'sys/inc/sess.php';
include_once H.'sys/inc/settings.php';
include_once H.'sys/inc/db_connect.php';
include_once H.'sys/inc/ipua.php';
include_once H.'sys/inc/fnc.php';
include_once H.'sys/inc/user.php';
only_level(3);
if(isset($_GET['id']))
{
	$id = dbassoc(dbquery("SELECT * FROM `smile` WHERE `dir` = '" . intval($_GET['id']) . "' LIMIT 1"));
	if (dbresult(dbquery("SELECT COUNT(*) FROM `smile_dir` WHERE `id` = '" . intval($_GET['id']) . "'"),0) == 0)
	header("Location: admin.php");
	// Удаление смайлов
	if(isset($_GET['del']))
	{
		$del = dbassoc(dbquery("SELECT * FROM `smile` WHERE `id` = '" . intval($_GET['del']) . "' LIMIT 1"));
		@unlink(H.'style/smiles/' . $del['id'] . '.gif');
		dbquery("DELETE FROM `smile` WHERE `id` = '".intval($_GET['del'])."'");
		$_SESSION['message'] = '已删除此表情';
		header('Location: ?id=' . intval($_GET['id']) . '&page=' . intval($_GET['page']));
		exit;
	}
	// Загрузка смайлов
	if(isset($_GET['act']) && $_GET['act'] == 'add_smile' && isset($_GET['ok']) && isset($_POST['forms']))
	{
		$forms = intval($_POST['forms']);
		for ($i = 0; $i < $forms; $i++)
		{
			if (isset($_FILES["file_$i"]) && 
			preg_match('#^\.|\.jpg|\.png$|\.gif$|\.jpeg$#i', $_FILES["file_$i"]['name']) && 
			filesize($_FILES["file_$i"]['tmp_name']) > 0 && 
			isset($_POST["smile_$i"]))
			{
				$file = text($_FILES["file_$i"]['name']);
				$smile = my_esc($_POST["smile_$i"]);
				dbquery("INSERT INTO `smile` (`smile`,`dir`) values('$smile','" . intval($_GET['id']) . "')");
				$ID = dbinsertid();
				if (@copy($_FILES["file_$i"]['tmp_name'], H.'style/smiles/' . $ID . '.gif'))
				{
					@chmod(H.'style/smiles/' . $ID . '.gif', 0777);
					$_SESSION['message'] = '上传成功';
				}
			}
			else
			{
				$err = '文件 (' . $i . ') 上传失败';
			}
		}
	}
}
/*
========================
Удаление категорий
========================
*/
if(isset($_GET['delete']))
{
	$q = dbquery("SELECT * FROM `smile` WHERE `dir` = '" . intval($_GET['delete']) . "'");
	while($post = dbarray($q))
	{
		@unlink(H.'style/smiles/' . $post['id'] . '.gif');
		dbquery("DELETE FROM `smile` WHERE `id` = '" . $post['id'] . "'");
	}
	dbquery("DELETE FROM `smile_dir` WHERE `id` = '" . intval($_GET['delete']) . "'");
	$_SESSION['message'] = '分类已成功删除';
	header("Location: ?");
	exit;
}
$set['title'] = '管理表情';
include_once H.'sys/inc/thead.php';
err();
title();
aut();
if (isset($_GET['id']))
{
	// Форма загрузки смайлов
	if(isset($_GET['act']) && $_GET['act'] == 'add_smile')
	{
		if(isset($_POST['forms']))
		$forms = intval($_POST['forms']);
		elseif (isset($_SESSION['forms']))
		$forms = intval($_SESSION['forms']);
		else 
		$forms = 1;
		$_SESSION['forms'] = $forms;
		?>
		<form enctype="multipart/form-data" action="?id=<?=intval($_GET['id'])?>&amp;act=add_smile&amp;ok" method="post">
		分类数量:<br />
		<input type="text" name="forms" value="<?=$forms?>"/><br />
		<input class="submit" type="submit" value="显示分类" /><br />
		<?
		for ($i=0; $i < $forms; $i++)
		{
			echo ($i+1) . ') 文件: <input name="file_' . $i . '" type="file" /><br />';
			echo ($i+1) . ') 一个微笑(例如:)或:D....)<br /><input type="text" name="smile_' . $i . '" maxlength="32" /><br />';
		}
		?>
		<input type="submit" value="添加" />
		<br /><a href="?id=<?=intval($_GET['id'])?>">返回</a><br />
		</form>
		<?
	}
	/*
	========================
	Вывод смайлов
	========================
	*/
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `smile` WHERE `dir`='".intval($_GET['id'])."'"),0);
	$k_page = k_page($k_post,$set['p_str']);
	$page = page($k_page);
	$start = $set['p_str']*$page-$set['p_str'];
	?><table class="post"><?
	if ($k_post == 0) 
	{
		?><div class="mess">表情符号列表为空</div><?
	}
	$q = dbquery("SELECT * FROM `smile` WHERE `dir`='" . intval($_GET['id']) . "' ORDER BY id DESC LIMIT $start, $set[p_str]");
	while($post = dbarray($q))
	{
		// Лесенка
		echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
		$num++;
		?>
		<img src="/style/smiles/<?=$post['id']?>.gif" alt="smile"/> <?=text($post['smile'])?> 
		<a href="?id=<?=intval($_GET['id'])?>&amp;edit=<?=$post['id']?>&amp;page=<?=$page?>"><img src="/style/icons/edit.gif" alt="*"></a> 
		<a href="?id=<?=intval($_GET['id'])?>&amp;del=<?=$post['id']?>&amp;page=<?=$page?>"><img src="/style/icons/delete.gif" alt="*"></a>
		<?
		/*
		========================
		Редактирование смайлов
		========================
		*/
		if(isset($_GET['edit']) && $_GET['edit'] == $post['id'])
		{
			// Редактирование смайлов
			if (isset($_POST['sav']))
			{
				$smile = my_esc($_POST['smile']);
				if(strlen2($smile) < 1)
				$err = '至少1个字符的名称'; 
				if (!isset($err))
				{
					dbquery("UPDATE `smile` SET `smile` = '$smile' WHERE `id` = '$post[id]'");
					$_SESSION['message'] = '接受更改';
					header("Location: ?id=$post[dir]&page=$page");
					exit;
				}
			}
			?>
			<form method="post" action="?id=<?=$post['dir']?>&amp;edit=<?=$post['id']?>&amp;page=<?=$page?>">
			<?=(isset($err) ? '<font color="red">' . $err . '</font><br />' : null)?>
			微笑 (例如 :-) ..)<br />
			<input type="text" name="smile" maxlength="32" value="<?=text($post['smile'])?>"/><br />
			<input type="submit" name="sav" value="修改" />
			</form>
			<?
		}
		?></div><?
	}
	?></table><?
	if ($k_page>1)str('?id=' . intval($_GET['id']) . '&amp;',$k_page,$page);
	?>
	<div class="foot">
	<img src="/style/icons/str.gif" alt="*" /> <a href="?id=<?=intval($_GET['id'])?>&amp;act=add_smile">添加一个微笑</a>
	</div>
	<div class="foot">
	<img src="/style/icons/str.gif" alt="*" /> <a href="smiles.php">表情符号的类别</a>
	</div>
	<?
	include_once H.'sys/inc/tfoot.php';
	exit;
}
/*
========================
Создание категории
========================
*/
if(isset($_GET['act']) && $_GET['act'] == 'add_kat')
{
	if(isset($_POST['save']))
	{
		$name = my_esc($_POST['name']);
		if(strlen2($name) < 1)
		$err = '名字太短了';
		if(!isset($err))
		{
			dbquery("INSERT INTO `smile_dir` (`name` ) VALUES ('$name')");
			$_SESSION['message'] = '该类别已成功创建';
			header("Location: ?act=add_kat");
			exit;
		}
	}
	err();
	?>
	<form method="post" action="?act=add_kat">
	标题<br />
	<input type="text" name="name" maxlength="32" /><br />
	<input type="submit" name="save" value="添加" />
	</form>
	<?
}
/*
========================
Вывод категорий
========================
*/
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `smile_dir`"),0);
?><table class="post"><?
if ($k_post == 0) 
{
	?><div class="mess">没有分类</div><?
}
$q = dbquery("SELECT * FROM `smile_dir`");
while($post = dbarray($q))
{
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	?>
	<img src="/style/themes/<?=$set['set_them']?>/loads/14/dir.png" alt="*"> 
	<a href="?id=<?=$post['id']?>"><?=text($post['name'])?></a> (<?=dbresult(dbquery("SELECT COUNT(*) FROM `smile` WHERE `dir` = '$post[id]'"),0)?>)
	<a href="?edit=<?=$post['id']?>"><img src="/style/icons/edit.gif" alt="*"></a> 
	<a href="?delete=<?=$post['id']?>"><img src="/style/icons/delete.gif" alt="*"></a>
	</div>
	<?
	/*
	========================
	Редактирование категорий
	========================
	*/
	if (isset($_GET['edit']) && $_GET['edit'] == $post['id'])
	{
		if (isset($_POST['sav']))
		{
			$name = my_esc($_POST['name']);
			if(strlen2($name) < 1)
			$err = '至少1个字符的名称';
			if (!isset($err))
			{
				dbquery("UPDATE `smile_dir` SET `name` = '" . $name . "' WHERE `id` = '" . intval($_GET['edit']) . "'");
				$_SESSION['message'] = '该类别已成功重命名';
				header("Location: ?");
				exit;
			}
		}
		?>
		<form method="post" action="?edit=<?=$post['id']?>">
		<?=(isset($err) ? '<font color="red">' . $err . '</font><br />' : null)?>
		标题:<br />
		<input type="text" name="name" maxlength="32" value="<?=text($post['name'])?>"/><br />
		<input type="submit" name="sav" value="修改" />
		</form>
		<?
	}
	?></div><?
}
?></table><?
?>
<div class="foot">
<img src="/style/icons/str.gif" alt="*"> <a href="?act=add_kat">添加类别</a><br />
</div>
<?
include_once H.'sys/inc/tfoot.php';
?>
