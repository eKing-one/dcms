<?php
//网页标题
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
/* 如果账号被封禁，跳转到封禁页面 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'files' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}
$set['title'] = '文件搜索'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
aut();

echo "<div class='foot'>";
echo '<img src="/style/icons/up_dir.gif" alt="*"> <a href="/down/">下载中心</a><br />';
echo "</div>";

if (isset($_GET['search']) && $_GET['search'] != NULL) {
	$search = $_GET['search'];
	$search = preg_replace("#( ){2,}#", " ", $search);
	$search = preg_replace("#^( ){1,}|( ){1,}$#", "", $search);

	$search_a = explode(' ', $search);
	for ($i = 0; $i < count($search_a); $i++) {
		$search_a2[$i] = '<span class="search_c">' . stripcslashes(htmlspecialchars($search_a[$i])) . '</span>';
		$search_a[$i] = stripcslashes(htmlspecialchars($search_a[$i]));
	}
	$q_search = str_replace('%', '', $search);
	$q_search = str_replace(' ', '%', $q_search);
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `opis` like '%" . my_esc($q_search) . "%' OR `name` like '%" . my_esc($q_search) . "%'"), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	if ($k_post == 0) echo "<div class=\"p_t\">没有结果</div>";
	$q = dbquery("SELECT * FROM `downnik_files` WHERE `opis` like '%" . my_esc($q_search) . "%' OR `name` like '%" . my_esc($q_search) . "%' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
	$i = 0;

	while ($post = dbassoc($q)) {
		$k_p = dbresult(dbquery("SELECT COUNT(*) FROM `downnik_komm` WHERE `id_file` = '$post[id]'"), 0);
		$ras = $post['ras'];
		$file = H . "files/down/$post[id].dat";
		$name = $post['name'];
		$size = $post['size'];
		$dir_id = dbarray(dbquery("SELECT * FROM `downnik_dir` WHERE `id` = '$post[id_dir]' LIMIT 1"));
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
		if (is_file(H . 'style/themes/' . $set['set_them'] . '/loads/14/' . $ras . '.png')) echo "<img src='/style/themes/$set[set_them]/loads/14/$ras.png' alt='$ras' /> ";
		else echo "<img src='/style/themes/$set[set_them]/loads/14/file.png' alt='file' /> ";
		if ($set['echo_rassh'] == 1) $ras = $post['ras'];
		else $ras = NULL;
		echo '<a href="/down' . $dir_id['dir'] . $post['id'] . '.' . $post['ras'] . '?showinfo"><b>' . $post['name'] . '.' . $ras . '</b></a> (' . size_file($post['size']) . ')<br />';
		if ($post['opis']) echo rez_text(htmlspecialchars($post['opis'])) . '<br />';
		echo '<a href="/down' . $dir_id['dir'] . $post['id'] . '.' . $post['ras'] . '?showinfo&amp;komm">评论</a> (' . $k_p . ')<br />';
		echo '</div>';
	}

	if ($k_page > 1) {
		str("search.php?go&amp;", $k_page, $page);
		echo '<br />';
	} // 输出页数
	$search = stripcslashes(htmlspecialchars($search));
} else {
	echo '<div class="foot">';
	$search = '';
}

echo '档案搜寻';
echo '</div>';
echo "<form method=\"get\" action=\"search.php?go\" class=\"search\">";
echo "<input type=\"text\" name=\"search\" maxlength=\"64\" value=\"{$search}\" /><br />";
echo "<input type=\"submit\" value=\"搜索\" />";
echo "</form>";
echo "<div class='foot'>";
echo '<img src="/style/icons/up_dir.gif" alt="*"> <a href="/down/">下载中心</a><br />';
echo "</div>";
include_once '../sys/inc/tfoot.php';
