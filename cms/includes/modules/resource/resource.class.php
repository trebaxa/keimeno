<?php

/**
 * @package    resource
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

class resource_class extends resource_master_class {

    var $RESOURCE = array();

    /**
     * resource_class::__construct()
     * 
     * @return
     */
    function __construct() {
        global $GBL_LANGID, $user_object;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GBL_LANGID = (intval($GBL_LANGID) > 0) ? intval($GBL_LANGID) : $this->gbl_config['std_lang_id'];
        $this->user_object = $user_object;
    }

    /**
     * resource_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('RESOURCE') != null) {
            $this->RESOURCE = array_merge($this->smarty->getTemplateVars('RESOURCE'), $this->RESOURCE);
            $this->smarty->clearAssign('RESOURCE');
        }
        $this->smarty->assign('RESOURCE', $this->RESOURCE);
    }


    /**
     * resource_class::set_template()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function set_template($PLUGIN_OPT) {
        $TPL = $this->load_html_tpl($PLUGIN_OPT['flxtpl']);
        return $TPL['t_tpl'];
    }

    /**
     * resource_class::get_dataset()
     * 
     * @param mixed $PLUGIN_OPT
     * @param mixed $flxtpl
     * @param mixed $cont_matrix_id
     * @return
     */
    function get_dataset($resrc, $cont_matrix_id) {
        $arr = array();
        $dataset = $this->load_dataset($resrc['f_table'], $cont_matrix_id);
        foreach ($dataset as $kkey => $row) {
            foreach ($row as $column => $value) {
                $thumb = $vident = "";
                $exists = true;
                if (substr($column, -4) == '_img') {
                    $col_opt = $resrc['coldef'][$column]['v_opt']['img'];
                    $img = ($value != "") ? './file_data/resource/images/' . $value : './images/opt_no_pic.jpg';
                    $gravity = $col_opt['foto_gravity'];
                    if (isset($row['ds_settings']['foto']) && $row['ds_settings']['foto']['foto_gravity'] != 'default') {
                        $gravity = $row['ds_settings']['foto']['foto_gravity'];
                    }
                    if ($col_opt['foto_resize'] != 'none' && self::get_ext($img) != 'svg') {
                        $thumb = thumbit_fe($img, $col_opt['foto_width'], $col_opt['foto_height'], $col_opt['foto_resize'], $gravity);
                    }
                    else {
                        $thumb = str_replace('./', PATH_CMS, $img);
                    }

                    $exists = (is_file(CMS_ROOT . 'file_data/resource/images/' . $value) && file_exists(CMS_ROOT . 'file_data/resource/images/' . $value));
                    $value = ($value != "") ? PATH_CMS . 'file_data/resource/images/' . $value : PATH_CMS . 'images/opt_no_pic.jpg';
                }

                if (substr($column, -5) == '_file') {
                    $col_opt = $resrc['coldef'][$column]['v_opt']['file'];
                    $exists = (is_file(CMS_ROOT . 'file_data/resource/files/' . $value) && file_exists(CMS_ROOT . 'file_data/resource/files/' . $value));
                    $value = ($exists == true) ? PATH_CMS . 'file_data/resource/files/' . $value : '';
                }

                if (substr($column, -5) == '_seli') {
                    list($vident, $value) = explode('|', $value);
                }

                if (!strstr($column, 'ds_') && isset($resrc['coldef'][$column]) && is_array($resrc['coldef'][$column])) {
                    $ds = array(
                        'column' => $column,
                        'value' => $value,
                        'hash' => md5($value . $vident . $column),
                        'vident' => $vident,
                        'thumb' => $thumb,
                        'exists' => $exists,
                        );
                    foreach ($resrc['coldef'][$column] as $ds_key => $ds_value) {
                        if (strstr($ds_key, 'v_'))
                            $ds[$ds_key] = $ds_value;
                    }
                    $arr[$kkey][$resrc['coldef'][$column]['v_varname']] = $ds;
                    # $arr[$kkey][$ds['column']] = $ds;
                }

            }
        }

        return $arr;
    }

    /**
     * resource_class::parse_flxt()
     * 
     * @param mixed $params
     * @return
     */
    function load_resrc_for_compile($resrc_id, $v_settings = array()) {
        $resrc = $this->load_resrc($resrc_id);
        $v_settings = (array )$v_settings;
        $this->RESOURCE['content_table'] = $this->load_content_table_fe($resrc_id, $v_settings);
        $resrc['tpl'] = $this->load_flexvars_table($resrc_id);
        $resrc['coldef'] = $this->load_dataset_vars_table($resrc_id);
        #echoarr($this->RESOURCE['content_table'] );
        foreach ($this->RESOURCE['content_table'] as $key => $resvar) {
            $cont_matrix_id = $resvar['id'];
            #$resrc = $this->load_flex_tpl($resrc_id);
            $this->RESOURCE['content_table'][$key]['dataset'] = $this->get_dataset($resrc, $cont_matrix_id);
            # echoarr($resrc['dataset']);

            $flexvarsdata = $this->load_flexvars_for_plugin($cont_matrix_id);
            $this->set_resrc_vars($resrc, $flexvarsdata, $key);
        }
        # echoarr($this->RESOURCE['content_table']);
        # die;
        return $this->RESOURCE['content_table'];
    }

    /**
     * resource_class::set_resrc_vars()
     * 
     * @param mixed $resrc
     * @return void
     */
    function set_resrc_vars($resrc, $flexvarsdata, $key = 0) {
        foreach ($resrc['tpl'] as $ident => $row) {
            $var_value = "";
            $detail_link = dao_class::get_data_first(TBL_CMS_PAGEINDEX, array(
                'pi_local' => 'de',
                'pi_modident' => 'resource',
                'pi_relatedid' => $flexvarsdata[$row['id']]['v_cid']));
            if ($row['v_type'] == 'rdate') {
                $var_value = my_date('d.m.Y', $flexvarsdata[$row['id']]['v_value']);
            }
            elseif ($row['v_type'] == 'img') {
                $row['options'] = $this->get_img_options($resrc['tpl'], $row['v_varname']);
                $exists = (is_file(CMS_ROOT . 'file_data/resource/images/' . $flexvarsdata[$row['id']]['v_value']) && file_exists(CMS_ROOT . 'file_data/resource/images/' . $flexvarsdata[$row['id']]['v_value']));
                if ($exists == true) {
                    $var_value = PATH_CMS . 'file_data/resource/images/' . $flexvarsdata[$row['id']]['v_value'];
                }

                # crop if needed
                if (isset($row['options']['img']) && $resrc['var']['options']['img']['foto_resize'] != 'none') {
                    $img_opt = $row['options']['img'];
                    $gravity = $img_opt['foto_gravity'];
                    if (isset($flexvarsdata[$row['id']]['v_settings']['foto']) && $flexvarsdata[$row['id']]['v_settings']['foto']['foto_gravity'] != 'default') {
                        $gravity = $flexvarsdata[$row['id']]['v_settings']['foto']['foto_gravity'];
                    }
                    if (self::get_ext($flexvarsdata[$row['id']]['v_value']) != 'svg') {
                        $img_opt['foto_width'] = ($img_opt['foto_width'] > 0) ? $img_opt['foto_width'] : 100;
                        $img_opt['foto_height'] = ($img_opt['foto_height'] > 0) ? $img_opt['foto_height'] : 100;
                        if ($exists == true) {
                            $var_value = thumbit_fe('./file_data/resource/images/' . $flexvarsdata[$row['id']]['v_value'], $img_opt['foto_width'], $img_opt['foto_height'], $img_opt['foto_resize'],
                                $gravity);
                        }
                    }
                }
            }
            elseif ($row['v_type'] == 'file') {
                $var_value = (is_file(CMS_ROOT . 'file_data/resource/files/' . $flexvarsdata[$row['id']]['v_value'])) ? PATH_CMS . 'file_data/resource/files/' . $flexvarsdata[$row['id']]['v_value'] :
                    '';
            }
            else {
                $var_value = $flexvarsdata[$row['id']]['v_value'];
            }

            $this->RESOURCE['content_table'][$key][$row['v_varname']] = $var_value;
            $this->RESOURCE['content_table'][$key]['resrc_link'] = $detail_link['pi_link'];
        }

        return $this->RESOURCE['content_table'];
    }

    /**
     * resource_class::load_detail_page()
     * 
     * @return void
     */
    function load_detail_page($id) {
        $content_id = (int)$id;
        $RES_CONTENT = dao_class::get_data_first(TBL_RESRC_CONTENT, array('id' => $content_id));
        $TPL = dao_class::get_data_first(TBL_RESRCPL, array('t_ftid' => $RES_CONTENT['c_ftid'], 't_use' => '1'));
        $resrc = $this->load_resrc($RES_CONTENT['c_ftid']);
        $resrc['tpl'] = $this->load_flexvars_table($RES_CONTENT['c_ftid']);
        foreach ($resrc['tpl'] as $key => $row) {
            $resrc['vardefs'][$row['v_varname']] = $row['v_type'];
        }
        $resrc['coldef'] = $this->load_dataset_vars_table($RES_CONTENT['c_ftid']);
        $arr = $this->get_dataset($resrc, $content_id);
        $resrc['dataset'] = $arr;
        $flexvarsdata = $this->load_flexvars_for_plugin($content_id);
        $arr = $this->set_resrc_vars($resrc, $flexvarsdata);
        $resrc['var'] = $arr[0];
        $this->smarty->assign('resrc', $resrc);
        $this->parse_to_smarty();
        #   echoarr($resrc);
        return array(
            'html' => $TPL['t_tpl'],
            'content' => $RES_CONTENT,
            'resrc' => $resrc);
    }

    /**
     * resource_class::parse_detail_page()
     * 
     * @return
     */
    function parse_detail_page($params) {
        $html = $params['html'];
        $langid = $params['langid'];
        $title = $description = $keywords = "";
        if (strstr($html, '{TMPL_RESRCDETAIL_')) {
            preg_match_all("={TMPL_RESRCDETAIL_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $wert) {
                $rep = array("{TMPL_RESRCDETAIL_", "}");
                $cont_matrix_id = intval(strtolower(str_replace($rep, "", $wert)));
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);

                # load detail page
                $resrc_html = "";
                if (isset($_GET['id'])) {
                    $arr = $this->load_detail_page((int)$_GET['id']);
                    $resrc_html = $arr['html'];
                    $title = htmlspecialchars(ucfirst(trim(strip_tags($arr['content']['c_label']))));

                    $str = "";
                    #  echoarr($arr['resrc']);die;
                    foreach ($arr['resrc']['var'] as $ikey => $value) {
                        if ($arr['resrc']['vardefs'][$ikey] == 'edt' || $arr['resrc']['vardefs'][$ikey] == 'hedt') {
                            $str .= $value . ' ';
                        }
                    }
                    $keywords = self::gen_meta_keywords($str);
                    $description = self::gen_meta_description($str);
                }

                $html = str_replace($tpl_tag[0][$key], $resrc_html, $html);
            }
        }
        $params['html'] = $html;

        # set metas
        $this->CORE->set_metas($title, $description, $keywords);
        return $params;
    }

}
