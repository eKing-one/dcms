<?php
$set['title'] = '平台验证';
include_once 'inc/head.php'; // 设计主题的顶部
?>

<!-- 表单：下一步按钮 -->
<form method="post" action="?<?php echo passgen(); ?>">
	<input type="submit" name="refresh" value="下一步" />
</form>

<?php
include_once H . 'sys/inc/testing.php';

// 错误处理
if (isset($err)) {
	if (is_array($err)) {
		foreach ($err as $value) {
			echo "<div class='err'>" . htmlspecialchars($value) . "</div>";
		}
	} else {
		echo "<div class='err'>" . htmlspecialchars($err) . "</div>";
	}
} elseif (isset($_GET['step']) && $_GET['step'] == '2') {
	$_SESSION['install_step']++;
	header("Location: index.php?" . passgen() . "&" . SID);
	exit;
}
?>

<hr />

<!-- 表单：继续按钮 -->
<form method="get" action="index.php">
	<input name="gen" value="<?php echo $passgen; ?>" type="hidden" />
	<input name="step" value="<?php echo $_SESSION['install_step'] + 1; ?>" type="hidden" />
	<input 
		value="<?php echo isset($err) ? '未准备好安装' : '继续'; ?>" 
		type="submit"
		<?php echo isset($err) ? 'disabled="disabled"' : ''; ?> 
	/>
</form>

<hr />

<!-- 当前步骤 -->
<b>步骤: <?php echo $_SESSION['install_step']; ?></b>

<?php include_once 'inc/foot.php'; // 设计主题的底部 ?>
