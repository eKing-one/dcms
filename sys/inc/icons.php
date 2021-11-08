<?

// функция ищет иконку сначала в выбранной теме,  потом в списке стандартных иконок
function icons($name,$code='path')
{
global $set;

$name=preg_replace('#[^a-z0-9 _\-\.]#i', null, $name);
if (file_exists(H."style/themes/$set[set_them]/icons/$name") && $name!=null)
{
$path= "/style/themes/$set[set_them]/icons/$name";

}
elseif (file_exists(H."style/icons/$name") && $name!=null)
{
$path= "/style/icons/$name";

}
else
{
$path= "/style/icons/default.png";

}


if ($code=='path')
return $path;
else
return "<img src=\"$path\" alt=\"\" />\n";
}
?>