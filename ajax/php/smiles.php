<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
//include_once '../../sys/inc/user.php';

if (!isset($_GET['dir']))
{
	$_SESSION['category'] = 21;
}
else
{
	$_SESSION['category'] = $_GET['dir'];
}

if (!isset($_SESSION['category']) || dbresult(dbquery("SELECT COUNT(*) FROM `smile_dir` WHERE `id`='".intval($_SESSION['category'])."'"),0) == 0)
{	
	// 如果您以前没有看过，请查找类别id

	$category = dbassoc(dbquery("SELECT * FROM `smile_dir` LIMIT 1"));
	$_SESSION['category'] = $category['id'];
}

$q = dbquery("SELECT * FROM `smile` WHERE `dir`='".intval($_SESSION['category'])."' ORDER BY id DESC ");
echo '<div class="layer">';
while($post = dbarray($q))
{
	echo '<a href="javascript:emoticon(\''.$post['smile'].'\')"><img src="/style/smiles/' . $post['id'] . '.gif" title="' . text($post['smile']) . '" /></a>';
}
echo '</div>';

$q = dbquery("SELECT * FROM `smile_dir` ORDER BY id ASC");
echo '<div class="title">分类</div>';
while ($dir = dbassoc($q))
{
	echo '<a onclick="showContent2(\'/ajax/php/smiles.php?dir='.$dir['id'].'\')" class="onclick">' . text($dir['name']) . '</a> ';
	echo '('.dbresult(dbquery("SELECT COUNT(*) FROM `smile` WHERE `dir` = '$dir[id]'"),0).') ';
}
?>