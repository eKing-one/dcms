<?
$set = array(); // массив с настройками
$set_default = array();
$set_dinamic = array();
$set_replace = array();

// загрузка настроек по умолчанию. Позволяет исключить отсутствие неопределенных переменных
$default = @parse_ini_file(H.'sys/dat/default.ini',true);
$set_default = @$default['DEFAULT'];
$set_replace = @$default['REPLACE'];

if ($fset = @file_get_contents(H.'sys/dat/settings_6.2.dat'))
{
	$set_dinamic = unserialize($fset);
}
elseif (file_exists(H.'install/index.php'))
{
	header("Location: /install/");
	exit;
}

$set = @array_merge ($set_default, $set_dinamic, $set_replace);
if ($set['show_err_php'])
{
	error_reporting(E_ALL); // включаем показ ошибок
	ini_set('display_errors',true); // включаем показ ошибок
}

if (isset($_SERVER["HTTP_USER_AGENT"]) && preg_match('#up-browser|blackberry|windows ce|symbian|palm|nokia#i', $_SERVER["HTTP_USER_AGENT"]))
$webbrowser = false;
elseif (isset($_SERVER["HTTP_USER_AGENT"]) && (preg_match('#windows#i', $_SERVER["HTTP_USER_AGENT"]) ||preg_match('#linux#i', $_SERVER["HTTP_USER_AGENT"]) ||preg_match('#bsd#i', $_SERVER["HTTP_USER_AGENT"]) ||preg_match('#x11#i', $_SERVER["HTTP_USER_AGENT"]) ||preg_match('#unix#i', $_SERVER["HTTP_USER_AGENT"]) ||preg_match('#macos#i', $_SERVER["HTTP_USER_AGENT"]) ||preg_match('#macintosh#i', $_SERVER["HTTP_USER_AGENT"])))
$webbrowser = true;else $webbrowser = false; // определение типа браузера

$set['web'] = false;
?>