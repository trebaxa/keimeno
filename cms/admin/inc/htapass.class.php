<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class htapass_class extends keimeno_class {

    var $default_ht = './htaccess.txt';
    var $isprotected = false;
    var $system_root = "";
    var $path = "";
    var $pwfile = "";

    /**
     * htapass_class::htapass_class()
     * 
     * @param mixed $path
     * @param mixed $system_root
     * @return
     */
    function htapass_class($path, $system_root) {
        parent::__construct();
        $this->path = $path;
        $this->system_root = $system_root;
        $this->isprotected = file_exists('./.htpasswd01');
        $this->pwfile = $this->system_root . "admin/.htpasswd01";
        $this->smarty->assign('HTAPASS', array('isprotected' => (($this->isprotected === TRUE) ? 1 : 0)));
    }

    /**
     * htapass_class::save_file()
     * 
     * @param mixed $name
     * @param mixed $password
     * @return
     */
    function save_file($name, $password) {
        $this->pwfile = $this->system_root . "admin/.htpasswd01";
        $passwd = crypt($password, substr($password, 0, 2));
        file_put_contents($this->pwfile, $name . ":" . $passwd);
        $pr = str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__)));
        $ht = "AuthType Basic\nAuthName \"" . $name . " - \"\nAuthUserFile " . $this->pwfile . "\nrequire valid-user\n\n\n";
        if (file_exists($this->default_ht)) {
            $this->path = str_replace('//', '/', '/' . $this->path);
            $ht .= "\n" . file_get_contents($this->default_ht);
            $ht = str_replace('!!PATH!!', $this->path, $ht);
        }
        file_put_contents('./.htaccess', $ht);
        $header = 'From: ' . FM_EMAIL . "\r\n" . 'Reply-To: ' . FM_EMAIL . "\r\n" . 'X-Mailer: PHP/' . phpversion();
        $inhalt = '
Hallo,

Dies sind die Zugangsdaten zu Ihrem administrativen Bereich für ' . FM_DOMAIN . ':
  
 Login Name: ' . $name . '
 Passwort: ' . $password . '
  
Dies ist einen automatische Email aus Ihrem System.
';
        mail(FM_EMAIL, 'Server Passwort ' . FM_DOMAIN, $inhalt, $header, '-f' . FM_EMAIL);
    }

    /**
     * htapass_class::htreset()
     * 
     * @return
     */
    function htreset() {
        if (file_exists($this->default_ht)) {
            $this->path = str_replace('//', '/', '/' . $this->path);
            $ht .= "\n" . file_get_contents($this->default_ht);
            $ht = str_replace('!!PATH!!', $this->path, $ht);
        }
        file_put_contents('./.htaccess', $ht);
        @unlink($this->pwfile);
    }

}
