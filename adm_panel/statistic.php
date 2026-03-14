<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_statistic', null, 'index.php?' . session_id());
adm_check();
$set['title'] = '网站统计';
include_once '../sys/inc/thead.php';
title();
err();
aut();

$hit = $db->queryColumn('SELECT SUM(hit_count) AS total_clicks FROM visit_today;');
$host2 = $db->queryColumn('SELECT COUNT(DISTINCT ip_ua_hash) AS visitor_count FROM visit_today;');

for ($i = 0; $i < 24; $i++) {
	$user_reg = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_reg` >= '" . mktime($i, 0, 0) . "' AND `date_reg` < '" . mktime($i + 1, 0, 0) . "'"), 0);
	$forum_them = dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `time_create` >= '" . mktime($i, 0, 0) . "' AND `time_create` < '" . mktime($i + 1, 0, 0) . "'"), 0);
	$forum_post = dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `time` >= '" . mktime($i, 0, 0) . "' AND `time` < '" . mktime($i + 1, 0, 0) . "'"), 0);
	$stat[] = array('hit'=>$hit, 'host'=>$host2, 'time'=>mktime($i, 0, 0), 'for_th'=>$forum_them, 'for_p'=>$forum_post, 'user'=>$user_reg);
}

echo "当前日期:<br />";
echo "点击量：{$hit}；访客数量{$host2}";
echo "<table border='1'>";
echo "<tr>";
echo "<td><b>时间</b></td>";
echo "<td><b>注册管理.</b></td>";
echo "<td><b>论坛-主题</b></td>";
echo "<td><b>论坛帖子</b></td>";
echo "</tr>";

for ($i = 0; $i < sizeof($stat); $i++) {
	if ($time < $stat[$i]['time']) continue;
	echo "<tr>";
	echo "<td>" . date('H', $stat[$i]['time'] + $user['set_timesdvig'] * 60 * 60) . "</td>";
	echo "<td>" . $stat[$i]['user'] . "</td>";
	echo "<td>" . $stat[$i]['for_th'] . "</td>";
	echo "<td>" . $stat[$i]['for_p'] . "</td>";
	echo "</tr>";
}

echo "</table><br />";
unset($stat);

echo "最后一个月：<br />"; 
$k_day = dbresult(dbquery("SELECT COUNT(*) FROM `visit_everyday`"), 0);
$q = dbquery("SELECT * FROM visit_everyday WHERE date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) ORDER BY date;");
while ($result = dbassoc($q)) {
	$day_st = mktime(0, 0, 0, date('n', strtotime($result['date'])), date('j', strtotime($result['date'])));
	$day_fn  = mktime(0, 0, 0, date('n', strtotime($result['date'])), date('j', strtotime($result['date'])) + 1);
	$user_reg = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_reg` >= '$day_st' AND `date_reg` < '$day_fn'"), 0);
	$forum_them = dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `time_create` >= '$day_st' AND `time_create` < '$day_fn'"), 0);
	$forum_post = dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `time` >= '$day_st' AND `time` < '$day_fn'"), 0);
	$stat[] = array('visitors'=>$result['visitors'], 'hit'=>$result['hit'], 'date'=>$result['date'], 'for_th'=>$forum_them, 'for_p'=>$forum_post, 'user'=>$user_reg);
}

echo "<table border='1'>";
echo "<tr>";
echo "<td><b>日期</b></td>";
echo "<td><b>点击数</b></td>";
echo "<td><b>主机</b></td>";
echo "<td><b>注册管理.</b></td>";
echo "<td><b>论坛-主题</b></td>";
echo "<td><b>论坛帖子</b></td>";
echo "</tr>";
for ($i = 0; $i < sizeof($stat); $i++) {
	echo "<tr>";
	echo "<td>" . date('Y.m.d', strtotime($stat[$i]['date'])) . "</td>";
	echo "<td>" . $stat[$i]['hit'] . "</td>";
	echo "<td>" . $stat[$i]['visitors'] . "</td>";
	echo "<td>" . $stat[$i]['user'] . "</td>";
	echo "<td>" . $stat[$i]['for_th'] . "</td>";
	echo "<td>" . $stat[$i]['for_p'] . "</td>";
	echo "</tr>";
}
echo "</table><br />";

if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "<a href='/adm_panel/'>管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';