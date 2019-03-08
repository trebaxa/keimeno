<?php




/**
 * @package    news
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class newssub_class extends keimeno_class
{

    /**
     * newssub_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * newssub_class::cmd_insert()
     * 
     * @return
     */
    function cmd_insert()
    {
        $FORM = $_POST['FORM'];
        if (count($FORM) > 0)
            foreach ($FORM as $key => $value)
                $FORM[$key] = trim(strip_tags($FORM[$key]));


        $tschapura = $FORM['tschapura'];
        if (!validate_email_input($tschapura))
            $this->msge("{LBL_EMAIL}");
        if (!validate_subject($FORM['nachname']))
            $this->msge("{LBL_NACHNAME}");
        if (!validate_subject($FORM['vorname']))
            $this->msge("{LBL_VORNAME}");

        if ($FORM['nachname'] == '')
            $this->msge("{LBL_NACHNAME}");

        if ($FORM['vorname'] == '')
            $this->msge("{LBL_VORNAME}");
        if (get_data_count(TBL_CMS_CUST, "kid", "email='" . $FORM['tschapura'] . "'") >
            0 && $FORM['tschapura'] != "")
            $this->msge("{LBL_EMAIL} {LBL_ALREADY_EXISTS}");


        # Token
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            $this->msge("invalid token.");
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP .
                ', ' . $_SERVER['REQUEST_URI']);
        }

        if (count($_SESSION['err_msgs']) == 0) {
            $FORM['time_int'] = time();
            $FORM['datum'] = date("Y-m-d");
            $FORM['mailactive'] = 1;
            $FORM['email'] = $FORM['tschapura'];
            $FORM['ip'] = REAL_IP;
            unset($FORM['tschapura']);
            insert_table(TBL_CMS_CUST, $FORM);
            foreach ($FORM as $key => $value)
                $email_msg .= strtoupper(str_replace("tschapura", "EMAIL", $key)) . ": " . $FORM[$key] .
                    "\n";
            $inhalt = pure_translation("IP:$ip - " . date("Y-m-d") . " - " . date("H:i:s") .
                "\n" . $email_msg, $GBL_LANGID);
            mail(FM_EMAIL, utf8_decode(pure_translation("{LBL_NEWS_REG} " . $FORM['nachname'] .
                ', ' . $FORM['vorname'], $GBL_LANGID)), utf8_decode($inhalt), "From: $tschapura");
            $this->msg("{LBL_SUCCESSFULLY} {LBL_SUBSCRIBED}");
            HEADER("location: " . $_SERVER['PHP_SELF'] . "?page=" . $_POST['page'] .
                "&section=done");
            $this->hard_exit();
        }
    }

    /**
     * newssub_class::cmd_remove()
     * 
     * @return
     */
    function cmd_remove()
    {
        $FORM = $_POST['FORM'];
        # Token
        if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
            $this->msge("invalid token.");
            $this->LOGCLASS->addLog('INVALID_TOKEN', 'invalid token over IP ' . REAL_IP .
                ', ' . $_SERVER['REQUEST_URI']);
        }
        if (!validate_email_input($tschapura)) {
            $this->msge("{LBL_EMAIL}");
        }

        if (count($_SESSION['err_msgs']) == 0) {
            $this->db->query("UPDATE " . TBL_CMS_CUST . " SET mailactive=0 WHERE email='" .
                $FORM['tschapura'] . "' LIMIT 1");
            $this->msg("{LBL_SUCCESSFULLY} {LBL_UNSUBSCRIBED}");
            HEADER("location: " . $_SERVER['PHP_SELF'] . "?page=" . $_POST['page'] .
                "&section=done");
            exit;
        }

    }

}

?>