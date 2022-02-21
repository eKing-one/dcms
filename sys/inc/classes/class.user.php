<?
/**
* / Основные пользовательские функции
* / nick() - выводит ник и значок онлайна
* / avatar - выводит аватар и иконку пользователя
* / у всех функций есть параметры что выводить а что нет
*/
class user
{
	/**
	* / Ссылка и Ник юзера
	*/
// все поля пользователя
  public static function user_db($user = 0)
  {
    static $nicks = [];
    if (empty($nicks[$user]))
    {
      $ank = dbassoc(query('SELECT `nick`, `date_last`, `rating`, `browser` FROM `user` WHERE `id` = "' . $user . '" LIMIT 1 '));
      $nicks[$user] = $ank;
    }
    else $ank = $nicks[$user];
  }
	public static function nick($user = 0, $url = 1, $on = 0, $medal = 0)
	{
		/*
		* $url == 0		Выводит только ник
		* $url == 1		Выводит ник с ссылкой на страницу юзера
		* $on  == 1		Выводит рядом с ником значок онлайн
		* $medal == 1	Выводит медальку рядом со значком онлайн
		*/
		static $nicks = [];
		if (empty($nicks[$user]))
    {
      $ank = dbassoc(query('SELECT `nick`, `date_last`, `rating`, `browser` FROM `user` WHERE `id` = "' . $user . '" LIMIT 1 '));
      $nicks[$user] = $ank;
    }
		else $ank = $nicks[$user];
		$nick = null;
		$online = null;
		$icon_medal = null;
		// Вывод ника 
		if ($user == 0)$ank = array('id' => '0', 'nick' => '系统', 'pol' => '1', 'rating' => '0', 'browser' => 'wap', 'date_last' => time());
		elseif (!$ank)$ank = array('id' => '0', 'nick' => '[已删除]', 'pol' => '1', 'rating' => '0', 'browser' => 'wap', 'date_last' => time());
		if ($url == true)
			$nick = ' <a href="/id' . $user . '">' . text($ank['nick']) . '</a> ';
		else
			$nick = text($ank['nick']);
		// Вывод значка онлайн
		if ($user != 0 && $ank['date_last'] > time()-600 && $on == true)
		{
			if ($ank['browser'] == 'wap')
				$online = ' <img src="/style/icons/online.gif" alt="WAP" /> ';
			else
				$online = ' <img src="/style/icons/online_web.gif" alt="WEB" /> ';
		}
		// Вывод медали
		$R = $ank['rating'];
		if ($medal == 1 && $R >= 6)
		{
			if ($R >= 6 && $R <= 11)		{$img = 1;}
			elseif ($R >= 12 && $R <= 19)	{$img = 2;}
			elseif ($R >= 20 && $R <= 27)	{$img = 3;}
			elseif ($R >= 28 && $R <= 37)	{$img = 4;}
			elseif ($R >= 38 && $R <= 47)	{$img = 5;}
			elseif ($R >= 48 && $R <= 59)	{$img = 6;}
			elseif ($R >= 60)				{$img = 7;}
			$icon_medal = ' <img src="/style/medal/' . $img . '.png" alt="*" /> ';
		}
		return $nick . $icon_medal . $online;
	}
	/**
	* / Аватар, иконка группы пользователя
	*/
	public static function avatar($user = 0, $type = 0)
	{
		/*
		* $type == 0 - Выводит аватар и иконку вместе
		* $type == 1 - Выводит только аватар
		* $type == 2 - Выводит только иконку
		*/
		global $time, $set;
		$AVATAR = null;
		$icon = null;
		if ($user != 0) $ank = dbassoc(query('SELECT `pol`, `id`, `group_access` FROM `user` WHERE `id` = "' . $user . '" LIMIT 1 '));
		  if ($user == 0) $ank = array('id' => '0', 'pol' => '1', 'group_access' => '0');
          elseif (!$ank)  $ank = array('id' => '0', 'pol' => '1', 'group_access' => '0');
    static $avatars= [];
		// Аватар
		if ($type == 0 || $type == 1)
		{
      if (empty($avatars[$user]))
      {
        $avatar = dbarray(query("SELECT id,ras FROM `gallery_foto` WHERE `id_user` = '$user' AND `avatar` = '1' LIMIT 1"));
        $avatars[$user] = $avatar;
      }
else $avatar = $avatars[$user];
			if (isset($avatar['id']) && test_file(H.'sys/gallery/50/' . $avatar['id'] . '.' . $avatar['ras']))
				$AVATAR = ' <img class="avatar" src="/foto/foto50/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar" /> ';
			else
				$AVATAR = '<img class="avatar" src="/style/user/avatar.gif" width="50" alt="No Avatar" />';			
		}
    static $icons= [];
		// Иконка пользователя
		if ($type == 0 || $type == 2)
		{
      if (empty($icons[$user]))
      {
        $result = dbresult(query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
        $icons[$user] = $result;
      }
      else $result = $icons[$user];
			if ($result != 0)
			{
				$icon = ' <img src="/style/user/ban.png" alt="*" class="icon" id="icon_group" /> ';
			}
			else 
			{
				if ($ank['group_access'] > 7 && ($ank['group_access'] < 10 || $ank['group_access'] > 14))
				{
					if ($ank['pol'] == 1) 
					$icon = '<img src="/style/user/1.png" alt="*" class="icon" id="icon_group" /> ';
					else
					$icon = '<img src="/style/user/2.png" alt="" class="icon"/> ';
				}
				elseif (($ank['group_access'] > 1 && $ank['group_access'] <= 7) || ($ank['group_access'] > 10 && $ank['group_access'] <= 14))
				{
					if ($ank['pol'] == 1)
						$icon = '<img src="/style/user/3.png" alt="*" class="icon" id="icon_group" /> ';
					else
						$icon = '<img src="/style/user/4.png" alt="*" class="icon" id="icon_group" /> ';
				}
				elseif (isset ($ank['status']) == 0)
				{
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
		* $ID	- ID юзера 
		* $photo - Параметр на выборку аватара
		*/
		global $user;
    static $users;
		$ank=array();
		$ID = (int)$ID; //Определяем ID и $ank
		$ank['group_name'] = null;
		// Если вы авторизованы, и функция вызывает 
		// ваш ID, то просто берем данные из $user
		if ($user['id'] == $ID)
		{
			$ank = $user;
		}
		else
		{
      if (empty($users[$ID]))
      {
        // Иначе выбираем из базы
        $ank = dbassoc(dbquery('SELECT * FROM `user` WHERE `id` = "' . $ID . '" LIMIT 1'));
        $users[$ID] = $ank;
      }
      else $ank = $users[$ID];
		}
		// Если система или неопределенный юзер
		if ($ID == 0) { $ank = array('id' => '0', 'pol' => '1', 'wmid' => '0', 'group_access' => '0', 'level' => '999'); } 
		elseif (!$ank){ $ank = array('id' => '0', 'pol' => '1', 'wmid' => '0', 'group_access' => '0', 'level' => '0'); }
		else 
		{
      static $tmps = [];
      if (empty($tmps[$ID]))
      {
        $tmp_us = dbassoc(dbquery("SELECT `level`,`name` AS `group_name` FROM `user_group` WHERE `id` = '" . $ank['group_access'] . "' LIMIT 1"));
        $tmps[$ID]=$tmp_us;
      }
		else $tmp_us = $tmps[$ID];
			$ank['group_name'] = $tmp_us['group_name'];
			$ank['level'] = $tmp_us['level'];
		}
		// Если поставлен параметр выводить фото
		if ($photo)
		{
      static $avatars = [];
      if (empty($avatars[$ID]))
      {
        // Определяем аватар
        $avatar = dbarray(dbquery("SELECT id,ras FROM `gallery_foto` WHERE `id_user` = '$ID' AND `avatar` = '1' LIMIT 1"));
        $avatars[$ID] = $avatar;
      }
      else $avatar = $avatars[$ID];
			if (test_file(H.'sys/gallery/50/' . $avatar['id'] . '.' . $avatar['ras']))
				$ank['avatar'] = ' <img class="avatar" src="/sys/gallery/50/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar" /> ';
			else
				$ank['avatar'] = ' <img class="avatar" src="/style/user/avatar.gif" width="50" alt="No Avatar" /> ';	
		}
		// Вывод значка онлайн
		if (isset($ank['date_last']) && $ID != 0 && $ank['date_last'] > time()-600)
		{
			if ($ank['browser'] == 'wap')
				$ank['online'] = ' <img src="/style/icons/online.gif" alt="WAP" /> ';
			else
				$ank['online'] = ' <img src="/style/icons/online_web.gif" alt="WEB" /> ';
		}
		else
		{
			$ank['online'] = null;
		}
		// Вывод медали
    if (isset($ank['rating'])) $R = $ank['rating'];
    else $R = 0;
		if ($R >= 6)
		{
			if ($R >= 6 && $R <= 11)		{$img = 1;}
			elseif ($R >= 12 && $R <= 19)	{$img = 2;}
			elseif ($R >= 20 && $R <= 27)	{$img = 3;}
			elseif ($R >= 28 && $R <= 37)	{$img = 4;}
			elseif ($R >= 38 && $R <= 47)	{$img = 5;}
			elseif ($R >= 48 && $R <= 59)	{$img = 6;}
			elseif ($R >= 60)				{$img = 7;}
			$ank['medal'] = ' <img src="/style/medal/' . $img . '.png" alt="*" /> ';
		}
		else
		{
			$ank['medal'] = null;
		}
    static $icons = array ();
		if (empty($icons[$ID]))
    {
      $result = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ID' AND (`time` > '" . time() . "' OR `navsegda` = '1')"), 0);
      $icons[$ID] = $result;
    }
		else $result = $icons[$ID];
		// Иконка пользователя
		if ($result!=0)
		{
			$ank['icon'] = ' <img src="/style/user/ban.png" alt="*" class="icon" id="icon_group" /> ';
		}
		else 
		{
			if ($ank['group_access'] > 7 && ($ank['group_access'] < 10 || $ank['group_access'] > 14))
			{
				if ($ank['pol'] == 1) 
				$ank['icon'] = '<img src="/style/user/2.png" alt="*" class="icon" id="icon_group" /> ';
				else
				$ank['icon'] = '<img src="/style/user/120.png" alt="" class="icon"/> ';
			}
			elseif (($ank['group_access'] > 1 && $ank['group_access'] <= 7) || ($ank['group_access'] > 10 && $ank['group_access'] <= 14))
			{
				if ($ank['pol'] == 1)
					$ank['icon'] = '<img src="/style/user/77.png" alt="*" class="icon" id="icon_group" /> ';
				else
					$ank['icon'] = '<img src="/style/user/118.png" alt="*" class="icon" id="icon_group" /> ';
			}
			else
			{
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
?>