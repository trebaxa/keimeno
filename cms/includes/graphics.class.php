<?PHP

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

define('ROOT_CACHE', 'cache/');

class graphic_class extends keimeno_class {
    var $width = "";
    var $height = "";
    var $countData = "";
    var $md5Legende = "";
    var $file_ident = "";
    var $cache_file = "";

    var $config_cache_maxfiles = 30;
    var $config_cache_maxage = 0;
    var $config_cache_maxsize = 0;

    /**
     * graphic_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }


    /**
     * graphic_class::HEX2RGB()
     * 
     * @param mixed $color
     * @return
     */
    public static function HEX2RGB($color) {
        $color_array = array();
        $hex_color = strtoupper($color);
        for ($i = 0; $i < 6; $i++) {
            $hex = substr($hex_color, $i, 1);
            switch ($hex) {
                case "A":
                    $num = 10;
                    break;
                case "B":
                    $num = 11;
                    break;
                case "C":
                    $num = 12;
                    break;
                case "D":
                    $num = 13;
                    break;
                case "E":
                    $num = 14;
                    break;
                case "F":
                    $num = 15;
                    break;
                default:
                    $num = $hex;
                    break;
            }
            array_push($color_array, $num);
        }
        $R = (($color_array[0] * 16) + $color_array[1]);
        $G = (($color_array[2] * 16) + $color_array[3]);
        $B = (($color_array[4] * 16) + $color_array[5]);
        return array(
            $R,
            $G,
            $B);
        unset($color_array, $hex, $R, $G, $B);
    }


    /**
     * graphic_class::gen_random_number()
     * 
     * @param integer $length
     * @return
     */
    public static function gen_random_number($length = 8) {
        $randstr = '';
        srand((double)microtime() * 1000000);
        //our array add all letters and numbers if you wish
        $chars = array(
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '0');
        for ($rand = 0; $rand < $length; $rand++) {
            $random = rand(0, count($chars) - 1);
            $randstr .= $chars[$random];
        }
        return $randstr;
    }


    /**
     * graphic_class::convert2ImgColor()
     * 
     * @param mixed $img
     * @param mixed $hex_color
     * @return
     */
    public static function convert2ImgColor($img, $hex_color) {
        $rgb = self::HEX2RGB($hex_color);
        return imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
    }

    /**
     * graphic_class::genCacheFileName()
     * 
     * @param string $prefix
     * @param string $ext
     * @return
     */
    function genCacheFileName($prefix = 'chart_', $ext = '.png') {
        $this->cache_file = ROOT_CACHE . $prefix . substr(base64_encode($this->width . $this->height . $this->file_ident), 1, 200) . $ext;
        return './' . $this->cache_file;
    }

    /**
     * graphic_class::LoadCacheFile()
     * 
     * @return
     */
    function LoadCacheFile() {
        $img_png = @ImageCreateFromPNG($this->genCacheFileName());
        if (!$img_png) {
            return false;
        }
        return $img_png;
    }

    /**
     * graphic_class::doCacheFileOutput()
     * 
     * @return
     */
    function doCacheFileOutput() {
        $this->CleanUpCacheDirectory();
        $im = $this->LoadCacheFile();
        if ($im) {
            imagePNG($im);
            exit;
        }
    }

    /**
     * graphic_class::CleanUpCacheDirectory()
     * 
     * @return
     */
    function CleanUpCacheDirectory() {
        if (($this->config_cache_maxage > 0) || ($this->config_cache_maxsize > 0) || ($this->config_cache_maxfiles > 0)) {
            $CacheDirOldFilesAge = array();
            $CacheDirOldFilesSize = array();
            if ($dirhandle = opendir(CACHE)) {
                while ($oldcachefile = readdir($dirhandle)) {
                    if (strstr($oldcachefile, '.png') && strstr($oldcachefile, 'chart_')) {
                        $CacheDirOldFilesAge[$oldcachefile] = fileatime(CACHE . $oldcachefile);
                        if ($CacheDirOldFilesAge[$oldcachefile] == 0) {
                            $CacheDirOldFilesAge[$oldcachefile] = filemtime(CACHE . $oldcachefile);
                        }

                        $CacheDirOldFilesSize[$oldcachefile] = filesize(CACHE . $oldcachefile);
                    }
                }
            }
            asort($CacheDirOldFilesAge);

            if ($this->config_cache_maxfiles > 0) {
                $TotalCachedFiles = count($CacheDirOldFilesAge);
                $DeletedKeys = array();
                foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
                    if ($TotalCachedFiles > $this->config_cache_maxfiles) {
                        $TotalCachedFiles--;
                        unlink(CACHE . $oldcachefile);
                        $DeletedKeys[] = $oldcachefile;
                    }
                    else {
                        // there are few enough files to keep the rest
                        break;
                    }
                }
                foreach ($DeletedKeys as $oldcachefile) {
                    unset($CacheDirOldFilesAge[$oldcachefile]);
                    unset($CacheDirOldFilesSize[$oldcachefile]);
                }
            }

            if ($this->config_cache_maxage > 0) {
                $mindate = time() - $this->config_cache_maxage;
                $DeletedKeys = array();
                foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
                    if ($filedate > 0) {
                        if ($filedate < $mindate) {
                            unlink(CACHE . $oldcachefile);
                            $DeletedKeys[] = $oldcachefile;
                        }
                        else {
                            // the rest of the files are new enough to keep
                            break;
                        }
                    }
                }
                foreach ($DeletedKeys as $oldcachefile) {
                    unset($CacheDirOldFilesAge[$oldcachefile]);
                    unset($CacheDirOldFilesSize[$oldcachefile]);
                }
            }

            if ($this->config_cache_maxsize > 0) {
                $TotalCachedFileSize = array_sum($CacheDirOldFilesSize);
                $DeletedKeys = array();
                foreach ($CacheDirOldFilesAge as $oldcachefile => $filedate) {
                    if ($TotalCachedFileSize > $this->config_cache_maxsize) {
                        $TotalCachedFileSize -= $CacheDirOldFilesSize[$oldcachefile];
                        unlink(CACHE . $oldcachefile);
                        $DeletedKeys[] = $oldcachefile;
                    }
                    else {
                        // the total filesizes are small enough to keep the rest of the files
                        break;
                    }
                }
                foreach ($DeletedKeys as $oldcachefile) {
                    unset($CacheDirOldFilesAge[$oldcachefile]);
                    unset($CacheDirOldFilesSize[$oldcachefile]);
                }
            }

        }
        return true;
    }

    /**
     * graphic_class::split2RGB()
     * 
     * @param mixed $color
     * @return
     */
    function split2RGB($color) {
        $rgb['r'] = ($color >> 16) & 0xFF;
        $rgb['g'] = ($color >> 8) & 0xFF;
        $rgb['b'] = $color & 0xFF;
        return $rgb;
    }

    /**
     * graphic_class::darken()
     * 
     * @param mixed $color
     * @param mixed $procent
     * @param mixed $image_obj
     * @return
     */
    function darken($color, $procent, $image_obj) {
        $rgb = $this->split2RGB($color);
        $rgb['r'] = ($rgb['r'] / 100) * $procent;
        $rgb['g'] = ($rgb['g'] / 100) * $procent;
        $rgb['b'] = ($rgb['b'] / 100) * $procent;
        $new_color = imagecolorallocate($image_obj, $rgb['r'], $rgb['g'], $rgb['b']);
        return $new_color;
    }

    /**
     * graphic_class::LoadJpeg()
     * 
     * @param mixed $imgname
     * @return
     */
    function LoadJpeg($imgname) {
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
        return $im_pic;
    }

    private static function func_is_available($func) {
        if (ini_get('safe_mode'))
            return false;
        $disabled = ini_get('disable_functions');
        if ($disabled) {
            $disabled = explode(',', $disabled);
            $disabled = array_map('trim', $disabled);
            return !in_array($func, $disabled);
        }
        return true;
    }

    /**
     * graphic_class::resize_picture_imageick()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    public static function resize_picture_imageick($filename, $dest_file, $maxWidth, $maxHeight) {
        if (self::get_ext($filename) != 'svg') {
            if (!self::func_is_available('system')) {
                ini_set('memory_limit', '256M');
                if (extension_loaded('imagick')) {
                    try {
                        $img = new Imagick($filename);
                        $img->thumbnailImage($maxWidth, $maxHeight, TRUE);
                        $img->writeImage($dest_file);
                    }
                    catch (Exception $e) {
                        #  echo 'Caught exception: ', $e->getMessage(), "n";
                    }
                }
                else {
                    # GD Resize
                    self::resizePicture($filename, $dest_file, $maxWidth, $maxHeight);
                }

            }
            else {
                $cmd = "convert " . $filename . " -colorspace RGB -quality " . self::get_config_value('gal_compress') . "% -strip -resize '" . $maxWidth . "x" . $maxHeight .
                    ">' -colorspace sRGB " . $dest_file;
                $lastLine = system($cmd, $retval);
            }
        }
        return $dest_file;
    }

    /**
     * graphic_class::zebra_report()
     * 
     * @param mixed $res
     * @return
     */
    public static function zebra_report($res) {
        if (!$res) {
            // if there was an error, let's see what the error is about
            switch ($res->error) {
                case 1:
                    echo 'Source file could not be found!';
                    break;
                case 2:
                    echo 'Source file is not readable!';
                    break;
                case 3:
                    echo 'Could not write target file!';
                    break;
                case 4:
                    echo 'Unsupported source file format!';
                    break;
                case 5:
                    echo 'Unsupported target file format!';
                    break;
                case 6:
                    echo 'GD library version does not support target file format!';
                    break;
                case 7:
                    echo 'GD library is not installed!';
                    break;
            }
            # echoarr($res);
            #die();

        }
        else {
            #   echo 'Success!';
        }
    }

    /**
     * graphic_class::resizePicture()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    public static function resizePicture($filename, $dest_file, $maxWidth, $maxHeight) {
        $image = new Zebra_Image();
        $image->source_path = $filename;
        $image->target_path = $dest_file;
        $image->jpeg_quality = self::get_config_value('gal_compress');
        $image->preserve_aspect_ratio = true;
        $image->enlarge_smaller_images = false;
        $image->preserve_time = true;
        list($width, $height, $type, $attr) = getimagesize($filename);
        if ($width == 0)
            return $dest_file;
        $newHeight = $height / $width * $maxWidth;

        if ($newHeight > $maxHeight) {
            $res = $image->resize(0, $maxHeight);
            #  echo $width.'x'.$height.' ' .$newHeight. ' '.$maxHeight;die;
        }
        else {
            $res = $image->resize($maxWidth, 0);
        }
        self::zebra_report($res);

        return $dest_file;
    }

    /**
     * graphic_class::resize_picture_to_size_imageick()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    public static function resize_picture_to_size_imageick($filename, $dest_file, $maxWidth, $maxHeight) {
        $cmd = "convert " . $filename . "  -quality " . self::get_config_value('gal_compress') . "% -strip -resize " . $maxWidth . "x" . $maxHeight . "\> -size " . $maxWidth .
            "x" . $maxHeight . " xc:white  +swap -gravity center -density 72 -colorspace sRGB -composite " . $dest_file;
        $lastLine = system($cmd, $retval); // Skalieren
        return $dest_file;
    }

    /**
     * graphic_class::resize_picture_to_size()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    public static function resize_picture_to_size($filename, $dest_file, $maxWidth, $maxHeight) {
        $image = new Zebra_Image();
        $image->source_path = $filename;
        $image->target_path = $dest_file;
        $image->jpeg_quality = self::get_config_value('gal_compress');
        $image->preserve_aspect_ratio = true;
        $image->enlarge_smaller_images = true;
        $image->preserve_time = true;
        $res = $image->resize($maxWidth, $maxHeight, ZEBRA_IMAGE_BOXED);
        self::zebra_report($res);
        return $dest_file;
    }

    /**
     * graphic_class::resize_picture_to_size_png_imageick()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    public static function resize_picture_to_size_png_imageick($filename, $dest_file, $maxWidth, $maxHeight) {
        $file_ex = strtolower(substr($filename, -4));
        if ($file_ex != '.png') {
            $dest_file = keimeno_class::change_file_ext($dest_file, 'png');
            $cmd = "convert " . $filename . " -quality " . self::get_config_value('gal_compress') . "  -type TrueColorMatte -strip -resize " . $maxWidth . "x" . $maxHeight .
                " -gravity center  -transparent white -extent " . $maxWidth . "x" . $maxHeight . "  PNG24:" . $dest_file;
            $lastLine = system($cmd, $retval); // Skalieren
        }
        else {
            self::resize_picture_to_size($filename, $dest_file, $maxWidth, $maxHeight);
        }

        return $dest_file;
    }

    /**
     * graphic_class::resize_picture_to_size_png()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    public static function resize_picture_to_size_png($filename, $dest_file, $maxWidth, $maxHeight) {
        $image = new Zebra_Image();
        $image->source_path = $filename;
        $image->target_path = self::change_file_ext($dest_file, 'png');
        $image->jpeg_quality = self::get_config_value('gal_compress');
        $image->preserve_aspect_ratio = true;
        $image->enlarge_smaller_images = true;
        $image->preserve_time = true;
        $res = $image->resize($maxWidth, $maxHeight, ZEBRA_IMAGE_BOXED, -1);
        self::zebra_report($res);
        return $dest_file;
    }

    /**
     * graphic_class::cuttofit()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $thWidth
     * @param mixed $thHeight
     * @param mixed $width_foto_px
     * @param mixed $height_foto_px
     * @param string $gravity
     * @return
     */
    public static function cuttofit($filename, $dest_file, $thWidth, $thHeight, $width_foto_px, $height_foto_px, $gravity = "center") {
        #http://www.imagemagick.org/script/command-line-options.php?#gravity
        $use_px = ($width_foto_px > $height_foto_px) ? $height_foto_px : $width_foto_px;
        $cmd = "convert -define jpeg:size=" . $use_px . "x" . $use_px . " " . $filename . "  -type TrueColorMatte -strip -colorspace RGB -quality " . self::
            get_config_value('gal_compress') . "% -thumbnail " . $thWidth . "x" . $thHeight . " -gravity " . $gravity . " -extent " . $thWidth . "x" . $thHeight .
            "  -colorspace sRGB " . $dest_file;
        system($cmd);
        return basename($dest_file);
    }

    /**
     * graphic_class::crop()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $thWidth
     * @param mixed $thHeight
     * @param mixed $width_foto_px
     * @param mixed $height_foto_px
     * @param string $gravity
     * @return
     */
    public static function crop($filename, $dest_file, $thWidth, $thHeight, $width_foto_px, $height_foto_px, $gravity = "center") {
        #http://www.imagemagick.org/Usage/crop/#crop
        #http://www.imagemagick.org/script/command-line-options.php?#gravity
        #http://www.imagemagick.org/discourse-server/viewtopic.php?t=18545
        if (!self::func_is_available('system')) {
            ini_set('memory_limit', '256M');
            if (extension_loaded('imagick')) {
                try {
                    self::crop_imagick($filename, $dest_file, $thWidth, $thHeight, $gravity);
                }
                catch (Exception $e) {
                    #    echo 'Caught exception: ', $e->getMessage(), "n";
                }
            }
            else {
                # GD Resize
                self::cuttofit_zebra($filename, $dest_file, $thWidth, $thHeight, $gravity);
            }
        }
        else {
            $use_px = ($width_foto_px > $height_foto_px) ? $height_foto_px : $width_foto_px;
            $cmd = 'convert -define jpeg:size=' . $use_px . 'x' . $use_px . ' ' . $filename . ' -type TrueColorMatte -strip -colorspace RGB -quality ' . self::
                get_config_value('gal_compress') . '% -resize "' . $thWidth . 'x' . $thHeight . '^" -gravity ' . $gravity . ' -crop ' . $thWidth . 'x' . $thHeight .
                '+0+0 +repage -colorspace sRGB ' . $dest_file;
            system($cmd);
        }
        return basename($dest_file);
    }

    /**
     * graphic_class::auto_rotate_image()
     * 
     * @param mixed $image
     * @return void
     */
    public static function auto_rotate_image($image) {
        $orientation = $image->getImageOrientation();

        switch ($orientation) {
            case imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage("#000", 180); // rotate 180 degrees
                break;

            case imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage("#000", 90); // rotate 90 degrees CW
                break;

            case imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                break;
        }

        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
    }

    /**
     * graphic_class::crop_imagick()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $new_w
     * @param mixed $new_h
     * @param string $gravity
     * @return void
     */
    public static function crop_imagick($filename, $dest_file, $new_w, $new_h, $gravity = 'center') {
        $image = new Imagick($filename);
        $w = $image->getImageWidth();
        $h = $image->getImageHeight();

        $image->setImageCompressionQuality((int)self::get_config_value('gal_compress'));
        $filter = 0.9;

        self::auto_rotate_image($image);
        if ($w >= $h && $new_w >= $new_h) {
            $image->resizeImage($new_w, 0, Imagick::FILTER_LANCZOS, $filter);
        }
        elseif ($w >= $h && $new_w < $new_h) {
            $image->resizeImage(0, $new_h, Imagick::FILTER_LANCZOS, $filter);
        }
        elseif ($w < $h && $new_w >= $new_h) {
            $image->resizeImage($new_w, 0, Imagick::FILTER_LANCZOS, $filter);
        }
        elseif ($w < $h && $new_w < $new_h) {
            $image->resizeImage(0, $new_h, Imagick::FILTER_LANCZOS, $filter);
        }

        $resize_w = $image->getImageWidth();
        $resize_h = $image->getImageHeight();

        switch (strtolower($gravity)) {
            case 'northwest':
                $image->cropImage($new_w, $new_h, 0, 0);
                break;

            case 'center':
                $image->cropImage($new_w, $new_h, ($resize_w - $new_w) / 2, ($resize_h - $new_h) / 2);
                break;

            case 'northeast':
                $image->cropImage($new_w, $new_h, $resize_w - $new_w, 0);
                break;

            case 'southwest':
                $image->cropImage($new_w, $new_h, 0, $resize_h - $new_h);
                break;

            case 'southeast':
                $image->cropImage($new_w, $new_h, $resize_w - $new_w, $resize_h - $new_h);
                break;

            case 'south':
                $image->cropImage($new_w, $new_h, ($resize_w - $new_w) / 2, $resize_h - $new_h);
                break;

            case 'north':
                $image->cropImage($new_w, $new_h, ($resize_w - $new_w) / 2, 0);
                break;
        }

        #header("Content-Type: image/jpg");        echo $image->getImageBlob();        die;


        $image->writeImage($dest_file);
        $image->destroy();
    }


    /**
     * graphic_class::cuttofit_zebra()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $thWidth
     * @param mixed $thHeight
     * @param string $gravity
     * @return
     */
    public static function cuttofit_zebra($filename, $dest_file, $thWidth, $thHeight, $gravity = "center") {

        $image = new Zebra_Image();
        $image->source_path = $filename;
        $image->target_path = $dest_file;
        $image->jpeg_quality = self::get_config_value('gal_compress');
        $image->preserve_aspect_ratio = true;
        $image->enlarge_smaller_images = true;
        $image->preserve_time = true;
        $gravity = strtolower($gravity);
        switch ($gravity) {
            case "center":
                $pos = ZEBRA_IMAGE_CROP_CENTER;
                break;
            case "northwest":
                $pos = ZEBRA_IMAGE_CROP_TOPLEFT;
                break;
            case "north":
                $pos = ZEBRA_IMAGE_CROP_TOPCENTER;
                break;
            case "northeast":
                $pos = ZEBRA_IMAGE_CROP_TOPRIGHT;
                break;
            case "west":
                $pos = ZEBRA_IMAGE_CROP_MIDDLELEFT;
                break;
            case "east":
                $pos = ZEBRA_IMAGE_CROP_MIDDLERIGHT;
                break;
            case "southwest":
                $pos = ZEBRA_IMAGE_CROP_BOTTOMLEFT;
                break;
            case "south":
                $pos = ZEBRA_IMAGE_CROP_BOTTOMCENTER;
                break;
            case "southeast":
                $pos = ZEBRA_IMAGE_CROP_BOTTOMRIGHT;
                break;
        }
        $pos = ZEBRA_IMAGE_CROP_CENTER;
        $res = $image->resize($thWidth, $thHeight, $pos);
        self::zebra_report($res);
        return basename($dest_file);
    }

    /**
     * graphic_class::boxed_image()
     * 
     * @param mixed $filename
     * @param mixed $dest_file
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    public static function boxed_image($filename, $dest_file, $maxWidth, $maxHeight) {
        $image = new Zebra_Image();
        $image->source_path = $filename;
        $image->target_path = $dest_file;
        $image->jpeg_quality = self::get_config_value('gal_compress');
        $image->preserve_aspect_ratio = true;
        $image->enlarge_smaller_images = true;
        $image->preserve_time = true;
        $res = $image->resize($maxWidth, $maxHeight, ZEBRA_IMAGE_BOXED);
        self::zebra_report($res);
        return $dest_file;
    }

    /**
     * graphic_class::resizePictureObj()
     * 
     * @param mixed $srcImage
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @return
     */
    function resizePictureObj($srcImage, $maxWidth, $maxHeight) {
        $src_dims[0] = imagesx($srcImage);
        $src_dims[1] = imagesy($srcImage);
        $srcRatio = $src_dims[0] / $src_dims[1]; // width/height ratio
        $destRatio = $maxWidth / $maxHeight;
        if ($destRatio > $srcRatio) {
            $destSize[1] = $maxHeight;
            $destSize[0] = $maxHeight * $srcRatio;
        }
        else {
            $destSize[0] = $maxWidth;
            $destSize[1] = $maxWidth / $srcRatio;
        }
        $thumb_w = $destSize[0];
        $thumb_h = $destSize[1];
        $dst_img = imageCreateTrueColor($thumb_w, $thumb_h);
        #imageantialias ($dst_img, True);
        imageCopyResampled($dst_img, $srcImage, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_dims[0], $src_dims[1]);
        return $dst_img;
    }


    /**
     * graphic_class::roundedThumbnail()
     * 
     * @param mixed $filename
     * @param mixed $retpath
     * @param mixed $maxWidth
     * @param mixed $maxHeight
     * @param integer $corner_size
     * @param mixed $addshadow
     * @param bool $usecache
     * @return
     */
    public static function roundedThumbnail($filename, $retpath, $maxWidth, $maxHeight, $corner_size = 15, $addshadow = array(), $usecache = true) {
        //http://www.imagemagick.org/Usage/thumbnails/#rounded_border
        $shd = "";
        if (is_array($addshadow))
            foreach ($addshadow as $key => $value)
                $shd .= $value;
        list($width_foto_px, $height_foto_px) = getimagesize($filename);
        $thumb_prefix = self::generate_prefix($filename);
        $dest_file = $retpath . $thumb_prefix . 'rounded_' . md5(basename($filename) . $maxWidth . $maxHeight . $width_foto_px . $height_foto_px . $corner_size . $shd) .
            '.png';
        if ($usecache === TRUE && file_exists($dest_file))
            return basename($dest_file);
        $feedback .= system("convert " . $filename . " " . $dest_file); // Umwandlung in PNG
        $feedback .= system("convert " . $dest_file . " -resize '" . $maxWidth . "x" . $maxHeight . ">'  " . $dest_file); // Skalieren
        $feedback .= system("convert " . $dest_file . " \
     \( +clone  -threshold -1 \
        -draw 'fill black polygon 0,0 0," . $corner_size . " " . $corner_size . ",0 fill white circle " . $corner_size . "," . $corner_size . " " . $corner_size .
            ",0' \
        \( +clone -flip \) -compose Multiply -composite \
        \( +clone -flop \) -compose Multiply -composite \
     \) +matte -compose CopyOpacity -composite  " . $dest_file); // Runde Ecken
        if ($addshadow['color'] != "") {
            $feedback .= system("convert -page +4+4 " . $dest_file . " -matte \
          \( +clone -background " . $addshadow['color'] . " -shadow " . $addshadow['transp'] . "x" . $addshadow['blur'] . "+" . $addshadow['left'] . "+" . $addshadow['top'] .
                " \) +swap \
          -background none -mosaic " . $dest_file); // Schatten
        }
        return basename($dest_file);
    }

    /**
     * graphic_class::GetDomain()
     * 
     * @return
     */
    function GetDomain() {
        $parts = explode('.', $_SERVER["HTTP_HOST"]);
        return $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
    }

    /**
     * graphic_class::GetDomainPure()
     * 
     * @return
     */
    public static function GetDomainPure() {
        $parts = explode('.', $_SERVER["HTTP_HOST"]);
        return $parts[count($parts) - 2];
    }

    /**
     * graphic_class::generate_prefix()
     * 
     * @param mixed $filename
     * @return
     */
    public static function generate_prefix($filename) {
        $umlaute = array(
            "ä" => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
            ' ' => '-',
            ',' => '-');
        $filename = mb_strtolower(basename($filename), 'UTF-8');
        $filename = trim(strtr($filename, $umlaute));

        $filename = strtolower(basename($filename));
        $filename = preg_replace('/[^0-9a-z?-????\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i', '', $filename);
        $filename = self::GetDomainPure() . '_' . substr($filename, 0, strpos($filename, '.')) . '_';
        #  echo $filename.'<br>';
        return $filename;
    }

    /**
     * graphic_class::makeThumb()
     * 
     * @param mixed $source
     * @param mixed $width
     * @param mixed $height
     * @param string $target_path
     * @param bool $usecache
     * @param string $th_type
     * @param mixed $rounded_config
     * @param mixed $watermark
     * @param string $gravity
     * @return
     */
    public static function makeThumb($source, $width, $height, $target_path = '', $usecache = TRUE, $th_type = 'resize', $rounded_config = array(), $watermark =
        array(), $gravity = "center") {

        $gravity = ($gravity == "") ? 'center' : $gravity;
        $th_type = ($th_type == "") ? 'resize' : $th_type;
        if ($target_path[strlen($target_path) - 1] != '/')
            $target_path .= '/';
        $thumb_prefix = self::generate_prefix($source);
        if (!file_exists($source) || !is_file($source) || keimeno_class::get_ext($source) == 'svg')
            return;


        if (isset($watermark['watermark']) && file_exists($watermark['watermark']) && count($watermark) > 1) {
            $new_source = keimeno_class::change_file_ext($source, 'wm.' . keimeno_class::get_ext($source));
            $new_source = './' . CACHE . basename($new_source);
            if (!file_exists($new_source)) {
                if (copy($source, $new_source)) {
                    $source = $new_source;
                    self::watermark($source, $watermark['watermark'], $watermark['pos'], $watermark['trans']);
                }
            }
        }
        $target_path = str_replace(array('../', './'), '', $target_path);
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $dir = str_replace(DIRECTORY_SEPARATOR . 'includes', '', $dir);
        $target_path = $dir . $target_path;
        $file_ext = self::get_ext($source);

        if ($th_type == "none") {
            copy($source, $target_path . basename($source));
            return basename($source);
        }

        if ($th_type === 'rounded') {
            if (count($rounded_config) == 0) {
                $rounded_config['transp'] = 70;
                $rounded_config['blur'] = 3;
                $rounded_config['left'] = 3;
                $rounded_config['top'] = 3;
                $rounded_config['color'] = 'black';
                $rounded_config['corner'] = 8;
            }
            $thumb_file = $target_path . self::roundedThumbnail($source, $target_path, $width, $height, $rounded_config['corner'], $rounded_config, $usecache);
        }
        #$usecache = false;
        if ($th_type == 'resize') {
            # $thumb_name = $thumb_prefix.$th_type.'_'.$width.'x'.$height.'.jpg';
            $thumb_name = $thumb_prefix . $th_type . '_' . $width . 'x' . $height . '_' . self::get_config_value('gal_compress') . '.' . $file_ext;
            $thumb_name = str_replace('_', '-', $thumb_name);
            $thumb_file = $target_path . $thumb_name;

            if (!file_exists($thumb_file) || $usecache === FALSE) {
                self::resizePicture($source, $target_path . $thumb_name, $width, $height);
            }
        }

        if ($th_type == 'resizetofit') {
            # $thumb_name = $thumb_prefix.$th_type.'_'.$width.'x'.$height.'.jpg';
            $thumb_name = $thumb_prefix . $th_type . '_' . $width . 'x' . $height . '_' . self::get_config_value('gal_compress') . '.' . $file_ext;
            $thumb_name = str_replace('_', '-', $thumb_name);
            $thumb_file = $target_path . $thumb_name;
            if (!file_exists($thumb_file) || $usecache === FALSE) {
                self::resize_picture_to_size($source, $target_path . $thumb_name, $width, $height);
            }
        }

        if ($th_type == 'resizetofitpng') {
            $thumb_name = $thumb_prefix . $th_type . '_' . $width . 'x' . $height . '_' . self::get_config_value('gal_compress') . '.png';
            $thumb_name = str_replace('_', '-', $thumb_name);
            $thumb_file = $target_path . $thumb_name;
            if (!file_exists($thumb_file) || $usecache === FALSE) {
                self::resize_picture_to_size_png($source, $target_path . $thumb_name, $width, $height);
            }
        }

        if ($th_type == 'boxed') {
            $thumb_name = $thumb_prefix . $th_type . '_' . $width . 'x' . $height . '_' . self::get_config_value('gal_compress') . '.' . $file_ext;
            $thumb_name = str_replace('_', '-', $thumb_name);
            $thumb_file = $target_path . $thumb_name;
            if (!file_exists($thumb_file) || $usecache === FALSE) {
                self::boxed_image($source, $target_path . $thumb_name, $width, $height);
            }
        }

        if ($th_type == 'crop') {
            list($width_foto_px, $height_foto_px) = getimagesize($source);
            $thumb_name = $thumb_prefix . $th_type . '_' . $width . 'x' . $height . '_' . $gravity . '_' . self::get_config_value('gal_compress') . '.' . $file_ext;
            $thumb_name = str_replace('_', '-', $thumb_name);
            $thumb_file = $target_path . $thumb_name;
            if (!file_exists($thumb_file) || $usecache === FALSE) {
                #echo $source . ' ' . $width . ' ' . $height . ' ' . $width_foto_px . ' ' . $height_foto_px . ' ' . $gravity . '<br>';
                $thumb_file = self::crop($source, $target_path . $thumb_name, $width, $height, $width_foto_px, $height_foto_px, $gravity);
            }
        }
        return basename($thumb_file);
    }

    /**
     * graphic_class::watermark()
     * 
     * @param mixed $org_filename
     * @param mixed $watermark
     * @param mixed $pos
     * @param mixed $tans
     * @return
     */
    public static function watermark($org_filename, $watermark, $pos, $tans) {
        #http://www.imagemagick.org/Usage/annotating/#watermarking
        if ($pos == 'TOP_LEFT') {
            $position = 'northwest';
        }
        else
            if ($pos == 'TOP_RIGHT') {
                $position = 'northeast';
            }
            else
                if ($pos == 'BOTTOM_RIGHT') {
                    $position = 'southeast';
                }
                else
                    if ($pos == 'BOTTOM_LEFT') {
                        $position = 'southwest';
                    }
                    else
                        if ($pos == 'LEFT') {
                            $position = 'west';
                        }
                        else
                            if ($pos == 'RIGHT') {
                                $position = 'east';
                            }
                            else
                                if ($pos == 'TOP') {
                                    $position = 'north';
                                }
                                else
                                    if ($pos == 'BOTTOM') {
                                        $position = 'south';
                                    }
                                    else
                                        if ($pos == 'CENTER') {
                                            $position = 'center';
                                        }
        #-geometry +".$x."+".$y."
        if (file_exists($watermark)) {
            $cmd = "composite -gravity " . $position . " -dissolve " . $tans . " " . $watermark . "   " . $org_filename . "  " . $org_filename;
            #	echo $cmd;
            $feedback .= system($cmd);
        }
    }

} // CLASS
