<?
echo "<div class='title'>网站菜单</a>";
echo "</div>";
$q_menu=dbquery("SELECT * FROM `menu` ORDER BY `pos` ASC");
while ($post_menu = dbassoc($q_menu))
{
if ($post_menu['type']=='link')echo "<a href='$post_menu[url]'>";
else echo "<div class='menu_razd'>";
if ($post_menu['type']=='link')echo "<div class='main_menu'>";
if (!isset($post_menu['icon']))dbquery('ALTER TABLE `menu` ADD `icon` VARCHAR( 32 ) NULL DEFAULT NULL');
if (!isset($post_menu['type']))dbquery("ALTER TABLE  `menu` ADD  `type` ENUM('link', 'razd') NOT NULL DEFAULT 'link' AFTER `id`");
echo $post_menu['name'].' ';
if ($post_menu['counter']!=NULL && is_file(H.$post_menu['counter']))
{
echo '<span class="mm_counter">';
@include H.$post_menu['counter'];
echo '</div>';
}
echo "</div>";
if ($post_menu['type']=='link')echo "</a>";
}
if (user_access('adm_panel_show')){
echo "<a href='/plugins/admin/'><div class='main_menu'>";
echo "管理员面板 ";
include_once H."plugins/admin/count.php";
echo "</div></a>";
}
if (isset($user))echo "<a href='/user/exit.php'><div class='main_menu'>退出</div></a>";
echo "<div class='tof'></a>";
echo "</div>";
?>