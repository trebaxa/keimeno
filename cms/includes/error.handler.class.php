<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

class core_error_handler {


    /**
     * core_error_handler::__construct()
     * 
     * @return
     */
    function __construct() {

    }


    /**
     * core_error_handler::handleError()
     * 
     * @param mixed $errno
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     * @param mixed $errcontext
     * @return
     */
    public static function handleError($errno, $errstr, $errfile = null, $errline = null, $errcontext = null) {
        if (error_reporting() == 0)
            return;
        $PHP_SCRIPT_ERROR_LOGFILE = CMS_ROOT . 'cache/phperr.log';
        $compile_error = true;
        $error = array(
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline);
        if (($errno == E_WARNING || $errno == E_ERROR || $errno == E_PARSE)) {
            #  if ($errno != E_NOTICE  ) {
            $err_line = $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b><br>';
            switch ($errno) {
                case E_USER_ERROR:
                    $compile_error = true;
                    $errmsg = 'User error: ' . $err_line;
                    break;
                case E_WARNING:
                    $compile_error = true;
                    $errmsg = 'PHP warning: ' . $err_line;
                    break;
                case E_USER_WARNING:
                    $compile_error = true;
                    $errmsg = 'User warning: ' . $err_line;
                    break;
                case E_NOTICE:
                    $compile_error = true;
                    $errmsg = 'PHP notice: ' . $err_line;
                    break;
                case E_USER_NOTICE:
                    $compile_error = true;
                    $errmsg = 'User notice: ' . $err_line;
                    break;
                case E_STRICT:
                    $compile_error = true;
                    $errmsg = 'E_STRICT information: ' . $err_line;
                    keimeno_class::msge($errmsg);
                    return $compile_error;
                    break;
                case E_RECOVERABLE_ERROR:
                    $compile_error = true;
                    $errmsg = 'Recoverable error: ' . $err_line;
                    break;
                case E_DEPRECATED:
                    $compile_error = true;
                    $errmsg = 'PHP deprecated: ' . $err_line;
                    break;
                case E_USER_DEPRECATED:
                    $compile_error = true;
                    $errmsg = 'User deprecated: ' . $err_line;
                    break;
                default:
                    $compile_error = false;
                    $errmsg = 'Un-recoverable error ' . $errno . ': ' . $err_line;
                    break;
            }
            #  if (file_exists($PHP_SCRIPT_ERROR_LOGFILE) && is_file($PHP_SCRIPT_ERROR_LOGFILE)) {
            #      $size = filesize($PHP_SCRIPT_ERROR_LOGFILE);
            #      $size = $size / 1024 / 1024; //MB
            #    if ($size > 50)
            #         @unlink($PHP_SCRIPT_ERROR_LOGFILE);
            #}
            #error_log($errmsg . "\n", 3, $PHP_SCRIPT_ERROR_LOGFILE);
            keimeno_class::msge($errmsg);
        }


        return $compile_error;
    }

    /**
     * core_error_handler::set_debug()
     * 
     * @param bool $debug
     * @return
     */
    public function set_debug($debug = true) {
        if ($debug === true) {
            set_error_handler(array($this, 'handleError'));
            #   error_reporting(-1);
        }
        elseif ($debug === false) {            
            error_reporting(0);
        }
        else {
            error_reporting(intval($debug));
        }
    }


}
