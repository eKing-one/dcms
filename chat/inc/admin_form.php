<?php





if (user_access('chat_room') && isset($_GET['set']) && is_numeric($_GET['set']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '".intval($_GET['set'])."'"),0)==1)





{











$room=dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '".intval($_GET['set'])."' LIMIT 1"));











echo "<form action='?set=$room[id]&amp;ok' method='post'>";





echo "房间名称:<br />\n<input type='text' name='name' value='$room[name]' /><br />\n";





echo "职位:<br />\n<input type='text' name='pos' value='$room[pos]' /><br />\n";





echo "资料描述:<br />\n<input type='text' name='opis' value='$room[opis]' /><br />\n";











echo "机器人:<br />\n<select name=\"bots\">\n";





echo "<option value='0'".(($room['umnik']==0 && $room['shutnik']==0)?' selected="selected"':null).">取消</option>\n";





echo "<option value='1'".(($room['umnik']==1 && $room['shutnik']==0)?' selected="selected"':null).">$set[chat_umnik]</option>\n";





echo "<option value='2'".(($room['umnik']==0 && $room['shutnik']==1)?' selected="selected"':null).">$set[chat_shutnik]</option>\n";





echo "<option value='3'".(($room['umnik']==1 && $room['shutnik']==1)?' selected="selected"':null).">$set[chat_umnik] 和 $set[chat_shutnik]</option>\n";





echo "</select><br />\n";











echo "<input class='submit' type='submit' value='申请' /><br />\n";





echo "<a href='?delete=$room[id]'>移走</a><br />\n";





echo "<a href='?cancel=$passgen'>取消</a><br />\n";





echo "</form>";





}











if (user_access('chat_clear') && isset($_GET['act']) && $_GET['act']=='clear')





{











echo "<div class=\"err\">";











echo "清除聊天？<br />\n";





echo "<a href=\"?act=clear2\">是的</a> \n";





echo "<a href=\"?\">取消</a><br />\n";





echo "</div>";





}











if (user_access('chat_room') && (isset($_GET['act']) && $_GET['act']=='add_room' || dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms`"),0)==0))





{





echo "<form class=\"foot\" action=\"?act=add_room&amp;ok\" method=\"post\">";





echo "房间名称:<br />\n";





echo "<input type='text' name='name' value='' /><br />\n";











$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `chat_rooms`"), 0)+1;





echo "职位:<br />\n";





echo "<input type='text' name='pos' value='$pos' /><br />\n";

















echo "资料描述:<br />\n";





echo "<input type='text' name='opis' value='' /><br />\n";











echo "机器人:<br />\n<select name=\"bots\">\n";











echo "<option value='0'>取消</option>\n";





echo "<option value='1'>$set[chat_umnik]</option>\n";





echo "<option value='2'>$set[chat_shutnik]</option>\n";





echo "<option value='3'>$set[chat_umnik] и $set[chat_shutnik]</option>\n";





echo "</select><br />\n";











echo "<input class=\"submit\" type=\"submit\" value=\"创建一个房间\" /><br />\n";





echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"?\">取消</a><br />\n";





echo "</form>";





}











echo "<div class=\"foot\">\n";





if (user_access('chat_clear'))





echo "<img src='/style/icons/str.gif' alt='*'> <a href=\"?act=clear\">从消息中清除聊天</a><br />\n";





if (user_access('chat_room') && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms`"),0)>0)





echo "<img src='/style/icons/str.gif' alt='*'> <a href=\"?act=add_room\">创建一个房间</a><br />\n";





echo "</div>\n";





?>