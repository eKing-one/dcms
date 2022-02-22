<?php
if (file_exists(H."style/themes/$set[set_them]/foot.php"))
include_once H."style/themes/$set[set_them]/foot.php";
else
{
	list($msec, $sec) = explode(chr(32), microtime());
	echo "<div class='foot'>";
	echo "<a href='/'>主要的</a><br />";
	echo "<a href='/users.php'>注册用户: ".dbresult(dbquery("SELECT COUNT(*) FROM `user`"), 0)."</a><br />";
	echo "<a href='/online.php'>在线用户: ".dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > ".(time()-600).""), 0)."</a><br />";
	echo "<a href='/online_g.php'>在线游客: ".dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0)."</a><br />";
	$page_size = ob_get_length();
	ob_end_flush();
	if(!isset($_SESSION['traf']))
		$_SESSION['traf'] = 0;
		$_SESSION['traf'] += $page_size;
	echo '
		页面大小: '.round($page_size / 1024, 2). ' KB<br />
		页面生成: '.round($_SESSION['traf'] / 1024, 2). ' KB <br />
		执行时间: '.round(($sec + $msec) - $conf['headtime'], 3). '秒' ;
	echo "</div>";
	echo "<div class='rekl'>";
	rekl(3);
	echo "</div>";
	echo "</div></body></html>";
}
exit;
