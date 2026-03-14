<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
$set['title'] = '发送私信';
include_once '../sys/inc/thead.php';

title();
aut();
only_reg();

if(isset($_GET['send']) AND isset($_POST['send'])) {
	$ank = $db->query('SELECT * FROM user WHERE nick = ? LIMIT 1', [$_POST['komu']]);
	if(empty($ank['id'])) {
		/* 检查是否有这样一个昵称的用户 */
		echo '<div class="nav2">你可能犯了一个错误，用户 ' . text($_POST['komu']) . ' 不存在</div>';
		echo '<div class="foot"> <a href="mails.php">返回</a></div>';
		include_once '../sys/inc/tfoot.php';
	} elseif ((strlen2($_POST['msg']) < 3) OR (strlen2($_POST['msg']) > 1024)) {
		/* 检查字符数量 */
		echo '<div class="nav2">消息中允许的字符数为2到1024。你已经进去了: ' . strlen2($_POST['msg']) . '</div>';
		echo '<div class="foot"><a href="mails.php">返回</a></div>';
		include_once '../sys/inc/tfoot.php';
	} else {
		/* 如果以上都正常，那么我们检查邮箱的隐私性 */
		$block = true;
		$uSet = dbarray(dbquery("SELECT `privat_mail` FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
		$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
		$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
		if ($user['group_access'] == 0) {
			if ($uSet['privat_mail'] == 2 && $frend != 2) { // 仅限好友
				echo '<div class="mess">只有他的朋友可以写消息给用户！</div>';
				echo '<div class="nav1">';
				if ($frend_new == 0 && $frend == 0){
					echo '<img src="../style/icons/druzya.png" alt="*"/> <a href="frends/create.php?add=' . $ank['id'] . '">添加到朋友</a><br />';
				}elseif ($frend_new == 1){
					echo '<img src="../style/icons/druzya.png" alt="*"/> <a href="frends/create.php?otm=' . $ank['id'] . '">拒绝申请</a><br />';
				}elseif ($frend == 2){
					echo '<img src="../style/icons/druzya.png" alt="*"/> <a href="frends/create.php?del=' . $ank['id'] . '">从朋友中删除</a><br />';
				}
				echo '</div>';
				$block = false;
			} elseif ($uSet['privat_mail'] == 0) { // 如果关闭
				echo '<div class="mess">用户已禁止向他写信息！</div>';
				$block = false;
			}
		}
		if ($block == true AND $ank['id'] != 0) {
			/* 如果一切正常的话，就发送 */
			$db->insert('INSERT INTO mail (id_user, id_kont, `time`, msg) VALUES (?, ?, ?, ?)', [$user['id'], $ank['id'], $time, $_POST['msg']]);
			header("Location: mail.php?id=$ank[id]");
			$_SESSION['message'] = '消息发送成功';
		}
	}
}
?>
<form class="nav2" action="mails.php?send" method="post">To（账号）:<br/>
	<input type="text" name="komu"><br/>
	<?php echo $tPanel; ?>
	<textarea name="msg"></textarea><br/>
	<input type="submit" value="发送" name="send">
</form>
<?php include_once '../sys/inc/tfoot.php';
