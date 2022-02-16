<?
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

$set['title'] = '类别清单';
include_once H.'sys/inc/thead.php';

err();
title();
aut();

?>
<div class="foot">
<img src="/style/icons/str2.gif" alt="*"> <b>类别</b>
</div>
<?

$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `smile_dir`"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];

?><table class="post"><?

if ($k_post == 0) 
{
	?><div class="mess">无类别</div><?
}

$q = dbquery("SELECT * FROM `smile_dir` ORDER BY id ASC");

while ($dir = dbassoc($q))
{
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;

	?>
	<img src="/style/themes/<?=$set['set_them']?>/loads/14/dir.png" alt="*"> 
	<a href="/plugins/smiles/dir.php?id=<?=$dir['id']?>"><?=text($dir['name'])?></a> (<?=dbresult(dbquery("SELECT COUNT(*) FROM `smile` WHERE `dir` = '$dir[id]'"),0)?>)
	</div>
	<?
}

?></table><?

if (isset($user) && $user['level'] > 3)
{
	?>
	<div class="foot">
	<img src="/style/icons/str.gif" alt="*"> <a href="/adm_panel/smiles.php">行政</a>
	</div>
	<?
}

?>
<div class="foot">
<img src="/style/icons/str2.gif" alt="*"> <b>类别</b>
</div>
<?

include_once H.'sys/inc/tfoot.php';
?>