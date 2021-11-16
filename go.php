<?
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
$set['title']='重定向';
include_once 'sys/inc/thead.php';
title();
if (!isset($_GET['go']) || (dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `id` = '".intval($_GET['go'])."'"),0)==0 && !preg_match('#^https?://#',@base64_decode($_GET['go']))))
{
header("Location: index.php?".SID);
exit;
}
if (preg_match('#^(ht|f)tps?://#',base64_decode($_GET['go'])))
{
if (isset($_SESSION['adm_auth']))unset($_SESSION['adm_auth']);
header("Location: ".base64_decode($_GET['go']));
exit;
}
else
{
$rekl=dbassoc(dbquery("SELECT * FROM `rekl` WHERE `id` = '".intval($_GET['go'])."'"));
dbquery("UPDATE `rekl` SET `count` = '".($rekl['count']+1)."' WHERE `id` = '$rekl[id]'");
if (isset($_SESSION['adm_auth']))unset($_SESSION['adm_auth']);
header("Refresh: 2; url=$rekl[link]");
echo "对于广告资源的内容<br />";
echo "地盘行政 ".strtoupper($_SERVER['HTTP_HOST'])." 不承担责任。<br />";
echo "<b><a href=\"$rekl[link]\">过渡期</a></b><br />";
echo "过渡时期: $rekl[count]<br />";
}
include_once 'sys/inc/tfoot.php';
?>