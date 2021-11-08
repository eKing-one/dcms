<?
if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && !preg_match('#(rules)|(smiles)|(secure)|(aut)|(reg)|(umenu)|(zakl)|(mail)|(anketa)|(settings)|(avatar)|(info)\.php#',$_SERVER['SCRIPT_NAME']))
$_SESSION['refer'] = NULL;

function otkuda($ref)
{
	if (preg_match('#^/forum/#', $ref))
		$mesto = ' сидит в <a href="/forum/">форуме</a> ';
	elseif (preg_match('#^/chat/#', $ref))
		$mesto = ' сидит в <a href="/chat/">чате</a> ';
	elseif (preg_match('#^/news/#', $ref))
		$mesto = ' читает <a href="/news/">新闻中心</a> ';
	elseif (preg_match('#^/guest/#', $ref))
		$mesto = ' пишет в <a href="/guest/">гостевой</a> ';
	elseif (preg_match('#^/user/users\.php#', $ref))
		$mesto = ' cмотрит в <a href="/user/users.php">обитателей</a> ';
	elseif (preg_match('#^/online\.php#', $ref))
		$mesto = ' cмотрит кто <a href="/online.php">онлайн</a> ';
	elseif (preg_match('#^/online_g\.php#', $ref))
		$mesto = ' cмотрит кто в <a href="/online_g.php">гостях</a> ';
	elseif (preg_match('#^/reg\.php#', $ref))
		$mesto = ' хочет <a href="/reg.php">зарегистрироваться</a> ';
	elseif (preg_match('#^/obmen/#', $ref))
		$mesto = ' cидит в <a href="/obmen/">зоне обмена</a> ';
	elseif (preg_match('#^/aut\.php#', $ref))
		$mesto = ' хочет <a href="/aut.php">авторизоваться</a> ';
	elseif (preg_match('#^/index\.php#', $ref))
		$mesto = ' на <a href="/index.php">главной</a> ';
	elseif (preg_match('#^/\??$#', $ref))
		$mesto = ' на <a href="/index.php">главной</a> ';
	else
		$mesto = ' где то <a href="/index.php">на сайте</a> ';

	return $mesto;
}


?>