<?
require 'mysql2i.class.php';
$err = NULL;
$db=NULL;
$time=NULL;
$passgen=NULL;
$sess=NULL;
$ip=NULL;
$ua=NULL;
$iplong=NULL;
$webbrowser=NULL;
$tpanel=NULL;
if (function_exists('error_reporting'))@error_reporting(0); // отключаем показ ошибок
// Ставим ограничение для выполнения скрипта на 60 сек
if (function_exists('set_time_limit'))@set_time_limit(60);
if (function_exists('ini_set'))
{
  
ini_set('display_errors',false); // отключаем показ ошибок
ini_set('register_globals', false); // вырубаем глобальные переменные
ini_set('session.use_cookies', true); // используем куки для сессий
ini_set('session.use_trans_sid', true); // используем url для передачи сессий
ini_set('arg_separator.output', "&amp;"); // разделитель переменных в url (для соответствия с xml)

  }


// принудительно вырубаем глобальные переменные
if (ini_get('register_globals')) {
  $allowed = array('_ENV' => 1, '_GET' => 1, '_POST' => 1, '_COOKIE' => 1, '_FILES' => 1, '_SERVER' => 1, '_REQUEST' => 1, 'GLOBALS' => 1);
  foreach ($GLOBALS as $key => $value) {
    if (!isset($allowed[$key])) {
      unset($GLOBALS[$key]);
    }
  }
}

list($msec, $sec) = explode(chr(32), microtime()); // время запуска скрипта
$conf['headtime'] = $sec + $msec;
$time=time();





$phpvervion=explode('.', phpversion());
$conf['phpversion']=$phpvervion[0];


$upload_max_filesize=ini_get('upload_max_filesize');
if (preg_match('#([0-9]*)([a-z]*)#i',$upload_max_filesize,$varrs))
{
if ($varrs[2]=='M')$upload_max_filesize=$varrs[1]*1048576;
elseif ($varrs[2]=='K')$upload_max_filesize=$varrs[1]*1024;
elseif ($varrs[2]=='G')$upload_max_filesize=$varrs[1]*1024*1048576;
}

function fiera($msg){
	$msg=str_replace("script","sсript",$msg);
	$msg=str_replace("javаscript:","javаscript:",$msg);
if ($_SERVER['PHP_SELF']!='/adm_panel/mysql.php')
	$msg=addslashes(stripslashes(trim($msg)));
return $msg;
}
 // Полночь
$ftime = mktime(0, 0, 0);

$replace_file = replace_file($_SERVER["PHP_SELF"]);
if (is_file($replace_file))
{
  include_source_once($replace_file);
  exit();
}

function replace_file ($file)
{
  return H."/replace".$file;
}

?>