<?php
$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$ank[id]'");
while ($gallery = dbassoc($gallery_q)) {
	$q=dbquery("SELECT * FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]'");
	while ($post = dbassoc($q)) {
		// 定义照片文件路径
		$photoPaths = [
			H."files/gallery/48/$post[id].jpg",
			H."files/gallery/128/$post[id].jpg",
			H."files/gallery/640/$post[id].jpg",
			H."files/gallery/photo/$post[id].jpg",
		];

		// 删除文件
		foreach ($photoPaths as $path) {
			if (file_exists($path) && is_writable($path)) {
				unlink($path);
			}
		}
		dbquery("DELETE FROM `gallery_photo` WHERE `id` = '$post[id]' LIMIT 1");
		dbquery("DELETE FROM `gallery_komm` WHERE `id_photo` = '$post[id]'");
		dbquery("DELETE FROM `gallery_rating` WHERE `id_photo` = '$post[id]'");
	}
}

dbquery("DELETE FROM `gallery` WHERE `id_user` = '$ank[id]'");
dbquery("DELETE FROM `gallery_komm` WHERE `id_user` = '$ank[id]'");

if (isset($_GET['all']) && count($collisions)>1) {
	for ($i=1;$i<count($collisions);$i++) {
		$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
		while ($gallery = dbassoc($gallery_q)) {
			$q=dbquery("SELECT * FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]'");
			while ($post = dbassoc($q)) {
				// 定义照片文件路径
				$photoPaths = [
					H."files/gallery/48/$post[id].jpg",
					H."files/gallery/128/$post[id].jpg",
					H."files/gallery/640/$post[id].jpg",
					H."files/gallery/photo/$post[id].jpg",
				];
			
				// 删除文件
				foreach ($photoPaths as $path) {
					if (file_exists($path) && is_writable($path)) {
						unlink($path);
					}
				}
				dbquery("DELETE FROM `gallery_photo` WHERE `id` = '$post[id]' LIMIT 1");
				dbquery("DELETE FROM `gallery_komm` WHERE `id_photo` = '$post[$i]'");
				dbquery("DELETE FROM `gallery_rating` WHERE `id_photo` = '$post[$i]'");
			}
		}
		dbquery("DELETE FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
	}
}