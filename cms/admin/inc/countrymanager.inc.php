<?php




/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



$CM = new country_class();
$CM->TCR->interpreter();

$menu = array("{LBL_COUNTRIES}" => "","{LBL_REGIONS}" => "cmd=region");

$ADMINOBJ->set_top_menu($menu);

$CM->load_regions();
$CM->load_region($_GET['id']);

if ($_GET['cmd'] == "") {
    $result = $kdb->query("SELECT * FROM " . TBL_CMS_LANDREGIONS . " ORDER BY lr_name");
    while ($row = $kdb->fetch_array_names($result)) {
        $land_regions[] = $row;
    }
    $smarty->assign('land_regions', $land_regions);

    $LNGOBJ->options = array('sql_table' => TBL_CMS_LANG_ADMIN, 'type' => 'yes');
    $LNGOBJ->load_langs();

    $ADMINOBJ->content .= '
	<div class="page-header"><h1>{LBL_COUNTRIES} Manager</h1></div>
	<div class="btn-group form-inline">
	<a class="btn btn-default" href="#" onclick="dc_show(\'addcountry\');">{LBL_ADD}</a>
    <label>{LBL_LANGUAGE}:</label><select class="form-control"  onChange="location.href=this.options[this.selectedIndex].value">
	<option value="<%$PHPSELF%>?epage=<%$epage%>&uselang=0">{LBL_ALL}</option>';
    foreach ($LNGOBJ->languages as $key => $sp) {
        $ADMINOBJ->content .= '<option ' . (($_GET['uselang'] == $sp['id']) ? 'selected' : '') . ' value="<%$PHPSELF%>?epage=<%$epage%>&uselang=' . $sp['id'] .
            '">' . $sp['post_lang'] . '</option>';
    }
    if ($_GET['uselang'] > 0) {
        foreach ($LNGOBJ->languages as $key => $sprache) {
            if ($sprache['id'] != (int)$_GET['uselang'])
                unset($LNGOBJ->languages[$key]);
        }
    }
    $ADMINOBJ->content .= '</select>
	</div>
    
    <div class="row divframe" id="addcountry">
        <div class="col-md-6">
	       <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
            <fieldset>
	       <legend>{LBL_ADD}</legend>
	       <input type="hidden" name="cmd" value="add_land">
	       <input type="hidden" name="epage" value="<%$epage%>">
	       <label>{LBL_COUNTRY}:</label>
            <input type="text" class="form-control" name="FORM[land]" value="">
            <label>Region:</label>
            <select class="form-control" name="FORM[region_id]" >	
		      <% foreach from=$land_regions item=lr %>	 
			     <option value="<%$lr.id%>"><%$lr.lr_name%></option>
		      <%/foreach%>
		      </select>
	       <div class="subright"><% $subbtn %></div>
	       </fieldset>
           </form>
        </div>
     </div>   
	
	<h3>{LBL_COUNTRIES} Manager</h3>
	<form action="<%$PHPSELF%>" method="post" class="jsonform form-inline" enctype="multipart/form-data">
	<input type="hidden" name="cmd" value="save_land_table">
	<input type="hidden" name="epage" value="<%$epage%>">
	<table  class="table table-striped table-hover">
	<thead><tr>
		<th>Nr.</th>
		<th>admin. Name</th>
		<th>CC 1</th>
		<th>CC 2</th>
		<th>Region</th>
		';
    foreach ($LNGOBJ->languages as $sprache) {
        $ADMINOBJ->content .= '<th>' . $sprache['post_lang'] . '</th>';
    } 
    $ADMINOBJ->content .= '		<th></th></tr></thead>';
    $resultc = $kdb->query("SELECT * FROM " . TBL_CMS_LANDCONTINET . " ORDER BY lc_name");
    while ($c_row = $kdb->fetch_array_names($resultc)) {
        $ADMINOBJ->content .= '<tr class="trsubheader"><td colspan="' . (6 + count($LNGOBJ->languages)) . '">' . $c_row['lc_name'] . '</td></tr>';

        $resultr = $kdb->query("SELECT * FROM " . TBL_CMS_LANDREGIONS . " WHERE lr_continet_id=" . $c_row['id'] . " ORDER BY lr_name");
        while ($region_row = $kdb->fetch_array_names($resultr)) {
            $ADMINOBJ->content .= '<tr class="trsubheader3"><td colspan="' . (6 + count($LNGOBJ->languages)) . '">' . $region_row['lr_name'] . '</td></tr>';

            $tarr = $org_land = $counteries = array();
            $result = $kdb->query("SELECT * FROM " . TBL_CMS_LAND . " WHERE region_id=" . $region_row['id'] . " ORDER BY land");
            while ($row = $kdb->fetch_array_names($result)) {
                $trans = explode(';', $row['transland']);
                foreach ($trans as $td) {
                    list($lang_id, $word) = explode('|', $td);
                    $tarr[$row['id']][$lang_id] = $word;
                    $org_land[$row['id']] = $row['land'];
                    $counteries[$row['id']] = $row;
                }
            }


            foreach ($tarr as $country_id => $word) {
                $k++;
                $ADMINOBJ->content .= '<tr>
		<td>' . $k . '</td>
		<td>
			<input name="LOPT[' . $counteries[$country_id]['id'] . '][land]" type="text" class="form-control" value="' . htmlspecialchars($counteries[$country_id]['land']) . '" size="' .
                    strlen($counteries[$country_id]['land']) . '" maxlength="30">
		</td>
		<td><input name="LOPT[' . $counteries[$country_id]['id'] . '][country_code]" type="text" class="form-control" value="' . htmlspecialchars($counteries[$country_id]['country_code']) .
                    '" size="3" maxlength="3"></td>
		<td><input name="LOPT[' . $counteries[$country_id]['id'] . '][country_code_2]" type="text" class="form-control" value="' . htmlspecialchars($counteries[$country_id]['country_code_2']) .
                    '" size="2" maxlength="3">
		<input name="LOPT[' . $counteries[$country_id]['id'] . '][id]" type="hidden" value="' . $counteries[$country_id]['id'] . '"></td>
		<td>
		<select class="form-control" name="LOPT[' . $counteries[$country_id]['id'] . '][region_id]" >
		<option <% if ($lr.id==0) %>selected<%/if%> value="0">- please choose -</option>
		<% foreach from=$land_regions item=lr %>	 
			<option <% if ($lr.id==' . $counteries[$country_id]['region_id'] . ') %>selected<%/if%> value="<%$lr.id%>"><%$lr.lr_name%></option>
		<%/foreach%>
		</select>
		</td>
		';
                foreach ($LNGOBJ->languages as $sprache) {
                    $land = ($tarr[$country_id][$sprache['id']] != "") ? $tarr[$country_id][$sprache['id']] : $org_land[$country_id];
                    $ADMINOBJ->content .= '<td><input name="FORM[' . $country_id . '][' . $sprache['id'] . ']" ' . kf::gen_inputtext_field($land) . '></td>';
                }
                $ADMINOBJ->content .= '
	 <td>' . (($counteries[$country_id]['id'] != 1) ? kf::gen_del_icon_reload($counteries[$country_id]['id'], 'del_country') : '') . '	 
	 </td>
	 </tr>';
            }
        }
    }

    $ADMINOBJ->content .= '</table><%$subbtn%></form><br><br><br>
    <a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=reinstall_countries">Reinstall countries</a>
    ';
}

$ADMINOBJ->inc_tpl('countrymanager');

?>