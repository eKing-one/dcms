<?php
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
include_once 'sys/inc/thead.php';


// 检查是否有 `browser-info` 数据，并处理浏览器信息
if (isset($_POST['browser-info'])) {
	// 获取浏览器信息
	$browserInfo = $_POST['browser-info'];

	// 你可以将浏览器信息保存到数据库或文件
	// 例如，将浏览器信息写入日志文件
	file_put_contents('browser_info_log.json', $browserInfo . "\n", FILE_APPEND);

	// 输出成功响应
	header('Content-Type: application/json');
	echo json_encode(['status' => 'success', 'message' => 'Browser info uploaded successfully']);
	exit;
}


title();
aut();
if (isset($_SESSION['captcha']) && isset($_POST['chislo'])) {
	if ($_SESSION['captcha'] == $_POST['chislo']) {
		msg('验证通过');
	} else {
		$err = '验证码错误';
	}
}
err();
?>

当前设备类型为：<?php echo $webbrowser ? 'PC' : 'NoPC'; ?><br>
当前设备UA为：<?php echo $ua; ?><br>
当前设备IP为：<?php echo $ip; ?><br>
<form method='post'>验证码测试：<img src='/captcha.php' alt='验证码图像' /><br /><input name='chislo' type='text' /><br/><input type='submit' value='继续' /></form>

<hr />

<!-- 按钮，用于触发浏览器信息显示 -->
<button id="show-info-button">Show Browser Info</button>

<!-- 用来显示浏览器信息的区域 -->
<div id="browser-info-container"></div>

<script type="module">
	// 使用 import 加载外部脚本
	import browserModule from "https://passer-by.com/browser/src/browser.js";

	// 异步获取浏览器信息
	async function getBrowserInfo() {
		// 使用 browserModule.getInfo() 获取浏览器信息
		let browserInfo = await browserModule.getInfo();
		console.log(browserInfo);
		return browserInfo;
	}

	getBrowserInfo().then(browserInfo => {
		// 将数据上传到服务器
		fetch('', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded', // 使用表单数据格式
			},
			body: `browser-info=${encodeURIComponent(JSON.stringify(browserInfo))}`, // 将浏览器信息作为表单数据发送
		})
		.then(response => response.json()) // 解析响应为 JSON
		.then(data => {
			console.log('服务器响应:', data);
			// 可以在这里显示上传成功的消息
		})
		.catch(error => {
			console.error('上传数据失败:', error);
		});
	});

	// 获取按钮元素
	const showInfoButton = document.getElementById('show-info-button');

	// 获取浏览器信息并显示的事件处理函数
	showInfoButton.addEventListener('click', () => {
		getBrowserInfo().then(browserInfo => {
			// 获取用于显示设备信息的 HTML 元素
			const infoContainer = document.getElementById('browser-info-container');
			// 将浏览器信息渲染到该元素
			infoContainer.innerHTML = `<pre>${JSON.stringify(browserInfo, null, 2)}</pre>`;
		});
	});
</script>

<?php
include_once 'sys/inc/tfoot.php';
