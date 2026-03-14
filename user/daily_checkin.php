<?php
require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
require_once '../sys/inc/fnc.php';
require_once '../sys/inc/user.php';
$set['title'] = '每日签到';
require_once '../sys/inc/thead.php';
title();
only_reg();
err();
aut();

// 查询今天是否已经签到
if (empty($db->query('SELECT * FROM checkin_records WHERE user_id = ? AND DATE(checkin_date) = CURDATE()', [$user['id']]))) {
	// 查询昨天是否签到
	$yesterday_checkin = $db->query('SELECT * FROM checkin_records WHERE user_id = ? AND DATE(checkin_date) = ?', [
		$user['id'],
		date('Y-m-d', strtotime('-1 day'))
	]);

	if (empty($yesterday_checkin)) {
		// 如果昨天没有签到，重置连续签到次数
		$streak = 1;
	} else {
		// 获取并设置连续签到次数
		$streak = $yesterday_checkin['streak'] + 1;
	}

	// 奖励逻辑
	if ($streak == 30) {
		$points = 500;
		$coins = 10;
		$db->update('UPDATE `user` SET `balls` = `balls` + ?, `money` = `money` + ? WHERE `id` = ? LIMIT 1', [
			$points,
			$coins,
			$user['id']
		]);
	} else {
		$points = ($streak > 1) ? 200 : 100;
		$db->update('UPDATE `user` SET `balls` = `balls` + ? WHERE `id` = ? LIMIT 1', [
			$points,
			$user['id']
		]);
	}

	// 插入签到记录
	$db->query('INSERT INTO checkin_records (user_id, checkin_date, streak) VALUES (?, NOW(), ?) ON DUPLICATE KEY UPDATE checkin_date = VALUES(checkin_date), streak = ?', [
		$user['id'],
		$streak,
		$streak
	]);

	echo "<div class=\"mess\">";
	if ($streak == 30) {
		echo "连续签到 $streak 天，获得 $points 积分和 $coins 硬币";
	} elseif ($streak > 1) {
		echo "连续签到 $streak 天，获得 $points 积分";
	} else {
		echo "签到成功，获得 $points 积分";
	}
	echo "</div>";
} else {
	echo "<div class=\"mess\">今天已经签到过啦</div>";
}

require_once '../sys/inc/tfoot.php';
