<?
$ipa = false;
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']!='127.0.0.1' && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['HTTP_X_FORWARDED_FOR']))
{
	$ip2['xff'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$ipa[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
if(isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP']!='127.0.0.1' && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['HTTP_CLIENT_IP']))
{
	$ip2['cl'] = $_SERVER['HTTP_CLIENT_IP'];
	$ipa[] = $_SERVER['HTTP_CLIENT_IP'];
}
if(isset($_SERVER['REMOTE_ADDR']) && preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_SERVER['REMOTE_ADDR']))
{
	$ip2['add'] = $_SERVER['REMOTE_ADDR'];
	$ipa[] = $_SERVER['REMOTE_ADDR'];
}

$ip = $ipa[0];

$iplong = ip2long($ip);

if (isset($_SERVER['HTTP_USER_AGENT']))
{
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$ua = strtok($ua, '/');
	$ua = strtok($ua, '('); // оставляем только то, что до скобки
	$ua = preg_replace('#[^a-z_\./ 0-9\-]#iu', null, $ua); // вырезаем все "левые" символы

	// Опера мини тоже посылает данные о телефоне :)
	if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && preg_match('#Opera#i',$ua))
	{
		$ua_om = $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'];
		$ua_om = strtok($ua_om, '/');
		$ua_om = strtok($ua_om, '(');
		$ua_om = preg_replace('#[^a-z_\. 0-9\-]#iu', null, $ua_om);
		$ua = 'Opera Mini ('.$ua_om.')';
	}

}
else $ua = 'Нет данных';
?>