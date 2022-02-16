<?
if (user_access('guest_clear'))
{
	if (isset($_GET['act']) && $_GET['act'] == 'create')
	{
		?>
		<form method="post" class="nav2" action="?">
		将删除撰写的帖子...返回<br />
		<input name="write" value="12" type="text" size="3" />
		<select name="write2">
		<option value="">       </option>
		<option value="mes">月</option>
		<option value="sut">24小时</option>
		</select><br />
		<input value="清除" type="submit" /> <a href="?">取消</a><br />
		</form>
		<?
	}
	?>
	<div class="foot">
	<img src='/style/icons/str.gif' alt='*'> <a href="?act=create">清除来宾</a><br />
	</div>
	<?
}
?>