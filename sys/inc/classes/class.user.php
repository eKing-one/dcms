<?

/**
 * 主要用户功能
 *  nick()-显示昵称和在线图标
 * 头像-显示头像和用户图标
 * 所有函数都有参数输出什么和不输出什么
 * 2022年2月23日23点42分修改nick()方法用户组输出
 */
class user
{
	/**
	 * / 参考文献及用户昵称
	 */
	// 所有用户字段
	public static function user_db($user = 0)
	{
		static $nicks = [];
		if (empty($nicks[$user])) {
			$ank = dbassoc(query('SELECT `nick`, `date_last`, `rating`, `browser` FROM `user` WHERE `id` = "' . $user . '" LIMIT 1 '));
			$nicks[$user] = $ank;
		} else $ank = $nicks[$user];
	}
	public static function nick($user = 0, $url = 1, $on = 0, $medal = 0)
	{
		/*
		* $url == 0		只输出昵称
		* $url == 1		输出昵称并链接到用户页的
		* $on  == 1		显示 Nick 旁边的在线图标和用户组图标
		* $medal == 1	在线输出图标旁边的奖牌
		*/
		static $nicks = [];
		if (empty($nicks[$user])) {
			$ank = dbassoc(query('SELECT `group_access`, `pol`, `nick`, `date_last`, `rating`, `browser` FROM `user` WHERE `id` = "' . $user . '" LIMIT 1 '));
			$nicks[$user] = $ank;
		} else $ank = $nicks[$user];
		$icon = null;
		$nick = null;
		$online = null;
		$icon_medal = null;
		// 用户名引线
		if ($user == 0) $ank = array('id' => '0', 'nick' => '系统', 'pol' => '1', 'rating' => '0', 'browser' => 'wap', 'date_last' => time());
		elseif (!$ank) $ank = array('id' => '0', 'nick' => '[已删除]', 'pol' => '1', 'rating' => '0', 'browser' => 'wap', 'date_last' => time());
		if ($url == true)
			$nick = ' <a href="/info.php?id=' . $user . '">' . text($ank['nick']) . '</a> ';
		else
			$nick = text($ank['nick']);

		// 用户组图标
		if ($on == true) {
			$is_ban = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user' AND (`time` > 'time()' OR `navsegda` = '1')"), 0);
			if ($is_ban != 0) {
				$icon = ' <img src="/style/user/ban.png" alt="*" class="icon" id="icon_group" /> ';
			} else {
				if (isset($ank['group_access']) && ($ank['group_access'] > 7 && ($ank['group_access'] < 10 || $ank['group_access'] > 14))) {
					if ($ank['pol'] == 1) {
						$icon = '<img src="/style/user/1.png" alt="*" class="icon" id="icon_group" /> ';
					} else {
						$icon = '<img src="/style/user/2.png" alt="" class="icon" id="icon_group"/> ';
					}
				} elseif (isset($ank['group_access']) && (($ank['group_access'] > 1 && $ank['group_access'] <= 7) || ($ank['group_access'] > 10 && $ank['group_access'] <= 14))) {
					if ($ank['pol'] == 1) {
						$icon = '<img src="/style/user/3.png" alt="*" class="icon" id="icon_group" /> ';
					} else {
						$icon = '<img src="/style/user/4.png" alt="*" class="icon" id="icon_group" /> ';
					}
				} else {
					if (isset($ank['pol']) && $ank['pol'] == 1) {
						$icon = '<img src="/style/user/5.png" alt="" class="icon" id="icon_group" /> ';
					} else {
						$icon = '<img src="/style/user/6.png" alt="" class="icon" id="icon_group" /> ';
					}
				}
			}
		}
		// 在线图标输出
		if ($user != 0 && $ank['date_last'] > time() - 600 && $on == true) {
			if ($ank['browser'] == 'wap')

				$online = ' <img src="/style/icons/online.gif" alt="WAP" /> ';
			else
				$online = ' <img src="/style/icons/online_web.gif" alt="WEB" /> ';
		}
		// 奖牌输出
		$R = $ank['rating'];
		if ($medal == 1 && $R >= 6) {
			if ($R >= 6 && $R <= 11) {
				$img = 1;
			} elseif ($R >= 12 && $R <= 19) {
				$img = 2;
			} elseif ($R >= 20 && $R <= 27) {
				$img = 3;
			} elseif ($R >= 28 && $R <= 37) {
				$img = 4;
			} elseif ($R >= 38 && $R <= 47) {
				$img = 5;
			} elseif ($R >= 48 && $R <= 59) {
				$img = 6;
			} elseif ($R >= 60) {
				$img = 7;
			}
			$icon_medal = ' <img src="/style/medal/' . $img . '.png" alt="*" /> ';
		}
		return $icon . $nick . $icon_medal . $online;
	}
	/**
	 * / 本身，用户组图标
	 */
	public static function avatar($user = 0, $type = 1)
	{
		/*
		* $type == 0 - 将头像和图标一起输出
		* $type == 1 - 只输出头像
		* $type == 2 - 只输出图标
		*/
		global $time, $set;
		$AVATAR = null;
		$icon = null;
		if ($user != 0) $ank = dbassoc(query('SELECT `pol`, `id`, `group_access` FROM `user` WHERE `id` = "' . $user . '" LIMIT 1 '));
		if ($user == 0) $ank = array('id' => '0', 'pol' => '1', 'group_access' => '0');
		elseif (!$ank)  $ank = array('id' => '0', 'pol' => '1', 'group_access' => '0');
		static $avatars = [];
		// Аватар
		if ($type == 0 || $type == 1) {
			if (empty($avatars[$user])) {
				$avatar = dbarray(query("SELECT id,ras FROM `gallery_photo` WHERE `id_user` = '$user' AND `avatar` = '1' LIMIT 1"));
				$avatars[$user] = $avatar;
			} else $avatar = $avatars[$user];
			if (isset($avatar['id']) && test_file(H . 'sys/gallery/50/' . $avatar['id'] . '.' . $avatar['ras']))
				$AVATAR = ' <img class="avatar" src="/photo/photo50/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar" /> ';
			else
				$AVATAR = '<img class="avatar" src="/style/user/avatar.gif" height= "50" width="50" alt="No Avatar" />';
		}
		return $AVATAR;
	}
	/**
	 * / 用户数据采样功能
	 * / 从用户表输出数据
	 * / 并生成一个头像，奖章图标和在线阵列
	 * 返回return
	 */
	static function get_user($ID = 0)
	{
		static $users; // 调用函数后不删除变量
		if ($ID == 0) {
			// бот
			$ank['id'] = 0;
			$ank['nick'] = '系统';
			$ank['level'] = 999;
			$ank['pol'] = 1;
			$ank['group_name'] = '系统机器人';
			$ank['ank_o_sebe'] = '为通知创建';
			return $ank;
		} else {

			$user_id = intval($ID);
			$ank[0] = FALSE;
			if (!isset($ank[$user_id])) {
				$ank[$user_id] = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$user_id' LIMIT 1"));

				if ($ank[$user_id]['id'] != 0) {


					$tmp_us = dbassoc(dbquery("SELECT `level`,`name` AS `group_name` FROM `user_group` WHERE `id` = '" . $users[$user_id]['group_access'] . "' LIMIT 1"));

					if (!isset($tmp_us) or empty($tmp_us['group_name'])) {
						$ank[$user_id]['level'] = 0;
						$ank[$user_id]['group_name'] = '用户';
					} else {
						$ank[$user_id]['level'] = $tmp_us['level'];
						$ank[$user_id]['group_name'] = $tmp_us['group_name'];
					}
				} else $ank[$user_id] = FALSE;
			}
			return $ank[$user_id];
		}
	}
}
