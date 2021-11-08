<?
//网页标题




include_once '../../../sys/inc/start.php';




include_once '../../../sys/inc/compress.php';




include_once '../../../sys/inc/sess.php';




include_once '../../../sys/inc/home.php';




include_once '../../../sys/inc/settings.php';




include_once '../../../sys/inc/db_connect.php';




include_once '../../../sys/inc/ipua.php';




include_once '../../../sys/inc/fnc.php';




include_once '../../../sys/inc/user.php';









$set['title']='阿尔敏聊天-谁在这里？'; // заголовок страницы




include_once '../../../sys/inc/thead.php';




title();




aut();




if (user_access('adm_panel_show')){




$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > '".(time()-100)."' AND `url` like '/plugins/admin/chat/%'"), 0);




$k_page=k_page($k_post,$set['p_str']);




$page=page($k_page);




$start=$set['p_str']*$page-$set['p_str'];




$q = dbquery("SELECT * FROM `user` WHERE `date_last` > '".(time()-100)."' AND `url` like '/plugins/admin/chat/%' ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");




echo "<table class='post'>\n";




if ($k_post==0)




{




echo "   <tr>\n";




echo "  <td class='p_t'>\n";




echo "没有人\n";




echo "  </td>\n";




echo "   </tr>\n";




}




while ($guest = dbarray($q))




{




echo "   <tr>\n";









if ($set['set_show_icon']==2){




echo "  <td class='icon48' rowspan='2'>\n";




avatar($guest['id']);




echo "  </td>\n";




}




elseif ($set['set_show_icon']==1)




{




echo "  <td class='icon14'>\n";




echo "".status($guest['id'])."";




echo "  </td>\n";




}



















echo "  <td class='p_t'>\n";




echo "<a href='/info.php?id=$guest[id]'>$guest[nick]</a>\n";




echo "  ".medal($guest['id'])." ".online($guest['id'])."\n";




echo "   </td>\n";




echo "   </tr>\n";




}









echo "</table>\n";














if ($k_page>1)str("?",$k_page,$page); // Вывод страниц



















}




include_once '../../../sys/inc/tfoot.php';




?>