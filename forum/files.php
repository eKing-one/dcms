<?php
include_once '../sys/inc/start.php';
//include_once '../sys/inc/compress.php'; // 如果取消注释，文件将无法正确摇摆
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
include_once '../sys/inc/downloadfile.php';

// 检查文件ID是否有效且文件存在
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_files` WHERE `id` = '" . intval($_GET['id']) . "'"), 0) == 1) {
    // 获取文件的详细信息
	$file = dbassoc(dbquery("SELECT * FROM `forum_files` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));

    // 如果文件存在，并且用户有足够权限，且请求删除
	if (is_file(H.'sys/forum/files/' . $file['id'] . '.frf') && isset($user) && $user['level'] >= 1 && isset($_GET['del'])) {
        // 获取返回的链接，如果没有，使用首页链接
		if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL) {
			$link = $_SERVER['HTTP_REFERER'];
		} else {
			$link = '/index.php';
		}

        // 从数据库中删除文件记录
		dbquery("DELETE FROM `forum_files` WHERE `id` = '$file[id]' LIMIT 1");
        // 删除实际的文件
		unlink(H . 'sys/forum/files/' . $file['id'] . '.frf');

        // 如果有返回地址，跳转回原页面，否则跳转到论坛首页
		if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL) {
			header("Location: $_SERVER[HTTP_REFERER]");
		} else {
			header("Location: /forum/index.php?" . session_id());
		}

	} elseif (is_file(H . 'sys/forum/files/' . $file['id'] . '.frf')) {
        // 如果文件存在且没有删除请求，更新下载次数并触发文件下载
		dbquery("UPDATE `forum_files` SET `count` = '" . ($file['count'] + 1) . "' WHERE `id` = '$file[id]' LIMIT 1");
        // 执行文件下载操作
		DownloadFile(H . 'sys/forum/files/' . $file['id'] . '.frf', $file['name'] . '.' . $file['ras'], ras_to_mime($file['ras']));
	} else {
		http_response_code(404);
		die('服务器错误：找不到文件');
	}
} else {
    // 如果文件ID无效或文件不存在，显示404错误并跳转到首页
	header("Refresh: 3; url=/index.php");
	header("Content-type: text/html", NULL, 404);
	echo "<html>
	      <head>
	      <title>错误 404</title>";
	echo "<link rel=\"stylesheet\" href=\"/style/themes/default/style.css\" type=\"text/css\" />";
	echo "</head><body><div class=\"body\"><div class=\"err\">";
	echo "没有这样的页面";
	echo "<br />";
	echo "<a href=\"/index.php\">返回首页</a>";
	echo "</div></div></body></html>";
}
