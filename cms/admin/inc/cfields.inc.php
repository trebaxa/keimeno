<?php




/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


require (CMS_ROOT . 'admin/inc/cfield.class.php');
$CFIELD = new cfield_class();
$CFIELD->TCR->interpreter();


if ($_GET['aktion'] == 'syncfields') {
    $CFIELD->syncFields();
    keimeno_class::msg('{LBL_SAVED}');
    header('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
    exit;
}

if ($_GET['aktion'] == 'a_new') {
    $CFIELD->newSet();
    keimeno_class::msg('{LBL_SAVED}');
    header('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
    exit;
}


$menu = array("{LBL_SHOWALL}" => "", "{LBL_CUSTOMERMANAGER}" => "cmd=a_custm");
$ADMINOBJ->set_top_menu($menu);

if ($_GET['aktion'] == '') {
    asort($CFIELD->CF_TYPES);
    $content .= '<div class="page-header"><h1>{LBL_CUSTOMFIELDS}</h1></div>
    <div class="btn-group">
        <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=a_new">{LBL_NEWCFENTRY}</a>
    </div>';
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_CUSTOMFIELDS . " ORDER BY cf_name");
    while ($row = $kdb->fetch_array_names($result)) {
        $opt = "";
        foreach ($CFIELD->CF_TYPES as $key => $value_arr) {
            $opt .= '<option ' . (($row['cf_type'] == $key) ? 'selected' : '') . ' value="' . $key . '">' . $value_arr['label'] . '</option>';
        }
        $tab .= '<tr>
	<td><input size="' . ((strlen($row['cf_name']) > 10) ? strlen($row['cf_name']) : 10) . '" class="form-control" type="text" name="FORM[cf_name][' . $row['id'] . ']"  value="' . $row['cf_name'] .
            '"></td>
	<td>Input-Field:{TMPL_CFI_' . $row['id'] . '}<br>Feldname:{TMPL_CFL_' . $row['id'] . '}<br>Beschreibung:{TMPL_CFD_' . $row['id'] . '}</td>
	<td><input type="checkbox" ' . (($row['cf_verify'] == 1) ? 'checked' : '') . ' name="FORM[cf_verify][' . $row['id'] . ']" value="1"></td>
	<td><input type="checkbox" ' . (($row['cf_duty'] == 1) ? 'checked' : '') . ' name="FORM[cf_duty][' . $row['id'] . ']" value="1"></td>
	<td><input type="checkbox" ' . (($row['cf_search'] == 1) ? 'checked' : '') . ' name="FORM[cf_search][' . $row['id'] . ']" value="1"></td>
	<td><select class="form-control" name="FORM[cf_type][' . $row['id'] . ']">' . $opt . '</select></td>
	<td>' . (($row['cf_type'] == 'L') ? '<input size="' . ((strlen($row['cf_select']) > 10) ? strlen($row['cf_select']) : 10) .
            '" type="text" name="FORM[cf_select][' . $row['id'] . ']"  value="' . $row['cf_select'] . '">' : '') . '</td>
	<td>' . kf::gen_del_icon($row['id'], true,'a_del').  kf::gen_edit_icon($row['id']) . '</td>
	</tr>';
    }
    $content .= '<form class="jsonform" action="<%$PHPSELF%>" method=post>{TMPL_TABLE_CFIELDS}';
    $tab = ($tab != "") ? kf::translate_admin('<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th>{LBL_CFNAME}</th>
        <th>{LBL_CFINPUTJOKER}</th>
        <th>{LBL_CFVERIFY}</th>
        <th>{LBL_CFDUTY}</th>
        <th>{LBL_CFSEARCH}</th>
        <th>{LBL_CFTYPES}</th>
        <th>{LBL_CFSELECT}</th>
        <th></th></tr></thead><tbody>' . $tab . '</tbody></table><input type="hidden" name="cmd" value="a_msave">
<input type="hidden" name="epage" value="<%$epage%>"><%$subbtn%></form>') : kf::translate_admin('<hr>{LBL_NOENTRIES}');
}

if ($_GET['aktion'] == 'edit') {
    $content .= '<div class="page-header"><h1>{LBL_DESCRIPTION}</h1></div>';
    $p_object = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUSTOMFIELDS . " WHERE id=" . $_GET['id']);
    $content .= '<h3>' . $p_object['cf_name'] . '</h3>
    <div class="row">
    <div class="col-md-6">
    <form method="post" class="jsonform" action="<%$PHPSELF%>"><input type=hidden name="cf_id" value="' . $_GET['id'] . '">';
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_LANG . " ORDER BY id");
    while ($row = $kdb->fetch_array_names($result)) {
        $FORM = $kdb->query_first("SELECT * FROM " . TBL_CMS_CUSTOMFIELDSCONT . " WHERE cf_id='" . $_GET['id'] . "' AND langid='" . $row['id'] . "' LIMIT 1");
        $content .= '<table class="table table-striped">
        <tr><td width=10%>{LBL_LANGUAGE}:</td><td width="500"><input type="hidden" name=lids[' . $row['id'] . '] value="' . $row['id'] . '"><b>' . $row['post_lang'] .
            '</b></td></tr>
            <tr><td>{LBL_CFLABEL}:</td><td><input type=text  class="form-control" name=FORM[' . $row['id'] . '][cf_label] size=80 value="' . $FORM['cf_label'] . '"></td></tr>
            <tr><td colspan="2">Text:<br><textarea name="FORM[' . $row['id'] . '][content]" class="se-html">' . htmlspecialchars($FORM['content']) .
            '</textarea></td></tr>
        </table><input type="hidden" name="cmd" value="save_lang">
        <input type="hidden" name="epage" value="<%$epage%>">
        <%$subbtn%>';
    }
    $content .= '</form>
        </div>
    </div>';
}

$content .= '</td></tr></table></div>';
$content = str_replace("{TMPL_TABLE_CFIELDS}", $tab, $content);
