<?

list($msec, $sec) = explode(chr(32), microtime());

echo "<div class='foot'>";

echo "<a href='/'>网站主页</a><br />\n";

echo "<a href='/users.php'>注册用户: ".dbresult(dbquery("SELECT COUNT(*) FROM `user`"), 0)."</a><br />\n";

echo "<a href='/online.php'>在线用户: ".dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > ".(time()-600).""), 0)."</a><br />\n";

echo "<a href='/online_g.php'>在线游客: ".dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0)."</a><br />\n";

if (isset($user) && $user['level']!=0) echo "执行时间: ".round(($sec + $msec) - $conf['headtime'], 3)." 秒<br />\n";

echo "</div>";

echo "</body></html>";

exit;

?>