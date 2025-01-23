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
	// 插入签到记录
	dbquery("UPDATE `user` SET `balls` = `balls` + 500 WHERE `id` = '{$user['id']}' LIMIT 1");
	dbquery("INSERT INTO checkin_records (user_id, checkin_date) VALUES ('{$user['id']}', NOW())");
	echo "<div class=\"mess\">";
	echo "签到成功!";
	echo "</div>";
}

include_once '../sys/inc/tfoot.php';
