<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set=$set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_sys',null,'index.php?'.SID);
adm_check();
$set['title']='CDN设置';
include_once '../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
	$temp_set['get_ip_from_header'] = in_array($_POST['get_ip_from_header'], ['disabled', 'Forwarded', 'X-Forwarded-For', 'X-Real-IP', 'CF-Connecting-IP', 'True-Client-IP']) ? $_POST['get_ip_from_header'] : 'disabled';
	try {
		if ($_POST['get_ip_from_header'] == 'CF-Connecting-IP') {

			/**
			 * Cloudflare IPv4 列表：https://www.cloudflare.com/ips-v4/
			 * Cloudflare IPv6 列表：https://www.cloudflare.com/ips-v6/
			 */
			$cloudflareCdnIpsUrls = [
				"https://www.cloudflare.com/ips-v4/",
				"https://www.cloudflare.com/ips-v6/"
			];

			// 创建一个空数组存储IP
			$cloudflareCdnIpList = [];

			// 遍历URL并获取内容
			foreach ($cloudflareCdnIpsUrls as $cloudflareCdnIpsUrl) {
				// 获取URL内容
				$cloudflareCdnIpsContent = file_get_contents($cloudflareCdnIpsUrl);
				if ($cloudflareCdnIpsContent === false) {
					throw new Exception("无法从 $cloudflareCdnIpsUrl 获取内容");
				}

				// 按行拆分内容并合并到IP列表
				$cloudflareCdnIpLines = explode("\n", trim($cloudflareCdnIpsContent));
				$cloudflareCdnIpList = array_merge($cloudflareCdnIpList, $cloudflareCdnIpLines);
			}

			// 去重和清理空行
			$save_cdn_ip_list = array_filter(array_unique($cloudflareCdnIpList));
		} else {
			// 先将所有换行符标准化为 "\n" （适应 Windows 和 Linux 系统）
			$save_cdn_ip_list = str_replace("\r\n", "\n", $_POST['cdn_ip_list']);

			// 然后按 "\n" 分割文本并转换为数组
			$save_cdn_ip_list = explode("\n", $save_cdn_ip_list);

			// 使用 array_filter 去除空白行或只包含空格/Tab 的行
			$save_cdn_ip_list = array_filter($save_cdn_ip_list, function($line) {
				return trim($line) !== '';
			});

			// 重新索引数组，去除空洞
			$save_cdn_ip_list = array_values($save_cdn_ip_list);
		}

		// 检查IP范围是否有效
		foreach ($save_cdn_ip_list as $save_cdn_ip_range) {
			if (!\IPLib\Factory::parseRangeString($save_cdn_ip_range)) {
				throw new Exception("IP范围 $save_cdn_ip_range 无效");
			}
		}

		// 清空数据库
		dbquery("TRUNCATE TABLE cdn_ips");

		// 保存CDN IP列表
		foreach ($save_cdn_ip_list as $save_cdn_ip_range) {
			dbquery("INSERT INTO cdn_ips (ip_range) VALUES ('$save_cdn_ip_range')");
		}

		if (save_settings($temp_set)) {
			admin_log('设置', '系统', '更改CDN设置');
			msg('已成功接受设置');
		} else {
			throw new Exception("无权更改配置文件");
		}
	} catch (Exception $e) {
		echo $err =  $e->getMessage();
	}
}

err();
aut();

echo "<form method=\"post\" action=\"?\">从请求标头获取用户IP：<br />
<select name='get_ip_from_header'>
	<option ".(setget('get_ip_from_header',"disabled")=="disabled"? " selected ":null)." value='disabled'>禁用</option>
	<option ".(setget('get_ip_from_header',"Forwarded")=="Forwarded"? " selected ":null)." value='Forwarded'>Forwarded</option>
	<option ".(setget('get_ip_from_header',"X-Forwarded-For")=="X-Forwarded-For"? " selected ":null)." value='X-Forwarded-For'>X-Forwarded-For</option>
	<option ".(setget('get_ip_from_header',"X-Real-IP")=="X-Real-IP"? " selected ":null)." value='X-Real-IP'>X-Real-IP</option>
	<option ".(setget('get_ip_from_header',"CF-Connecting-IP")=="CF-Connecting-IP"? " selected ":null)." value='CF-Connecting-IP'>Cloudflare</option>
	<option ".(setget('get_ip_from_header',"True-Client-IP")=="True-Client-IP"? " selected ":null)." value='True-Client-IP'>True-Client-IP</option>
</select>
<br />";
// 从数据库获取所有 IP 范围
$getIpRangeResult = dbquery("SELECT ip_range FROM cdn_ips");
// 检查查询是否成功
if ($getIpRangeResult) {
    // 遍历查询结果并将每个 IP 范围拼接成列表
    $ip_ranges = [];
    while ($row = mysqli_fetch_assoc($getIpRangeResult)) {
        $ip_ranges[] = $row['ip_range'];
    }

    // 将 IP 范围输出为文本格式，每个范围一行
    $cdnIpsExistingContent = implode("\n", $ip_ranges);
} else {
    $cdnIpsExistingContent = "";
}
echo "CDN 列表：<br />
<textarea name='cdn_ip_list'>{$cdnIpsExistingContent}</textarea><br />";
echo "<input value=\"保存\" name='save' type=\"submit\" />";
echo "</form>";

if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';