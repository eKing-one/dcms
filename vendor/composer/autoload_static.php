<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1d5c899fd1cfa4469bbd76b2735efb15
{
    public static $prefixLengthsPsr4 = array (
        'U' => 
        array (
            'UAParser\\' => 9,
        ),
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Cache\\' => 10,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'I' => 
        array (
            'IPLib\\' => 6,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'D' => 
        array (
            'Detection\\' => 10,
        ),
        'C' => 
        array (
            'Composer\\CaBundle\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'UAParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/ua-parser/uap-php/src',
        ),
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/cache/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'IPLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/mlocati/ip-lib/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Detection\\' => 
        array (
            0 => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src',
        ),
        'Composer\\CaBundle\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/ca-bundle/src',
        ),
    );

    public static $classMap = array (
        'AMFReader' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'AMFStream' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'AVCSequenceParameterSetReader' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Image_XMP' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.xmp.php',
        'getID3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.php',
        'getID3_cached_dbm' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/extension.cache.dbm.php',
        'getID3_cached_mysql' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/extension.cache.mysql.php',
        'getID3_cached_mysqli' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/extension.cache.mysqli.php',
        'getID3_cached_sqlite3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/extension.cache.sqlite3.php',
        'getid3_7zip' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.7zip.php',
        'getid3_aa' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.aa.php',
        'getid3_aac' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.aac.php',
        'getid3_ac3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.ac3.php',
        'getid3_amr' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.amr.php',
        'getid3_apetag' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.apetag.php',
        'getid3_asf' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.asf.php',
        'getid3_au' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.au.php',
        'getid3_avr' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.avr.php',
        'getid3_bink' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.bink.php',
        'getid3_bmp' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.bmp.php',
        'getid3_bonk' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.bonk.php',
        'getid3_cue' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.cue.php',
        'getid3_dsdiff' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.dsdiff.php',
        'getid3_dsf' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.dsf.php',
        'getid3_dss' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.dss.php',
        'getid3_dts' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.dts.php',
        'getid3_efax' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.efax.php',
        'getid3_exception' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.php',
        'getid3_exe' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.exe.php',
        'getid3_flac' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.flac.php',
        'getid3_flv' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.flv.php',
        'getid3_gif' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.gif.php',
        'getid3_gzip' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.gzip.php',
        'getid3_handler' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.php',
        'getid3_hpk' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.hpk.php',
        'getid3_id3v1' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.id3v1.php',
        'getid3_id3v2' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.id3v2.php',
        'getid3_iso' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.iso.php',
        'getid3_ivf' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.ivf.php',
        'getid3_jpg' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.jpg.php',
        'getid3_la' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.la.php',
        'getid3_lib' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/getid3.lib.php',
        'getid3_lpac' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.lpac.php',
        'getid3_lyrics3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.lyrics3.php',
        'getid3_matroska' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.matroska.php',
        'getid3_midi' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.midi.php',
        'getid3_mod' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.mod.php',
        'getid3_monkey' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.monkey.php',
        'getid3_mp3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.mp3.php',
        'getid3_mpc' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.mpc.php',
        'getid3_mpeg' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.mpeg.php',
        'getid3_msoffice' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.msoffice.php',
        'getid3_nsv' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.nsv.php',
        'getid3_ogg' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.ogg.php',
        'getid3_optimfrog' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.optimfrog.php',
        'getid3_par2' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.par2.php',
        'getid3_pcd' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.pcd.php',
        'getid3_pdf' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.pdf.php',
        'getid3_png' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.png.php',
        'getid3_quicktime' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.quicktime.php',
        'getid3_rar' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.rar.php',
        'getid3_real' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.real.php',
        'getid3_riff' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.riff.php',
        'getid3_rkau' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.rkau.php',
        'getid3_shorten' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.shorten.php',
        'getid3_svg' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.svg.php',
        'getid3_swf' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.swf.php',
        'getid3_szip' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.szip.php',
        'getid3_tag_nikon_nctg' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.tag.nikon-nctg.php',
        'getid3_tak' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.tak.php',
        'getid3_tar' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.tar.php',
        'getid3_tiff' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.graphic.tiff.php',
        'getid3_torrent' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.misc.torrent.php',
        'getid3_ts' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.ts.php',
        'getid3_tta' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.tta.php',
        'getid3_voc' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.voc.php',
        'getid3_vqf' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.vqf.php',
        'getid3_wavpack' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio.wavpack.php',
        'getid3_write_apetag' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.apetag.php',
        'getid3_write_id3v1' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.id3v1.php',
        'getid3_write_id3v2' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.id3v2.php',
        'getid3_write_lyrics3' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.lyrics3.php',
        'getid3_write_metaflac' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.metaflac.php',
        'getid3_write_real' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.real.php',
        'getid3_write_vorbiscomment' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.vorbiscomment.php',
        'getid3_writetags' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/write.php',
        'getid3_wtv' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.audio-video.wtv.php',
        'getid3_xz' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.xz.php',
        'getid3_zip' => __DIR__ . '/..' . '/james-heinrich/getid3/getid3/module.archive.zip.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1d5c899fd1cfa4469bbd76b2735efb15::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1d5c899fd1cfa4469bbd76b2735efb15::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1d5c899fd1cfa4469bbd76b2735efb15::$classMap;

        }, null, ClassLoader::class);
    }
}
