<?php

/**
 * @package    resource
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class resource_content_class extends resource_admin_class {

    /**
     * resource_content_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * resource_content_class::cmd_show_add_datasets()
     * 
     * @return void
     */
    function cmd_show_add_datasets() {
        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table'], $_GET['langid']);
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.dataset.langform');
    }

    /**
     * resource_content_class::cmd_show_addds()
     * 
     * @return void
     */
    function cmd_show_addds() {
        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table'], $_GET['langid']);
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.addds');
    }


    /**
     * resource_content_class::cmd_show_edit_dataset()
     * 
     * @return void
     */
    function cmd_show_edit_dataset() {
        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table'], $_GET['langid']);
        $this->RESOURCE['seldataset'] = $this->RESOURCE['flextpl']['dataset'][$_GET['rowid']];
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.addds');
    }

    /**
     * resource_content_class::cmd_load_resource()
     * 
     * @return void
     */
    function cmd_load_resource() {
        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table']);
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent');
    }

    /**
     * resource_content_class::cmd_add_ds_to_db()
     * 
     * @return void
     */
    function cmd_add_ds_to_db() {
        $FORM = self::trim_array($_POST['FORM']);
        $rowid = (int)$_POST['rowid'];
        $flxid = (int)$_POST['flxid'];
        $table = (string )$_POST['table'];
        $FORM['ds_cid'] = $content_matrix_id = (int)$_POST['content_matrix_id'];

        $arr = $this->load_dataset_vars_table($flxid, $table);
        $langid = ((int)$_POST['langid'] <= 0) ? 1 : (int)$_POST['langid'];

        foreach ($FORM as $key => $value) {
            $FORM[$key] = self::html_editor_transform_content($value);

            if ($arr[$key]['v_type'] == 'rdate') {
                $FORM[$key] = self::date_to_sqldate($FORM[$key]);
            }
        }

        if ($rowid > 0) {
            # $this->load_resrc_for_edit($_GET['flxid']);
            # $seldataset = $this->RESOURCE['flextpl']['dataset'][$rowid];
        }
        if (!is_dir(CMS_ROOT . 'file_data/resource/'))
            mkdir(CMS_ROOT . 'file_data/resource/', 0775);

        if (!is_dir($this->froot))
            mkdir($this->froot, 0775);

        if (!is_dir($this->file_root))
            mkdir($this->file_root, 0775);

        if (isset($_FILES['datei']) && is_array($_FILES['datei']['name'])) {
            foreach ($_FILES['datei']['name'] as $column => $fname) {
                if ($fname != "" && (self::is_image($_FILES['datei']['tmp_name'][$column]) || self::is_image($fname))) {
                    # remove existing one
                    if ($rowid > 0) {
                        $this->deldatasetimg($flxid, $rowid, $column, $table, $langid);
                    }
                    $gen_img_name = self::gen_seo_name($FORM, $fname);
                    $fname = $this->unique_filename($this->froot, $gen_img_name);
                    $target = $this->froot . $fname;
                    if (!move_uploaded_file($_FILES['datei']['tmp_name'][$column], $target)) {
                        $this->msge('Image file error: ' . self::file_upload_err_to_txt($_FILES["datei"]["error"][$column]));
                        unset($FORM[$column]);
                    }
                    else {
                        chmod($target, 0755);
                        if (self::get_ext($fname) != 'svg') {
                            graphic_class::resize_picture_imageick('../file_data/resource/images/' . $fname, '../file_data/resource/images/' . $fname, 2000, 2000);
                        }
                        $FORM[$column] = $fname;
                    }
                }
                else {
                    unset($FORM[$column]);
                }
            }
        }

        if (isset($_FILES['fdatei']) && is_array($_FILES['fdatei']['name'])) {
            foreach ($_FILES['fdatei']['name'] as $column => $fname) {
                if ($fname != "") {
                    # remove existing one
                    if ($rowid > 0) {
                        $this->deldatasetfile($flxid, $rowid, $column, $table, $langid);
                    }

                    $fname = $this->unique_filename($this->file_root, $fname);
                    $target = $this->file_root . $fname;
                    if (!move_uploaded_file($_FILES['fdatei']['tmp_name'][$column], $target)) {
                        $this->msge('File error: ' . self::file_upload_err_to_txt($_FILES["fdatei"]["error"][$column]));
                        unset($FORM[$column]);
                    }
                    else {
                        chmod($target, 0755);
                        $FORM[$column] = $fname;
                    }
                }
                else {
                    unset($FORM[$column]);
                }
            }
        }


        $this->load_resrc_for_edit($flxid, 0, $table, $langid);
        $FORM['ds_settings'] = serialize((array )$FORM['ds_settings']);

        if ($rowid == 0) {
            $LAST = $this->db->query_first("SELECT * FROM " . TBL_CMS_PREFIX . $table . " WHERE 
                ds_group=" . (int)$FORM['ds_group'] . " 
                AND ds_cid=" . $FORM['ds_cid'] . " 
                AND ds_langid=" . $langid . "
                ORDER BY ds_order DESC LIMIT 1");
            $FORM['ds_order'] = $LAST['ds_order'] + 10;
            $FORM['id'] = self::microtime_float() + rand(1, 10000000);
            $FORM['ds_langid'] = $langid;
            $rowid = insert_table(TBL_CMS_PREFIX . $table, $FORM);
        }
        else {
            #update_table($this->RESOURCE['flextpl']['f_table'], 'id', $rowid, $FORM);
            dao_class::update_table(TBL_CMS_PREFIX . $table, $FORM, array('id' => $rowid, 'ds_langid' => $langid));
        }


        $this->ej('reload_dataset', $content_matrix_id . ',' . $langid . ",'" . $table . "'");
    }


    /**
     * resource_content_class::cmd_import_datasets_by_lang()
     * 
     * @return void
     */
    function cmd_import_datasets_by_lang() {
        $table = TBL_CMS_PREFIX . $_GET['table'];
        $result = $this->db->query("SELECT * FROM " . $table . " F WHERE 
            ds_langid=" . (int)$_GET['importlang'] . "
            AND ds_cid=" . $_GET['content_matrix_id'] . "
            ");
        while ($row = $this->db->fetch_array_names($result)) {
            unset($row['id']);
            unset($row['ds_langid']);
            foreach ($row as $key => $value) {
                if (substr($key, -4) == '_img') {
                    $gen_img_name = self::gen_seo_name($row, $value);
                    $fname = $this->unique_filename($this->froot, $gen_img_name);
                    $target = $this->froot . $fname;
                    copy($this->froot . $value, $target);
                    $row[$key] = $fname;
                }
            }
            $row['id'] = self::microtime_float() + rand(1, 10000000);
            $row['ds_langid'] = $_GET['langid'];
            insert_table($table, self::real_escape($row));
        }

        $this->load_resrc_for_edit($_GET['flxid'], $_GET['content_matrix_id'], $_GET['table'], $_GET['langid']);
        $this->parse_to_smarty();
        kf::echo_template('resource.addcontent.dataset.form');
    }
}
