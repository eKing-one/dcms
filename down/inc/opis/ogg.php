<?
echo 'Размер: ' . size_file($size) . "<br />";
$jfile = $name;
$media = dbassoc(dbquery("SELECT * FROM `media_info` WHERE `file` = '" . my_esc($jfile) . "' AND `size` = '$size' LIMIT 1"));
if ($media != NULL) {
    echo 'Время: ' . $media['lenght'] . "<br />";
    echo "Битрейт: " . $media['bit'] . " KBPS<br />";
} elseif (class_exists('ffmpeg_movie')) {
    $media = new ffmpeg_movie($file);
    if (intval($media->getDuration()) > 3599)
        echo 'Время: ' . intval($media->getDuration() / 3600) . ":" . date('s', fmod($media->getDuration() / 60, 60)) . ":" . date('s', fmod($media->getDuration(), 3600)) . "<br />";
    elseif (intval($media->getDuration()) > 59)
        echo 'Время: ' . intval($media->getDuration() / 60) . ":" . date('s', fmod($media->getDuration(), 60)) . "<br />";
    else
        echo 'Время: ' . intval($media->getDuration()) . " 秒<br />";
    echo "Битрейт: " . ceil(($media->getBitRate()) / 1024) . " KBPS<br />";
    if (intval($media->getDuration()) > 3599)
        dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . intval($media->getDuration() / 3600) . ":" . date('s', fmod($media->getDuration() / 60, 60)) . ":" . date('s', fmod($media->getDuration(), 3600)) . "', '" . ceil(($media->getBitRate()) / 1024) . "', 'mp3')");
    if (intval($media->getDuration()) > 59)
        dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . intval($media->getDuration() / 60) . ":" . date('s', fmod($media->getDuration(), 60)) . "', '" . ceil(($media->getBitRate()) / 1024) . "', 'mp3')");
    else
        dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . intval($media->getDuration()) . " 秒', '" . ceil(($media->getBitRate()) / 1024) . "', 'mp3')");
}
$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$post[id_user]' LIMIT 1"));
echo "<br />";
