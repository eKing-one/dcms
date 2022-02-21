<?
echo "Плейлист<br />";
echo 'Размер: '.size_file($size)."<br />";
echo 'Загружен: '.vremja(filectime($dir_loads.'/'.$dirlist[$i]))."<br />";
