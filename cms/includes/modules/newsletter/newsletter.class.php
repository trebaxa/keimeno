<?php

/**
 * @package    newsletter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class newsletter_class extends newsletter_master_class {

   

    /**
     * newsletter_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();        
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * newsletter_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->NEWSLETTER['not_finished_newsletter'] = get_data_count(TBL_CMS_CUST, 'kid', "mailsend=0") > 0;
        if ($this->smarty->getTemplateVars('NEWSLETTER') != NULL) {
            $this->NEWSLETTER = array_merge($this->smarty->getTemplateVars('NEWSLETTER'), $this->NEWSLETTER);
            $this->smarty->clearAssign('NEWSLETTER');
        }
        $this->smarty->assign('NEWSLETTER', $this->NEWSLETTER);
    }


    /**
     * newsletter_class::cmd_a_edisable()
     * 
     * @return
     */
    function cmd_a_edisable() {
        $_GET['id'] = intval($_GET['id']);
        if ($_GET['group'] > 0) {
            $cuobj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . (int)$_GET['group'] . " LIMIT 1");
            if (md5($cuobj['email']) == $_GET['n'] && $gbl_config['newsletter_disable_unreg'] == 0) {
                $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailactive=0 WHERE kid=" . (int)$_GET['group'] . " LIMIT 1");
                keimeno_class::msg('Newsletter deaktiviert');
                HEADER("Location: index.html");
            }
            else {
                keimeno_class::msge('Abmeldung nicht möglich');
                HEADER("Location: index.html");
            }
        }
        $this->hard_exit();
    }


    /**
     * newsletter_class::cmd_recordn()
     * 
     * @return
     */
    function cmd_recordn() {
        $im = @imagecreatetruecolor(1, 1);
        imagesavealpha($im, true);
        imagealphablending($im, false);
        $transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
        imagefill($im, 0, 0, $transparent);
        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
        $_GET['id'] = (int)$_GET['id'];
        if ($_GET['id'] > 0) {
            $track_obj = $this->db->query_first("SELECT tracking FROM " . TBL_CMS_EMAILER . " WHERE id=" . $_GET['id'] . " LIMIT 1");
            $email = base64_decode($_GET['n']);
            if (validate_email_input($email)) {
                if ($track_obj['tracking'] != "")
                    $track_obj['tracking'] .= '!';
                $track_obj['tracking'] .= $email;
                update_table(TBL_CMS_EMAILER, "id", $_GET['id'], $track_obj);
            }
        }
        $this->hard_exit();
    }


    /**
     * newsletter_class::cmd_remoteadd()
     * 
     * @return
     */
    function cmd_remoteadd() {
        $vorlage = get_template(580);
        if ($_GET['type'] == 'news') {
            $N_OBJ = $this->db->query_first("SELECT * FROM
		" . TBL_CMS_NEWSLIST . " NL
		LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
		WHERE NL.id=" . $_GET['id']);
            $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_NEWSCONTENT . " WHERE lang_id=" . $_GET['uselang'] . " AND nid='" . $_GET['id'] . "' LIMIT 1");
            $vorlage = str_replace('{TMPL_INHALT}', $FORM_CON['content'], $vorlage);
        }
        else {
            die();
        }
        $NEWSLETTER['e_date'] = date('Y-m-d');
        $NEWSLETTER['e_time'] = date('h:i:s');
        $NEWSLETTER['e_timeint'] = time();
        $NEWSLETTER['e_anrede_m'] = 'Sehr geehrter Herr';
        $NEWSLETTER['e_anrede_w'] = 'Sehr geehrte Frau';
        $NEWSLETTER['e_subject'] = $FORM_CON['title'];
        $NEWSLETTER['e_content'] = $this->db->real_escape_string($vorlage);
        $NEWSLETTER['e_html'] = 1;
        $id = insert_table(TBL_CMS_EMAILER, $NEWSLETTER);
        $this->msg("{LBLA_SAVED}");
        HEADER("location:" . $_SERVER['PHP_SELF'] . "?epage=" . $_REQUEST['epage'] . "&cmd=edit&id=" . $id);
        exit;
    }

}
