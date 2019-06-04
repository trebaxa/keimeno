<?php

/**
 * @package    newsletter
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

$NEWSADMIN = new newsletter_admin_class();
$NEWSADMIN->TCR->interpreter();

$NEWSLETTER_OBJ = new newsletter_class();
$NEWSLETTER_OBJ->TCR->interpreter();
$_GET['id'] = ($_GET['id'] == 0) ? $_POST['id'] : $_GET['id'];

//*****************************
//***** SENDEN
//*****************************
if (($_GET['aktion'] == "START_SEND" || $_POST['aktion'] == "START_SEND") && $_GET['id'] > 0) {
    $gbl_config['news_num'] = 100;
    $pause_time = 30;

    $_SESSION['EMAILS_SEND_ARR'] = array();
    if ((int)$_POST['roundzero'] == 1) {
        $kdb->query("UPDATE " . TBL_CMS_CUST . " SET mailsend=0"); // Setzt alle Kunden auf verschickbar bei ERSTEN Start
    }
    $E_OBJ = $kdb->query_first("SELECT * FROM " . TBL_CMS_EMAILER . " WHERE id='" . $_GET['id'] . "' LIMIT 1");
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_CUST . " U, " . TBL_CMS_CUSTTOGROUP . " G 
 			WHERE U.mailsend=0
 			AND (U.email<>'' OR U.email_notpublic<>'')
 			AND U.mailactive=1
 			AND U.kid=G.kid 
 			AND G.gid=" . $E_OBJ['groups'] . " 
 			GROUP BY U.kid
 			ORDER BY U.kid DESC 
 			LIMIT " . $gbl_config['news_num']);
    $start_time = time();
    $mail_feedback .= '<table class="table table-striped table-hover" >';
    while ($row = $kdb->fetch_array_names($result)) {
        $row['email'] = (!validate_email_input($row['email_notpublic'])) ? $row['email'] : $row['email_notpublic'];
        $z++;
        if (!in_array($row['email'], $_SESSION['EMAILS_SEND_ARR'])) {
            $SendSuccess = $NEWSLETTER_OBJ->sendNewsToEmail($row['email'], $gbl_config['news_senderemail'], $E_OBJ, $row);
        }
        $mail_feedback .= '<tr><td>' . $z . '</td><td>' . $row['email'] . '</td><td align="right"><div class="bg-success">OK</div></td></tr>';
        $kdb->query("UPDATE " . TBL_CMS_CUST . " SET mailsend=1 WHERE kid=" . $row['kid'] . " OR email LIKE '" . $row['email'] . "' OR email_notpublic LIKE '" . $row['email'] .
            "'");
        sleep(0.01);
        $_SESSION['EMAILS_SEND_ARR'][] = $row['email'];
    }
    unset($_SESSION['EMAILS_SEND_ARR']);
    $mail_feedback .= '</table>';
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_CUST . " U, " . TBL_CMS_CUSTTOGROUP . " G 
 			WHERE U.kid>0 
 			AND U.mailsend=0
 			AND (U.email<>'' OR U.email_notpublic<>'')
 			AND U.mailactive=1
 			AND U.kid=G.kid 
 			AND G.gid=" . $E_OBJ['groups'] . "
 			GROUP BY U.kid
 			ORDER BY U.kid DESC ");
    $count = $kdb->num_rows($result);
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_CUST . " U, " . TBL_CMS_CUSTTOGROUP . " G 
 			WHERE U.kid>0 
 			AND U.mailsend=1
 			AND (U.email<>'' OR U.email_notpublic<>'')
 			AND U.mailactive=1
 			AND U.kid=G.kid 
 			AND G.gid=" . $E_OBJ['groups'] . "
 			GROUP BY U.kid
 			ORDER BY U.kid DESC ");
    $count_done = $kdb->num_rows($result);
    if ($count > 0) {
        $restart_in = ($pause_time - (time() - $start_time));
        if ($restart_in < 0)
            $restart_in = 10;
        HEADER("Refresh: " . $restart_in . ";  URL=" . $_SERVER['PHP_SELF'] . "?epage=" . $_REQUEST['epage'] . "&id=" . $_GET['id'] . "&aktion=START_SEND&rand=" .
            urlencode(gen_sid(10)));
        $total = $count + $count_done;
        $kdb->query("UPDATE " . TBL_CMS_EMAILER . " SET e_timeint='" . time() . "',e_sendcount='" . $count_done . "' WHERE id='" . $E_OBJ['id'] . "'");


        $ADMINOBJ->content .= '
        <table class="table table-striped table-hover">
        <tr>
            <td><b>{LBLA_FINISHED}: </td><td>' . (round(100 / $total * $count_done, 2)) . '%</b></td>
        </tr>
        <tr>    
            <td>{LBLA_LEFT}:</td><td>' . $count . '</td></tr>
            <tr>
                <td>{LBLA_RESTARTIN} </td><td>' . $restart_in . 's</td></tr>
                <tr>    
            <td>Verbleibend:</td><td>' . round(($count / $gbl_config['news_num']), 2) . ' min = ' . round(($count / $gbl_config['news_num'] / 60), 2) .
            ' hours</td></tr>
            </table>
            <b>bitte warten...</b><br><br>' . $E_OBJ['e_subject'] . '
            <script language="JavaScript" type="text/javascript">
					<!--
 						var sekunden = ' . $restart_in . ';
 					function setSecs() {	
 					  sekunden=sekunden-1;
 					  if (sekunden<=0) {
 					  	document.clock.sekunden.value = "GO";
 					  	} else {
 							document.clock.sekunden.value = sekunden;
 							setTimeout("setSecs()", 1000);
 						}
 					}	
 					setTimeout("setSecs()", 1000);
 					//-->
					</script>	
		 <form method="POST" name="clock">{LBLA_RESTARTIN} <input type="text" class="form-control" name="sekunden" size="3" value="' . $restart_in .
            '"> {LBLA_SECONDS}</form>';
        if ($mail_feedback != "")
            $ADMINOBJ->content .= $mail_feedback;

    }
    else {
        $sento_mails = array();
        $result = $kdb->query("SELECT * FROM " . TBL_CMS_CUST . " WHERE mailsend=1");
        while ($row = $kdb->fetch_array_names($result)) {
            $em = ($row['email_notpublic'] != "") ? $row['email_notpublic'] : $row['email'];
            if ($em != "")
                $sento_mails[] = $em;
        }
        $sento_mails = array_unique($sento_mails);
        $send_mails = implode('!', $sento_mails);
        $kdb->query("UPDATE " . TBL_CMS_EMAILER . " SET e_date='" . date("Y-m-d") . "',e_time='" . date("H:i:s") . "',send_emails='" . $TCMASTER->db->
            real_escape_string($send_mails) . "',e_timeint='" . time() . "',e_done='1',e_sendcount='" . count($sento_mails) . "' WHERE id='" . $E_OBJ['id'] . "'");
        $kdb->query("UPDATE " . TBL_CMS_CUST . " SET mailsend=1");
        $TCMASTER->LOGCLASS->addLog('SENDMAIL', 'newsletter finished');
        $NEWSLETTER_OBJ->msg("{LBLA_NEWSFINISHED}.");
        header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=show_hist');
        exit;
    }

}
else {
    $menu = array(
        "{LBLA_SHOWALL}" => "cmd=show_hist",
        "Empfänger" => "cmd=members",
        "Email-Listen" => "cmd=load_lists");
    $ADMINOBJ->set_top_menu($menu);
}

$ADMINOBJ->inc_tpl('newsletter');
$NEWSLETTER_OBJ->parse_to_smarty();
$NEWSADMIN->parse_to_smarty();
