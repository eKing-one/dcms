<?php
$umnik_last = dbassoc(dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `umnik_st` <> '0' ORDER BY id DESC"));
if ($umnik_last != NULL && $umnik_last['umnik_st'] != 4 && $umnik_last['umnik_st'] != 0) {
	$umnik_vopros = dbassoc(dbquery("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last[vopros]' LIMIT 1"));
	$umnik_post = dbassoc(dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `msg` like '%$umnik_vopros[otvet]%' AND `umnik_st` = '0' AND `time` >= '" . ($time - $umnik_last['time']) . "' ORDER BY `id` ASC LIMIT 1"));
	if ($umnik_post != NULL) {
		$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$umnik_post[id_user]' LIMIT 1"));
		$add_balls = 0;
		if ($umnik_last['umnik_st'] == 1) {
			$add_balls = 25;
			$pods = '没有使用提示';
		}
		if ($umnik_last['umnik_st'] == 2) {
			$add_balls = 10;
			$pods = '使用一个提示';
		}
		if ($umnik_last['umnik_st'] == 3) {
			$add_balls = 5;
			$pods = '使用两个提示';
		}
		$msg = "非常好，[b]$ank[nick][/b]，回答了正确答案 [b]$umnik_vopros[otvet] [/b] 并且$pods，获得 $add_balls 积分。下一个问题将在 $set[umnik_new] 秒后提出。";
		dbquery("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('4', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
		dbquery("UPDATE `user` SET `balls` = '" . ($ank['balls'] + $add_balls) . "' WHERE `id` = '$ank[id]' LIMIT 1");
	}
}
$umnik_last1 = dbassoc(dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `umnik_st` = '1' ORDER BY id DESC"));
if ($umnik_last1 != NULL && $umnik_last['umnik_st'] != 4 && $umnik_last1['time'] < time() - $set['umnik_time']) {
	$umnik_vopros = dbassoc(dbquery("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last1[vopros]' LIMIT 1"));
	$msg = "没有人回复或答对这个问题。正确答案: $umnik_vopros[otvet]。下一个问题将于 $set[umnik_new] 秒后提出。";
	dbquery("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('4', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
$umnik_last = dbassoc(dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `umnik_st` <> '0' ORDER BY id DESC"));
if ($umnik_last == NULL || $umnik_last['umnik_st'] == 4 && $umnik_last['time'] < time() - $set['umnik_new']) {
	// задается вопрос
	$k_vopr = dbresult(dbquery("SELECT COUNT(*) FROM `chat_vopros`"), 0);
	$umnik_vopros = dbassoc(dbquery("SELECT * FROM `chat_vopros` LIMIT " . rand(0, $k_vopr) . ", 1"));
	$msg = "[b]问题：[/b] \"$umnik_vopros[vopros]\"[b]回复字数：[/b] " . strlen2($umnik_vopros['otvet']) . "个字";
	dbquery("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('1', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
if ($umnik_last != NULL && $umnik_last['umnik_st'] == 1 && $umnik_last['time'] < time() - $set['umnik_help']) {
	$umnik_vopros = dbassoc(dbquery("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last[vopros]' LIMIT 1"));
	if (function_exists('iconv_substr'))
		$help = iconv_substr($umnik_vopros['otvet'], 0, 1, 'utf-8');
	else
		$help = substr($umnik_vopros['otvet'], 0, 2);
	for ($i = 0; $i < strlen2($umnik_vopros['otvet']) - 1; $i++) {
		$help .= '*';
	}
	$msg = "[b]问题：[/b] \"$umnik_vopros[vopros]\"[b]第一个提示：[/b] $help (" . strlen2($umnik_vopros['otvet']) . "个字)";
	dbquery("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('2', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
if ($umnik_last != NULL && $umnik_last['umnik_st'] == 2 && $umnik_last['time'] < time() - $set['umnik_help']) {
	$umnik_vopros = dbassoc(dbquery("SELECT * FROM `chat_vopros` WHERE `id` = '$umnik_last[vopros]' LIMIT 1"));
	if (function_exists('iconv_substr'))
		$help = iconv_substr($umnik_vopros['otvet'], 0, 2, 'utf-8');
	else
		$help = substr($umnik_vopros['otvet'], 0, 4);
	for ($i = 0; $i < strlen2($umnik_vopros['otvet']) - 2; $i++) {
		$help .= '*';
	}
	$msg = "[b]问题：[/b] \"$umnik_vopros[vopros]\"[b]第二个提示：[/b] $help (" . strlen2($umnik_vopros['otvet']) . "个字)";
	dbquery("INSERT INTO `chat_post` (`umnik_st`, `time`, `msg`, `room`, `vopros`, `privat`) values('3', '$time', '$msg', '$room[id]', '$umnik_vopros[id]', '0')");
}
?>
