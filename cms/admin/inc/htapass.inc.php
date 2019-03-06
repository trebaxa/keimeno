<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/htapass.class.php');
$HTA = new htapass_class(PATH_CMS, CMS_ROOT);

if ($_POST['aktion'] == 'reset') {
    $HTA->htreset();
    HEADER('location:login.html?msg=' . base64_encode('{LBL_DONE}'));
    exit;
}

if ($_POST['aktion'] == 'save') {
    if (!preg_match("/^[a-zA-Z0-9]+$/s", $_POST['htapassword'])) {
        $HTA->msge('Ungueltige Zeichen im Passwort.');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }
    if (!preg_match("/^[a-zA-Z0-9]+$/s", $_POST['htauser'])) {
        $HTA->msge('Ungueltige Zeichen im Login Namen.');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }
    if ($_POST['htapassword'] != $_POST['htapassword2']) {
        $HTA->msge('Passwort Wdh. falsch');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }
    if ($_POST['htapassword'] != "" && $_POST['htauser'] != "") {
        $HTA->save_file($_POST['htauser'], $_POST['htapassword']);
    }
    HEADER('location:login.html?msg=' . base64_encode('Password set'));
    exit;
}

$ADMINOBJ->content .= '

<%include file="cb.panel.header.tpl" icon="fa-key" title="Server Passwort festlegen"%>
<div class="row">
<div class="col-md-6">
<form action="<%$PHPSELF%>" method="post" class="form-inline" enctype="multipart/form-data">
	<input type="hidden" name="aktion" value="save">
	<input type="hidden" name="epage" value="<% $epage %>">
<div >
<fieldset>	
 <table class="table table-striped table-hover" >
    <tr> 
		<td>Login Name:</td>
    <td><input autocomplete="off" name="htauser" type="text" class="form-control" value=""></td>
	</tr>
 <tr> 
		<td>Passwort:</td>
    <td><input autocomplete="off" name="htapassword" class="form-control" type="password" value=""></td>
	</tr>	
 <tr> 
		<td>Passwort Wiederholung:</td>
    <td><input autocomplete="off" name="htapassword2" class="form-control" type="password" value=""></td>
	</tr>	
  </table><div class="subright"><%$subbtn%></div><br><br><br>

  </fieldset>	
</div> 
  </form>
</div>
<div class="col-md-6">
    <div class="alert alert-info">Sollten Sie diese Zugangsdaten vergessen, so wenden Sie sich bitte an Ihren Administrator. Er wird Ihren administrativen Bereich wieder freigeben.</div>  
 <% if ($HTAPASS.isprotected==1) %> 
    <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="aktion" value="reset">
	<input type="hidden" name="epage" value="<% $epage %>">
    <div >
    <fieldset>	
    <legend>Server Passwort aufheben</legend>
    <div class="subright"><%$execbtn%></div>
    </fieldset>	
    </div> 
  </form>  
<%/if%>
</div> 
</div>
<%include file="cb.panel.footer.tpl"%> 
';

?>