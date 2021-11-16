<?
//网页标题




include_once '../sys/inc/start.php';




include_once '../sys/inc/compress.php';




include_once '../sys/inc/sess.php';




include_once '../sys/inc/home.php';




include_once '../sys/inc/settings.php';




include_once '../sys/inc/db_connect.php';




include_once '../sys/inc/ipua.php';




include_once '../sys/inc/fnc.php';




include_once '../sys/inc/user.php';









/* Бан пользователя */ 




if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'chat' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)




{




header('Location: /ban.php?'.SID);exit;




}














if (isset($user))dbquery("DELETE FROM `chat_who` WHERE `id_user` = '$user[id]'");




dbquery("DELETE FROM `chat_who` WHERE `time` < '".($time-120)."'");




if (isset($user) && isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."'"),0)==1




&& isset($_GET['msg']) && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '".intval($_GET['msg'])."'"),0)==1)




{




$room=dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));




$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '".intval($_GET['msg'])."' LIMIT 1"));




if (isset($user))dbquery("INSERT INTO `chat_who` (`id_user`, `time`,  `room`) values('$user[id]', '$time', '$room[id]')");




if ($set['time_chat']!=0)header("Refresh: $set[time_chat]; url=/chat/room/$room[id]/".rand(1000,9999).'/'); // автообновление




$set['title']='Чат - '.$room['name'].' ('.dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).')'; // заголовок страницы




include_once '../sys/inc/thead.php';




title();




echo "<a href='/info.php?id=$ank[id]'>查看问卷</a><br />";




echo "<form method=\"post\" action=\"/chat/room/$room[id]/".rand(1000,9999)."/\">";




echo "信息:<br /><textarea name=\"msg\">$ank[nick], </textarea><br />";




echo "<label><input type=\"checkbox\" name=\"privat\" value=\"$ank[id]\" /> 私下里</label><br />";




if ($user['set_translit']==1)echo "<label><input type=\"checkbox\" name=\"translit\" value=\"1\" /> 音译,音译</label><br />";




echo "<input value=\"发送\" type=\"submit\" />";




echo "</form>";




echo "<div class=\"foot\">";




echo " <img src='/style/icons/str2.gif' alt='*'><a href=\"/chat/room/$room[id]/".rand(1000,9999)."/\">进入房间</a><br />";




echo " <img src='/style/icons/str2.gif' alt='*'><a href=\"/chat/\">走廊</a><br />";




echo "</div>";




include_once '../sys/inc/tfoot.php';




}




if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)




{




$room=dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));




if (isset($user))dbquery("INSERT INTO `chat_who` (`id_user`, `time`,  `room`) values('$user[id]', '$time', '$room[id]')");




if ($set['time_chat']!=0)header("Refresh: $set[time_chat]; url=/chat/room/$room[id]/".rand(1000,9999).'/'); // автообновление




$set['title']='Чат - '.$room['name'].' ('.dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).')'; // заголовок страницы




include_once '../sys/inc/thead.php';




title();




include 'inc/room.php';




echo "<div class=\"foot\">";




echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/chat/\">走廊</a><br />";




echo "</div>";




include_once '../sys/inc/tfoot.php';




}




$set['title']='聊天-走廊'; // заголовок страницы




include_once '../sys/inc/thead.php';




title();




include 'inc/admin_act.php';




err();




aut(); // форма авторизации




echo "<table class='post'>";




$q=dbquery("SELECT * FROM `chat_rooms` ORDER BY `pos` ASC");




if ( dbrows($q) == 0 ) {




echo "  <div class='mess'>";




echo "Нет комнат";




echo "  </div>";




}




while ($room = dbassoc($q))




{




/*-----------зебра-----------*/if ($num==0){echo '<div class="nav1">';$num=1;}elseif ($num==1){echo '<div class="nav2">';$num=0;}/*---------------------------*/




echo "<img src='/style/themes/$set[set_them]/chat/14/room.png' alt='' /> ";














echo "<a href='/chat/room/$room[id]/".rand(1000,9999)."/'>$room[name] (".dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"),0).")</a> ";




if (user_access('chat_room'))echo "<a href='?set=$room[id]'><img src='/style/icons/edit.gif' alt='*' /></a> "; 




if ($room['opis']!=NULL)echo '<br />'.esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($room['opis']))))))))."<br />";





























echo "   </div>";




}




echo "</table>";




echo "<div class=\"foot\">";




echo "<img src='/style/icons/str.gif' alt='*'> <a href='who.php'>谁在聊天？</a><br />"; 




echo "</div>";




include 'inc/admin_form.php';




include_once '../sys/inc/tfoot.php';




?>