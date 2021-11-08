<?
if (user_access('guest_clear'))
{
	if (isset($_GET['act']) && $_GET['act'] == 'create')
	{
		?>
		<form method="post" class="nav2" action="?">
		Будут удалены посты, написаные ... тому 返回<br />
		<input name="write" value="12" type="text" size="3" />
		<select name="write2">
		<option value="">       </option>
		<option value="mes">Месяцев</option>
		<option value="sut">Суток</option>
		</select><br />
		<input value="Очистить" type="submit" /> <a href="?">Отмена</a><br />
		</form>
		<?
	}
	?>
	<div class="foot">
	<img src='/style/icons/str.gif' alt='*'> <a href="?act=create">Очистить гостевую</a><br />
	</div>
	<?
}
?>