<?php
$set['web'] = true;
header("Content-type: text/html");
?>
<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo htmlspecialchars($set['title']); ?></title>
		<link rel="shortcut icon" href="<?php echo $set['siteurl']; ?>/favicon.ico" />
		<link rel="stylesheet" href="<?php echo $set['siteurl']; ?>/style/themes/<?php echo $set['set_them']; ?>/style.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $set['siteurl']; ?>/style/themes/<?php echo $set['set_them']; ?>/tables.css" type="text/css" />

		<!-- Модальное окно -->
		<link rel="stylesheet" href="<?php echo $set['siteurl']; ?>/assets/css/style.css" type="text/css"/>

		<!-- 多余的jQuery
		<script type="text/javascript" src="<?php echo get_http_type(); ?>://code.jquery.com/jquery-1.2.1.js"></script>
		-->

		<script src="<?php echo $set['siteurl']; ?>/assets/js/jquery/jquery-1.8.3.js"></script>
		<script type="text/javascript" src="<?php echo $set['siteurl']; ?>/assets/js/facebox.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('a[rel*=facebox]').facebox({
					loading_image: '<?php echo $set['siteurl']; ?>/assets/img/loading.gif',
					close_image: '<?php echo $set['siteurl']; ?>/assets/img/closelabel.gif'
				})
			})
		</script>

		<script type="text/javascript" src="<?php echo $set['siteurl']; ?>/assets/js/ajax.js"></script>
		<script type="text/javascript" src="<?php echo $set['siteurl']; ?>/assets/js/form-submit.js"></script>
		<script src="<?php echo $set['siteurl']; ?>/style/themes/<?php echo $set['set_them']; ?>/js.js" type="text/javascript" language="JavaScript" charset="utf-8"></script>

		<!-- 对话框 -->
		<script src="<?php echo $set['siteurl']; ?>/assets/js/dialog.js"></script>
		<link type="text/css" href="<?php echo $set['siteurl']; ?>/assets/css/dialog.css" rel="stylesheet" />
		<script>
			function showContent2(link) {

				var cont = document.getElementById('contentBody');
				var loading = document.getElementById('loading');

				cont.innerHTML = loading.innerHTML;

				var http = createRequestObject();
				if (http) {
					http.open('get', link);
					http.onreadystatechange = function() {
						if (http.readyState == 4) {
							cont.innerHTML = http.responseText;
						}
					}
					http.send(null);
				} else {
					document.location = link;
				}
			}

			function createRequestObject() {
				try {
					return new XMLHttpRequest();
				}
				catch (e) {
					try {
						return new ActiveXObject('Msxml2.XMLHTTP');
					}
					catch (e) {
						try {
							return new ActiveXObject('Microsoft.XMLHTTP');
						}
						catch (e) {
							return null;
						}
					}
				}
			}
		</script>  
	</head>
	<body><?php include H . 'style/themes/' . $set['set_them'] . '/title.php'; ?>
		<div class="head">
			<table class="nav">
				<tr>
					<td class="logo">
						<a href="<?php echo $set['siteurl']; ?>" title="到主页"><img src="<?php echo $set['siteurl']; ?>/style/themes/<?php echo $set['set_them']; ?>/logo.png" alt="Logotype" /></a>
					</td>
					<td class="head_menu">
						<?php include H . 'style/themes/' . $set['set_them'] . '/navigation.php'; ?>
					</td>
				</tr>
			</table>
		</div>
		<div class="body">
			<table class="table">
				<tr>
					<td class="block_menu_nav">
						<?php include H . 'style/themes/' . $set['set_them'] . '/menu.php'; ?>

					</td>
					<td class="block_all_nav">
						<div class="ind_cont">
							<div class="title">
								<?php echo $set['title']; ?>
							</div>
							<div class='content_block'> 
								<?php if (isset($user)): ?>
									<!-- 用于加载表情符号的块 -->
									<div id="dialog" title="表情符号列表">
										<div id="contentBody">  

										</div>

										<div id="loading" style="display: none"> 
										正在加载...
										</div>
									</div>
								<?php endif;