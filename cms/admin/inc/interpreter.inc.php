<?php



/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */




$ir = $INTERPRETER->interpreter($_REQUEST['aktion']);
if ($ir['status']===TRUE) {
	$ir['redirect'] = (($ir['redirect']!="") ? $ir['redirect'] : $_SERVER['PHP_SELF'] .'?epage='.$_GET['epage']);
	if (!empty($ir['msg'])) {
		$ir['redirect'] = $INTERPRETER->modify_url($ir['redirect'], array('msg' => base64_encode($ir['msg'])));
		HEADER('location:'. $ir['redirect']);
		exit;
	}
	if (!empty($ir['msge'])) {
		if (empty($ir['aktion'])) {
			$ir['redirect'] = $INTERPRETER->modify_url($ir['redirect'], array('msge' => base64_encode($ir['msge'])));
			HEADER('location:'. $ir['redirect']);
			exit;
			} else {
				$TCMASTER->GBLPAGE['err'] = $INTERPRETER->GBLPAGE['err'];
				$_GET['aktion'] = $_REQUEST['aktion'] = $_POST['aktion'] = $ir['aktion'];
			}
		}
}
unset($ir);


?>