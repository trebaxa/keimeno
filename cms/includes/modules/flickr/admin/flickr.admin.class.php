<?php

/**
 * @package    flickr
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


class flickr_admin_class extends flickr_master_class
{

    protected $FLICKR = array();

    /**
     * flickr_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GRAPHIC_FUNC = new graphic_class();
        $this->load_local_fotos();
    }

    /**
     * flickr_admin_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item()
    {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        # fillin delete function
        $this->hard_exit();
    }

    /**
     * flickr_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item()
    {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        #$this->db->query("UPDATE " . TBL_CMS_TABLE . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * flickr_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty()
    {
        $this->smarty->assign('FLICKR', $this->FLICKR);
    }

    /**
     * flickr_admin_class::cmd_save_config()
     * 
     * @return
     */
    function cmd_save_config()
    {
        $CONFIG_OBJ = new config_class();
        $CONFIG_OBJ->save($_POST['FORM']);
        $this->hard_exit();
    }

    /**
     * flickr_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf()
    {
        $CONFIG_OBJ = new config_class();
        $this->FLICKR['CONFIG'] = $CONFIG_OBJ->buildTable(53, 53);
    }

    /**
     * flickr_admin_class::cmd_get_token()
     * 
     * @return
     */
    function cmd_get_token()
    {
        $api = new Phlickr_Api($this->gblconfig->fli_api_key, $this->gblconfig->
            fli_api_secret);
        $frob = $api->requestFrob();
        $url = $api->buildAuthUrl('delete', $frob); // delete = read and write and delete
        $_SESSION['flickr']['frob'] = $frob;
        header('location:' . $url);
        $this->hard_exit();
    }

    /**
     * flickr_admin_class::cmd_save_token()
     * 
     * @return
     */
    function cmd_save_token()
    {
        $api = new Phlickr_Api($this->gblconfig->fli_api_key, $this->gblconfig->
            fli_api_secret);
        $token = $api->setAuthTokenFromFrob($_SESSION['flickr']['frob']);
        $this->db->query("UPDATE " . TBL_CMS_GBLCONFIG . " SET config_value='" . $token .
            "' WHERE config_name='fli_token'");
        unset($_SESSION['flickr']['frob']);
        $this->msg('Token saved');
        $this->echo_json_fb();
    }


    /**
     * flickr_admin_class::syncstream()
     * 
     * @return
     */
    function syncstream()
    {
        $this->db->query("DELETE FROM " . TBL_CMS_FLICKRSTREAM);
        $res = $this->get_own_fotos();
        #        echoarr($res);
        foreach ($this->FLICKR['ownfotos'] as $key => $foto) {
            $this->grab_foto_to_db($foto);
            echo $foto['title'] . '<br>';
            $k++;
        }
        return (int)$k;
    }

    /**
     * flickr_admin_class::cmd_sync_stream()
     * 
     * @return
     */
    function cmd_sync_stream()
    {
        echo 'Bitte warten...<br>';
        $count = $this->syncstream();
        echo '<div class="bg-success">' . $count . ' Fotos importiert.</div>';
        $this->hard_exit();
    }

    /**
     * flickr_admin_class::load_local_fotos()
     * 
     * @return
     */
    function load_local_fotos()
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FLICKRSTREAM .
            " WHERE 1 ORDER BY p_time DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row[thumb] = kf::gen_thumbnail('/file_data/flickr/' . $row['p_localefile'], 300,
                160, 'crop');
            $row['date'] = date('d.m.Y', $row['p_time']);
            $this->FLICKR['fotostram'][] = $row;
        }
    }

    /**
     * flickr_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params)
    {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES .
            " WHERE modident='flickr' AND gbl_template=1 ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * flickr_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params)
    {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" .
            (int)$id);
        $upt = array('tm_content' => '{TMPL_FLICKR_FOTOSTREAM_' . $cont_matrix_id . '}',
                'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

}

?>