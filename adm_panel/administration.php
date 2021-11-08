<?//网页标题








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








user_access('adm_show_adm',null,'index.php?'.SID);








adm_check();








$set['title']='Администрация'; // заголовок страницы








include_once '../sys/inc/thead.php';








title();








aut();

















$k_post=dbresult(dbquery("SELECT COUNT(`user`.`id`) FROM `user` LEFT JOIN `user_group` ON `user`.`group_access` = `user_group`.`id` WHERE `user_group`.`level` != 0 AND `user_group`.`level` IS NOT NULL"),0);








$k_page=k_page($k_post,$set['p_str']);








$page=page($k_page);








$start=$set['p_str']*$page-$set['p_str'];








echo "<table class='post'>\n";








if ($k_post==0)








{








echo "   <tr>\n";








echo "  <td class='p_t'>\n";








echo "Нет результатов\n";








echo "  </td>\n";








echo "   </tr>\n";

















}








$q=dbquery("SELECT `user`.`id` FROM `user` LEFT JOIN `user_group` ON `user`.`group_access` = `user_group`.`id` WHERE `user_group`.`level` != 0 AND `user_group`.`level` IS NOT NULL ORDER BY `user_group`.`level` DESC LIMIT $start, $set[p_str]");








while ($ank = dbassoc($q))








{








$ank=get_user($ank['id']);








echo "   <tr>\n";

















if ($set['set_show_icon']==2){








echo "  <td class='icon48' rowspan='2'>\n";








avatar($ank['id']);








echo "  </td>\n";








}








elseif ($set['set_show_icon']==1)








{








echo "  <td class='icon14'>\n";








echo "".status($ank['id'])."";








echo "  </td>\n";








}








echo "  <td class='p_t'>\n";








if (user_access('adm_log_read') && $ank['level']!=0 && ($ank['id']==$user['id'] || $ank['level']<$user['level']))








echo "<a href='adm_log.php?id=$ank[id]'>$ank[nick]</a> ($ank[group_name])".online($ank['id'])."\n";








else








echo "<a href='/info.php?id=$ank[id]'>$ank[nick]</a> ($ank[group_name])".online($ank['id'])."\n";








echo "  </td>\n";








echo "   </tr>\n";








echo "   <tr>\n";








if ($set['set_show_icon']==1)echo "  <td class='p_m' colspan='2'>\n"; else echo "  <td class='p_m'>\n";

















echo "<span class=\"ank_n\">Пол:</span> <span class=\"ank_d\">".(($ank['pol']==1)?'Мужской':'Женский')."</span><br />\n";

















$adm_log_c_all=dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `id_user` = '$ank[id]'"), 0);








$mes=mktime(0, 0, 0, date('m')-1); // время месяц назад








$adm_log_c_mes=dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `id_user` = '$ank[id]' AND `time` > '$mes'"), 0);








echo "<span class='ank_n'>Вся активность:</span> <span class='ank_d'>$adm_log_c_all</span><br />\n";








echo "<span class='ank_n'>Активность за месяц:</span> <span class='ank_d'>$adm_log_c_mes</span><br />\n";

















echo "<span class=\"ank_n\">Посл. посещение:</span> <span class=\"ank_d\">".vremja($ank['date_last'])."</span><br />\n";








if (isset($user) && ($user['level']>$ank['level'] || $user['level']==4))








{








echo "<a href='/adm_panel/user.php?id=$ank[id]'>Редактировать профиль</a><br />\n";








}

















echo "  </td>\n";








echo "   </tr>\n";








}








echo "</table>\n";








if ($k_page>1)str("?",$k_page,$page); // Вывод страниц

















if (user_access('adm_panel_show')){








echo "<div class='foot'>\n";








echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";








echo "</div>\n";








}








include_once '../sys/inc/tfoot.php';








?>