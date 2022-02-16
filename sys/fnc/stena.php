<?php
 /*
 ==========
 Вывод активности на стене
 ==========
 */
 function stena($id_us=NULL,$id=NULL){
 global $webbrowser;
 $ank_stena=dbassoc(dbquery("SELECT `id`,`pol` FROM `user` WHERE `id`='".$id_us."' LIMIT 1")); //Определяем автора комментария
 if(dbresult(dbquery("SELECT COUNT(`id`)FROM `stena` WHERE `id`='".$id."' LIMIT 1"),0)==1){ //Если комментарий с такой записью существует, то...
 $post=dbassoc(dbquery("SELECT * FROM `stena` WHERE `id`='".$id."' LIMIT 1"));
 if ($post)
 {
 if($post['type']=='foto'){ //Если смена аватара
 echo " <span style='color:darkgreen;'>已安装".($ank_stena['pol']==0 ? 'а' : null)." 页面上的新头像</span><br/>";
 $foto=dbassoc(dbquery("SELECT `id`,`id_gallery`,`ras` FROM `gallery_foto` WHERE `id`='".$post['info_1']."' LIMIT 1"));
 echo "<a href='/foto/".$ank_stena['id']."/".$foto['id_gallery']."/".$foto['id']."/'><img class='stenka' style='width:".($webbrowser ? '240px;' : '60px;')."' src='/foto/foto0/".$foto['id'].".".$foto['ras']."'></a>";
 }elseif($post['type']=='note'){ //Если новый дневник
 $note=dbquery("SELECT `id`,`name`,`msg` FROM `notes` WHERE `id`='".$post['info_1']."' LIMIT 1");
 if(dbrows($note)==0){ //Если такого дневника не существует, то...
 echo " <span style='color:#666;'>写道".($ank_stena['pol']==0 ? 'a' : null)." 已删除的新日记。</span>";
 }else{ //А, если существует, то...
 $notes=dbassoc($note);
 echo " <span style='color:darkgreen;'>创建".($ank_stena['pol']==0 ? 'a' : null)." 我日记中的新条目</span><br/>";
 echo "<a href='/plugins/notes/list.php?id=".$notes['id']."'><b style='color:#999;'>".text($notes['name'])."</b></a><br/>";
 echo '<span style="color:#666;">'.rez_text($notes['msg'],82).'</span>';
 }
 }elseif($post['type']=='them'){ //Если это тема форума
 $dump=dbquery("SELECT `id`,`id_forum`,`id_razdel`,`name`,`text` FROM `forum_t` WHERE `id`='".$post['info_1']."' LIMIT 1");
 if(dbrows($dump)==0){ //Если нет такой темы, то...
 echo " <span style='color:#666;'>写道".($ank_stena['pol']==0 ? 'a' : null)." 论坛中已被删除的主题。</span>";
 }else{ //Если есть,  то...
 $them=dbassoc($dump);
 echo " <span style='color:darkgreen;'>创建".($ank_stena['pol']==0 ? 'a' : null)." 论坛的新话题</span><br/>";
 echo " <a href='/forum/".$them['id_forum']."/".$them['id_razdel']."/".$them['id']."/'><b style='color:#999;'>".text($them['name'])."</b></a><br/>";
 echo " <span style='color:#666;'>".rez_text($them['text'],82)."</span>";
 }
 }

 }
 }
 }
 
?>