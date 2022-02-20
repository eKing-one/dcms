<?
$set['web'] = false;
//header("Content-type: application/vnd.wap.xhtml+xml");
header("Content-type: application/xhtml+xml; charset=utf-8");
//header("Content-type: text/html");
echo '<?xml version="1.0" encoding="utf-8"?>';
?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
<title><?echo $set['title'];?></title>
<link rel="shortcut icon" href="/style/themes/<?echo $set['set_them'];?>/favicon.ico" />
<link rel="stylesheet" href="/style/themes/<?echo $set['set_them'];?>/style.css" type="text/css" />
<link rel="alternate" title="新闻 RSS" href="/news/rss.php" type="application/rss+xml" />
</head>
<body>
<?
if ($_SERVER['PHP_SELF']=='/index.php')
{
echo "<div class='logo'>";
echo "<img src='/style/themes/$set[set_them]/graph/logo.png' alt='' />";
echo "</div>";
}
?>