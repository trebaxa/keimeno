<?PHP

$version = '1.0.2.7';

$tpl_rep = array('HTA_CMSFIXLINKS_CMS' => 'HTA_CMSFIXLINKS', );
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);
$this->replaceInTemplates($tpl_rep);

@unlink(CMS_ROOT . 'admin/tpl/standardform.validate.tpl');
@unlink(CMS_ROOT . 'includes/sitemapini.class.php');

$c = '<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Willkommen auf <% $gbl_config.adr_firma %></title>
    <link rel="stylesheet" type="text/css" href="<%$cms_url%>/includes/modules/fbwp/images/fblayout.css" />
    <script src="http://connect.facebook.net/en_US/all.js#xfbml=1" type="text/javascript"></script> 
  </head>
<body>
    
  <div id="fb">
  
<% if ($FBWP.WPU.is_fan) %>
    <div id="fbilikenow">
        Click on &quot;I like&quot; button above to be a fan! 
    </div>
<%/if%>    

   
    
    <div id="fb-root"></div>
    <fb:fan profile_id="<% $gbl_config.fb_profilid %>" logobar="false" width="400" connections="10" stream="false" header="false" ></fb:fan>

    <div id="fbfooter" class="stdfbwidth">
        <a href="<%$cms_url%>" target="_tcblank">Besuchen Sie uns auf 
        <% $gbl_config.adr_firma %></a>
    </div>
</div>


  </body>
</html>
';
$this->execSQL("INSERT INTO " . TBL_CMS_FBWPCONTENT . " SET id=1,fb_content='" . $this->db->real_escape_string($c) . "'");
#$this->execSQL("INSERT INTO " . TBL_CMS_WLUFBWPCONTENT . " SET id=1,fb_content='" . $this->db->real_escape_string($c) . "'");

$tpl_rep = array('{TMPL_PHPMSG}' => '<% $PHPMSG %>', );
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);
$this->replaceInTemplates($tpl_rep);

global $gbl_config;
$this->execSQL("UPDATE " . TBL_GBLCONFIG . " SET config_value='" . $this->db->real_escape_string($gbl_config['adr_firma']) .
    "' WHERE config_value='' AND config_name='adr_general_firmname'");
$this->execSQL("UPDATE " . TBL_CMS_MAILTEMP . " SET t_email='" . $gbl_config['adr_service_email'] . "'");

$this->execSQL("UPDATE " . TBL_GBLCONFIG . " SET config_value='" . $this->db->real_escape_string($gbl_config['adr_service_email']) .
    "' WHERE config_value='' AND config_name='email_registration'");

/*
if (mod_installed('mod_wilinku')) {
$this->db->query("DELETE FROM " . TBL_CMS_WLU_COUNTRY_TO_CAT . " WHERE cm_countryid=211 OR cm_countryid=334");
$result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . " ORDER BY kid");
while ($row = $this->db->fetch_array_names($result)) {
$LL = $this->db->query_first("SELECT * FROM " . TBL_CMS_LAND . " WHERE id=" . $row['land']);
$u = strtoupper($LL['country_code_2']) . '-' . $row['kid'];
$this->execSQL("UPDATE " . TBL_CMS_CUST . " SET wlu_referrerid='WLU" . $row['kid'] . "',wlu_userid='" . $u . "' WHERE kid=" . $row['kid']);
}

$C = new country_class();
$result = $this->db->query("SELECT * FROM " . TBL_CMS_LAND . " WHERE region_id=6 ORDER BY id");
while ($row = $this->db->fetch_array_names($result)) {
$L = $this->db->query_first("SELECT * FROM " . TBL_CMS_LAND . " WHERE id<>" . $row['id'] . " AND  land='" . $this->db->real_escape_string($row['land']) . "'");
if ($L['id'] > 0) {
$this->db->query("UPDATE " . TBL_CMS_WLU_COUNTRY_TO_CAT . " SET cm_countryid=" . $L['id'] . " WHERE cm_countryid=" . $row['id']);
$this->db->query("UPDATE " . TBL_CMS_WLU_VIDEO_TO_COUNTRY . " SET vc_countryid=" . $L['id'] . " WHERE vc_countryid=" . $row['id']);
}

$C->delete_country($row['id']);
}
*/
unset($C);
/*
DEFINE('TBL_CMS_WLU_QUERY_TO_COUNTRY', TBL_CMS_PREFIX . 'wlu_query_to_country');
DEFINE('TBL_CMS_WLU_CAT_TRANSLATION', TBL_CMS_PREFIX . 'wlu_cat_lang');
$result = $this->db->query("SELECT * FROM " . TBL_CMS_WLU_VPQUERY . " WHERE vp_countryid>=0");
while ($row = $this->db->fetch_array_names($result)) {
    $this->db->query("DELETE FROM " . TBL_CMS_WLU_QUERY_TO_COUNTRY . " WHERE qc_qid=" . $row['id']);
    $this->db->query("INSERT INTO " . TBL_CMS_WLU_QUERY_TO_COUNTRY . " SET qc_qid=" . $row['id'] . ",qc_countryid=" . $row['vp_countryid']);
    $this->db->query("UPDATE " . TBL_CMS_WLU_VPQUERY . " SET vp_countryid=-1 WHERE id=" . $row['id']);
}

$resultl = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE approval=1 ORDER BY s_order");
while ($rowl = $this->db->fetch_array_names($resultl)) {
    $result = $this->db->query("SELECT * FROM " . TBL_CMS_WLU_CATS . " WHERE 1");
    while ($row = $this->db->fetch_array_names($result)) {
        $U = array(
            'cl_label' => $this->db->real_escape_string($row['ytc_name']),
            'cl_catid' => $row['id'],
            'cl_langid' => $rowl['id']);
        replaceTable(TBL_CMS_WLU_CAT_TRANSLATION, $U);
    }
}
*/

$this->execSQL("DELETE FROM " . TBL_CMS_LAND . " WHERE region_id=0");

$tpl_rep = array('capcha_active' => 'captcha_active', );
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);
$this->replaceInTemplates($tpl_rep);

@unlink(CMS_ROOT . 'fonts/XFILES.TTF');

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to ' . $version);

?>