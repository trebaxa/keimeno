<?php

/**
 * @package    flickr
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

include_once CMS_ROOT . 'includes/modules/flickr/Phlickr/Api.php';

DEFINE('TBL_CMS_FLICKRSTREAM', TBL_CMS_PREFIX . 'flickr_stream');

class flickr_master_class extends modules_class {

    /**
     * flickr_master_class::build_img_url()
     * 
     * @param mixed $foto
     * @return
     */
    function build_img_url($foto) {
        return 'http://farm' . $foto->attributes()->farm . '.staticflickr.com/' . $foto->attributes()->server . '/' . $foto->attributes()->id . '_' . $foto->attributes()->
            secret . '_b.jpg';
    }

    /**
     * flickr_master_class::flickr_upload()
     * 
     * @param mixed $foto
     * @return
     */
    function flickr_upload($foto) {
        require_once CMS_ROOT . 'includes/modules/flickr/Phlickr/Uploader.php';
        $api = new Phlickr_Api($this->gblconfig->fli_api_key, $this->gblconfig->fli_api_secret, $this->gblconfig->fli_token);
        $uploader = new Phlickr_Uploader($api);
        $foto['tags'] = explode(',', $foto['tags']);
        $id = $uploader->upload($foto['file'], $foto['title'], $foto['description'], $foto['tags']);
    }

    /**
     * flickr_master_class::get_own_user()
     * 
     * @return
     */
    function get_own_user() {
        $api = new Phlickr_Api($this->gblconfig->fli_api_key, $this->gblconfig->fli_api_secret, $this->gblconfig->fli_token);
        $resp = $api->executeMethod('flickr.people.findByUsername', array('username' => $this->gblconfig->fli_youruser));
        #$nsid = (string )$resp->getXml()->user->attributes()->nsid;
        return $resp->getXml();
    }

    /**
     * flickr_master_class::get_own_fotos()
     * 
     * @param integer $page
     * @return
     */
    function get_own_fotos($page = 1) {
        $page = (int)$page;
        $page = ($page == 0) ? 1 : $page;
        $this->FLICKR['ownfotos'] = array();
        $api = new Phlickr_Api($this->gblconfig->fli_api_key, $this->gblconfig->fli_api_secret, $this->gblconfig->fli_token);
        if ($api->isAuthValid()) {
            $resp = $api->executeMethod('flickr.people.findByUsername', array('username' => $this->gblconfig->fli_youruser));
            $nsid = (string )$resp->getXml()->user->attributes()->nsid;
            $resp = $api->executeMethod('flickr.photos.search', array(
                'page' => $page,
                'user_id' => $nsid,
                'extras' => 'description,date_upload'));
            #  echoarr($resp->getXml()->photos);
            foreach ($resp->getXml()->photos->photo as $foto) {
                $this->FLICKR['ownfotos'][] = array(
                    'url' => $this->build_img_url($foto),
                    'title' => $foto->attributes()->title,
                    'date_upload' => $foto->attributes()->dateupload,
                    'description' => $foto->description);
            }
            return $resp->getXml();
        }
        else {
            return $api;
        }
    }

    /**
     * flickr_master_class::grab_foto_to_db()
     * 
     * @param mixed $foto
     * @return
     */
    function grab_foto_to_db($foto) {
        $path = CMS_ROOT . 'file_data/flickr/';
        if (!is_dir($path))
            mkdir($path, 0775);
        $this->db->query("DELETE FROM " . TBL_CMS_FLICKRSTREAM . " WHERE p_localefile='" . basename($foto['url']) . "'");
        $this->curl_get_data_to_file($foto['url'], $path . basename($foto['url']));
        $arr = array(
            'p_url' => $foto['url'],
            'p_comment' => $foto['description'],
            'p_localefile' => basename($foto['url']),
            'p_time' => $foto['date_upload'],
            'p_title' => $foto['title']);
        insert_table(TBL_CMS_FLICKRSTREAM, $arr);
    }

}
