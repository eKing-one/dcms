<?
$set['web'] = false;
//header("Content-type: application/vnd.wap.xhtml+xml");
//header("Content-type: application/xhtml+xml");
header("Content-type: text/html");
/*echo '<?xml version="1.0" encoding="utf-8"?>';
*/
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title><?=$set['title']?></title>
<link rel="shortcut icon" href="/style/themes/<?=$set['set_them']?>/favicon.ico" />
<link rel="stylesheet" href="/style/themes/<?=$set['set_them']?>/style.css?v2" type="text/css" />
</head>
<body>
<div class="body">
<?
if (isset($_SESSION['message']))
{
	echo '<div class="msg">' . $_SESSION['message'] . '</div>';
	$_SESSION['message'] = NULL;
}
if ((setget('header',"index")=="index" and $_SERVER['PHP_SELF'] == '/index.php') or setget('header',"index")=="all" )
{
	?>
	<div class="logo">
	<a href="/"><img src="/style/themes/<?=$set['set_them']?>/logo.png" alt="logo" /></a>
	</div>
	<?
} 
?>