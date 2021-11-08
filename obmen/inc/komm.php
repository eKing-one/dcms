<?














$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id_file` = '$file_id[id]'"),0);




$k_page=k_page($k_post,$set['p_str']);




$page=page($k_page);




$start=$set['p_str']*$page-$set['p_str'];




echo "<table class='post'>\n";









echo "<div class='foot'>";




echo "评论：\n";




echo '</div>';









if ($k_post==0)




{




echo "<div class='mess'>";




echo "Нет сообщений\n";




echo '</div>';




}else{




/*------------сортировка по времени--------------*/




if (isset($user)){




echo "<div id='comments' class='menus'>";




echo "<div class='webmenu'>";




echo "<a href='?komm&amp;page=$page&amp;sort=1' class='".($user['sort']==1?'activ':'')."'>在下面</a>";




echo "</div>"; 




echo "<div class='webmenu'>";




echo "<a href='?komm&amp;page=$page&amp;sort=0' class='".($user['sort']==0?'activ':'')."'>在顶部</a>";




echo "</div>"; 




echo "</div>";




}




/*---------------alex-borisi---------------------*/




}









$q=dbquery("SELECT * FROM `obmennik_komm` WHERE `id_file` = '$file_id[id]' ORDER BY `id` $sort LIMIT $start, $set[p_str]");




while ($post = dbassoc($q))




{




$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$post[id_user]' LIMIT 1"));









/*-----------зебра-----------*/ 




if ($num==0){




echo '<div class="nav1">';




$num=1;




}




elseif ($num==1){




echo '<div class="nav2">';




$num=0;}




/*---------------------------*/









echo " ".group($ank['id'])." <a href='/info.php?id=$ank[id]'>$ank[nick]</a>";




if (isset($user) && $user['id'] != $ank['id'])echo " <a href='?showinfo&amp;page=$page&amp;response=$ank[id]'>[*]</a> \n";




echo "".online($ank['id'])." (".vremja($post['time']).")<br />\n";









$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'files') AND `post` = '1' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);









if ($postBan == 0) // Блок сообщения




{	




	echo output_text($post['msg']);




}else{




	echo output_text($banMess).'<br />'; // Текст при блоке поста




}









if (isset($user))




{




echo '<div style="text-align:right;">';









if ($ank['id']!=$user['id'])




echo "<a href=\"?showinfo&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*' title='Это спам'></a> "; 









if (user_access('obmen_komm_del') || $avtor['id'] == $user['id'])




echo '<a href="?showinfo&amp;page='.$page.'&amp;del_post='.$post['id'].'"><img src="/style/icons/delete.gif" alt="*"></a>';









echo "   </div>\n";




}




echo "   </div>\n";









}




echo "</table>\n";
























if ($k_page>1)str('?showinfo&amp;',$k_page,$page); // Вывод страниц














if (isset($user))




{









echo "<form method=\"post\" name='message' action=\"?showinfo&amp;".$go_otv."\">\n";




if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))




include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';




else




echo "$tPanel<textarea name=\"msg\">$respons_msg</textarea><br />\n";




echo "<input value=\"发送\" type=\"submit\" />\n";




echo "</form>\n";









}









?>