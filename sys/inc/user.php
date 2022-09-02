<?
/*
-----------------------------------------------------------------
加载类
-----------------------------------------------------------------
*/

require 'classes/class.user.php';



// Определение юзера
if (isset($_SESSION['id_user']) && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$_SESSION[id_user]' LIMIT 1"), 0) == 1) {
	$user = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $_SESSION[id_user] LIMIT 1"));
	dbquery("UPDATE `user` SET `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
	$user['type_input'] = 'session';
} elseif (!isset($input_page) && isset($_COOKIE['id_user']) && isset($_COOKIE['pass']) && $_COOKIE['id_user'] && $_COOKIE['pass']) {
	if (!isset($_POST['token'])) {
		header("Location: /login.php?return=" . urlencode($_SERVER['REQUEST_URI']) . "&$passgen");
		exit;
	}
}


if (!isset($_SERVER['HTTP_REFERER']))
	$_SERVER['HTTP_REFERER'] = '/index.php';

// если аккаунт не активирован
if (isset($user['activation']) && $user['activation'] != NULL) {
	$err[] = '您需要使用发送到注册时指定的电子邮件的链接激活您的帐户';
	unset($user);
}


if (isset($user)) {
	$tmp_us = dbassoc(dbquery("SELECT `level` FROM `user_group` WHERE `id` = '$user[group_access]' LIMIT 1"));
	if (isset($tmp_us['level'])) $user['level'] = $tmp_us['level'];
	else $user['level'] = 0;
	$timeactiv  =  time() - $user['date_last'];

	if ($timeactiv < 120) {
		$newtimeactiv = $user['time'] + $timeactiv;
		dbquery("UPDATE `user` SET `time` ='$newtimeactiv' WHERE `id` = '$user[id]' LIMIT 1");
	}

	if (isset($user['type_input']) && isset($_SERVER['HTTP_REFERER']) && !preg_match('#' . preg_quote($_SERVER['HTTP_HOST']) . '#', $_SERVER['HTTP_REFERER']) && preg_match('#^https?://#i', $_SERVER['HTTP_REFERER']) && $ref = @parse_url($_SERVER['HTTP_REFERER'])) {
		if (isset($ref['host'])) {
			if (dbresult(dbquery("SELECT COUNT(*) FROM `user_ref` WHERE `id_user` = '$user[id]' AND `url` = '" . my_esc($ref['host']) . "'"), 0) == 0)
				dbquery("INSERT INTO `user_ref` (`time`, `id_user`, `type_input`, `url`) VALUES ('$time', '$user[id]', '$user[type_input]', '" . my_esc($ref['host']) . "')");
			else
				dbquery("UPDATE `user_ref` SET `time` = '$time' WHERE `id_user` = '$user[id]' AND `url` = '" . my_esc($ref['host']) . "'");
		}
	}

	// Время обновления чата
	if ($user['set_time_chat'] != NULL)
		$set['time_chat'] = $user['set_time_chat'];

	// Постраничная навигация
	if ($user['set_p_str'] != NULL)
		$set['p_str'] = $user['set_p_str'];

	// Режим иконок
	$set['set_show_icon'] = $user['set_show_icon'];


	if (!isset($banpage)) // бан пользователя
	{
		if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'all' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
			header('Location: /ban.php?' . SID);
			exit;
		}
	}

	/*
	========================================
	Создание настроек юзера 
	========================================
	*/

	if (dbresult(dbquery("SELECT COUNT(*) FROM `user_set` WHERE `id_user` = '$user[id]'"), 0) == 0)
		dbquery("INSERT INTO `user_set` (`id_user`) VALUES ('$user[id]')");

	if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions_set` WHERE `id_user` = '$user[id]'"), 0) == 0)
		dbquery("INSERT INTO `discussions_set` (`id_user`) VALUES ('$user[id]')");

	if (dbresult(dbquery("SELECT COUNT(*) FROM `tape_set` WHERE `id_user` = '$user[id]'"), 0) == 0)
		dbquery("INSERT INTO `tape_set` (`id_user`) VALUES ('$user[id]')");

	if (dbresult(dbquery("SELECT COUNT(*) FROM `notification_set` WHERE `id_user` = '$user[id]'"), 0) == 0)
		dbquery("INSERT INTO `notification_set` (`id_user`) VALUES ('$user[id]')");

	// Записываем url 
	dbquery("UPDATE `user` SET `url` = '" . my_esc($_SERVER['SCRIPT_NAME']) . "' WHERE `id` = '$user[id]' LIMIT 1");

	// для web темы
	if ($webbrowser) {
		if (is_dir(H . 'style/themes/' . $user['set_them2']))
			$set['set_them'] = $user['set_them2'];
		else
			dbquery("UPDATE `user` SET `set_them2` = '$set[set_them]' WHERE `id` = '$user[id]' LIMIT 1");
	} else {
		if (is_dir(H . 'style/themes/' . $user['set_them'])) $set['set_them'] = $user['set_them'];
		else dbquery("UPDATE `user` SET `set_them` = '$set[set_them]' WHERE `id` = '$user[id]' LIMIT 1");
	}

	// Пишем ip пользователя
	if (isset($ip2['add'])) dbquery("UPDATE `user` SET `ip` = " . ip2long($ip2['add']) . " WHERE `id` = '$user[id]' LIMIT 1");
	else dbquery("UPDATE `user` SET `ip` = null WHERE `id` = '$user[id]' LIMIT 1");
	if (isset($ip2['cl'])) dbquery("UPDATE `user` SET `ip_cl` = " . ip2long($ip2['cl']) . " WHERE `id` = '$user[id]' LIMIT 1");
	else dbquery("UPDATE `user` SET `ip_cl` = null WHERE `id` = '$user[id]' LIMIT 1");
	if (isset($ip2['xff'])) dbquery("UPDATE `user` SET `ip_xff` = " . ip2long($ip2['xff']) . " WHERE `id` = '$user[id]' LIMIT 1");
	else dbquery("UPDATE `user` SET `ip_xff` = null WHERE `id` = '$user[id]' LIMIT 1");
	if ($ua) dbquery("UPDATE `user` SET `ua` = '" . my_esc($ua) . "' WHERE `id` = '$user[id]' LIMIT 1");

	// Непонятная сессия
	dbquery("UPDATE `user` SET `sess` = '$sess' WHERE `id` = '$user[id]' LIMIT 1");

	// Тип браузера
	dbquery("UPDATE `user` SET `browser` = '" . ($webbrowser == true ? "web" : "wap") . "' WHERE `id` = '$user[id]' LIMIT 1");

	// Проверяем на схожие ники
	$collision_q = dbquery("SELECT * FROM `user` WHERE `ip` = '$iplong' AND `ua` = '" . my_esc($ua) . "' AND `date_last` > '" . (time() - 600) . "' AND `id` <> '$user[id]'");

	while ($collision = dbassoc($collision_q)) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `user_collision` WHERE `id_user` = '$user[id]' AND `id_user2` = '$collision[id]' OR `id_user2` = '$user[id]' AND `id_user` = '$collision[id]'"), 0) == 0)
			dbquery("INSERT INTO `user_collision` (`id_user`, `id_user2`, `type`) values('$user[id]', '$collision[id]', 'ip_ua_time')");
	}


	/*
	========================================
	回复内核 > v.1.7.4
	========================================
	*/

	if (!isset($insert))
		$insert = null;

	if (isset($_GET['response']) && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['response']) . "'"), 0) == 1) {
		$ank_reply = dbassoc(dbquery("SELECT nick,id FROM `user` WHERE `id` = '" . intval($_GET['response']) . "' LIMIT 1"));
		$insert = user::nick($ank_reply['id'], 1, 0, 0) . ', ';
		$go_link = '?' . $passgen . '&amp;response=' . $ank_reply['id'];
	} else {
		$go_link = NULL;
	}

	// 响应时链接
	define("REPLY", $go_link);
} else {
	// 嘉宾主题
	if ($webbrowser)
		$set['set_them'] = $set['set_them2'];

	// 嘉宾:
	if ($ip && $ua) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `ip` = '$iplong' AND `ua` = '" . my_esc($ua) . "' LIMIT 1"), 0) == 1) {
			$guests = dbassoc(dbquery("SELECT * FROM `guests` WHERE `ip` = '$iplong' AND `ua` = '" . my_esc($ua) . "' LIMIT 1"));
			dbquery("UPDATE `guests` SET `date_last` = " . time() . ", `url` = '" . my_esc($_SERVER['SCRIPT_NAME']) . "', `pereh` = '" . ($guests['pereh'] + 1) . "' WHERE `ip` = '$iplong' AND `ua` = '" . my_esc($ua) . "' LIMIT 1");
		} else {
			dbquery("INSERT INTO `guests` (`ip`, `ua`, `date_aut`, `date_last`, `url`) VALUES ('$iplong', '" . my_esc($ua) . "', '" . time() . "', '" . time() . "', '" . my_esc($_SERVER['SCRIPT_NAME']) . "')");
		}
	}
	unset($access);
}


if (!isset($user) || $user['level']  ==  0) {
	@error_reporting(0);
	@ini_set('display_errors', false); // показ ошибок
	if (function_exists('set_time_limit')) @set_time_limit(20); // Ставим ограничение на 20 сек
}

if (!isset($user) && $set['guest_select']  ==  '1' && !isset($show_all) && $_SERVER['PHP_SELF'] != '/index.php') {
	header("Location: /aut.php");
	exit;
}

if (isset($user)) {
	$user_gr = dbassoc(dbquery("SELECT * FROM `user_group` WHERE `id` = $user[group_access] LIMIT 1"));
	$user['group_name'] = $user_gr['name'];
	if (isset($_GET['sess_abuld']) && $_GET['sess_abuld']  ==  1) // Продолжаем просмотр файла с меткой 18+
	{
		$_SESSION['abuld'] = 1;
	}

	if (isset($_SESSION['abuld']) && $_SESSION['abuld']  ==  1)
		$user['abuld'] = 1;
}

/*
========================================
Смена тем для гостей
========================================
*/

if (isset($_GET['t']) && $_GET['t'] == 'wap' && !isset($user)) {
	$_SESSION['guest_theme'] = 'wap';
	header('Location: ' . htmlspecialchars($_SERVER['HTTP_REFERER']));
	exit;
} elseif (isset($_GET['t']) && $_GET['t'] == 'web' && !isset($user)) {
	$_SESSION['guest_theme'] = 'web';
	header('Location: ' . htmlspecialchars($_SERVER['HTTP_REFERER']));
	exit;
}

if (isset($_SESSION['guest_theme']) && $_SESSION['guest_theme'] == 'web' && !isset($user)) {
	$set['set_them'] = 'web';
	$set['set_them2'] = 'web';
} elseif (isset($_SESSION['guest_theme']) && $_SESSION['guest_theme'] == 'wap' && !isset($user)) {
	$set['set_them'] = 'default';
	$set['set_them2'] = 'default';
}

/*
========================================
Смена тем для юзеров папки wap и web
========================================
*/

if (isset($user) && isset($_GET['t'])) {
	if ($webbrowser == 'WEB') {
		$set_t = 'set_them2';
	} else {
		$set_t = 'set_them';
	}

	$wap = 'default';
	$web = 'web';

	if ($_GET['t'] == 'wap')
		dbquery("update `user` set `$set_t` = '$wap' where `id`='$user[id]' limit 1");
	elseif ($_GET['t'] == 'web')
		dbquery("update `user` set `$set_t` = '$web' where `id`='$user[id]' limit 1");
	header('Location: ' . htmlspecialchars($_SERVER['HTTP_REFERER']));
	exit;
}


/*
========================================
Сортировка списка по времени
========================================
*/

if (isset($user) && isset($_GET['sort']) && ($_GET['sort'] == '0' || $_GET['sort'] == '1')) {
	dbquery("update `user` set `sort` = 'my_esc($_GET[sort])' where `id` = '$user[id]' limit 1");
	header('Location: ' . htmlspecialchars($_SERVER['HTTP_REFERER']));
}


if (isset($user)) $sort = ($user['sort'] == 1 ? ' ASC ' : ' DESC ');
else $sort = 'DESC';

// Страницы 
if (isset($user) && $user['sort']  ==  1)
	$pageEnd = 'end';
else $pageEnd = '1';

/*
========================================
Ответы в комм [DELETE]
========================================
*/

if (isset($user) && isset($_GET['response'])) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['response']) . "'"), 0) == 1) {
		$id_response = intval($_GET['response']);
		$ank_response = user::get_user($id_response);
	} else {
		$id_response = NULL;
		$ank_response = NULL;
	}
}

if (isset($_GET['go_otv']) && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['go_otv']) . "'"), 0) == 1) {
	$otv_ank = intval($_GET['go_otv']);
	$ank_otv = user::get_user($otv_ank);
	$respons = TRUE;
} else {
	$otv_ank = NULL;
	$ank_otv = NULL;
	$respons = false;
}
if (isset($_GET['response'])) {
	$otvet = "$ank_response[nick], ";
	$go_otv = '?' . $passgen . '&amp;go_otv=' . $ank_response['id'] . '';
} else {
	$otvet = NULL;
	$go_otv = NULL;
}

if (isset($_GET['response'])) {
	$id_response = intval($_GET['response']);
	$ank_response = user::get_user($id_response);
	$respons_msg = '' . $ank_response['nick'] . ', ';
} else {
	$id_response = NULL;
	$ank_response = NULL;
	$respons_msg = NULL;
}

/*
========================================
Скрытие новости
========================================
*/

if (isset($user) && isset($_GET['news_read'])) {
	dbquery("update `user` set `news_read` = '1' where `id` = '$user[id]' limit 1");
	$_SESSION['message'] = "该消息已成功隐藏"; // Оповещаем
	header("Location: /?");
	exit;
}

/*
========================================
Панель навигации над полем ввода
========================================
*/

$tPanel = "<div id='comments' class='tpanel'>
<div class='tmenu'><a href='/plugins/smiles/'>表情符号</a></div>
<div class='tmenu'><a href='/plugins/rules/bb-code.php'>标签</a></div>
</div>";

/*
========================================
Причины бана
========================================
*/

$pBan[0] = "其他";
$pBan[1] = "垃圾邮件/广告";
$pBan[2] = "欺诈行为";
$pBan[3] = "淫亵用语";
$pBan[4] = "克隆昵称";
$pBan[5] = "煽动、挑衅和煽动侵略";
$pBan[6] = "洪水泛滥";
$pBan[7] = "火焰";

/*
========================================
Раздел бана
========================================
*/

$rBan['all'] = "整个网站";
$rBan['notes'] = "日记";
$rBan['forum'] = "论坛";
$rBan['files'] = "档案";
$rBan['guest'] = "客人";
$rBan['chat'] = "聊天";
$rBan['lib'] = "图书馆";
$rBan['photo'] = "照片";

/*
========================================
Сообщение в комментариях
========================================
*/

$banMess = '[red]这条消息去蒸汽与作者在洗澡！[/red]';

if (isset($_POST['msg']) && !isset($user)) {
	echo "您没有登录!";
	exit;
}



/*
========================================
Валюта
========================================
*/

$sMonet[0] = '硬币';
$sMonet[1] = '硬币';
$sMonet[2] = '硬币';


// Загрузка остальных плагинов из папки "sys/inc/plugins"
$opdirbase = opendir(H . 'sys/inc/plugins');

while ($filebase = readdir($opdirbase)) {
	if (preg_match('#\.php$#i', $filebase)) {
		require_once(check_replace(H . 'sys/inc/plugins/' . $filebase));
	}
}


if ($_SERVER["REQUEST_URI"] == "/" or $_SERVER["REQUEST_URI"] == "/index.php") {

	if (!empty(setget('main', "")) and setget('main', "") != "index" and setget('main', "") != "index.php") {

		header("Location: " . setget('main', ""));
		exit();
	}
}

if (empty(setget('job', 1))) {
	if (((isset($user) and $user['level'] < 5) or (!isset($user)))  and  $_SERVER["PHP_SELF"] != "/aut.php" and $_SERVER["PHP_SELF"] != "/login.php" and  $_SERVER["PHP_SELF"] != "/exit.php" and  $_SERVER["PHP_SELF"] != "/pass.php")
		exit("技术工作正在进行中");
}
