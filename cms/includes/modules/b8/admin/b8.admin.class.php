<?php

/**
 * @package    B8
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */
 
defined('IN_SIDE') or die('Access denied.');

class b8_admin_class extends b8_master_class {

    protected $B8 = array();

    /**
     * b8_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $CONFIG_OBJ = new config_class('b8');
        $this->B8['CONFIG'] = $CONFIG_OBJ->buildTable();
        if (is_file(MODULE_ROOT . 'b8/b8/b8/timetake.txt')) {
            $this->B8['timetaken'] = file_get_contents(MODULE_ROOT . 'b8/b8/b8/timetake.txt');
        }
        else {
            $this->B8['timetaken'] = '...';
        }
    }

    /**
     * b8_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('B8', $this->B8);
    }

    /**
     * b8_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }


    /**
     * b8_admin_class::cmd_classify()
     * 
     * @return
     */
    function cmd_classify() {
        $text = stripslashes($_POST['text']);
        $rating = $this->formatRating($this->b8->classify($text));
        $this->ej('show_classify', $rating['rating'] . ',' . $rating['red'] . ',' . $rating['green']);
    }

    /**
     * b8_admin_class::cmd_save_spam()
     * 
     * @return
     */
    function cmd_save_spam() {
        $text = stripslashes($_POST['text']);
        $ratingBefore = $this->b8->classify($text);
        $this->b8->learn($text, b8::SPAM);
        $rating = $this->formatRating($this->b8->classify($text));
        $this->ej('show_classify', $rating['rating'] . ',' . $rating['red'] . ',' . $rating['green']);
    }

    /**
     * b8_admin_class::cmd_save_ham()
     * 
     * @return
     */
    function cmd_save_ham() {
        $text = stripslashes($_POST['text']);
        $ratingBefore = $this->b8->classify($text);
        $this->b8->learn($text, b8::HAM);
        $rating = $this->b8->classify($text);
        $this->ej('show_classify', $rating['rating'] . ',' . $rating['red'] . ',' . $rating['green']);
    }

    /**
     * b8_admin_class::cmd_del_spam()
     * 
     * @return
     */
    function cmd_del_spam() {
        $text = stripslashes($_POST['text']);
        $this->b8->unlearn($text, b8::SPAM);
        $this->ej();
    }

    /**
     * b8_admin_class::cmd_del_ham()
     * 
     * @return
     */
    function cmd_del_ham() {
        $text = stripslashes($_POST['text']);
        $this->b8->unlearn($text, b8::SPAM);
        $this->ej();
    }

    /**
     * b8_admin_class::cmd_reset_db()
     * 
     * @return
     */
    function cmd_reset_db() {
        $this->db->query("DELETE FROM " . TBL_CMS_B8WORDS . " WHERE token!='b8*texts' AND token!='b8*dbversion'");
        $this->ej();
    }


}

?>