<?php







if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='set')



{



	echo "<form class=\"foot\" action='?act=set&amp;ok&amp;page=$page' method=\"post\">";



	echo "文件夹名称:<br />";



	echo "<input type='text' name='name' value='" . htmlspecialchars($dir_id['name']) . "' /><br />";



if ($dir_id['upload']==1)$check=' checked="checked"'; else $check=NULL;



	echo "<label><input type=\"checkbox\"$check name=\"upload\" value=\"1\" /> 卸货</label><br />";



	echo "扩展通过 \";\"分隔:<br />";



	echo "<input type='text' name='ras' value='$dir_id[ras]' /><br />";



	echo "最大文件大小：<br />";



if ($dir_id['maxfilesize']<1024)$size=$dir_id['maxfilesize'];



elseif($dir_id['maxfilesize']>=1024 && $dir_id['maxfilesize']<1048576)$size=intval($dir_id['maxfilesize']/1024);



elseif($dir_id['maxfilesize']>=1048576)$size=intval($dir_id['maxfilesize']/1048576);







	echo '<input type="text" name="size" size="4" value="'.$size.'" />';



	echo '<select name="mn">';



if ($dir_id['maxfilesize']<1024)$sel=' selected="selected"';else $sel=NULL;



	echo '<option value="1"'.$sel.'>B</option>';



if ($dir_id['maxfilesize']>=1024 && $dir_id['maxfilesize']<1048576)$sel=' selected="selected"';else $sel=NULL;



	echo '<option value="1024"'.$sel.'>KB</option>';



if ($dir_id['maxfilesize']>=1048576)$sel=' selected="selected"';else $sel=NULL;



	echo '<option value="1048576"'.$sel.'>MB</option>';



	echo '</select><br />';



	echo '*服务器设置不允许卸载超过: '.size_file($upload_max_filesize).'<br />';



	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="接受改变" /> ';



	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">取消</a>]<br />';



	echo '</form>';



}



































if (user_access('obmen_dir_create') && isset($_GET['act']) && $_GET['act']=='mkdir')



{



	echo '<form class="foot" action="?act=mkdir&amp;ok&amp;page='.$page.'" method="post">';



	echo '文件夹名称:<br />';



	echo '<input type="text" name="name" value="" /><br />';



	echo '<label><input type="checkbox" name="upload" value="1" /> 卸货</label><br />';



	echo '通过扩展 ";":<br />';



	echo '<input type="text" name="ras" value="" /><br />';



	echo '最大文件大小:<br />';



	echo '<input type="text" name="size" size="4" value="500" />';



	echo '<select name="mn">';



	echo '<option value="1">B</option>';



	echo '<option value="1024" selected="selected">KB</option>';



	echo '<option value="1048576">MB</option>';



	echo '</select><br />';



	echo '*服务器设置不允许卸载超过: '.size_file($upload_max_filesize).'<br />';



	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="创建文件夹" /> ';	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">取消</a>]<br />';



	echo '</form>';



}







if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='rename' && $l!='/')



{



	echo '<form class="foot" action="?act=rename&amp;ok&amp;page='.$page.'" method="post">';



	echo '文件夹名称:<br />';



	echo '<input type="text" name="name" value="'.$dir_id['name'].'"/><br />';	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="改名" /> ';	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">取消</a>]<br />';



	echo '</form>';



}











if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='mesto' && $l!='/')



{



	echo '<form class="foot" action="?act=mesto&amp;ok&amp;page='.$page.'" method="post">';



	echo '新的道路:<br />';



	echo '<select class="submit" name="dir_osn">';



	echo '<option value="/">[彻底地]</option>';



	$q=dbquery("SELECT DISTINCT `dir` FROM `obmennik_dir` WHERE `dir` not like '$l%' ORDER BY 'dir' ASC");



while ($post = dbassoc($q))



{



	echo '<option value="'.$post['dir'].'">'.$post['dir'].'</option>';



}











	echo '</select><br />';



	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="移动" />';	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">Отмена</a>]<br />';



	echo '</form>';



}







if (user_access('obmen_dir_delete') && isset($_GET['act']) && $_GET['act']=='delete' && $l!='/')



{







	echo '<div class="mess">';



	echo '删除当前文件夹 ('.$dir_id['name'].')?<br />';



	echo '[<a href="?act=delete&amp;ok&amp;page='.$page.'"><img src="/style/icons/ok.gif" alt="*"> 是的</a>] ';



	echo '[<a href="?page='.$page.'"><img src="/style/icons/delete.gif" alt="*"> 取消</a>]<br />';



	echo '</div>';



}











if (user_access('obmen_dir_edit') || user_access('obmen_dir_delete') || user_access('obmen_dir_create'))



{



	echo '<div class="foot">';







if (user_access('obmen_dir_create'))



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=mkdir&amp;page='.$page.'">创建文件夹</a><br />';







if ($l!='/'){







if (user_access('obmen_dir_edit')){



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=rename&amp;page='.$page.'">重命名文件夹</a><br />';



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=set&amp;page='.$page.'">文件夹设置</a><br />';



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=mesto&amp;page='.$page.'">移动文件夹</a><br />';



}







if (user_access('obmen_dir_delete') && $dir_id['my'] == 0)



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=delete&amp;page='.$page.'">删除文件夹</a><br />';



}











	echo '</div>';



}











?>