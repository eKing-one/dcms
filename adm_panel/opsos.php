<?







include_once '../sys/inc/start.php';







include_once '../sys/inc/compress.php';







include_once '../sys/inc/sess.php';







include_once '../sys/inc/home.php';







include_once '../sys/inc/settings.php';







include_once '../sys/inc/db_connect.php';







include_once '../sys/inc/ipua.php';







include_once '../sys/inc/fnc.php';







include_once '../sys/inc/adm_check.php';







include_once '../sys/inc/user.php';







user_access('adm_ip_edit',null,'index.php?'.SID);







adm_check();







$opsos=NULL;







$set['title']='Добавление оператора';







include_once '../sys/inc/thead.php';







title();







































if (isset($_POST['min']) && isset($_POST['max']) && isset($_POST['opsos']))







{







if (!preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_POST['min']))$err='Неверный формат IP';







if (!preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_POST['max']))$err='Неверный формат IP';







if ($_POST['opsos']==NULL)$err='Введите название оператора';























$min=ip2long($_POST['min']);







$max=ip2long($_POST['max']);







$opsos=my_esc(stripcslashes(htmlspecialchars($_POST['opsos'])));















dbquery("INSERT INTO `opsos` (`min`, `max`, `opsos`) values('$min', '$max', '$opsos')",$db);







msg ('Диапазон успешно добавлен');















}























if (isset($_GET['delmin'])  && isset($_GET['delmax']) &&







 dbresult(dbquery("SELECT COUNT(*) FROM `opsos` WHERE `min` = '".$_GET['delmin']."' AND `max` = '".$_GET['delmax']."' LIMIT 1",$db), 0)!=0)







{







dbquery("DELETE FROM `opsos` WHERE `min` = '".$_GET['delmin']."' AND `max` = '".$_GET['delmax']."' LIMIT 1");







dbquery("OPTIMIZE TABLE `opsos`");







msg('Диапазон успешно удален');







}























err();







aut();























$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `opsos`"),0);







$k_page=k_page($k_post,$set['p_str']);







$page=page($k_page);







$start=$set['p_str']*$page-$set['p_str'];















echo "<table class='post'>\n";







if ($k_post==0)







{







echo "   <tr>\n";







echo "  <td class='p_t'>\n";







echo "Нет операторов\n";







echo "  </td>\n";







echo "   </tr>\n";















}







$q=dbquery("SELECT * FROM `opsos` ORDER BY `opsos` ASC LIMIT $start, $set[p_str]");







while ($post = dbassoc($q))







{







echo "   <tr>\n";







echo "  <td class='p_t'>\n";







echo long2ip($post['min']).' - '.long2ip($post['max']);







echo "  </td>\n";







echo "   </tr>\n";







echo "   <tr>\n";







echo "  <td class='p_m'>\n";







echo "$post[opsos]<br />\n";







echo "<a href=\"?page=$page&amp;delmin=$post[min]&amp;delmax=$post[max]\">Удалить</a><br />\n";







echo "  </td>\n";







echo "   </tr>\n";







}







echo "</table>\n";







if ($k_page>1)str('?',$k_page,$page); // Вывод страниц















echo "<form method=\"post\" action=\"\">\n";







echo "Начальный IP адрес:<br />\n<input name=\"min\" size=\"16\"  value=\"\" type=\"text\" /><br />\n";







echo "Завершающий IP:<br />\n<input name=\"max\" size=\"16\" value=\"\" type=\"text\" /><br />\n";







echo "Оператор:<br />\n<input name=\"opsos\" size=\"16\" value=\"$opsos\" type=\"text\" /><br />\n";







echo "<input value=\"Добавить\" type=\"submit\" />\n";







echo "</form>\n";















if (user_access('adm_panel_show')){







echo "<div class='foot'>\n";







echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";







echo "</div>\n";







}







include_once '../sys/inc/tfoot.php';







?>