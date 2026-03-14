<?php
/**
 * 检查并返回用户的在线状态图标
 *
 * 该函数用于检查指定用户是否在线，并根据用户的在线时长、设备类型等信息返回相应的在线状态图标。
 * 使用静态变量缓存每个用户的在线状态，以减少重复查询数据库的开销。
 * 
 * @param int $user 用户的 ID（可选，默认为 NULL）
 * @return string 返回用户的在线状态图标，可能是在线图标或 null
 */
function online($user = NULL) {
	// 声明全局变量 $set，以便在函数内部访问
	global $set;
	
	// 声明一个静态变量 $users，用于缓存每个用户的在线状态
	static $users;

	// 如果缓存中没有该用户的在线状态，则执行以下代码
	if (!isset($users[$user])) {
		
		// 执行数据库查询，检查用户是否在线
		// dbquery 执行 SQL 查询，dbresult 获取查询结果的第一行的第一列值
		// 检查该用户的 `date_last` 是否在 600 秒（10 分钟）之内，表示用户在线
		if (dbresult(dbquery("SELECT COUNT(id) FROM `user_log` WHERE `id_user` = '$user' AND `last_online` > NOW() - INTERVAL 10 MINUTE LIMIT 1"), 0) == 1) {
			
			// 如果设置 `show_away` 为 0（即不显示离开状态），则标记用户为 "online"
			if ($set['show_away'] == 0) {
				$on = 'online';
			} else {
				// 如果 `show_away` 为 1，则显示用户离开状态
				// 通过查询用户的 `date_last` 字段来判断用户的在线时长
				$ank = strtotime(dbresult(dbquery("SELECT ul.last_online FROM `user_log` ul WHERE ul.id_user = '$user' AND ul.ban = 0 AND ul.last_online > NOW() - INTERVAL 10 MINUTE ORDER BY ul.last_online DESC LIMIT 1;"), 0));

				// 如果 `date_last` 等于当前时间，说明用户是立即在线的
				if ((time() - $ank['last_online']) == 0) {
					$on = 'online';
				} else {
					// 否则，用户显示为离开状态，并显示离开的时长（秒）
					$on = 'away: ' . (time() - $ank['last_online']) . ' sec';
				}
			}
			
			// 查询用户的其他信息（例如浏览器类型）
			$ank = dbassoc(dbquery("SELECT `browser` FROM `user_log` WHERE `id_user` = '$user' ORDER BY `date` DESC LIMIT 1"));
			
			// 根据用户的浏览器类型判断是手机浏览器（wap）还是其他类型的浏览器
			if ($ank['browser'] == 'wap') {
				// 如果是 wap（移动设备），显示手机图标
				$users[$user] = " <img src='/style/icons/online.gif' alt='*' /> ";
			} else {
				// 如果是其他浏览器，显示网页图标
				$users[$user] = " <img src='/style/icons/online_web.gif' alt='*' /> ";
			}
		} else {
			// 如果用户不在线（未在 600 秒内活跃），则将缓存中的用户状态设为 null
			$users[$user] = null;
		}
	}

	// 返回缓存中的用户状态（在线或离开图标）
	return $users[$user];
}
