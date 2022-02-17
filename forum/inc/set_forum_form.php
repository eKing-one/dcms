<?


if (user_access('forum_razd_create') && (isset($_GET['act']) && $_GET['act']=='new' || !isset($_GET['act']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id_forum` = '$forum[id]'"),0)==0))
{
	echo "<form method=\"post\" action=\"/forum/$forum[id]/?act=new&amp;ok\">";
	echo "部分名称:<br />";
	echo "<input name=\"name\" type=\"text\" maxlength='32' value='' /><br />";
	echo "资料描述<br/>";
	echo "<textarea name='opis' placeholder='部分描述'></textarea><br/>";
	echo "<input value=\"创建\" type=\"submit\" /><br />";
	echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\">取消</a><br />";
	echo "</form>";
}


if (user_access('forum_for_edit') && (isset($_GET['act']) && $_GET['act']=='set')) 
{ 
    echo "<form method='post' action='/forum/$forum[id]/?act=set&ok'>"; 
    echo "论坛名称:<br>"; 
    echo '<input name="name" type="text" maxlength="32" value="' . text($forum['name']) . '" />';   
    echo "<br>资料描述:<br>"; 
    echo "<textarea name='opis'>".esc(trim(stripcslashes(htmlspecialchars($forum['opis']))))."</textarea>"; 
    $icon=array(); 
    $opendiricon=opendir(H.'style/forum'); 
    while ($icons=readdir($opendiricon)) 
    { 
        if (preg_match('#^.|default.png#',$icons))continue; 
        $icon[]=$icons; 
    } 
    closedir($opendiricon); 
     
    echo "<br>图标:"; 
    echo "<select name='icon'>"; 
    echo "<option value='default.png'>默认情况下</option>"; 
    for ($i=0;$i<sizeof($icon);$i++) 
    { 
        echo "<option value='$icon[$i]'>$icon[$i]</option>"; 
    } 
    echo "</select>"; 
    echo "<br>职位:"; 
    echo "<input name='pos' type='text' maxlength='3' value='$forum[pos]' />"; 
     
    if ($user['level'] >= 3) { 
        if ($forum['adm']==1)$check=' checked="checked"'; 
        else  
        $check=NULL; 
         
        echo "<br><label><input type='checkbox".$check."' name='adm' value='1' /> 仅用于管理</label>"; 
    } 
     
    echo "<br><input value='更改' type='submit' />"; 
    echo "<br><img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/'>取消</a>"; 
    echo "</form>"; 
} 

if (isset($_GET['act']) && $_GET['act']=='del' && user_access('forum_for_delete')) 
{ 
    echo "<div class='err'>"; 
    echo "确认删除论坛 "; 
    echo '<a href="/forum/'.$forum['id'].'/?act=delete&ok">是的</a> / <a href="/forum/'.$forum['id'].'/">取消</a>'; 
    echo "</div>"; 
} 

if (user_access('forum_razd_create') || user_access('forum_for_edit') || user_access('forum_for_delete'))
{ 
    echo "<div class='foot'>"; 

    if(user_access('forum_razd_create')) 
    echo "<img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/?act=new'>新组</a>"; 

    if(user_access('forum_for_edit')) 
    echo "<br/><img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/?act=set'>论坛参数</a>"; 

    if(user_access('forum_for_delete')) 
    echo "<br/><img src='/style/icons/str.gif' alt='*'> <a href='/forum/$forum[id]/?act=del'>删除论坛</a>"; 
    echo "</div>"; 
}
?>