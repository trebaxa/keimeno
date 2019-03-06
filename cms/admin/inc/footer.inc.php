<?PHP

include (CMS_ROOT . 'admin/inc/smarty.inc.php');
global $ADMINOBJ; //important
$ADMINOBJ->content .= $content;
$ADMINOBJ->output();

?>