<?
$set['web'] = false;
//header("Content-type: application/vnd.wap.xhtml+xml");
//header("Content-type: application/xhtml+xml");
header("Content-type: text/html");
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>

<title>
<?=$set['title']?>
</title>

<link rel="shortcut icon" href="/style/themes/<?=$set['set_them']?>/favicon.ico" />
<link rel="stylesheet" href="/style/themes/<?=$set['set_them']?>/style.css" type="text/css" />
</head>
<body>
<div class="body">
<?
if (isset($_SESSION['message']))
{
	echo '<div class="msg">' . $_SESSION['message'] . '</div>';
	$_SESSION['message'] = NULL;
}

if ($_SERVER['PHP_SELF'] == '/index.php')
{
	?>
	<center>
	<div class="logo">
	<img src="/style/themes/<?=$set['set_them']?>/logo.png" alt="logo" /><br />
	<?=$set['title']?>
	</div>
	</center>
	<?
} 
?>