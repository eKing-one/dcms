<?php
/**
 * 递归收集基于给定用户 ID 数组的碰撞用户 ID。
 *
 * 此函数接受一个初始的用户 ID 数组，并检查数据库表（`user_collision`）中
 * 这些 ID 作为 `id_user` 或 `id_user2` 的记录。然后，它识别相关的用户 ID（碰撞伙伴），
 * 并在满足以下条件时将其添加到数组中：
 * - 新用户 ID 不在当前数组中。
 * - 当前用户的等级高于碰撞伙伴的等级。
 * - 可选条件：如果 $im 设置为 1，则确保碰撞伙伴不是当前用户。
 * 如果有新 ID 被添加，函数会递归调用自身，以确保所有相关碰撞伙伴都被包含。
 *
 * @param array $massive 用户 ID 数组，用于检查碰撞关系。
 * @param int $im 可选标志（默认值为 0）。若为 1，则排除当前用户 ID 被添加。
 * @return array 返回更新后的用户 ID 数组，包含所有发现的碰撞伙伴。
 */
function user_collision($massive, $im = 0) {
	global $user;
	$new = false;
	for ($i = 0; $i < count($massive); $i++) {
		$collision_q = dbquery("SELECT * FROM `user_collision` WHERE `id_user` = '" . $massive[$i] . "' OR `id_user2` = '" . $massive[$i] . "'");
		while ($collision = dbassoc($collision_q)) {
			if ($collision['id_user'] == $massive[$i]) {
				$coll = $collision['id_user2'];
			} else { 
				$coll = $collision['id_user'];
			}
			$ank_coll2 = user::get_user($coll);
			if (isset($ank_coll2['id']) && !in_array($coll, $massive) && (!empty($user) && $user['level'] > $ank_coll2['level']) && ($im == 0 || (!empty($user) && $user['id'] != $ank_coll2['id']))) {
				$massive[] = $coll;
				$new = true;
			}
		}
	}
	if ($new) $massive = user_collision($massive);
	return $massive;
}
