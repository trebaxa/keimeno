<?php
/**
 * @package    frames
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class frames_setup_class extends modules_class
{


    /**
     * frames_setup_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * frames_setup_class::install()
     * 
     * @return
     */
    function install()
    {
        $sql[] = "DROP TABLE IF EXISTS `" . TBL_CMS_FRAMECOLORS . "`;";
        $sql[] = "CREATE TABLE `" . TBL_CMS_FRAMECOLORS . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fcolor_1` varchar(7) DEFAULT NULL,
  `fcolor_2` varchar(7) DEFAULT NULL,
  `kname` varchar(255) DEFAULT NULL,
  `frame_foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;";
        $sql[] = "INSERT INTO `" . TBL_CMS_FRAMECOLORS .
            "` (`id`, `fcolor_1`, `fcolor_2`, `kname`, `frame_foto`) VALUES
(2, '2B242C', 'B8AD80', '323-08-1 30 schwarz gold', 'PROFIL_FOTO_36.jpg'),
(3, '2B242C', '2B242C', '323-08-2 30 schwarz (einfarbig)', 'PROFIL_FOTO_37.jpg'),
(4, '9E1A16', '9E1A16', '323-02-2 30 rot (einfarbig)', 'PROFIL_FOTO_38.jpg'),
(5, '9E1A16', 'BD885E', '323-02-1 30 rot gold', 'PROFIL_FOTO_39.jpg'),
(6, '292B66', '292B66', '323-06-2 30 blau (einfarbig)', 'PROFIL_FOTO_40.jpg'),
(7, '292B66', '7D8391', '323-06-1 30 blau silber', 'PROFIL_FOTO_41.jpg'),
(8, '524652', '524652', '323-05-2 30 dunkelgrau (einfarbig)', 'PROFIL_FOTO_42.jpg'),
(9, '524652', '898788', '323-05-1 30 dunkelgrau silber', 'PROFIL_FOTO_43.jpg'),
(10, '89896F', '89896F', '323-03-2 30 graugrün (einfarbig)', 'PROFIL_FOTO_44.jpg'),
(11, '89896F', '879193', '323-03-1 30 graugrün silber', 'PROFIL_FOTO_45.jpg'),
(12, '894739', '894739', '323-04-2 30 braun (einfarbig)', 'PROFIL_FOTO_46.jpg'),
(13, '894739', '7E8597', '323-04-1 30 braun silber', 'PROFIL_FOTO_47.jpg'),
(14, 'A57C68', 'A57C68', '323-07-2 30 hellbraun (einfarbig)', 'PROFIL_FOTO_48.jpg'),
(15, 'A57C68', 'B2A59D', '323-07-1 30 hellbraun silber', 'PROFIL_FOTO_49.jpg'),
(16, 'A5A48F', 'A5A48F', '323-01-2 30 silbergrün (einfarbig)', 'PROFIL_FOTO_50.jpg'),
(17, 'A5A48F', '8C9497', '323-01-1 30 silbergrün silberhhh', 'PROFIL_FOTO_51.jpg'),
(18, '7D849E', '7D849E', '323-09-2 30 hellblau (einfarbig)', 'PROFIL_FOTO_52.jpg'),
(19, '44355C', '44355C', '323-10-2 30 lila (einfarbig)', 'PROFIL_FOTO_53.jpg'),
(20, '9D591C', '9D591C', '323-11-2 30P orangebraun (einfarbig)', 'PROFIL_FOTO_54.jpg'),
(21, '44355C', '9B99A7', '323-10-1 30 lila silber', 'PROFIL_FOTO_55.jpg'),
(22, '9D591C', 'AE8547', '323-11-1 30 orangebraun gold', 'PROFIL_FOTO_56.jpg'),
(23, '7D849E', 'D3B883', '323-09-1 30 hellblau sandsilber', 'PROFIL_FOTO_57.jpg');";
        $sql[] = "DROP TABLE IF EXISTS `" . TBL_CMS_FRAMEDEF . "`;";
        $sql[] = "CREATE TABLE `" . TBL_CMS_FRAMEDEF . "` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `fcolor_1` varchar(6) DEFAULT NULL,
  `fcolor_2` varchar(6) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `frame_width` int(11) NOT NULL DEFAULT '0',
  `frame_width_inner` int(11) NOT NULL DEFAULT '0',
  `profil_type` int(11) NOT NULL DEFAULT '1',
  `paspa_width` int(11) NOT NULL DEFAULT '0',
  `fname_shop` varchar(255) DEFAULT NULL,
  `height_cm` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paspa_width_cm` decimal(10,2) NOT NULL DEFAULT '0.00',
  `height_px` int(11) NOT NULL DEFAULT '0',
  `ulappung_px` int(11) NOT NULL DEFAULT '0',
  `aufpreis_br` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `aufpreis_desc` varchar(255) DEFAULT NULL,
  `frame_foto` varchar(255) DEFAULT NULL,
  `width_cm` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=126 ;";
        $sql[] = "INSERT INTO `" . TBL_CMS_FRAMEDEF .
            "` (`id`, `fcolor_1`, `fcolor_2`, `fname`, `frame_width`, `frame_width_inner`, `profil_type`, `paspa_width`, `fname_shop`, `height_cm`, `paspa_width_cm`, `height_px`, `ulappung_px`, `aufpreis_br`, `aufpreis_desc`, `frame_foto`, `width_cm`) VALUES
(53, '', '', '305x407 - 353', 15, 9, 1, 0, 'Rahmen Aldebaran major, ohne Passepartout', 30.50, 0.00, 0, 5, 0.0000, '', '', 40.70),
(37, '', '', '203x305 - 353', 23, 14, 1, 0, 'Rahmen Vega major, ohne Passepartout', 20.30, 0.00, 0, 8, 0.0000, '', '', 30.50),
(48, '', '', '127x178 - 323', 36, 22, 1, 0, 'Rahmen Regulus minor, ohne Passepartout', 12.70, 0.00, 0, 13, 0.0000, '', '', 17.80),
(52, '', '', '210x280 - 353', 22, 13, 1, 0, 'Rahmen Sirius major, ohne Passepartout', 21.00, 0.00, 0, 8, 0.0000, '', '', 28.00),
(61, '', '', '305x450 - 353', 15, 9, 1, 0, 'Rahmen Capella major, ohne Passepartout', 30.50, 0.00, 0, 5, 0.0000, '', '', 45.00),
(83, '', '', 'EL Sonnenuntergang 210x280 - 353', 22, 13, 1, 0, 'Rahmen', 21.00, 0.00, 0, 8, 0.0000, '', '', 28.00),
(111, '', '', 'Sol-y-Mar 400x600 - 353', 12, 7, 1, 0, 'Rahmen', 40.00, 0.00, 0, 4, 0.0000, '', '', 60.00),
(117, '', '', 'PolarSky 305x450 - 353', 15, 9, 1, 0, 'Rahmen', 30.50, 0.00, 0, 5, 0.0000, '', '', 45.00);";
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    /**
     * frames_setup_class::uninstall()
     * 
     * @return
     */
    function uninstall()
    {
        $sql[] = "DROP TABLE " . TBL_CMS_FRAMEDEF;
        $sql[] = "DROP TABLE " . TBL_CMS_FRAMECOLORS;
        foreach ($sql as $key => $sql) {
            $this->exec_sql($sql);
        }
    }

    /**
     * frames_setup_class::update()
     * 
     * @return
     */
    function update()
    {

    }
}

?>