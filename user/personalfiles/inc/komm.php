<?
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
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `downnik_komm` WHERE `id_file` = '$file_id[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
echo '<div class="foot">';
echo "评论：";
echo '</div>';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo "没有留言";
	echo '</div>';
} else if (isset($user)) {
	/*------------сортировка по времени--------------*/
	if (isset($user)) {
		echo "<div id='comments' class='menus'>";
		echo "<div class='webmenu'>";
		echo "<a href='?id_file=$file_id[id]&amp;sort=1' class='" . ($user['sort'] == 1 ? 'activ' : '') . "'>在下面</a>";
		echo "</div>";
		echo "<div class='webmenu'>";
		echo "<a href='?id_file=$file_id[id]&amp;sort=0' class='" . ($user['sort'] == 0 ? 'activ' : '') . "'>在顶部</a>";
		echo "</div>";
		echo "</div>";
	}
	/*---------------alex-borisi---------------------*/
}
$q = dbquery("SELECT * FROM `downnik_komm` WHERE `id_file` = '$file_id[id]' ORDER BY `id` $sort LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$anketa = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$post[id_user]' LIMIT 1"));
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo user::nick($anketa['id'],1,1,0);
	if (isset($user) && $anketa['id'] != $user['id']) echo ' <a href="?id_file=' . $file_id['id'] . '&amp;page=' . $page . '&amp;response=' . $anketa['id'] . '">[*]</a> ';
	echo " (" . vremja($post['time']) . ")<br />";
	$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'files') AND `post` = '1' AND `id_user` = '$anketa[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
	if ($postBan == 0) // Блок сообщения
	{
		echo esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($post['msg'])))))))) . "<br />";
	} else {
		echo output_text($banMess) . '<br />';
	}
	if (isset($user)) {
		echo '<div style="text-align:right;">';
		if ($anketa['id'] != $user['id'])
			echo "<a href=\"?id_file=$file_id[id]&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*' title='Это спам'></a> ";
		if (user_access('down_komm_del') || $anketa['id'] == $user['id'])
			echo '<a href="?id_file=' . $file_id['id'] . '&amp;page=' . $page . '&amp;del_post=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*"></a>';
		echo "   </div>";
	}
	echo "   </div>";
}
echo "</table>";
if ($k_page > 1) str('?id_file=' . $file_id['id'] . '&amp;', $k_page, $page); // 输出页数
if (isset($user)) {
	echo "<form method=\"post\" name='message' action=\"?id_file=$file_id[id]" . $go_otv . "\">";
	if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
		echo "$tPanel<textarea name=\"msg\">$respons_msg</textarea><br />";
	echo "<input value=\"发送\" type=\"submit\" />";
	echo "</form>";
}
