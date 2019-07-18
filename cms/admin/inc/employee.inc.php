<?php



/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


$EMPMAN = new employee_class();
$EMPMAN->TCR->interpreter();

$CM = new country_class();
$menu = array(
	"{LBLA_SHOWALL}" => ""
	);
$ADMINOBJ->set_top_menu($menu);

  
$EMPMAN->load_employees();
$EMPMAN->load_admin_groups();
		  

/*
if ($_GET['aktion']=="countryrelated") {
	$CM->load_regions();
	$CM->load_regions_by_continent($_GET['continentid']);
	$CM->load_countries_by_region($_GET['regionid']);
	$EMPMAN->load_employee((int)$_GET['id'], 'empobjform');
}
*/

if ($_GET['aktion']=="edit") {
	$EMPMAN->load_lang_list();
	$EMPMAN->load_employee((int)$_GET['id'], 'empobjform');
	if (isset($_POST['FORM'])) {
		$FORM = $_POST['FORM'];
		$smarty->assign('empobjform', $FORM);
		}
		#if ($_SESSION['admin_obj']['GROUPID']==1) $smarty->assign('mitgroups', build_options_for_selectbox_opt(TBL_CMS_ADMINGROUPS, 'id', 'mgname', $where='ORDER BY mgname',$FORM['gid'], 0, "-"));
}

$ADMINOBJ->inc_tpl('employee.admin');