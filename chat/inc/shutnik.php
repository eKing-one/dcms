<?php
/**
 * 笑话机器人相关的逻辑部分
 */

$k_vopr = dbresult(dbquery("SELECT COUNT(*) FROM `chat_shutnik`"),0);
if ($k_vopr > 0) {
	$shutnik_last = dbassoc(dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `shutnik` = '1' ORDER BY id DESC LIMIT 1"));
	if ($shutnik_last == NULL || $shutnik_last['time'] < time() - $set['shutnik_new']) {
		$shutnik = dbassoc(dbquery("SELECT * FROM `chat_shutnik` LIMIT ".rand(0,$k_vopr).",1"));
		dbquery("INSERT INTO `chat_post` (`shutnik`, `time`, `msg`, `room`, `privat`) values('1', '$time', '$shutnik[anek]', '$room[id]', '0')");
	}
} else {
	$no_problem_last = dbassoc(dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `shutnik` = '2' ORDER BY id DESC LIMIT 1"));
	if ($no_problem_last == NULL || $no_problem_last['shutnik'] == 0) {
		$msg = "没有笑话。";
		dbquery("INSERT INTO `chat_post` (`shutnik`, `time`, `msg`, `room`, `privat`) values('2', '$time', '$msg', '$room[id]', '0')");
	}
}