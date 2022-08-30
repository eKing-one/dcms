<?
$list = null;
if ($l == '/')
	$set['title'] = '文件下载中心'; //网页标题
else $set['title'] = '下载中心 - ' . $dir_id['name']; //网页标题
$_SESSION['page'] = 1;
include_once '../sys/inc/thead.php';
title();
// Файл который перемещаем
if (isset($_GET['trans']))
	$trans = dbassoc(dbquery("SELECT * FROM `downnik_files` WHERE `id` = '" . intval($_GET['trans']) . "' AND `id_user` = '$user[id]' LIMIT 1"));
// Загрузка файла
include 'inc/upload_act.php';
// Действие над папкой
include 'inc/admin_act.php';
err();
aut(); // форма авторизации
if ($l != '/') {
	echo '<div class="foot">';
	echo '<img src="/style/icons/up_dir.gif" alt="*"> <a href="/down/">下载中心</a> &gt; ' . down_path($l) . '<br />';
	echo '</div>';
}
if (!isset($_GET['act']) && !isset($_GET['trans'])) {
	echo '<div class="foot">';
	echo '<img src="/style/icons/search.gif" alt="*"> <a href="/down/search.php">档案搜寻</a> ';
	if (isset($user) && $dir_id['upload'] == 1) {
		$dir_user = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id_user` = '$user[id]' AND `osn` = '1'"));
		echo ' | <a href="/user/personalfiles/' . $user['id'] . '/' . $dir_user['id'] . '/?down_dir=' . $dir_id['id'] . '">添加文件</a>';
	}
	echo '</div>';
}
echo '<table class="post">';
$q = dbquery("SELECT * FROM `downnik_dir` WHERE `dir_osn` = '/$l' OR `dir_osn` = '$l/' OR `dir_osn` = '$l' " . (user_access('down_dir_edit') ? "" : "AND `my` = '0'") . " ORDER BY `name`,`num` ASC");
while ($post = dbassoc($q)) {
	$set['p_str'] = 50;
	$list[] = array('dir' => 1, 'post' => $post);
}
$q = dbquery("SELECT * FROM `downnik_files` WHERE `id_dir` = '$id_dir' ORDER BY `$sort_files` DESC");
while ($post = dbassoc($q)) {
	$list[] = array('dir' => 0, 'post' => $post);
}
if (isset($list) && count($list) > 0) {
	$k_post = sizeof($list);
} else $k_post = 0;
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
if (isset($dir_id['upload']) && $dir_id['upload'] == 1 && $k_post > 1 && !isset($_GET['trans'])) {
	/*------------сортировка файлов--------------*/
	echo "<div id='comments' class='menus'>";
	echo "<div class='webmenu'>";
	echo "<a href='?komm&amp;page=$page&amp;sort_files=0' class='" . ($_SESSION['sort'] == 0 ? 'activ' : '') . "'>新的</a>";
	echo "</div>";
	echo "<div class='webmenu'>";
	echo "<a href='?komm&amp;page=$page&amp;sort_files=1' class='" . ($_SESSION['sort'] == 1 ? 'activ' : '') . "'>流行的</a>";
	echo "</div>";
	echo "</div>";
	/*---------------alex-borisi---------------------*/
}
if (isset($user) && isset($dir_id['upload']) && $dir_id['upload'] == 1 && isset($_GET['trans'])) {
	echo '<div class="mess">';
	echo '<img src="/style/icons/ok.gif" alt="*"> <b><a href="?act=upload&amp;trans=' . $trans['id'] . '&amp;ok">添加在这里</a></b><br />';
	echo '</div>';
}
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '文件夹为空';
	echo '</div>';
}
for ($i = $start; $i < $k_post && $i < $set['p_str'] * $page; $i++) {
	if ($list[$i]['dir'] == 1) // папка 
	{
		$post = $list[$i]['post'];
		/*-----------代码-----------*/
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}
		/*---------------------------*/
		echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/dir.png" alt="" /> ';
		if (!isset($_GET['trans'])) {
			echo '<a href="/down' . $post['dir'] . '">' . htmlspecialchars($post['name']) . '</a>';
			$k_f = 0;
			$k_n = 0;
			$q3 = dbquery("SELECT * FROM `downnik_dir` WHERE `dir_osn` like '$post[dir]%'");
			while ($post2 = dbassoc($q3)) {
				$k_f = $k_f + dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_dir` = '$post2[id]'"), 0);
				$k_n = $k_n + dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_dir` = '$post2[id]' AND `time_go` > '" . $ftime . "'", $db), 0);
			}
			$k_f = $k_f + dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_dir` = '$post[id]'"), 0);
			$k_n = $k_n + dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_dir` = '$post[id]' AND `time_go` > '" . $ftime . "'", $db), 0);
			if ($k_n == 0) $k_n = NULL;
			else $k_n = '<font color="red">+' . $k_n . '</font>';
			echo ' (' . $k_f . ') ' . $k_n . '<br />';
		} else {
			echo '<a href="/down' . $post['dir'] . '?trans=' . $trans['id'] . '">' . htmlspecialchars($post['name']) . '</a>';
		}
		echo '</div>';
	} elseif (!isset($_GET['trans'])) {
		$post = $list[$i]['post'];
		$k_p = dbresult(dbquery("SELECT COUNT(*) FROM `downnik_komm` WHERE `id_file` = '$post[id]'"), 0);
		$ras = $post['ras'];
		$file = H . "sys/down/files/$post[id].dat";
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
		include 'inc/icon48.php';
		if (test_file(H . 'style/themes/' . $set['set_them'] . '/loads/14/' . $ras . '.png'))
			echo "<img src='/style/themes/$set[set_them]/loads/14/$ras.png' alt='$ras' /> ";
		else
			echo "<img src='/style/themes/$set[set_them]/loads/14/file.png' alt='file' /> ";
		if ($set['echo_rassh'] == 1) $ras = $post['ras'];
		else $ras = NULL;
		echo '<a href="/down' . $dir_id['dir'] . $post['id'] . '.' . $post['ras'] . '?showinfo"><b>' . htmlspecialchars($post['name']) . '.' . $ras . '</b></a> (' . size_file($post['size']) . ') ';
		if ($post['metka'] == 1) echo '<font color=red><b>(18+)</b></font> ';
		echo '<br />';
		if ($post['opis']) echo rez_text(htmlspecialchars($post['opis'])) . '<br />';
		echo '<a href="/down' . $dir_id['dir'] . $post['id'] . '.' . $post['ras'] . '?showinfo&amp;komm">评论</a> (' . $k_p . ')<br />';
		echo '</div>';
	}
}
echo '</table>';
if ($k_page > 1 && !isset($_GET['trans'])) str('?', $k_page, $page); // 输出页数
if ($l != '/') {
	echo '<div class="foot">';
	echo '<img src="/style/icons/up_dir.gif" alt="*"> <a href="/down/">下载中心</a> &gt; ' . down_path($l) . '<br />';
	echo '</div>';
}
include 'inc/admin_form.php';
