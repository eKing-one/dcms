<?
$q_menu = dbquery("SELECT * FROM `menu` ORDER BY `pos` ASC");
while ($post_menu = dbassoc($q_menu))
{
	if ($post_menu['type'] == 'link')
	echo '<div class="main_menu">';
	if ($post_menu['type'] == 'link')
	echo '<img src="/style/icons/' . $post_menu['icon'] . '" alt="*" /> ';
	if ($post_menu['type'] == 'link')
	echo '<a href="' . $post_menu['url'] . '">';
	else 
	echo '<div class="menu_razd">';
	echo $post_menu['name'];
	if ($post_menu['type'] == 'link')
	echo '</a> ';
	if ($post_menu['counter'] != NULL && test_file(H . $post_menu['counter']))
	{
		@include H . $post_menu['counter'];
	}
	echo '</div>';
}
if (user_access('adm_panel_show'))
{
	?>
	<div class="main2">
	<img src="/style/icons/adm.gif" alt="DS" /> <a href="/plugins/admin/">网站管理</a> 
	<?
  include_once check_replace(H.'plugins/admin/count.php');
	?>
	</div>
	<?
}
?>