<?
echo "Плейлист<br />\n";
echo 'Размер: '.size_file($size)."<br />\n";
echo 'Загружен: '.vremja(filectime($dir_loads.'/'.$dirlist[$i]))."<br />\n";
?>