<?
if (is_file(H."sys/obmen/screens/128/$file_id[id].gif"))
{
	echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='Скрин...' /><br />\n";
}
echo '<script type="text/javascript" src="/ajax/js/audio-player.js"></script>                                <script type="text/javascript">                                AudioPlayer.setup                                (                                    "/ajax/js/player.swf",                                    {                                        width:"100%",                                        animation:"yes",                                        encode:"no",                                        initialvolume:"100",                                        remaining:"yes",                                        noinfo:"no",                                        buffer:"2",                                        checkpolicy:"no",                                        rtl:"no",                                        bg:"064a91",                                        text:"000000",                                        leftbg:"064a91",                                        lefticon:"fee300",                                        volslider:"fee300",                                        voltrack:"ffffff",                                        rightbg:"064a91",                                        rightbghover:"064a91",                                        righticon:"fee300",                                        righticonhover:"fee300",                                        track:"FFFFFF",                                        loader:"fee300",                                        border:"D2F0FF",                                        tracker:"fee300",                                        skip:"ff284b",                                        pagebg:"064a91",                                        transparentpagebg:"yes"                                    }                                );                                </script>                                <p id="audioplayer_1">Для отображение плеера необходимо включить Javascript</p>                                <script type="text/javascript">                                AudioPlayer.embed                                (                                    "audioplayer_1",                                    {                                        soundFile: "http://'.$_SERVER['SERVER_NAME'].'/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'",                                        titles: "'.$file_id['name'].'",                                        artists: "",                                        autostart: "no"                                    }                                );                                </script>';

if ($file_id['opis']!=NULL)
{
	echo "Описание: ";
	echo output_text($file_id['opis']);
	echo "<br />\n";
}

if (class_exists('ffmpeg_movie')){
$media = new ffmpeg_movie($file);

if (intval($media->getDuration())>3599)
echo ''.intval($media->getDuration()/3600).":".date('s',fmod($media->getDuration()/60,60)).":".date('s',fmod($media->getDuration(),3600))."\n";
elseif (intval($media->getDuration())>59)
echo ''.intval($media->getDuration()/60).":".date('s',fmod($media->getDuration(),60))."\n";
else
echo ''.intval($media->getDuration())." сек\n";
echo "| ".ceil(($media->getBitRate())/1024)." KBPS\n";
if($media->getAudioChannels()==1)echo "| Mono\n";else echo "| Stereo\n";
echo '| '.$media->getAudioSampleRate()." Гц\n";
if(($media->getArtist())<>""){
if (function_exists('iconv'))
echo '| '.iconv('windows-1251', 'utf-8', $media->getArtist())."\n";
else
echo '| '.$media->getArtist()."\n";
}
if(($media->getGenre())<>"")echo '| '.$media->getGenre()."\n";
}
else
{
include_once H.'sys/inc/mp3.php';
$id3 = new MP3_Id(); 
  $result = $id3->read($file); 
  $result = $id3->study();
if(($id3->getTag('length')<>0)){echo ''.$id3->getTag('length')."\n";}
if(($id3->getTag('bitrate'))<>0){echo'| '.$id3->getTag('bitrate')." KBPS\n";}
if(($id3->getTag('mode'))<>""){echo '| '.$id3->getTag('mode')."\n";}
if(($id3->getTag('frequency'))<>0){echo '| '.$id3->getTag('frequency')." Гц\n";}
if(($id3->getTag('album'))<>""){
if (function_exists('iconv'))
echo '| '.iconv('windows-1251', 'utf-8', $id3->getTag('album'))."\n";
else
echo '| '.$id3->getTag('album')."\n";
}
if(($id3->getTag('artists'))<>""){
if (function_exists('iconv'))
echo '| '.iconv('windows-1251', 'utf-8', $id3->getTag('artists'))."\n";
else
echo '| '.$id3->getTag('artists')."\n";
}
if(($id3->getTag('genre'))<>""){echo ', '.$id3->getTag('genre')."\n";}
}
?>