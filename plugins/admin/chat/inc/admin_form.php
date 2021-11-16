<?







返回<

if (user_access('guest_clear'))




{




if (isset($_GET['act']) && $_GET['act']=='create')




{









echo "<form method=\"post\" class='foot' action=\"?\">";




echo "Будут удалены посты, написаные ... тому назад<br />";




echo "<input name=\"write\" value=\"12\" type=\"text\" size='3' />";




echo "<select name=\"write2\">";




echo "<option value=\"\">       </option>";




echo "<option value=\"mes\">Месяцев</option>";




echo "<option value=\"sut\">Суток</option>";




echo "</select><br />";




echo "<img src='/style/icons/ok.gif' alt='*' /> <input value=\"Очистить\" type=\"submit\" /> ";




echo "<img src='/style/icons/delete.gif' alt='*' /> <a href=\"?\">Отмена</a><br />";




echo "</form>";




}



















echo "<div class=\"foot\">";




echo "<img src='/style/icons/str.gif' alt='*' /> <a href=\"?act=create\">Очистить чат</a><br />";




echo "</div>";




}




?>