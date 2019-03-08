<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

DEFINE('IN_SIDE', 1);
DEFINE('NO_MODULES', 1);
DEFINE('RESTREQUEST', 1);
include ('../../system.corestartup.inc.php');


class kcaptcha extends keimeno_class {

    /**
     * kcaptcha::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * kcaptcha::make_micro_time()
     * 
     * @param mixed $len
     * @return
     */
    public static function make_micro_time() {
        list($usec, $sec) = explode(' ', microtime());
        return (float)$sec + ((float)$usec * 100000);
    }

    /**
     * kcaptcha::create_captcha_string()
     * 
     * @param mixed $len
     * @return
     */
    private static function create_captcha_string($len) {

        srand(self::make_micro_time());
        $possible = static::$config['captcha_chars'];
        if (empty($possible)) {
            throw new kException('No chars for captcha defined. Check CMS config.');
        }
        $str = "";
        while (strlen($str) < $len) {
            $str .= substr($possible, (rand() % (strlen($possible))), 1);
        }
        return ($str);
    }

    /**
     * kcaptcha::set_string_to_session()
     * 
     * @return
     */
    private static function set_string_to_session() {
        # set string to session
        static::$config['captcha_charcount'] = (static::$config['captcha_charcount'] <= 0) ? 1 : static::$config['captcha_charcount'];
        $text = self::create_captcha_string(static::$config['captcha_charcount']);
        $_SESSION['captcha_spam'] = $text;
        return $text;
    }

    /**
     * kcaptcha::send_image_to_browser()
     * 
     * @return
     */
    public static function send_image_to_browser() {
        header('Content-type: image/png');
        while (intval($rnd_num) == intval($_SESSION['captcha_num']) || $rnd_num == "")
            $rnd_num = rand(1, 4);
        $img = ImageCreateFromPNG('../../../images/opt_captcha' . $rnd_num . '.png');
        $_SESSION['captcha_num'] = $rnd_num;
        $color = ImageColorAllocate($img, 0, 0, 0);
        $ttf = CMS_ROOT . 'fonts/' . static::$config['captcha_fonts'] . '.ttf';
        $ttfsize = static::$config['captcha_font_size'];
        $angle = rand(0, 5);
        $t_x = rand(static::$config['captcha_x_start'], static::$config['captcha_x_end']);
        $t_y = (int)static::$config['captcha_y'];
        $text = self::set_string_to_session();
        imagettftext($img, $ttfsize, $angle, $t_x, $t_y, $color, $ttf, $text);
        imagepng($img);
        imagedestroy($img);
    }

    /**
     * kcaptcha::run()
     * executes capcha process
     *
     */
    public static function run() {
        unset($_SESSION['captcha_spam']);
        self::send_image_to_browser();
    }
}

kcaptcha::run();
