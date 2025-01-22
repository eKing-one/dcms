<?PHP
/*
=======================================
DCMS-Social 用户个人文件
作者：探索者
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
---------------------------------------
接点
ICQ：587863132
http://dcms-social.ru
=======================================
*/
$set['title'] = text($dir['name']);
title();
aut();

// 编辑和删除文件/文件夹
if (isset($user) && (user_access('down_file_edit') || $ank['id'] == $user['id'])) {
	// 删除其中的文件夹和文件
	include "inc/folder.delete.php";
	// 文件夹管理
	include "inc/folder.edit.php";
	// 其他形式的提款
	include "inc/all.form.php";
}

// 反向导航输出
echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '文件' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; ' . text($dir['name'])) . "";
echo "</div>";

// 将文件移动到另一个文件夹
if (isset($_GET['go']) && dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id` = '" . intval($_GET['go']) . "'"), 0) == 1) {
	$file_go = dbassoc(dbquery("SELECT * FROM `downnik_files` WHERE `id` = '" . intval($_GET['go']) . "'"));
	if (isset($_GET['ok']) && isset($_GET['ok']) && $ank['id'] == $user['id']) {
		dbquery("UPDATE `downnik_files` SET `my_dir` = '$dir[id]' WHERE `id` = '$file_go[id]' LIMIT 1");
		$_SESSION['message'] = '文件已成功移动';
		header("Location: ?");
		exit;
	}
}

/*--------------------密码文件夹--------------------*/
if ($dir['pass'] != NULL) {
	if (isset($_POST['password'])) {
		$_SESSION['pass'] = my_esc($_POST['password']);
		if ($_SESSION['pass'] != $dir['pass']) {
			$_SESSION['message'] = '密码无效';
			$_SESSION['pass'] = NULL;
		}
		header("Location: ?");
	}
	if (!user_access('down_dir_edit') && ($user['id'] != $ank['id'] && $_SESSION['pass'] != $dir['pass'])) {
		echo '<form action="?" method="POST">密码: <br />		<input type="pass" name="password" value="" /><br />		
		      <input type="submit" value="登录"/></form>';
		echo "<div class='foot'>";
		echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '档案' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; ' . text($dir['name'])) . "";
		echo "</div>";
		include_once '../../sys/inc/tfoot.php';
	}
}

/*---------------------------------------------------------*/
if (isset($_GET['go'])) {
	echo '<div class="foot">';
	echo "<img src='/style/icons/ok.gif' alt='*'> <a href='/user/personalfiles/$ank[id]/$dir[id]/?go=$file_go[id]&amp;ok'>移动苏是的</a>";
	echo "</div>";
	echo '<div class="mess">';
	echo "选择文件的文件夹";
	echo "</div>";
}

if (isset($_SESSION['down_dir']) || isset($_GET['down_dir'])) {
	if (!isset($_SESSION['down_dir']) && dbresult(dbquery("SELECT COUNT(*) FROM `downnik_dir` WHERE `id` = '" . intval($_GET['down_dir']) . "' AND `upload` = '1'"), 0) == 1)
		$_SESSION['down_dir'] = abs(intval($_GET['down_dir']));
	if (isset($_SESSION['down_dir'])) {
		echo '<div class="mess">';
		echo "选择要下载文件的文件夹";
		echo "</div>";
	}
}

$k_files = dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files`  WHERE `my_dir` = '$dir[id]' AND `id_user` = '$ank[id]'"), 0);
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_dir` = '$dir[id]' AND `id_user` = '$ank[id]'"), 0);
$k_post = $k_post + $k_files;
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
if ($k_post == 0) {
	echo '<div class="mess">';
	echo "文件夹为空";
	echo "  </div>";
}
$q = dbquery("SELECT * FROM `user_files`  WHERE `id_dir` = '$dir[id]'  AND `id_user` = '$ank[id]' ORDER BY time DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo "<img src='/style/themes/$set[set_them]/loads/14/" . ($post['pass'] != null ? 'lock.gif' : 'dir.png') . "' alt='*'>";
	if (isset($_GET['go'])) // 如果我们移动一个文件
		echo " <a href='/user/personalfiles/$ank[id]/$post[id]/?go=$file_go[id]'>" . text($post['name']) . "</a>";
	else
		echo " <a href='/user/personalfiles/$ank[id]/$post[id]/'>" . text($post['name']) . "</a>";
	/*----------------------文件夹计数器---------------------*/
	$k_f = 0;
	$q3 = dbquery("SELECT * FROM `user_files` WHERE `id_dires` like '%$post[id]%'");
	while ($post2 = dbassoc($q3)) {
		$k_f = $k_f + dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_dir` = '$post2[id]'"), 0);
	}
	$k_f = $k_f + dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_dir` = '$post[id]'"), 0);
	/*---------------------------------------------------------*/
	/*----------------------文件计数器--------------------*/
	$k_f2 = 0;
	$q4 = dbquery("SELECT * FROM `user_files` WHERE `id_dires` like '%$post[id]%'");
	while ($post3 = dbassoc($q4)) {
		$k_f2 = $k_f2 + dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `my_dir` = '$post3[id]'"), 0);
	}
	$k_f2 = $k_f2 + dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `my_dir` = '$post[id]'"), 0);
	/*---------------------------------------------------------*/
	echo ' (' . $k_f . '/' . $k_f2 . ') ';
	if (isset($user) && $user['group_access'] > 2 || $ank['id'] == $user['id'])
		echo "<a href='?edit_folder=$post[id]'><img src='/style/icons/edit.gif' alt='*'></a> <a href='?delete_folder=$post[id]'><img src='/style/icons/delete.gif' alt='*'></a><br />";
	echo "</div>";
}

if (!isset($_GET['go'])) {
	$q2 = dbquery("SELECT * FROM `downnik_files`  WHERE `my_dir` = '$dir[id]' AND `id_user` = '$ank[id]' ORDER BY time DESC LIMIT $start, $set[p_str]");
	//echo "<form method='post' action='?move_file'>";
	while ($post = dbassoc($q2)) {
		$k_p = dbresult(dbquery("SELECT COUNT(*) FROM `downnik_komm` WHERE `id_file` = '$post[id]'"), 0);
		$dir_id = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `id` = '$post[id_dir]' LIMIT 1"));
		$ras = $post['ras'];
		$file = H . "files/down/$post[id].dat";
		$name = $post['name'];
		$size = $post['size'];
		/*-----------代码-----------*/
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}
		/*---------------------------*/
		if (is_file(H . "down/inc/icon48/$ras.php")) {
			include H . "down/inc/icon48/$ras.php";
		}
		//echo "<input type='checkbox' name='files_$post[id]' value='1' /> ";
		if (is_file(H . 'style/themes/' . $set['set_them'] . '/loads/14/' . $ras . '.png'))
			echo "<img src='/style/themes/$set[set_them]/loads/14/$ras.png' alt='$ras' /> ";
		else
			echo "<img src='/style/themes/$set[set_them]/loads/14/file.png' alt='file' /> ";
		if ($set['echo_rassh'] == 1) $ras = $post['ras'];
		else $ras = NULL;
		echo '<a href="?id_file=' . $post['id'] . '&amp;page=' . $page . '"><b>' . text($post['name']) . '.' . $ras . '</b></a> (' . size_file($post['size']) . ') ';
		if ($post['metka'] == 1) echo ' <font color=red>(18+)</font>';
		if (isset($user)) {
			if ($user['id'] == $post['id_user'] && $dir_id['my'] == 1) echo '<a href="/down/?trans=' . $post['id'] . '"><img src="/style/icons/z.gif" alt="*"> 到区域</a> ';
			if (user_access('down_file_edit') || $user['id'] == $post['id_user']) echo '<a href="?id_file=' . $post['id'] . '&amp;edit"><img src="/style/icons/edit.gif" alt="*"></a> ';
			if (user_access('down_file_delete') || $user['id'] == $post['id_user']) echo '<a href="?id_file=' . $post['id'] . '&amp;delete&amp;page=' . $page . '"><img src="/style/icons/delete.gif" alt="*"></a> ';
		}
		echo '<br />';
		if ($post['opis']) {
			echo rez_text(text($post['opis'])) . '<br />';
		}
		echo '<a href="?id_file=' . $post['id'] . '&amp;page=' . $page . '&amp;komm">评论</a> (' . $k_p . ')<br />';
		echo '</div>';
	}
}

//echo "<input value=\"任务\" type=\"submit\" name=\"job\" />";
//echo "</form>";
echo "</table>";
if ($k_page > 1) str('?', $k_page, $page); // 输出页数

echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '文件' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; ' . text($dir['name'])) . "";
echo "</div>";
