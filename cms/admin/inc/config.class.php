<?PHP

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class config_class extends keimeno_class {

    protected $content = "";
    var $ctable = array();
    var $MODIDENT = "";

    /**
     * config_class::__construct()
     * 
     * @param string $modident
     * @return
     */
    function __construct($modident = "") {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->MODIDENT = $modident;
    }

    /**
     * config_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config() {
        $this->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * config_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('CMSCONFIG', $this->content);
    }

    /**
     * config_class::init()
     * 
     * @return
     */
    function init() {
        $result = $this->db->query("SELECT C.*,CG.catgroup AS Ccatgroup, CG.id AS CGID	FROM " . TBL_CMS_GBLCONFIG . " C, " . TBL_CMS_CONFGROUPS . " CG 
				WHERE CG.visible=1 AND CG.id=C.gid ORDER BY CG.catgroup,C.morder");
        if (isset($_GET['configcid'])) {
            $confid = $_GET['configcid'];
        }
        else {
            $confid = 0;
        }
        $this->content = $this->buildTable($confid, "", $result);
        $this->parse_to_smarty();
    }

    /**
     * config_class::get_value_from_table()
     * 
     * @param mixed $table
     * @param mixed $column
     * @param string $where
     * @return
     */
    function get_value_from_table($table, $column, $where = '') {
        $result = $this->db->query_first("SELECT $column FROM $table WHERE $where LIMIT 1");
        return $result[$column];
    }

    /**
     * config_class::load()
     * 
     * @return
     */
    function load() {
        $result = $this->db->query("SELECT *	FROM " . TBL_CMS_GBLCONFIG);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->gbl_config[$row['config_name']] = $row['config_value'];
        }
        $rep = array('www.');
        $this->gbl_config['opt_domain'] = str_replace($rep, "", $_SERVER['HTTP_HOST']);
        $this->gbl_config['max_att_size'] = 100000;
        $this->gbl_config['opt_site_domain'] = self::get_http_protocol() . "://www." . $this->gbl_config['opt_domain'] . PATH_CMS;
        return $this->gbl_config;
    }

    /**
     * config_class::load_all_fields()
     * 
     * @return
     */
    function load_all_fields() {
        $result = $this->db->query("SELECT *	FROM " . TBL_CMS_GBLCONFIG);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->ctable[$row['config_name']] = $row;
        }
    }

    /**
     * config_class::save()
     * 
     * @param mixed $FORM
     * @return
     */
    function save($FORM) {
        $this->load_all_fields();
        $FORM['opt_product_direc'] = strtoupper($FORM['opt_product_direc']);
        foreach ($FORM as $key => $wert) {
            if ($key == "opt_product_direc" && $FORM[$key] != "DESC" && $FORM[$key] != "ASC") {
                $wert = 'ASC';
            }
            if ($this->ctable[$key]['is_time'] == 1) {
                $wert = date('H:i:s', strtotime($wert));
            }
            if ($this->ctable[$key]['isnumeric'] == 1)
                $wert *= 1;
            if ($this->ctable[$key]['max'] > 0 && $wert > $this->ctable[$key]['max'])
                $wert = $this->ctable[$key]['max'];
            if ($this->ctable[$key]['is_schalter'] == 1)
                if ($wert > 0)
                    $wert = ($wert / $wert) * 1;
                else
                    $wert = 0;
            if ($this->ctable[$key]['del_spaces'] == 1)
                $wert = trim(str_replace(" ", "", $wert));
            $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $wert . "'" . (($this->MODIDENT != "") ? ", modident='" . $this->MODIDENT . "'" : "") .
                " WHERE config_name='" . $key . "' LIMIT 1");
        }
        $i = 0;
        if (is_array($_POST['ids']) && count($_POST['ids']) > 0) {
            foreach ($_POST['ids'] as $key => $wert) {
                $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET gid='" . $_POST['groups'][$i] . "' WHERE config_name='" . $wert . "' LIMIT 1");
                $i++;
            }
        }
        $this->db->disconnect();
        die(); // WICHTIG! Da jetzt mit AJAX gespeichert wird...kein page reload
    }

    /**
     * config_class::buildTable()
     * 
     * @param string $selected_cid
     * @param string $load_cid
     * @param string $result
     * @param mixed $addkeys
     * @return
     */
    function buildTable($selected_cid = "", $load_cid = "", $result = "", $addkeys = array()) {
        $k = 0;
        $CAT_GROUPS = array();
        $content = "";
        if ($load_cid > 0 && $this->MODIDENT == "") {
            $result = $this->db->query("SELECT C.*,CG.catgroup AS Ccatgroup, CG.id AS CGID	FROM " . TBL_CMS_GBLCONFIG . " C, " . TBL_CMS_CONFGROUPS . " CG 
				WHERE CG.id=" . $load_cid . " AND CG.id=C.gid ORDER BY CG.menu_order,C.morder");
        }
        else
            if ($this->MODIDENT != "") {
                $result = $this->db->query("SELECT C.* FROM " . TBL_CMS_GBLCONFIG . " C 
				WHERE modident='" . $this->MODIDENT . "' ORDER BY C.morder");
            }
            else {
                $result = $this->db->query("SELECT C.*,CG.catgroup AS Ccatgroup, CG.id AS CGID	FROM " . TBL_CMS_GBLCONFIG . " C, " . TBL_CMS_CONFGROUPS . " CG 
				WHERE CG.visible=1 AND CG.id=C.gid ORDER BY CG.menu_order,C.morder");
            }
            while ($row = $this->db->fetch_array_names($result)) {
                $CAT_GROUPS[$row['CGID']]['label'] = $row['Ccatgroup'];
                $CAT_GROUPS[$row['CGID']]['options'][] = $row;
                if (intval($selected_cid) == 0)
                    $selected_cid = $row['CGID'];
            }

        if (count($CAT_GROUPS) > 1) {
            $group_table = '<ul class="nav nav-sidebar" id="vertnav">';
            foreach ($CAT_GROUPS as $CGID => $CAT_GROUP) {
                $CAT_GROUPS[$CGID]['ident'] = $k;
                $group_table .= '	<li>
   <a title="' . $CAT_GROUP['label'] . '" href="javascript:void(0)" data-layer="' . $k . '" class="vertmenuclick" >' . $CAT_GROUP['label'] . '</a>   </li>';
                $k++;
            }
            $group_table .= '</ul>';
        }

        $content .= '
	<%include file="cb.panel.header.tpl" icon="fa-cog" title="Konfiguration"%>
    <form class="stdform form-inline" method="post" action="' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '">';
        if (count($addkeys) > 0) {
            foreach ($addkeys as $key => $value) {
                $content .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
            }
        }
        $content .= '
	<input type="hidden" name="aktion" value="save_config">
	<input type="hidden" name="epage" value="' . $_GET['epage'] . '">
	<input type="hidden" id="configcid" name="configcid" value="' . $selected_cid . '">
	<div class="row">
	' . ((count($CAT_GROUPS) > 1) ? '<div class="col-md-2 sidebar">' . $group_table . '</div>' : "") . '<div class="col-md-' . ((count($CAT_GROUPS) > 1) ? "10" :
            "12") . '">';

        foreach ($CAT_GROUPS as $CGID => $CAT_GROUP) {
            $content .= '

	 <!-- LAYER' . $CAT_GROUP['ident'] . ' //-->
	<div id="layer' . $CAT_GROUP['ident'] . '" class="vertmenulayer" style="display:' . (($selected_cid == $CGID) ? 'block' : 'none') . '">
	<table class="table table-striped table-hover"    >
	<thead><tr><th>Beschreibung</th><th>Wert</th><th>Template Variable</th></tr></thead>';
            foreach ($CAT_GROUP['options'] as $row) {
                if (!strstr($row['config_desc'], ":"))
                    $row['config_desc'] .= ':';
                $content .= '<tr><td style="width:390px;word-break:break-all;word-wrap:break-word" >' . $row['config_desc'] . '</td>';
                $TABLE_LADMIN = TBL_CMS_LANG_ADMIN;
                $TABLE_LANG = TBL_CMS_LANG;
                $TABLE_LAND = TBL_CMS_LAND;
                $TABLE_TZ = TBL_CMS_TIMEZONE;
                $TABLE_CURR = "";


                if ($row['is_schalter'] == 1) {
                    $content .= '<td >
                   <%include file="cb.radioswitch.tpl" value="' . $row['config_value'] . '" name="FORM[' . $row['config_name'] . ']"%>                    
                 </td>';
                }
                else
                    if ($row['is_text'] == 1) {
                        $content .= '<td ><textarea class="form-control input-sm" rows="10" cols="90" name="FORM[' . $row['config_name'] . ']">' . htmlspecialchars($row['config_value']) .
                            '</textarea></td>';
                    }
                    else
                        if ($row['is_password'] == 1) {
                            $content .= '<td ><input class="form-control input-sm" autocomplete="off" type="password" name="FORM[' . $row['config_name'] . ']" value="' . htmlspecialchars($row['config_value']) .
                                '"></td>';
                        }
                        else
                            if ($row['is_list'] != "") {
                                $content .= '<td ><select class="form-control input-sm" name="FORM[' . $row['config_name'] . ']">';
                                $list = explode("|", $row['is_list']);
                                foreach ($list as $lv)
                                    $content .= '<option ' . (($lv == $row['config_value']) ? 'selected' : '') . ' value="' . $lv . '">' . $lv . '</option>';
                                $content .= '</select></td>';
                            }
                            else
                                if ($row['is_countryiso'] == 1) {
                                    $content .= '<td ><select class="form-control input-sm" name="FORM[' . $row['config_name'] . ']">';
                                    $result2 = $this->db->query("SELECT * FROM " . $TABLE_LAND . " WHERE 1 ORDER BY land");
                                    while ($row2 = $this->db->fetch_array_names($result2)) {
                                        $content .= '<option ' . (($row['config_value'] == $row2['country_code_2']) ? 'selected' : '') . ' value="' . $row2['country_code_2'] . '">' . $row2['land'] .
                                            ' [' . $row2['country_code_2'] . ']</option>';
                                    }
                                    $content .= '</select></td>';
                                }
                                else
                                    if ($row['is_timezone'] == 1) {
                                        $content .= '<td ><select class="form-control input-sm" name="FORM[' . $row['config_name'] . ']">';
                                        $result2 = $this->db->query("SELECT * FROM " . $TABLE_TZ . " WHERE 1 ORDER BY gmt");
                                        while ($row2 = $this->db->fetch_array_names($result2)) {
                                            $content .= '<option ' . (($row['config_value'] == $row2['tz']) ? 'selected' : '') . ' value="' . $row2['tz'] . '">' . $row2['gmt'] . ' [' . $row2['tz'] .
                                                ']</option>';
                                        }
                                        $content .= '</select></td>';
                                    }
                                    else
                                        if ($row['is_font'] == 1) {
                                            $content .= '<td ><select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                            $file_list = array();
                                            $dh = opendir('../fonts/');
                                            while (false !== ($filename = readdir($dh))) {
                                                if ($filename != '.' && $filename != '..')
                                                    $file_list[$filename] = $filename;
                                            }
                                            asort($file_list);
                                            foreach ($file_list as $key => $filename) {
                                                $ext = strtolower(strrchr($filename, '.'));
                                                $content .= '<option ' . (($row['config_value'] == str_ireplace($ext, "", $filename)) ? 'selected' : '') . ' value="' . str_ireplace($ext, "", $filename) . '">' .
                                                    str_ireplace($ext, "", $filename) . '</option>';
                                            }
                                            $content .= '</select></td>';
                                        }
                                        else
                                            if ($row['is_lang'] == 1) {
                                                $content .= '<td >
						<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] "><option value="0">DYNAMIC</option>';
                                                $result2 = $this->db->query("SELECT * FROM " . $TABLE_LADMIN . " WHERE approval='1' ORDER BY language");
                                                while ($row2 = $this->db->fetch_array_names($result2)) {
                                                    $content .= '<option ' . (($row['config_value'] == $row2['id']) ? 'selected' : '') . ' value="' . $row2['id'] . '">' . $row2['post_lang'] . '</option>';
                                                }
                                                $content .= '</select></td>';
                                            }
                                            else
                                                if ($row['is_gbltplmodname'] != "") {
                                                    $content .= '<td >
						<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                    $result2 = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=1 AND modident='" . $row['is_gbltplmodname'] . "' ORDER BY description");
                                                    while ($row2 = $this->db->fetch_array_names($result2)) {
                                                        $content .= '<option ' . (($row['config_value'] == $row2['id']) ? 'selected' : '') . ' value="' . $row2['id'] . '">' . $row2['description'] . '</option>';
                                                    }
                                                    $content .= '</select></td>';
                                                }
                                                else
                                                    if ($row['is_mail'] != "") {
                                                        $content .= '<td >
						<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                        $result2 = $this->db->query("SELECT * FROM " . TBL_CMS_MAILTEMP . " WHERE  module_id='" . $row['is_mail'] . "' ORDER BY title");
                                                        while ($row2 = $this->db->fetch_array_names($result2)) {
                                                            $content .= '<option ' . (($row['config_value'] == $row2['id']) ? 'selected' : '') . ' value="' . $row2['id'] . '">' . $row2['title'] . '</option>';
                                                        }
                                                        $content .= '</select></td>';
                                                    }
                                                    else
                                                        if ($row['is_rediapi'] == 1) {
                                                            $content .= '<td >
						<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                            $result2 = $this->db->query("SELECT * FROM " . TBL_CMS_REDIAPI . " WHERE 1 ORDER BY r_name");
                                                            while ($row2 = $this->db->fetch_array_names($result2)) {
                                                                $content .= '<option ' . (($row['config_value'] == $row2['id']) ? 'selected' : '') . ' value="' . $row2['id'] . '">' . $row2['r_name'] . '</option>';
                                                            }
                                                            $content .= '</select></td>';
                                                        }
                                                        else
                                                            if ($row['is_lang_fe'] == 1) {
                                                                $content .= '<td >
						<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                                $result2 = $this->db->query("SELECT * FROM " . $TABLE_LANG . " WHERE approval='1' ORDER BY language");
                                                                while ($row2 = $this->db->fetch_array_names($result2)) {
                                                                    $content .= '<option ' . (($row['config_value'] == $row2['id']) ? 'selected' : '') . ' value="' . $row2['id'] . '">' . $row2['post_lang'] . '</option>';
                                                                }
                                                                $content .= '</select></td>';
                                                            }
                                                            else
                                                                if ($row['is_jqueryver'] == 1) {
                                                                    $content .= '<td >
							<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                                    $file_list = array();
                                                                    $dh = opendir('../js/');
                                                                    while (false !== ($filename = readdir($dh))) {
                                                                        if ($filename != '.' && $filename != '..' && strstr($filename, 'jquery-'))
                                                                            $file_list[$filename] = $filename;
                                                                    }
                                                                    asort($file_list);
                                                                    foreach ($file_list as $key => $filename) {
                                                                        $content .= '<option ' . (($row['config_value'] == $filename) ? 'selected' : '') . ' value="' . $filename . '">' . $filename . '</option>';
                                                                    }
                                                                    $content .= '</select></td>';
                                                                }
                                                                else
                                                                    if ($row['is_modernizr'] == 1) {
                                                                        $content .= '<td >
							<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                                        $file_list = array();
                                                                        $dh = opendir('../js/');
                                                                        while (false !== ($filename = readdir($dh))) {
                                                                            if ($filename != '.' && $filename != '..' && strstr($filename, 'modernizr-'))
                                                                                $file_list[$filename] = $filename;
                                                                        }
                                                                        asort($file_list);
                                                                        foreach ($file_list as $key => $filename) {
                                                                            $content .= '<option ' . (($row['config_value'] == $filename) ? 'selected' : '') . ' value="' . $filename . '">' . $filename . '</option>';
                                                                        }
                                                                        $content .= '</select></td>';
                                                                    }
                                                                    else
                                                                        if ($row['is_land'] == 1) {
                                                                            $content .= '<td >
						<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                                            $result2 = $this->db->query("SELECT * FROM " . $TABLE_LAND . " WHERE 1 ORDER BY land");
                                                                            while ($row2 = $this->db->fetch_array_names($result2)) {
                                                                                $content .= '<option ' . (($row['config_value'] == $row2['id']) ? 'selected' : '') . ' value="' . $row2['id'] . '">' . $row2['land'] . '</option>';
                                                                            }
                                                                            $content .= '</select></td>';
                                                                        }
                                                                        else
                                                                            if ($row['is_curr'] == 1 && $TABLE_CURR != "") {
                                                                                $content .= '<td >
						<select class="form-control input-sm" name="FORM[' . $row['config_name'] . '] ">';
                                                                                $result2 = $this->db->query("SELECT * FROM " . $TABLE_CURR . " WHERE 1 ORDER BY letter_code");
                                                                                while ($row2 = $this->db->fetch_array_names($result2)) {
                                                                                    $content .= '<option ' . (($row['config_value'] == $row2['letter_code']) ? 'selected' : '') . ' value="' . $row2['letter_code'] . '">' . $row2['letter_code'] .
                                                                                        '</option>';
                                                                                }
                                                                                $content .= '</select></td>';
                                                                            }
                                                                            else {
                                                                                $content .= '<td ><input autocomplete="off" ' . kf::gen_inputtext_field($row['config_value'], 60) . ' name="FORM[' . $row['config_name'] . ']">' . (($row['max'] >
                                                                                    0) ? '&nbsp;<span class="small">max.: ' . $row['max'] : '') . '</span></td>';
                                                                            }
                                                                            if (isset($_GET['show_group']))
                                                                                $content .= '<td ><input type="hidden" name="ids[]" value="' . $row['config_name'] . '"><select class="form-control input-sm" name="groups[]" >' .
                                                                                    build_options_for_selectbox(TBL_CONFGROUPS, 'id', 'catgroup', $where = 'ORDER BY catgroup', $row[gid]) . '</select></td>';

                $content .= '<td><code>' . htmlspecialchars('<% $gbl_config.' . $row['config_name'] . ' %>') . '</code></td></tr>';
            } //WHILE
            $content .= '</table></div>';
        } // foreach GROUPS
        $content .= '<div >' . kf::gen_admin_sub_btn('{LBLA_SAVE}') . '</div>
        </div></div></form>
        <%include file="cb.panel.footer.tpl"%>';
        return smarty_compile($content);
    }

}
