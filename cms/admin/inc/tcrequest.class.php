<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class kcontrol_class {

    var $GET = array();
    var $POST = array();
    var $REQUEST = array();
    var $fault_form = false;
    var $just_turn_back=false;

    /**
     * kcontrol_class::__construct()
     * 
     * @param mixed $class_obj
     * @return
     */
    function __construct($class_obj = NULL) {
        if (is_array($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->GET[$key] = $value;
                $this->REQUEST[$key] = $value;
            }
        }
        if (is_array($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->POST[$key] = $value;
                $this->REQUEST[$key] = $value;
            }
        }
        $this->class_obj = $class_obj;
        $this->epage = (isset($this->REQUEST['epage']) ? $this->REQUEST['epage'] : "");
        $this->page = (isset($this->REQUEST['page']) ? (int)$this->REQUEST['page'] : 0);
        if (isset($_REQUEST['aktion'])) {
            $this->GET['cmd'] = $this->POST['cmd'] = $this->REQUEST['cmd'] = $_REQUEST['aktion'];
        }
    }

    /**
     * kcontrol_class::set_url_tag()
     * 
     * @param mixed $key
     * @return
     */
    function set_url_tag($key) {
        $this->url_tags[$key] = $this->REQUEST[$key];
    }

    /**
     * kcontrol_class::set_url_tags()
     * 
     * @param mixed $tags
     * @return
     */
    function set_url_tags($tags = array()) {
        $tags = (array )$tags;
        foreach ($tags as $key)
            $this->url_tags[$key] = $this->REQUEST[$key];
    }

    /**
     * kcontrol_class::add_url_tag()
     * 
     * @param mixed $key
     * @param mixed $value
     * @return
     */
    function add_url_tag($key, $value) {
        $this->url_tags[$key] = $value;
    }

    /**
     * kcontrol_class::add_msg()
     * 
     * @param mixed $value
     * @return
     */
    function add_msg($value) {
        $this->add_url_tag('msg', base64_encode($value));
    }

    /**
     * kcontrol_class::add_msge()
     * 
     * @param mixed $value
     * @return
     */
    function add_msge($value) {
        $this->add_url_tag('msge', base64_encode($value));
    }

    /**
     * kcontrol_class::reset_cmd()
     * 
     * @param mixed $cmd
     * @return
     */
    function reset_cmd($cmd) {
        $this->add_url_tag('cmd', $cmd);
    }

    /**
     * kcontrol_class::redirect()
     * 
     * @param mixed $redirect_params
     * @param string $php
     * @return
     */
    function redirect($redirect_params, $php = "") {
        HEADER('location:' . (($php != "") ? $php : $_SERVER['PHP_SELF']) . '?' . $redirect_params);
    }


    /**
     * kcontrol_class::interpreter()
     * 
     * @return
     */
    function interpreter() {
        global $TCMASTER;
        $method = "";
        if (array_key_exists('cmd', $this->REQUEST))
            $method = 'cmd_' . $this->REQUEST['cmd'];
        if (method_exists($this->class_obj, $method)) {
            $this->class_obj->$method();


            if ($this->just_turn_back === TRUE) {
                $REF = ($_SERVER['HTTP_REFERER'] == "") ? $_SESSION['lastPage'] : $_SERVER['HTTP_REFERER'];
                $args = $this->parse_query($REF);
                $unsets = array('msg', 'msge');
                foreach ($unsets as $key)
                    unset($args[$key]);
                foreach ($args as $key => $value)
                    $this->add_url_tag($key, $value);
                $this->url_tags = (array )$this->url_tags;
                $query_str = http_build_query($this->url_tags);
                $parts = explode('?', $REF);
                $sign = (strstr($parts[0], '?') ? '&' : '?');
                $redirect_url = (($query_str != "") ? $parts[0] . $sign . $query_str : $REF);
                HEADER('location:' . $redirect_url);
                exit;
            }

            $this->url_tags['epage'] = $this->epage;
            $redirect_url = http_build_query($this->url_tags);
            $redirect_url = (($redirect_url != "") ? $_SERVER['PHP_SELF'] . '?' . $redirect_url : $_SERVER['PHP_SELF'] . '?epage=' . $this->epage);

            if (!empty($this->url_tags['msg'])) {
                HEADER('location:' . $redirect_url);
                exit;
            }

            if ($this->fault_form === TRUE) {
                $TCMASTER->GBLPAGE['err'] = $this->class_obj->GBLPAGE['err'];
                $_REQUEST['aktion'] = $_GET['cmd'] = $_REQUEST['cmd'] = $_POST['cmd'] = $this->url_tags['cmd'];
            }

            if (!empty($this->url_tags['msge'])) {
                if (empty($this->url_tags['cmd'])) {
                    HEADER('location:' . $redirect_url);
                    exit;
                }
                else {
                    $TCMASTER->GBLPAGE['err'] = $this->class_obj->GBLPAGE['err'];
                    $_GET['cmd'] = $_REQUEST['cmd'] = $_POST['cmd'] = $this->url_tags['cmd'];
                }
            }

            unset($ir);
        }
    }

    /**
     * kcontrol_class::set_just_turn_back()
     * 
     * @param bool $v
     * @return
     */
    function set_just_turn_back($v = true) {
        $this->just_turn_back = $v;
    }

    /**
     * kcontrol_class::tb()
     * 
     * @param bool $v
     * @return
     */
    function tb($v = true) {
        $this->set_just_turn_back($v);
    }

    /**
     * kcontrol_class::set_fault_form()
     * 
     * @param mixed $v
     * @return
     */
    function set_fault_form($v) {
        $this->fault_form = $v;
    }

    /**
     * kcontrol_class::parse_query()
     * 
     * @param mixed $url
     * @return
     */
    function parse_query($url) {
        $var = parse_url($url, PHP_URL_QUERY);
        $var = html_entity_decode($var);
        $var = explode('&', $var);
        $arr = array();

        foreach ($var as $val) {
            $x = explode('=', $val);
            $arr[$x[0]] = $x[1];
        }
        unset($val, $x, $var);
        return $arr;
    }

    /**
     * kcontrol_class::interpreterfe()
     * 
     * @return
     */
    function interpreterfe() {
        global $TCMASTER;
        $method = "";
        if (array_key_exists('cmd', $this->REQUEST))
            $method = 'cmd_' . $this->REQUEST['cmd'];

        if (method_exists($this->class_obj, $method)) {
            $this->class_obj->$method();

            if ($this->redirecto != "") {
                HEADER('location:' . $this->redirecto);
                exit;
            }

            if ($this->just_turn_back === TRUE) {
                $REF = ($_SERVER['HTTP_REFERER'] == "") ? $_SESSION['lastPage'] : $_SERVER['HTTP_REFERER'];
                $args = $this->parse_query($REF);
                $unsets = array('msg', 'msge');
                foreach ($unsets as $key)
                    unset($args[$key]);
                foreach ($args as $key => $value)
                    $this->add_url_tag($key, $value);
                $this->url_tags = (array )$this->url_tags;
                $query_str = http_build_query($this->url_tags);
                $parts = explode('?', $REF);
                $sign = (strstr($parts[0], '?') ? '&' : '?');
                $redirect_url = (($query_str != "") ? $parts[0] . $sign . $query_str : $REF);
                HEADER('location:' . $redirect_url);
                exit;
            }

            $this->url_tags['page'] = $this->page;
            $redirect_url = http_build_query($this->url_tags);
            $redirect_url = (($redirect_url != "") ? $_SERVER['PHP_SELF'] . '?' . $redirect_url : $_SERVER['PHP_SELF'] . '?page=' . $this->page);

            if (!empty($this->url_tags['msg']) && $this->fault_form === FALSE) {
                HEADER('location:' . $redirect_url);
                exit;
            }

            if (!empty($this->url_tags['msge']) && $this->fault_form === FALSE) {
                HEADER('location:' . $redirect_url);
                exit;
            }

            if ($this->fault_form === TRUE) {
                $TCMASTER->GBLPAGE['err'] = $this->class_obj->GBLPAGE['err'];
                $_GET['cmd'] = $_REQUEST['cmd'] = $_POST['cmd'] = $this->url_tags['cmd'];
            }

        }

    }

}
