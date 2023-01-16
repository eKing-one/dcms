<?
if (isset($_GET['act']) && $_GET['act']=='post_delete' && (user_access('forum_post_ed') || (isset($user) && $ank2['id']==$user['id'])))
{
echo "<input value=\"删除选定的帖子\" type=\"submit\" /> ";
echo "<a href='/forum/$forum[id]/$razdel[id]/$them[id]/'><img src='/style/icons/delete.gif' alt='*'> 取消</a> ";
echo "</form>";
}
echo "<div class=\"foot\">";
if ((!isset($_GET['act']) || $_GET['act']!='post_delete') && (user_access('forum_post_ed') || (isset($user) && $ank2['id']==$user['id']))){
echo "<a href='/forum/$forum[id]/$razdel[id]/$them[id]/?act=post_delete'>删除 | ";
}
echo '<a href="txt">下载</a> ';
echo "</div>";
?>