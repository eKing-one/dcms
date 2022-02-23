<?

/**
* 主要用户功能
*  nick()-显示昵称和在线图标
* 头像-显示头像和用户图标
* 所有函数都有参数输出什么和不输出什么
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
		* $url == 0		只输出尼克
		* $url == 1		输出引用到用户页的 Nick
		* $on  == 1		在线显示 Nick 旁边的图标
		* $medal == 1	在线输出图标旁边的奖牌
		*/
		static $nicks = [];
		if (empty($nicks[$user])) {
			$ank = dbassoc(query('SELECT `nick`, `date_last`, `rating`, `browser` FROM `user` WHERE `id` = "' . $user . '" LIMIT 1 '));
			$nicks[$user] = $ank;
		} else $ank = $nicks[$user];
		$nick = null;
		$online = null;
		$icon_medal = null;
		// 尼克引线
		if ($user == 0) $ank = array('id' => '0', 'nick' => '系统', 'pol' => '1', 'rating' => '0', 'browser' => 'wap', 'date_last' => time());
		elseif (!$ank) $ank = array('id' => '0', 'nick' => '[已删除]', 'pol' => '1', 'rating' => '0', 'browser' => 'wap', 'date_last' => time());
		if ($url == true)
			$nick = ' <a href="/id' . $user . '">' . text($ank['nick']) . '</a> ';
		else
			$nick = text($ank['nick']);
		// 联机图标输出
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
		return $nick . $icon_medal . $online;
	}
	/**
	 * / 化身，用户组图标
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
				$avatar = dbarray(query("SELECT id,ras FROM `gallery_foto` WHERE `id_user` = '$user' AND `avatar` = '1' LIMIT 1"));
				$avatars[$user] = $avatar;
			} else $avatar = $avatars[$user];
			if (isset($avatar['id']) && test_file(H . 'sys/gallery/50/' . $avatar['id'] . '.' . $avatar['ras']))
				$AVATAR = ' <img class="avatar" src="/foto/foto50/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar" /> ';
			else
				$AVATAR = '<img class="avatar" src="/style/user/avatar.gif" height= "50" width="50" alt="No Avatar" />';
		}
		static $icons = [];
		// Иконка пользователя
		if ($type == 0 || $type == 2) {
			if (empty($icons[$user])) {
				$result = dbresult(query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
				$icons[$user] = $result;
			} else $result = $icons[$user];
			if ($result != 0) {
				$icon = ' <img src="/style/user/ban.png" alt="*" class="icon" id="icon_group" /> ';
			} else {
				if ($ank['group_access'] > 7 && ($ank['group_access'] < 10 || $ank['group_access'] > 14)) {
					if ($ank['pol'] == 1)
						$icon = '<img src="/style/user/1.png" alt="*" class="icon" id="icon_group" /> ';
					else
						$icon = '<img src="/style/user/2.png" alt="" class="icon"/> ';
				} elseif (($ank['group_access'] > 1 && $ank['group_access'] <= 7) || ($ank['group_access'] > 10 && $ank['group_access'] <= 14)) {
					if ($ank['pol'] == 1)
						$icon = '<img src="/style/user/3.png" alt="*" class="icon" id="icon_group" /> ';
					else
						$icon = '<img src="/style/user/4.png" alt="*" class="icon" id="icon_group" /> ';
				} elseif (isset($ank['status']) == 0) {
					if ($ank['pol'] == 1)
						$icon = '<img src="/style/user/5.png" alt="" class="icon" id="icon_group" /> ';
					else
						$icon = '<img src="/style/user/6.png" alt="" class="icon" id="icon_group" /> ';
				}
			}
		}
		return $AVATAR . $icon;
	}
	/**
	 * / 用户数据采样功能
	 * / 从用户表输出数据
	 * / 并生成一个头像，奖章图标和在线阵列
	 * $ank['link'], $ank['avatar'], $ank['online'], 
	 * $ank['medal'], $ank['icon']
	 */
	static function get_user($ID = 0, $photo = 1)
	{
		/*
		* $ID	- 用户 ID
		* $photo - 化身选择参数
		*/
		global $user;
		static $users;
		$ank = array();
		$ID = (int)$ID; //定义 ID 和 $ank
		$ank['group_name'] = null;
		// 如果你被授权并且函数调用
		// 你的 ID，你只需要从 $user 中提取数据
		if ($user['id'] == $ID) {
			$ank = $user;
		} else {
			if (empty($users[$ID])) {
				// 否则我们就从基地里挑出来
				$ank = dbassoc(dbquery('SELECT * FROM `user` WHERE `id` = "' . $ID . '" LIMIT 1'));
				$users[$ID] = $ank;
			} else $ank = $users[$ID];
		}
		// 如果系统或未定义用户
		if ($ID == 0) {
			$ank = array('id' => '0','nick' => '系统','level' => '999','pol' => '1','group_name' => '系统机器人','ank_o_sebe' => '为通知创建');
		} elseif (!$ank) {
			$ank = array('id' => '0', 'nick' => '未知用户', 'level' => '0', 'pol' => '1', 'group_name' => '未知用户', 'ank_o_sebe' => '未知用户');
		} else {
			static $tmps = [];
			if (empty($tmps[$ID])) {
				$tmp_us = dbassoc(dbquery("SELECT `level`,`name` AS `group_name` FROM `user_group` WHERE `id` = '" . $ank['group_access'] . "' LIMIT 1"));
				$tmps[$ID] = $tmp_us;
			} else $tmp_us = $tmps[$ID];
			$ank['group_name'] = $tmp_us['group_name'];
			$ank['level'] = $tmp_us['level'];
		}
		// 如果设置了“打印照片”选项
		if ($photo) {
			static $avatars = [];
			if (empty($avatars[$ID])) {
				// 识别化身
				$avatar = dbarray(dbquery("SELECT id,ras FROM `gallery_foto` WHERE `id_user` = '$ID' AND `avatar` = '1' LIMIT 1"));
				$avatars[$ID] = $avatar;
			} else $avatar = $avatars[$ID];
			if (test_file(H . 'sys/gallery/50/' . $avatar['id'] . '.' . $avatar['ras']))
				$ank['avatar'] = ' <img class="avatar" src="/sys/gallery/50/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar" /> ';
			else
				$ank['avatar'] = ' <img class="avatar" src="/style/user/avatar.gif" width="50" alt="No Avatar" /> ';
		}
		// 联机图标输出
		if (isset($ank['date_last']) && $ID != 0 && $ank['date_last'] > time() - 600) {
			if ($ank['browser'] == 'wap')
				$ank['online'] = ' <img src="/style/icons/online.gif" alt="WAP" /> ';
			else
				$ank['online'] = ' <img src="/style/icons/online_web.gif" alt="WEB" /> ';
		} else {
			$ank['online'] = null;
		}
		// 奖牌输出
		if (isset($ank['rating'])) $R = $ank['rating'];
		else $R = 0;
		if ($R >= 6) {
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
			$ank['medal'] = ' <img src="/style/medal/' . $img . '.png" alt="*" /> ';
		} else {
			$ank['medal'] = null;
		}
		static $icons = array();
		if (empty($icons[$ID])) {
			$result = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ID' AND (`time` > '" . time() . "' OR `navsegda` = '1')"), 0);
			$icons[$ID] = $result;
		} else $result = $icons[$ID];
		// 用户图标
		if ($result != 0) {
			$ank['icon'] = ' <img src="/style/user/ban.png" alt="*" class="icon" id="icon_group" /> ';
		} else {
			if ($ank['group_access'] > 7 && ($ank['group_access'] < 10 || $ank['group_access'] > 14)) {
				if ($ank['pol'] == 1)
					$ank['icon'] = '<img src="/style/user/2.png" alt="*" class="icon" id="icon_group" /> ';
				else
					$ank['icon'] = '<img src="/style/user/120.png" alt="" class="icon"/> ';
			} elseif (($ank['group_access'] > 1 && $ank['group_access'] <= 7) || ($ank['group_access'] > 10 && $ank['group_access'] <= 14)) {
				if ($ank['pol'] == 1)
					$ank['icon'] = '<img src="/style/user/77.png" alt="*" class="icon" id="icon_group" /> ';
				else
					$ank['icon'] = '<img src="/style/user/118.png" alt="*" class="icon" id="icon_group" /> ';
			} else {
				if ($ank['pol'] == 1)
					$ank['icon'] = '<img src="/style/user/23.png" alt="" class="icon" id="icon_group" /> ';
				else
					$ank['icon'] = '<img src="/style/user/117.png" alt="" class="icon" id="icon_group" /> ';
			}
		}
		if (isset($ank['link'])) 	$ank['link'] = ' <a href="/id' . $ID . '">' . text($ank['nick']) . '</a> ';
		else $ank['link'] = null;
		if (isset($ank['nick'])) $ank['nick'] = text($ank['nick']);
		else $ank['nick'] = null;
		return $ank;
	}
}
