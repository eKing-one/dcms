<?



include_once '../../sys/inc/start.php';



include_once '../../sys/inc/compress.php';



include_once '../../sys/inc/sess.php';



include_once '../../sys/inc/home.php';



include_once '../../sys/inc/settings.php';



include_once '../../sys/inc/db_connect.php';



include_once '../../sys/inc/ipua.php';



include_once '../../sys/inc/fnc.php';



include_once '../../sys/inc/adm_check.php';



include_once '../../sys/inc/user.php';







$set['title']='额外服务';



include_once '../../sys/inc/thead.php';



title();



if (!isset($user))



header("location: /index.php?");







err();



aut();



echo "<div class='foot'>\n";



echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | 额外服务<br />\n";



echo "</div>\n";







echo "<div class='nav1'>\n";



echo "<b>个人帐户:</b><br />



- <b><font color='red'>$user[balls]</font></b> 积分.<br />



- <b><font color='green'>$user[money]</font></b> $sMonet[0]";



echo "</div>\n";







echo "<div class='nav2'>\n";







echo "<font color='red'>&rarr; <a href='money.php'><font color='red'>接收 $sMonet[2]</font></a></font>";







echo "</div>\n";











echo "<div class='foot'>\n";



echo "<b><font color='blue'>服务</font> $sMonet[2]</b>";



echo "</div>\n";











echo '<div class="nav1">';



$c = dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `id_user` = '$user[id]' AND `time` > '$time'"), 0);



echo '&rarr; <a href="liders.php">网站负责人</a> ' . ($c == 0 ? '<span class="off">[残疾人士]</span> ' : '<span class="on">[已启用]</span>');



echo '</div>';











echo '<div class="nav2">';



$c2 = dbresult(dbquery("SELECT COUNT(*) FROM `user_set` WHERE `id_user` = '$user[id]' AND `ocenka` > '$time'"), 0);



echo "&rarr; <a href='plus5.php'>评估</a> <img src='/style/icons/6.png' alt='*'> " . ($c2==0?'<span class="off">[残疾人士]</span> ':'<span class="on">[已启用]</span>')."";



echo "</div>\n";



























echo "<div class='foot'>\n";



echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | Доп. услуги<br />\n";



echo "</div>\n";







include_once '../../sys/inc/tfoot.php';



?>