CREATE TABLE IF NOT EXISTS `downnik_dir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num` int(11) DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `ras` varchar(512) NOT NULL,
  `maxfilesize` int(11) NOT NULL,
  `dir` varchar(512) NOT NULL DEFAULT '/',
  `dir_osn` varchar(512) DEFAULT '/',
  `upload` set('1','0') NOT NULL DEFAULT '0',
  `my` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `num` (`num`),
  KEY `dir` (`dir`(333))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Дамп данных таблицы `downnik_dir`
--

--INSERT INTO `downnik_dir` (`id`, `num`, `name`, `ras`, `maxfilesize`, `dir`, `dir_osn`, `upload`, `my`) VALUES
--(1, 0, '个人档案', 'wmv;zip;rar;tar;avi;3gp;mp4;mp3;amr;txt;cab;thm;sdt;nth;mtf;col;scs;utz;gif;jpg;jpeg;bmp;png;wbmp;pic;ani;pco;mmf;mid;amr;mp3;wav;aac;seq;vox;dxm;imy;emy;pmd;rng;doc;docx;swf;tsk;apk;sis;sisx;jar', 33554432, '/Lichnye_fajly/', '/', '1', 1);

-- 默认不限制文件后缀
INSERT INTO `downnik_dir` (`id`, `num`, `name`, `ras`, `maxfilesize`, `dir`, `dir_osn`, `upload`, `my`) VALUES (1, 0, '个人档案', '*', 33554432, '/Lichnye_fajly/', '/', '1', 1);
