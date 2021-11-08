<?







返回<

if (user_access('guest_clear'))




{




if (isset($_GET['act']) && $_GET['act']=='create')




{









echo "<form method=\"post\" class='foot' action=\"?\">\n";




echo "Будут удалены посты, написаные ... тому назад<br />\n";




echo "<input name=\"write\" value=\"12\" type=\"text\" size='3' />\n";




echo "<select name=\"write2\">\n";




echo "<option value=\"\">       </option>\n";




echo "<option value=\"mes\">Месяцев</option>\n";




echo "<option value=\"sut\">Суток</option>\n";




echo "</select><br />\n";




echo "<img src='/style/icons/ok.gif' alt='*' /> <input value=\"Очистить\" type=\"submit\" /> \n";




echo "<img src='/style/icons/delete.gif' alt='*' /> <a href=\"?\">Отмена</a><br />\n";




echo "</form>\n";




}



















echo "<div class=\"foot\">\n";




echo "<img src='/style/icons/str.gif' alt='*' /> <a href=\"?act=create\">Очистить чат</a><br />\n";




echo "</div>\n";




}




?>