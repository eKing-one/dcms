<?php
/*
 * 用户组设置
 * 检测登录与退出
 * 
 * 该函数用于验证用户在某些特定访问权限下是否具备访问权限。
 * - 如果用户没有指定 ID，则默认使用当前全局用户（通过 `$user` 变量）。
 * - 如果用户没有组权限或组权限为空，且需要退出，则重定向到退出页面。
 * - 如果需要验证访问权限，则检查用户所属组是否有对应的权限。
 */
function user_access($access, $u_id = null, $exit = false) {
	// 如果未传递用户 ID，则使用全局变量 `$user`
	if ($u_id == null) {
		global $user;
	} else {
		// 否则通过传递的 ID 获取用户数据
		$user = user::get_user($u_id);
	}

	// 初始化用户权限的默认值
	if (isset($user)) $user['group_access2'] = 0;	// 原来是你？？？折腾了我两个多小时

	// 检查用户是否有组权限，如果没有权限或权限为空，则处理退出
	if (!isset($user['group_access']) || $user['group_access'] == null) {
		if ($exit !== false) {
			// 如果指定了退出地址，则跳转到该地址并终止执行
			header('Location: ' . $exit);
			exit;
		} else {
			// 如果没有指定退出地址，则返回 false
			return false;
		}
	}

	// 如果需要验证访问权限，并且指定了退出地址
	if ($exit !== false) {
		// 查询数据库，检查用户的组是否具有相应的访问权限
		// 注意：这里原本有对比 `group_access` 和 `group_access2` 的权限，但被注释掉了
		if (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$user[group_access]' AND `id_access` = '" . my_esc($access) . "'"), 0) == 0) {
			// 如果没有权限，则跳转到退出页面
			header("Location: $exit");
			exit;
		}
	} else {
		// 如果没有指定退出地址，则直接返回权限检查结果
		return (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE (`id_group` = '$user[group_access]' or `id_group` = '$user[group_access2]') and `id_access` = '" . my_esc($access) . "'"), 0) == 1 ? true : false);
	}
}
