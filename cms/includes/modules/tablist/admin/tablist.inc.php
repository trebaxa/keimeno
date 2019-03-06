<?php

# Scripting by Trebaxa Company(R) 2009   									*

/**
 * @package    tablist
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$ADMINTAB = new tablistadmin_class();
$ADMINTAB->TCR->interpreter();

if ($_GET['gid'] > 0)
    $_SESSION['tablistgroup_id'] = $_GET['gid'];
if ($_GET['tabextview'] == 1)
    $_SESSION['tabextview'] = 1;
if ($_GET['tabextview'] == 2)
    unset($_SESSION['tabextview']);
$smarty->assign('tabextview', $_SESSION['tabextview']);


$menu = array("{LBL_TABELLEN}" => "aktion=", "{LBL_GROUPS}" => "aktion=calgroups");
$ADMINOBJ->set_top_menu($menu);
$LNGOBJ->init_uselang();
$_SESSION['CNT_TABBEDLANG'] = $LNGOBJ->build_lang_select();

$k = 0;
$result = $kdb->query("SELECT * FROM " . TBL_CMS_TABLIST_GROUP . " ORDER BY groupname");
while ($row = $kdb->fetch_array_names($result)) {
    if (intval($_SESSION['tablistgroup_id']) == 0 && $k == 0)
        $_SESSION['tablistgroup_id'] = $row['id'];
    $sel_box .= '<li' . (($row['id'] == $_SESSION['tablistgroup_id']) ? ' class="active"' : '') . ' ><a href="http://www.' . str_replace("//", "/", FM_DOMAIN .
        PATH_CMS . $_SERVER['PHP_SELF']) . '?epage=' . $_REQUEST['epage'] . '&gid=' . $row['id'] . '">' . $row['groupname'] . '</a></li>';
    if ($row['id'] == $_SESSION['tablistgroup_id'])
        $GROUP = $row;
    $k++;
}

$smarty->assign('sel_box', $sel_box);
$smarty->assign('group', $GROUP);

$_SESSION['tablistgroup_id'] = intval($_SESSION['tablistgroup_id']);


#$LNGOBJ->init_uselang();


if ($_GET['aktion'] == "") {
    $tab_items = array();
    $result = $kdb->query("SELECT NL.id AS NLID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID
	FROM " . TBL_CMS_TABLIST . " NL
	INNER JOIN " . TBL_CMS_TABLIST_GROUP . " NG ON (NG.id=NL.group_id AND NL.group_id=" . $_SESSION['tablistgroup_id'] . ")
	LEFT JOIN " . TBL_CMS_TABLIST_CONTENT . " NC ON (NL.id=NC.tab_id AND NC.lang_id=" . $_SESSION['alang_id'] . ")
	LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
	WHERE 1
	GROUP BY NL.id ORDER BY tab_name");
    while ($row = $kdb->fetch_array_names($result)) {
        $row['icon_del'] = kf::gen_del_icon($row['NLID'], true, 'del_table');
        #kf::gen_del_icon_reload($row['NLID']);
        $row['icon_edit'] = kf::gen_edit_icon($row['NLID']);
        $row['icon_approve'] = kf::gen_approve_icon($row['NLID'], $row['approval'], 'approverow');
        # kf::gen_approve_icon_reload($row['NLID'], $row['approval'], '&column=approval&idcol=id');
        #  $row['inlay'] = genTableImplement($row['NLID']);
        $row['select'] = build_html_selectbox('FORM[' . $row['NLID'] . '][tpl]', TBL_CMS_TEMPLATES, 'id', 'tpl_name', " WHERE modident='tablist' AND layout_group='1'",
            $row['tpl']);
        $tab_items[] = $row;
    }
    $smarty->assign('tab_items', $tab_items);

}


$ADMINOBJ->inc_tpl('tablist');
$ADMINTAB->parse_to_smarty();

?>