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
$set['title'] = '每日签到';
include_once '../sys/inc/thead.php';
title();
only_reg();
err();
aut();

// 查询今天是否已经签到
if (dbresult(dbquery("SELECT * FROM checkin_records WHERE user_id = '{$user['id']}' AND DATE(checkin_date) = CURDATE()"), 0) > 0) {
	echo "<div class=\"mess\">";
	echo "今天已经签到过啦";
	echo "</div>";
} else {
	// 查询昨天是否签到
	$yesterday = date('Y-m-d', strtotime('-1 day'));
	$yesterday_checkin = dbresult(dbquery("SELECT * FROM checkin_records WHERE user_id = '{$user['id']}' AND DATE(checkin_date) = '$yesterday'"), 0);

	if ($yesterday_checkin > 0) {
		// 获取连续签到次数
		$streak = dbresult(dbquery("SELECT streak FROM checkin_records WHERE user_id = '{$user['id']}'"), 0);
		$streak++;
	} else {
		$streak = 1;
	}

	// 奖励逻辑
	if ($streak > 30) {
		$points = 500;
		$coins = 10;
		dbquery("UPDATE `user` SET `balls` = `balls` + $points, `money` = `money` + $coins WHERE `id` = '{$user['id']}' LIMIT 1");
	} else {
		$points = ($streak > 1) ? 300 : 200;
		dbquery("UPDATE `user` SET `balls` = `balls` + $points WHERE `id` = '{$user['id']}' LIMIT 1");
	}

	// 插入签到记录
	dbquery("INSERT INTO checkin_records (user_id, checkin_date, streak) VALUES ('{$user['id']}', NOW(), $streak) ON DUPLICATE KEY UPDATE checkin_date = VALUES(checkin_date), streak = $streak");

	echo "<div class=\"mess\">";
	if ($streak > 30) {
		echo "连续签到 $streak 天，获得 $points 积分和 $coins 金币";
	} elseif ($streak > 1) {
		echo "连续签到 $streak 天，获得 $points 积分";
	} else {
		echo "签到成功，获得 $points 积分";
	}
}

include_once '../sys/inc/tfoot.php';
