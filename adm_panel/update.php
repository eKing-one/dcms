<?
ini_set('max_execution_time', 180);
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
//user_access('adm_mysql', NULL, 'index.php?' . SID);
adm_check();
user_access('adm_set_sys', NULL, 'index.php?' . SID);
$temp_set = $set;
$set['title'] = '引擎升级(阿尔法版)';
include_once '../sys/inc/thead.php';
title();
err();
aut();

if (isset($_POST['update'])) {  // 请求更新
	if (function_exists("disk_free_space")) {   // 检测剩余空间是否充足
		if (disk_free_space("/") < 1048576) exit("升级至少需要 20MB 的可用空间");
	}

	$temp_set['job'] = 0;
	save_settings($temp_set);

	// 备份文件
	if (isset($_POST['backup'])) {
		$backup = H . "sys/backup/";
		if (!file_exists($backup)) {
			if (!mkdir($backup, 0777, TRUE) && !is_dir($backup)) {
				throw new \RuntimeException(sprintf('Directory "%s" was not created', $backup));
			}
		}
		if (!file_exists($backup . ".htaccess")) {
			$f = fopen($backup . ".htaccess", "a+");
			fwrite($f, "Options All -Indexes
deny from all");
			fclose($f);
		}
		$version = $temp_set['dcms_version'];
		$backup = $backup . $version . "_" . time() . "/";
		$dir30 = H;
		$files_new = getFileListAsArray($dir30);
		foreach ($files_new as $index => $file) {
			if (!file_exists(dirname($backup . $index))) {
				if (!mkdir($concurrentDirectory = dirname($backup . $index), 0755, TRUE) && !is_dir($concurrentDirectory)) {
					throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
				}
			}
			copy($dir30 . $index, $backup . $index);
		}
	}

	$data = getLatestStableRelease();
	$temp_set['dcms_version'] = $data['version'];

	// 检查并创建下载目录
	$downloads = H . "sys/update/";
	if (!file_exists($downloads)) {
		if (!mkdir($downloads, 0777, TRUE) && !is_dir($downloads)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $downloads));
		}
	}

	// 防止用户通过浏览器直接查看目录内容
	if (!file_exists($downloads . ".htaccess")) {
		$f = fopen($downloads . ".htaccess", "a+");
		fwrite($f, "Options All -Indexes
deny from all");
		fclose($f);
	}
	$url = $data['zip_url'];          // 提取下载链接
	$version = $data['version'];  // 提取版本号

	// 下载更新包
	if ($updated = file_get_contents($url)) {
		$nf = $data['version'] . ".social-new.zip";   // 定义更新包文件名
		file_put_contents($downloads . $nf, $updated);          // 将下载的文件内容保存到下载目录
		//  echo "Скачивание</br>";

		// 解压更新包
		$zip = new ZipArchive;
		$res = $zip->open($downloads . $nf);
		if ($res === TRUE) {
			// 创建解压目录
			$dir30 = $downloads . $version . "_" . time() . "/";
			if (!file_exists($dir30)) {
				if (!mkdir($dir30, 0777, TRUE) && !is_dir($dir30)) {
					throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir30));
				}
			}

			// 解压文件
			$zip->extractTo($dir30);
			$zip->close();

			//  echo "Установка</br>";
			// 获取解压缩后的文件列表
			$files_new = getFileListAsArray($dir30);
			$newpatch = H . "s";
			if (!file_exists($newpatch)) {
				if (!mkdir($newpatch, 0755, TRUE) && !is_dir($newpatch)) {
					throw new \RuntimeException(sprintf('Directory "%s" was not created', $newpatch));
				}
			}

			// 遍历解压缩后的文件并创建相应的子目录
			// $index 变量为文件相对路径
			foreach ($files_new as $index => $file) {
				// 检查目标目录下是否存在该文件的父目录，如果没有就创建
				if (!file_exists(dirname($newpatch . $index))) mkdir(dirname($newpatch . $index), 0755, TRUE);
				copy($dir30 . $index, $newpatch . $index);
				// 复制文件到目标目录
			}

			// 完成更新
			$temp_set['job'] = 1;
			save_settings($temp_set);
			if (save_settings($temp_set)) {
				admin_log('设置', '系统', '更新');
				msg('更新');
			}
			header("Location: /adm_panel/update.php");
		}
	}
}

$data = getLatestStableRelease();
echo "<div class='mess'>";
echo "<center><span style='font-size:16px;'><strong>DCMS-Social v.$set[dcms_version]</strong></span></center>";
echo "<center><span style='font-size:14px;'> 官方支持网站 <a href='https://dcms-social.ru'>https://dcms-social.ru</a></span></center>";
echo "";
if (version_compare($set['dcms_version'], $data['version']) >= 0) {
	echo "<div class='mess'> 你有最新的相关版本。你可以在中文版CN_DCMS-Social的<a target='_blank' href='https://github.com/zzyh1145/CN_DCMS-Social'>GitHub仓库</a>上手动查看新版本</div>";
} else {
	echo "<div class='mess' style='font-size: 16px; background-color: #9aff9a' >有个新版本 - " . $data['version'] . "! 需要升级。新发布的所有信息在 <a target='_blank' href='https://github.com/zzyh1145/CN_DCMS-Social'>GitHub仓库</a> 你可以在此页面上自动更新引擎。</div>";
}
echo "<div class='mess'> <h3 style='color: red'>注意！这是自动更新的 Alpha 版本，明智地使用！在 /replace/ 文件夹之外对原始引擎文件所做的所有手动更改都将丢失，请做备份！</h3>  </div>";
echo "<form method='post' >";
echo "<label><input type='checkbox' name='backup'> 备份文件到 /sys/backup/</label></br> ";
if (version_compare($set['dcms_version'], $data['version']) < 0)
	echo "<input type='submit' name='update' value='更新!' />";
else
	echo "<input type='submit' name='update' value='重新安装当前版本！' />";
echo "</form>";

// 页脚
if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回控制面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';


// 选择性（通过$recursive参数）递归遍历一个目录，并返回一个包含所有文件的数组（包括子目录中的文件）
// 用于获取子目录中的文件列表
function getFileListAsArray(string $dir, bool $recursive = TRUE, string $basedir = ''): array
{
	if ($dir == '') {
		return array();
	} else {
		$results = array();
		$subresults = array();
	}
	if (!is_dir($dir)) {
		$dir = dirname($dir);
	} // so a files path can be sent
	if ($basedir == '') {
		$basedir = realpath($dir) . DIRECTORY_SEPARATOR;
	}
	$files = scandir($dir);
	foreach ($files as $key => $value) {
		if (($value != '.') && ($value != '..')) {
			$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
			if (is_dir($path)) { // do not combine with the next line or..
				if ($recursive) { // ..non-recursive list will include subdirs
					$subdirresults = getFileListAsArray($path, $recursive, $basedir);
					$results = array_merge($results, $subdirresults);
				}
			} else { // strip basedir and add to subarray to separate file list
				$subresults[str_replace($basedir, '', $path)] = $value;
			}
		}
	}
	// merge the subarray to give the list of files then subdirectory files
	if (count($subresults) > 0) {
		$results = array_merge($subresults, $results);
	}
	return $results;
}