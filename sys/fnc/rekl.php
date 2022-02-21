<?
function rekl($sel)
{
	global $set;
	// для страниц, кроме главной, у нас другая позиция
	if ($sel == 3 && $_SERVER['PHP_SELF'] != '/index.php')
		$sel = 4; 
	$q = dbquery("SELECT * FROM `rekl` WHERE `sel` = '$sel' AND `time_last` > '" . time() . "' ORDER BY id ASC");
	while ($post = dbassoc($q))
	{
		if ($sel == 2)
			echo icons('rekl.png','code');
		if ($post['dop_str'] == 1)
			echo '<a' . ($set['web'] ? ' target="_blank"' : null) . ' href="http://' . $_SERVER['SERVER_NAME'] . '/go.php?go=' . $post['id'] . '">';
		else
			echo '<a' . ($set['web'] ? ' target="_blank"' : null) . ' href="' . $post['link'] . '">';
		if ($post['img'] == NULL)
			echo $post['name'];
		else
			echo '<img src="' . $post['img'] . '" alt="' . $post['name'] . '" />';
		echo '</a><br />';
	}
}
?>