<?php




/**
 * @package    frames
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class frame_class extends keimeno_class
{
    #index.php?page=frame_png&fid='.$fid.'&color_id='.$color_id.'&frame_pic='.$this->frame_pic.'&ink='.$reduce_procent

    var $fid = null;
    var $color_id = "";
    var $frame_pic = "";
    var $ink = 0;
    var $frame_obj = "";
    var $newWidth = 0;
    var $newHeight = 0;
    var $frame_img_x = 0;
    var $frame_img_y = 0;
    var $col_ids = array();


    var $frame_img = null;
    var $cachefile_root = "";
    var $cachefile = "";
    var $foto_img = null;
    var $foto_width = 0;
    var $foto_height = 0;
    var $width_orig = 0;
    var $height_orig = 0;


    /**
     * frame_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->graph_func = new graphic_class();
    }

    /**
     * frame_class::set_opt()
     * 
     * @param mixed $fid
     * @param mixed $fcolor_1
     * @param mixed $fcolor_2
     * @param mixed $color_id
     * @return
     */
    function set_opt($fid, $fcolor_1, $fcolor_2, $color_id)
    {
        $this->fid = (int)$fid;
        $this->fcolor_1 = $fcolor_1;
        $this->fcolor_2 = $fcolor_2;
        $this->color_id = $color_id;
        $this->loadFrameObj();
    }

    /**
     * frame_class::load_frame_colors()
     * 
     * @return
     */
    function load_frame_colors()
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FRAMECOLORS .
            " ORDER BY kname");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->frame_color_ids[] = $row['id'];
        }
    }

    /**
     * frame_class::loadFrameObj()
     * 
     * @return
     */
    function loadFrameObj()
    {
        if ($this->fid > 0) {
            $this->frame_obj = $this->db->query_first("SELECT F.*,C.* FROM " .
                TBL_CMS_FRAMEDEF . " F,  " . TBL_CMS_FRAMECOLORS . " C WHERE C.id='" . $this->
                color_id . "' AND F.id='" . $this->fid . "' LIMIT 1");
        }
        if ($this->fcolor_1 != "" && $this->fcolor_2 != "") {
            $this->frame_obj = $this->db->query_first("SELECT F.*,C.* FROM " .
                TBL_CMS_FRAMEDEF . " F, " . TBL_CMS_FRAMECOLORS . " C WHERE C.fcolor_1='" . $this->
                fcolor_1 . "' AND C.fcolor_2='" . $this->fcolor_2 . "' LIMIT 1");
        }
        $this->frame_obj['ident'] = 'ka';
    }

    /**
     * frame_class::LoadJpeg()
     * 
     * @param mixed $imgname
     * @return
     */
    function LoadJpeg($imgname)
    {
        try {
            $im_pic = ImageCreateFromJPEG($imgname);
            /* Versuch, Datei zu öffnen */
            if (!$im_pic) {
                /* Prüfen, ob fehlgeschlagen */
                $im_pic = ImageCreate(150, 30);
                /* Erzeugen eines leeren Bildes */
                $bgc = ImageColorAllocate($im_pic, 255, 255, 255);
                $tc = ImageColorAllocate($im_pic, 0, 0, 0);
                ImageFilledRectangle($im_pic, 0, 0, 150, 30, $bgc);
                /* Ausgabe einer Fehlermeldung */
                ImageString($im_pic, 1, 5, 5, "Fehler beim Öffnen von: $imgname", $tc);
            }
        }
        catch (exception $e) {
            #var_dump($e->getMessage());
        }
        return $im_pic;
    }

    /**
     * frame_class::createFoto()
     * 
     * @param mixed $foto_jpg
     * @param mixed $fw
     * @param mixed $fh
     * @return
     */
    function createFoto($foto_jpg, $fw, $fh)
    {
        list($width_orig, $height_orig) = getimagesize($this->frame_pic);
        $ratio_orig = $width_orig / $height_orig;
        if ($fw / $fh > $ratio_orig)
            $fw = $fh * $ratio_orig;
        else
            $fh = $fw / $ratio_orig;
        $this->foto_width = $fw - ($this->frame_obj['ulappung_px'] * 2);
        $this->foto_height = $fh - ($this->frame_obj['ulappung_px'] * 2);
        #$image_p = imagecreatetruecolor($fw, $fh);
        #imagecopyresampled($image_p, $foto_jpg, 0, 0, $this->frame_obj['ulappung_px'], $this->frame_obj['ulappung_px'], $this->foto_width, $this->foto_height, $width_orig-$this->frame_obj['ulappung_px'], $height_orig-$this->frame_obj['ulappung_px']);
        #$this->foto_img=$image_p;

        $this->foto_img = $this->resizeImage($foto_jpg, $fw, $fh);
        return $this->foto_img;
    }

    /**
     * frame_class::genCacheFileName()
     * 
     * @return
     */
    function genCacheFileName()
    {
        return 'cache/frame_' . $this->fid . '_' . $this->frame_obj['paspa_width'] . '_' .
            $this->color_id . '_' . $this->ink . '_' . $this->newWidth . '_' . $this->
            newHeight . '_' . basename($this->frame_pic) . '.jpg';
    }

    /**
     * frame_class::genFramePic()
     * 
     * @param bool $pastrough
     * @param integer $max_width
     * @return
     */
    function genFramePic($pastrough = false, $max_width = 0)
    {
        $this->cachefile = $this->genCacheFileName();
        $this->cachefile_root = CMS_ROOT . $this->cachefile;

        if (file_exists($this->cachefile_root) && ISADMIN != 1) {
            $this->frame_img = $this->LoadJpeg($this->cachefile_root);
        } else {
            $this->createFrame(true);
            imageJPEG($this->frame_img, $this->cachefile_root, 85);
            if ($max_width > 0) {
                system("convert " . $this->cachefile_root . " -resize " . $max_width . "x  " . $this->
                    cachefile_root . "");
            }
            clearstatcache();
        }
        $this->frame_img_x = imagesx($this->frame_img);
        $this->frame_img_y = imagesy($this->frame_img);
        if ($pastrough === true) {
            header('Content-type: image/jpeg');
            imagejpeg($this->frame_img);
        }
    }

    /**
     * frame_class::calcFotoDim()
     * 
     * @return
     */
    function calcFotoDim()
    {
        list($this->width_orig, $this->height_orig) = getimagesize($this->frame_pic);
        if ($this->width_orig > $this->height_orig) {
            $this->foto_height = $this->FOTO_HEIGHT_PX;
            $this->foto_width = $this->foto_height / $this->height_orig * $this->width_orig;
        } else {
            $this->foto_width = $this->FOTO_HEIGHT_PX;
            $this->foto_height = $this->foto_width / $this->width_orig * $this->height_orig;
        }
    }

    /**
     * frame_class::resizeImage()
     * 
     * @param mixed $filename
     * @param mixed $width
     * @param mixed $height
     * @return
     */
    function resizeImage($filename, $width, $height)
    {
        system("convert " . $filename . " -resize '" . $width . "x" . $height . ">'  " .
            $filename . "_tmp.jpg"); // Skalieren
        $img = $this->LoadJpeg($filename . "_tmp.jpg");
        @unlink($filename . "_tmp.jpg");
        return $img;
    }

    /**
     * frame_class::createFrame()
     * 
     * @param bool $addFoto
     * @return
     */
    function createFrame($addFoto = true)
    {
        if ($this->frame_pic != "" && file_Exists($this->frame_pic)) {
            $root_frame = $this->frame_pic;
            $root_target = CMS_ROOT . CACHE . 'foto_sm_' . basename($this->frame_pic);
            if (!file_exists($root_target)) {
                system("convert " . $root_frame . " -resize '1500x>'  " . $root_target); // Skalieren
            }
            $image_tmp = $this->LoadJpeg($root_target);
            $fill_color = imagecolorat($image_tmp, 1, 1);
            $this->calcFotoDim();
            $height = floor($this->foto_height + (($this->frame_obj['frame_width'] - $this->
                frame_obj['ulappung_px'] + $this->frame_obj['frame_width_inner'] + $this->
                frame_obj['paspa_width']) * 2));
            $width = floor($this->foto_width + (($this->frame_obj['frame_width'] - $this->
                frame_obj['ulappung_px'] + $this->frame_obj['frame_width_inner'] + $this->
                frame_obj['paspa_width']) * 2));
        }

        if (!$width)
            $width = 300;
        if (!$height)
            $height = 200;

        $im = imagecreatetruecolor($width, $height);
        imageantialias($im, true);

        //**********************
        // COLORS
        //**********************
        if ($this->fcolor_1)
            $this->frame_obj['fcolor_1'] = $this->fcolor_1;
        if ($this->fcolor_2)
            $this->frame_obj['fcolor_2'] = $this->fcolor_2;

        $rgb = $this->graph_func->HEX2RGB($this->frame_obj['fcolor_1']);
        $fcolor_1 = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        $rgb = $this->graph_func->HEX2RGB($this->frame_obj['fcolor_2']);
        $fcolor_2 = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        $black = imagecolorallocate($im, 0, 0, 0); //black text
        $white = imagecolorallocate($im, 255, 255, 255); //white
        $red = imagecolorallocate($im, 255, 0, 0); //black text
        $grey = imagecolorallocate($im, 209, 209, 209);
        $hellblau = imagecolorallocate($im, 233, 245, 253);
        $hellorange = imagecolorallocate($im, 251, 213, 163);
        $green = imagecolorallocate($im, 45, 186, 3);


        //*************************************
        // HOLZ RAHMEN AUS PROFILFOTO ERZEUGEN
        //*************************************

        if ($this->frame_obj['frame_foto'] != "") {
            $rahmen_foto = $this->LoadJpeg(CMS_ROOT .
                'includes/modules/frames/data/profil_foto/' . $this->frame_obj['frame_foto']);

            list($rf_width, $rf_height) = getimagesize(CMS_ROOT .
                'includes/modules/frames/data/profil_foto/' . $this->frame_obj['frame_foto']);
            $leisten_breite_px = $this->frame_obj['frame_width'] + $this->frame_obj['frame_width_inner'];
            # echo $rf_width;
            # echoarr($this->frame_obj);die;
            # $rf_width entspricht $leisten_breite_px
            if ($leisten_breite_px == 0)
                return;
            $new_width = $leisten_breite_px;
            $new_height = round($rf_height / $rf_width * $new_width);
            $image_p = imagecreatetruecolor($new_width, $new_height);
            if (!is_bool($image_p) && !empty($image_p))
                imageantialias($image_p, true);
            imagecopyresampled($image_p, $rahmen_foto, 0, 0, 0, 0, $new_width, $new_height,
                $rf_width, $rf_height);

            // Rechter Rand
            $anz = ceil($height / $new_height);
            for ($i = 0; $i <= $anz; $i++) {
                imagecopymerge($im, $image_p, ($width - $new_width), ($i * $new_height), 0, 0, $new_width,
                    $new_height, 100);
            }

            // Linker Rand
            $rotate = imagerotate($image_p, 180, 0);
            $anz = ceil($height / $new_height);
            for ($i = 0; $i <= $anz; $i++) {
                imagecopymerge($im, $rotate, 0, ($i * $new_height), 0, 0, $new_width, $new_height,
                    100);
            }

            // Drehung für obere und untere Leiste
            $flip_image = imagecreatetruecolor($new_height, $new_height);
            imagecopymerge($flip_image, $image_p, 0, 0, 0, 0, $new_width, $new_height, 100);
            $flip_image = imagerotate($flip_image, 90, 0);
            $image_p = imagecreatetruecolor($new_height, $new_width);
            imagecopymerge($image_p, $flip_image, 0, 0, 0, ($new_height - $new_width), $new_height,
                $new_height, 100);
            imagedestroy($flip_image);

            //Vervielfältigung auf die Länge in ein neues Image
            $o_leiste = imagecreatetruecolor($width, $new_width); // die new_height entspricht in diesem Falle der width (bild wurde gedreht)
            $anz = ceil($width / $new_height);
            for ($i = 0; $i <= $anz; $i++) {
                imagecopymerge($o_leiste, $image_p, ($i * $new_height), 0, 0, 0, $new_height, $new_width,
                    100);
            }

            // 45 Grad Schnitt oben rechts
            $k = 0;
            for ($x = ($width - $new_height); $x <= $width; $x++) {
                $y = ($new_height - $k);
                imageline($o_leiste, $x, $y, $width, $y, $white);
                $k++;
            }
            // 45 Grad Schnitt oben links
            $y = 0;
            for ($x = 0; $x <= $new_height; $x++) {
                imageline($o_leiste, 0, $y, $x, $y, $white);
                $y++;
            }
            imagecolortransparent($o_leiste, $white);

            // Obere Leiste in das Rahmenmodel hinzufügen
            imagecopymerge($im, $o_leiste, 0, 0, 0, 0, $width, $new_width, 100);
            #imagestring ($im, 1, 1, 1, $width, $red );


            // Untere Leiste
            $rotate = imagerotate($o_leiste, 180, 0);
            imagecolortransparent($rotate, $white);
            imagecopymerge($im, $rotate, 0, ($height - $new_width), 0, 0, $width, $new_width,
                100);
        } else {
            // ZEICHNE WIE FRÜHER EINEN RAHMEN
            imagefill($im, 0, 0, $fcolor_1);
            imagefilledrectangle($im, $this->frame_obj['frame_width'], $this->frame_obj['frame_width'],
                $width - $this->frame_obj['frame_width'], $height - $this->frame_obj['frame_width'],
                $fcolor_2);
        }

        //**********************
        // FRAME OUTER&INNER
        //**********************
        $next_x = $this->frame_obj['frame_width'] + $this->frame_obj['frame_width_inner'];
        $next_x1 = $width - ($this->frame_obj['frame_width'] + $this->frame_obj['frame_width_inner']);
        $next_y1 = $height - ($this->frame_obj['frame_width'] + $this->frame_obj['frame_width_inner']);
        //**********************
        // PASPA
        //**********************
        if ($this->frame_obj['paspa_width'] > 0) {
            imagefilledrectangle($im, $next_x, $next_x, $next_x1, $next_y1, $white);
            // Umrandung innen
            imagerectangle($im, $next_x, $next_x, $next_x1, $next_y1, $black);
            $paspa_x = $next_x + $this->frame_obj['paspa_width'];
            $paspa_x1 = $next_x1 - $this->frame_obj['paspa_width'];
            $paspa_y1 = $next_y1 - $this->frame_obj['paspa_width'];
            // Grauer IMG Platzhalter
            ImageColorTransparent($im, $fill_color);
            if ($this->frame_pic != "")
                imagefilledrectangle($im, $paspa_x, $paspa_x, $paspa_x1, $paspa_y1, $fill_color);
            else
                imagefilledrectangle($im, $paspa_x, $paspa_x, $paspa_x1, $paspa_y1, $grey);
        } else {
            //**********************
            // WITHOUT PASPA
            //**********************
            if ($this->frame_pic != "")
                imagefilledrectangle($im, $next_x, $next_x, $next_x1, $next_y1, $fill_color);
            else
                imagefilledrectangle($im, $next_x, $next_x, $next_x1, $next_y1, $grey);
        }

        //**********************
        // ADD FOTO
        //**********************
        if ($this->frame_pic != "" && file_Exists($this->frame_pic) && $addFoto == true) {
            $bild_x = $this->frame_obj['frame_width'] + $this->frame_obj['frame_width_inner'] +
                $this->frame_obj['paspa_width'];

            $this->createFoto($root_target, $this->foto_width, $this->foto_height);

            #imagecopymerge($im, $this->foto_img, $bild_x, $bild_x, 0, 0, imagesx($this->foto_img), imagesy($this->foto_img),100);
            imagecopymerge($im, $this->foto_img, $bild_x, $bild_x, 0, 0, $this->foto_width,
                $this->foto_height, 100);
            #imageJPEG($im, CMS_ROOT.'includes/test.jpg',85);
        }

        // ******************************
        // Profil Type "KA" = KANTEN AUSSEN
        // ******************************
        if ($this->frame_obj['ident'] == 'ka') {
            $ka_color = imagecolorat($im, 1, 1);
            $ka_color = $this->graph_func->darken($ka_color, 90, $im);
            // oben links schräge linie
            imageline($im, 0, 0, $this->frame_obj['frame_width'], $this->frame_obj['frame_width'],
                $ka_color);
            //unten rechts schräge linie
            imageline($im, $width - $this->frame_obj['frame_width'], $height - $this->
                frame_obj['frame_width'], $width, $height, $ka_color);
            //unten links schräge linie
            imageline($im, $this->frame_obj['frame_width'], $height - $this->frame_obj['frame_width'],
                0, $height, $ka_color);
            //oben rechts schräge linie
            imageline($im, $width - $this->frame_obj['frame_width'], $this->frame_obj['frame_width'],
                $width, 0, $ka_color);
        }

        // ******************************
        // Profil Type "KI" = KANTEN INNEN
        // ******************************
        if ($this->frame_obj['ident'] == 'ki') {
            $ki_color = imagecolorat($im, $this->frame_obj['frame_width'], $this->frame_obj['frame_width']);
            $ki_color = $this->graph_func->darken($ki_color, 70, $im);
            // oben links schräge linie
            imageline($im, $this->frame_obj['frame_width'], $this->frame_obj['frame_width'],
                $next_x, $next_x, $ki_color);
            //unten rechts schräge linie
            imageline($im, $width - $this->frame_obj['frame_width'], $height - $this->
                frame_obj['frame_width'], $next_x1, $next_y1, $ki_color);
            //unten links schräge linie
            imageline($im, $this->frame_obj['frame_width'], $height - $this->frame_obj['frame_width'],
                $next_x, $next_y1, $ki_color);
            //oben rechts schräge linie
            imageline($im, $width - $this->frame_obj['frame_width'], $this->frame_obj['frame_width'],
                $next_x1, $next_x, $ki_color);
        }


        //**********************
        // Umrandung innen
        //**********************
        #$rgb=$this->graph_func->HEX2RGB($this->frame_obj['fcolor_4']);
        #$inner_color = 			imagecolorallocate ($im, $rgb[0], $rgb[1], $rgb[2]);
        $inner_color = imagecolorat($im, $next_x, $next_x);
        $inner_color = $this->graph_func->darken($inner_color, 60, $im);
        imagerectangle($im, $next_x, $next_x, $next_x1, $next_y1, $inner_color);
        //**********************
        // Umrandung aussen
        //**********************
        #$rgb=$this->graph_func->HEX2RGB($this->frame_obj['fcolor_3']);
        #$fcolor_3 = 			imagecolorallocate ($im, $rgb[0], $rgb[1], $rgb[2]);
        $outer_color = imagecolorat($im, $width - 1, $height - 1);
        $r = ($outer_color >> 16) & 0xFF;
        $g = ($outer_color >> 8) & 0xFF;
        $b = $outer_color & 0xFF;
        $outer_color = imagecolorallocate($im, $r / 2, $g / 2, $b / 2);
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $outer_color);


        //**********************
        // RESIZE PICTURE IF NEEDED
        //**********************
        if ($this->newWidth > 0 || $this->newHeight > 0) {
            $im = $this->graph_func->resizePictureObj($im, $this->newWidth, $this->
                newHeight);
        }

        $this->ink = intval($this->ink);
        if ($this->ink > 0) {
            $ratio_orig = $width / $height;
            $this->foto_width = $this->ink * $ratio_orig;
            $image_p = imagecreatetruecolor($this->foto_width, $this->ink);
            imageantialias($image_p, true);
            imagecopyresampled($image_p, $im, 0, 0, 0, 0, $this->foto_width, $this->ink, $width,
                $height);
            $im = $image_p;
        }
        $this->frame_img = $im;
        #   header('Content-type: image/jpeg');
        #             imagejpeg($im);
        #             die;
    }


}

?>