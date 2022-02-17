<?








if (user_access('guest_clear'))




{




if (isset($_GET['act']) && $_GET['act']=='create')




{









echo "<form method=\"post\" class='foot' action=\"?\">";




echo "将删除以前写的帖子。<br />";




echo "<input name=\"write\" value=\"12\" type=\"text\" size='3' />";




echo "<select name=\"write2\">";




echo "<option value=\"\">       </option>";




echo "<option value=\"mes\">月</option>";




echo "<option value=\"sut\">昼夜</option>";




echo "</select><br />";




echo "<img src='/style/icons/ok.gif' alt='*' /> <input value=\"清除\" type=\"submit\" /> ";




echo "<img src='/style/icons/delete.gif' alt='*' /> <a href=\"?\">取消</a><br />";




echo "</form>";




}



















echo "<div class=\"foot\">";




echo "<img src='/style/icons/str.gif' alt='*' /> <a href=\"?act=create\">清空聊天室</a><br />";




echo "</div>";




}




?>