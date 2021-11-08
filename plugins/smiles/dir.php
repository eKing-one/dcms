<?php
/**
 * & CMS Name :: DCMS-Social
 * & Author   :: Alexandr Andrushkin
 * & Contacts :: ICQ 587863132
 * & Site     :: http://dcms-social.ru
 */
include_once '../../sys/inc/home.php';
include_once H.'sys/inc/start.php';
include_once H.'sys/inc/compress.php';
include_once H.'sys/inc/sess.php';
include_once H.'sys/inc/settings.php';
include_once H.'sys/inc/db_connect.php';
include_once H.'sys/inc/ipua.php';
include_once H.'sys/inc/fnc.php';
include_once H.'sys/inc/user.php';

if(isset($_GET['id']))
{
	$id = intval($_GET['id']);
}
else
{
	header("Location: /index.php");
}

$dir = dbarray(dbquery("SELECT * FROM `smile_dir` WHERE `id` = '" . $id . "'"));

if (!$dir['id'])
header("Location: /index.php");

$set['title'] = text($dir['name']) . ' | Список смайлов';
include_once H.'sys/inc/thead.php';

title();
aut();

?>
<div class="foot">
<img src="/style/icons/str2.gif" alt="*"> <a href="index.php">Категории</a> | <b><?=text($dir['name'])?></b>
</div>
<?
				
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `smile` WHERE `dir` = '$id'"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str']*$page-$set['p_str'];

?><table class="post"><?

if ($k_post == 0) 
{
	?><div class="mess">Список смайлов пуст</div><?
}

$q = dbquery("SELECT * FROM `smile` WHERE `dir` = '$id' ORDER BY `id` ASC LIMIT $start, $set[p_str]");

while($post = dbarray($q))
{
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;

	?>
	<img src="/style/smiles/<?=$post['id']?>.gif" alt="<?=$post['name']?>"/> <?=text($post['smile'])?>
	</div>
	<?
}

if($k_page>1)str('dir.php?id='.$id.'&amp;',$k_page,$page);

if (isset($user) && $user['level'] > 3)
{
	?>
	<div class="foot">
	<img src="/style/icons/str.gif" alt="*"> <a href="/adm_panel/smiles.php">Админка</a>
	</div>
	<?
}

?>
<div class="foot">
<img src="/style/icons/str2.gif" alt="*"> <a href="index.php">Категории</a> | <b><?=text($dir['name'])?></b>
</div>
<?

include_once H.'sys/inc/tfoot.php';
?>