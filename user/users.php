<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
$set['title'] = '搜索使用者'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();
$sort = 'id';
$por = 'DESC';
if (isset($_GET['ASC'])) $por = 'ASC'; // прямой порядок
if (isset($_GET['DESC'])) $por = 'DESC'; // обратный порядок
switch (@$_GET['sort']) {
	case 'balls':
		$sql_sort = '`user`.`balls`';
		$sort = 'balls'; // 积分
		break;
	case 'level':
		$sql_sort = '`user_group`.`level`';
		$sort = 'level'; // 水平
		break;
	case 'rating':
		$sql_sort = '`user`.`rating`';
		$sort = 'rating'; // 评级
		break;
	case 'pol':
		$sql_sort = '`user`.`pol`';
		$sort = 'pol'; // 性别
		break;
	default:
		$sql_sort = '`user`.`id`';
		$sort = 'id'; // ID
		break;
}
if (!isset($_GET['go'])) {
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user`"), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	echo "<div class='main'>
	按顺序排序: <br />
	<select name='menu' onchange='top.location.href = this.options[this.selectedIndex].value;'> 
	<option selected>-选择-
	<option value='?sort=balls&amp;DESC&amp;page=$page'>积分</option>
	<option value='?sort=level&amp;DESC&amp;page=$page'>等级</option>
	<option value='?sort=rating&amp;DESC&amp;page=$page'>评级</option>
	<option value='?sort=id&amp;ASC&amp;page=$page'>id</option>
	<option value='?sort=pol&amp;ASC&amp;page=$page'>性别</option>
	<option value='?sort=id&amp;DESC&amp;page=$page'>新的</option>
	</select></option>
	</div>
	<table class='post'>";
	if ($k_post == 0) {
		echo '<div class="mess">';
		echo '没有结果';
		echo '</div>';
	}
	$q = dbquery("SELECT `user`.`id` FROM `user` LEFT JOIN `user_group` ON `user`.`group_access` = `user_group`.`id` ORDER BY $sql_sort $por LIMIT $start, $set[p_str]");
	while ($ank = dbassoc($q)) {
		$ank = user::get_user($ank['id']);
		/*-----------代码-----------*/
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}
		/*---------------------------*/
		echo user::nick($ank['id'],1,1,0);//输出用户名
		if ($ank['group_access'] > 1) echo "<span class='status'>$ank[group_name]</span><br />";
		if ($sort == 'rating')
			echo "<span class=\"ank_n\">评级:</span> <span class=\"ank_d\">$ank[rating]</span><br />";
		if ($sort == 'balls')
			echo "<span class=\"ank_n\">积分:</span> <span class=\"ank_d\">$ank[balls]</span><br />";
		if ($sort == 'pol')
			echo "<span class=\"ank_n\">性别:</span> <span class=\"ank_d\">" . (($ank['pol'] == 1) ? '男' : '女') . "</span><br />";
		if ($sort == 'id')
			echo "<span class=\"ank_n\">注册时间:</span> <span class=\"ank_d\">" . vremja($ank['date_reg']) . "</span><br />";
		echo "<span class=\"ank_n\">最后登录:</span> <span class=\"ank_d\">" . vremja($ank['date_last']) . "</span><br />";
		if (user_access('user_prof_edit') && $user['level'] > $ank['level']) {
			echo "<a href='/adm_panel/user.php?id=$ank[id]'>编辑个人资料</a><br />";
		}
		echo '</div>';
	}
	echo "</table>";
	if ($k_page > 1) str("users.php?sort=$sort&amp;$por&amp;", $k_page, $page); // 输出页数
}
$usearch = NULL;
if (isset($_SESSION['usearch'])) $usearch = $_SESSION['usearch'];
if (isset($_POST['usearch'])) $usearch = $_POST['usearch'];
if ($usearch == NULL)
	unset($_SESSION['usearch']);
else
	$_SESSION['usearch'] = $usearch;
$usearch = preg_replace("#( ){1,}#", "", $usearch);
if (isset($_GET['go']) && $usearch != NULL) {
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` like '%" . mysql_real_escape_string($usearch) . "%' OR `id` = '" . intval($usearch) . "'"), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	echo "<table class='post'>
	<div class='main'>
	按顺序排序: <br />
	 <select name='menu' onchange='top.location.href = this.options[this.selectedIndex].value;'> 
	<option selected>-选择-
	<option value='?sort=balls&amp;DESC&amp;page=$page'>积分</option>
	<option value='?sort=level&amp;DESC&amp;page=$page'>等级</option>
	<option value='?sort=rating&amp;DESC&amp;page=$page'>评级</option>
	<option value='?sort=id&amp;ASC&amp;page=$page'>id</option>
	<option value='?sort=pol&amp;ASC&amp;page=$page'>性别</option>
	<option value='?sort=id&amp;DESC&amp;page=$page'>新的</option>
	</select></option>
	</div>";
	if ($k_post == 0) {
		echo "   <tr>
	<td class='p_t'>
	没有结果
	</td>
	</tr>";
	}
	$q = dbquery("SELECT `id` FROM `user` WHERE `nick` like '%" . mysql_real_escape_string($usearch) . "%' OR `id` = '" . intval($usearch) . "' ORDER BY `$sort` $por LIMIT $start, $set[p_str]");
	while ($ank = dbassoc($q)) {
		$ank = user::get_user($ank['id']);
		/*-----------代码-----------*/
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}
		/*---------------------------*/
		echo user::nick($ank['id'],1,1,0);//输出用户名
		if ($ank['level'] != 0) echo "<span class=\"status\">$ank[group_name]</span><br />";
		if ($sort == 'rating')
			echo "<span class=\"ank_n\">评级:</span> <span class=\"ank_d\">$ank[rating]</span><br />";
		if ($sort == 'balls')
			echo "<span class=\"ank_n\">积分</span> <span class=\"ank_d\">$ank[balls]</span><br />";
		if ($sort == 'pol')
			echo "<span class=\"ank_n\">性别:</span> <span class=\"ank_d\">" . (($ank['pol'] == 1) ? '男' : '女') . "</span><br />";
		if ($sort == 'id')
			echo "<span class=\"ank_n\">注册时间:</span> <span class=\"ank_d\">" . vremja($ank['date_reg']) . "</span><br />";
		echo "<span class=\"ank_n\">最后登录:</span> <span class=\"ank_d\">" . vremja($ank['date_last']) . "</span><br />";
		if (user_access('user_prof_edit') && $user['level'] > $ank['level']) {
			echo "<a href='/adm_panel/user.php?id=$ank[id]'>编辑个人资料</a><br />";
		}
		echo '</div>';
	}
	echo "</table>";
	if ($k_page > 1) str("users.php?go&amp;sort=$sort&amp;$por&amp;", $k_page, $page); // 输出页数
} else
	echo "<div class=\"post\">输入用户的ID或昵称</div>";
echo "<form method=\"post\" action=\"?go&amp;sort=$sort&amp;$por\">";
// ShaMan
$usearch = stripcslashes(htmlspecialchars($usearch));
// 这是我的末日
echo "<input type=\"text\" name=\"usearch\" maxlength=\"16\" value=\"$usearch\" /><br />";
echo "<input type=\"submit\" value=\"查找用户\" />";
echo "</form>";
include_once '../sys/inc/tfoot.php';
