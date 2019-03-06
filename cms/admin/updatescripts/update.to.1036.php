<?PHP

$version = '1.0.3.6';

$tpl_rep = array('<% include file="dcmetatags.tpl"%>' => '', );
$this->replaceInTemplates($tpl_rep);


function change_1036($dir, $ext, $rep) {
    global $found;
    if (!is_dir($dir))
        return;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && $file != 'cms.php' && strstr($file, "." . $ext)) {
                $c = file_get_contents($dir . $file);
                $found = false;

                foreach ($rep as $from => $to) {
                    if (strstr($c, $from)) {
                        $c = str_ireplace($from, $to, $c);
                        $found = true;
                    }
                }
                if ($found) {
                    file_put_contents($dir . $file, $c);
                    # echo $dir . $file . '<br>';
                }
            }
        }
    }
}

$dirs = array(CMS_ROOT . 'includes/modules/');
if ($handle = opendir(CMS_ROOT . 'includes/modules/')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && is_dir(CMS_ROOT . 'includes/modules/' . $file)) {
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/';
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/admin/';
            $dirs[] = CMS_ROOT . 'includes/modules/' . $file . '/admin/tpl/';
        }
    }
}

$rep = array(
    'genApproveImgTagAJAX' => 'gen_approve_icon',
    'getCount' => 'get_data_count',
    'updateTable' => 'update_table',
    'buildLandSelect' => 'build_land_selectbox',
    'buildTopMenu' => '$ADMINOBJ->set_top_menu',
    'myDate' => 'my_date',
    'genDelImgTagADMINConfirmAJAX' => 'gen_del_icon',
    'mod_installed' => 'is_module_installed');
foreach ($dirs as $dir) {
    change_1036($dir, 'php', $rep);
}

$rep = array('class="trheader"' => '', 'class="tdright"' => 'class="text-right"');
foreach ($dirs as $dir) {
    change_1036($dir, 'tpl', $rep);
}

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET admin=0,gbl_template=1 WHERE id=860 LIMIT 1");

$this->execSQL("ALTER TABLE " . TBL_CMS_PAGENF . " DROP PRIMARY KEY");
$this->execSQL("ALTER TABLE " . TBL_CMS_PAGENF . " ADD PRIMARY KEY ( `pnf_uri` ) ");

$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='gallery' WHERE config_name LIKE 'gal_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='tcblog' WHERE config_name LIKE 'blog_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='tagcloud' WHERE config_name LIKE 'tagcloud_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='tagcloud' WHERE config_name LIKE 'tag_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='indexsearch' WHERE config_name LIKE 'si_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='webcam' WHERE config_name LIKE 'webcam_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='calendar' WHERE config_name LIKE 'event_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='calendar' WHERE config_name LIKE 'events_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='calendar' WHERE config_name LIKE 'cal_%'");
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='sellform' WHERE config_name LIKE 'sf_%'");


$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET layout_group=1 WHERE id=100018 LIMIT 1");

if (class_exists('tplvars_admin_class')) {
    $TPLVARS = new tplvars_admin_class();
    $TPLVARS->resave_all_used_content_pages();
}
$W = new websites_class();
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE 1");
while ($row = $this->db->fetch_array_names($result)) {
    $W->breadcrumb_update($row['id']);
}

$value = str_replace('RewriteRule .* /------------http----------- [F,NC]', '', $this->gbl_config['hta_specialtext']);
$value = str_replace('RewriteRule http: /---------http----------- [F,NC]', 'RewriteRule http: /index.php?cmd=loghack [L,R=301]', $value);
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $this->db->real_escape_string($value) . "' WHERE config_name='hta_specialtext'");

$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/b8/b8/doc');
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/b8/b8/example');
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/b8/b8/install');
$this->delete_dir_with_subdirs(CMS_ROOT . 'includes/modules/b8/b8/update');

@unlink(CMS_ROOT . 'admin/tpl/jsapi.tpl');
$this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET modident='gallery',gid=0 WHERE config_name='cat_thumb_width' OR config_name='cat_thumb_height'");
$this->db->query("DELETE FROM " . TBL_CMS_GBLCONFIG . " WHERE config_name='gal_user_mooflow'");


$WEB = new websites_class();
$WEB->create_default_dirs();
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE theme_image!=''");
while ($row = $this->db->fetch_array_names($result)) {
    if (file_exists(CMS_ROOT . 'file_server/template/' . $row['theme_image'])) {
        if (copy(CMS_ROOT . 'file_server/template/' . $row['theme_image'], CMS_ROOT . 'file_data/themeimg/' . $row['theme_image'])) {
            @unlink(CMS_ROOT . 'file_server/template/' . $row['theme_image']);
        }
    }
}
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TPLCON . " WHERE theme_image!=''");
while ($row = $this->db->fetch_array_names($result)) {
    if (file_exists(CMS_ROOT . 'file_server/template/' . $row['theme_image']) && is_file(CMS_ROOT . 'file_server/template/' . $row['theme_image'])) {
        if (copy(CMS_ROOT . 'file_server/template/' . $row['theme_image'], CMS_ROOT . 'file_data/themeimg/' . $row['theme_image'])) {
            @unlink(CMS_ROOT . 'file_server/template/' . $row['theme_image']);
        }
    }
    if (file_exists(CMS_ROOT . 'file_server/template/' . $row['tpl_icon']) && is_file(CMS_ROOT . 'file_server/template/' . $row['tpl_icon'])) {
        if (copy(CMS_ROOT . 'file_server/template/' . $row['tpl_icon'], CMS_ROOT . 'file_data/themeimg/' . $row['tpl_icon'])) {
            @unlink(CMS_ROOT . 'file_server/template/' . $row['tpl_icon']);
        }
    }
}

#clean up toplevel
$result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE 1");
while ($row = $this->db->fetch_array_names($result)) {
    $tarr[] = $row['id'];
}
$this->db->query("DELETE FROM " . TBL_CMS_TPLCON . " WHERE tid NOT IN (" . implode(',', $tarr) . ")");
$this->db->query("UPDATE " . TBL_CMS_PAGENF . " SET pnf_hash=MD5(pnf_uri) WHERE 1");

$this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET approval=0, xml_sitemap=0 WHERE id=680 OR id=670 OR gbl_template=1");


#UPDATE `s9y_comments` SET `ip` = CONCAT(SUBSTRING_INDEX(`ip`, '.', 2), '.0.0');

#if (TBL_CMS_PIN_CONTENT != 'TBL_CMS_PIN_CONTENT')
#$this->db->query("UPDATE " . TBL_CMS_PIN_CONTENT . " SET b_image=image WHERE b_image=''");

$this->db->query("UPDATE " . TBL_CMS_CONFIG . " SET wert='" . $version . "' WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE', 'CMS version has been updated to' . $version);
$NO_FAILURE = true;

?>