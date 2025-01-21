<?php
// 执行搜索

if (isset($_POST['in']) && $_POST['in'] != null && preg_match('#^(r|f)([0-9]+)$#', $_POST['in'], $in)) {
	if ($in[1] == 'f' && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE `id` = '$in[2]' " . ((!isset($user) || $user['level'] == 0) ? "AND `adm` = '0'" : null)), 0) == 1) {
		$searched['in']['m'] = 'f';
		$searched['in']['id'] = $in[2];
	} elseif ($in[1] == 'r') {
		$searched['in']['m'] = 'r';
		$searched['in']['id'] = $in[2];
	}
}
if (!isset($_POST['text']) || strlen2($_POST['text']) < 2) $err[] = '错误查询';
else {
	$s_arr = preg_split("/[\s,]+/", $_POST['text']);;
	$searched['text'] = implode(' ', $s_arr);
	for ($i = 0; $i < count($s_arr); $i++) {
		$st = $s_arr[$i];
		if (strlen2($st) < 3) continue;
		$searched['mark'][$i] = '#(' . $st . '[a-z0-9]*)#uim';
		$s_arr_mysql[$i] = my_esc('+' . $st . '*');
	}
}
if (isset($s_arr_mysql)) {
	$adm_add = [];
	$adm_add2 = NULL;
	if (!isset($user) || $user['level'] == 0) {
		$q222 = dbquery("SELECT * FROM `forum_f` WHERE `adm` = '1'");
		while ($adm_f = dbassoc($q222)) {
			$adm_add[] = "`forum_p`.`id_forum` <> '$adm_f[id]'";
		}
		if (sizeof($adm_add) != 0) $adm_add2 = implode(' AND ', $adm_add) . ' AND ';
	}
	$searched['query'] = implode(' ', $s_arr_mysql);
	$searched['sql_query'] = "SELECT 
	COUNT(`forum_p`.`id`) AS `k_post`,
	`forum_t`.`id`,
	`forum_t`.`id_user`,
	`forum_t`.`name`,
	`forum_t`.`id_forum`,
	`forum_t`.`id_razdel`,
	`forum_t`.`up`,
	`forum_t`.`close`,
	`forum_t`.`time_create`,
	`forum_p`.`id` AS `id_post`,
	`forum_p`.`msg`
	FROM `forum_t` LEFT JOIN `forum_p` ON `forum_p`.`id_them` = `forum_t`.`id` WHERE " . $adm_add2 .
		($searched['in']['m'] == 'f' ? "`forum_t`.`id_forum` = '" . $searched['in']['id'] . "' AND " : null) . // только в выбранном форуме
		($searched['in']['m'] == 'r' ? "`forum_t`.`id_razdel` = '" . $searched['in']['id'] . "' AND " : null) . // только в выбранном разделе
		"MATCH (`forum_p`.`msg`,`forum_t`.`name`) AGAINST ('$searched[query]' IN BOOLEAN MODE) GROUP BY `forum_t`.`id`";
	$q = dbquery($searched['sql_query']);
	while ($result = dbassoc($q)) {
		$searched['result'][] = $result;
	}
}
