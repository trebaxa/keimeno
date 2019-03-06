<?PHP

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


if (IN_SIDE != 1) {
    header('location:/index.html');
    exit;
}


class fbwp_class extends fbwp_master_class {

    var $FBWP = array();
    var $facebook = null;

    /**
     * fbwp_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $id = ($_REQUEST['id'] > 0) ? (int)$_REQUEST['id'] : 1;
        $this->FBWP['WP'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " WHERE id=" . $id);

        if ($this->FBWP['WP']['fb_appid'] != "" && $this->FBWP['WP']['fb_secret'] != "") {
            $config = array(
                'app_id' => trim($this->FBWP['WP']['fb_appid']),
                'app_secret' => trim($this->FBWP['WP']['fb_secret']),
                'cookie' => true,
                'default_graph_version' => DEFAULT_GRAPH_VERSION);
            $this->facebook = new Facebook\Facebook($config);
        }
        else {
            $this->facebook = null;
        }

    }

    /**
     * fbwp_class::parse_to_smarty()
     * 
     * @return void
     */
    function parse_to_smarty() {
        if ($this->smarty->getTemplateVars('FBWP') != NULL) {
            $this->FBWP = array_merge($this->smarty->getTemplateVars('FBWP'), $this->FBWP);
            $this->smarty->clearAssign('FBWP');
        }
        $this->smarty->assign('FBWP', $this->FBWP);
    }


    /**
     * fbwp_class::autorun()
     * 
     * @return void
     */
    function autorun() {
        if ($this->facebook != NULL) {
            $this->load_status_fanpage();
            #  $this->FBWP['loginurl'] = $this->facebook->getLoginUrl(array(
            #      'scope' => 'email,user_address,publish_stream,user_location', #email,user_birthday,status_update,user_photos,user_videos,publish_stream
            #      'redirect_uri' => SSLSERVER . 'index.php?page=950&func=fblogin',
            #      ));
            $this->parse_to_smarty();
        }
    }


    /**
     * fbwp_class::login()
     * 
     * @return void
     */
    function login() {
        if ($_GET['func'] == 'failed' || $_GET['error_code'] > 0) {
            $this->msge('Facebook Login fehlgeschlagen. ' . $_GET['error_code'] . ' ' . $_GET['error_description']);
            HEADER('location:' . SSL_PATH_SYSTEM . $_SERVER['PHP_SELF'] . '?page=950');
            $this->hard_exit();
        }

        if ($_GET['func'] == 'fblogin') {
            if ($_GET['code'] != "") {
                $userid = $this->facebook->getUser();
                if ($userid) {
                    try {
                        $user_profile = $this->facebook->api('/me');
                        $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE email='" . $user_profile['email'] . "'");
                        if ($k_obj['kid'] == 0) {
                            $passwort = gen_sid(5);
                            $LAND = $this->db->query_first("SELECT * FROM " . TBL_CMS_LAND . " WHERE country_code_2='" . strtoupper(substr($user_profile['locale'], 0, 2)) . "'");
                            $geschlecht = ($user_profile['gender'] == 'male') ? 'm' : 'w';
                            $arr = array(
                                'nachname' => $user_profile['last_name'],
                                'vorname' => $user_profile['first_name'],
                                'email' => $user_profile['email'],
                                'emailwdh' => $user_profile['email'],
                                'land' => $LAND['id'],
                                'geschlecht' => $geschlecht,
                                'anrede' => get_customer_salutation($geschlecht),
                                'ort' => substr($user_profile['hometown']['name'], 0, strpos($user_profile['hometown']['name'], ',')),
                                'express_reg' => 1,
                                'passwort' => md5($passwort));
                            $kid = insert_table(TBL_CMS_CUST, $arr);
                            $k_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_CUST . " WHERE kid=" . $kid);
                        }
                        $M = new member_class();
                        $M->login($k_obj);
                        $_SESSION['kid'] = $k_obj['kid'];
                        $_SESSION['password'] = $passwort;
                        keimeno_class::msg('{MSG_LOGINOK}');
                        HEADER("Location: http://www." . FM_DOMAIN . PATH_CMS . "index.php?&sid_id=" . session_id() . "&loginok=1");
                        $this->hard_exit();
                    }
                    catch (FacebookApiException $e) {
                        error_log($e);
                        $user = null;
                    }
                }
            }
        }

    }


    /**
     * fbwp_class::load_fb()
     * 
     * @return void
     */
    function load_fb() {
        $id = ($_REQUEST['id'] > 0) ? (int)$_REQUEST['id'] : 1;
        $this->FBWP['WP'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " WHERE id=" . $id);

        if ($this->FBWP['WP']['fb_appid'] == "" && $this->FBWP['WP']['fb_secret'] == "") {
            die('Facebook API: Invalid application id or pageid');
        }

        exec_evt('fb_page_loaded', array(), $this);
    }

    /**
     * fbwp_class::get_fotos_of_fanpage_stream()
     * 
     * @param mixed $feed
     * @param mixed $foto_width
     * @param mixed $foto_height
     * @param mixed $foto_resize_method
     * @return
     */
    function get_fotos_of_fanpage_stream(&$feed, $foto_width, $foto_height, $foto_resize_method) {

        foreach ($feed['data'] as $key => $row) {
            # echoarr( $feed['data'][$key]);
            $feed['data'][$key]['date_ger'] = date('d.m.Y H:i:s', strtotime($row['created_time']));
            $basename_foto = "";
            $facebook_images = $response = null;
            $k++;
            if ($row['object_id'] != "") {
                try {
                    $response = $this->facebook->get('/' . $row['object_id'] . '/?fields=images');
                    $facebook_images = $response->getDecodedBody();
                    if (isset($facebook_images['images'])) {
                        foreach ($facebook_images['images'] as $img) {

                            $local_foto = $foto_width . 'x' . $foto_width . '_' . md5(basename($img['source'])) . '.jpg';
                            if (!file_exists(CMS_ROOT . CACHE . $local_foto)) {
                                file_put_contents(CMS_ROOT . CACHE . $local_foto, $this->curl_get_data($img['source']));
                            }
                        }
                    }
                }
                catch (Exception $e) {
                    #  echoArr($class_name);
                    #  throw new kException($e->getMessage());
                }
            }
            $feed['data'][$key]['images'] = array();
            if (isset($facebook_images['images'])) {
                $basename_foto = $foto_width . 'x' . $foto_width . '_' . md5($row['object_id'] . $row['id']) . '.jpg';
                if (!file_exists(CMS_ROOT . CACHE . $basename_foto)) {
                    $foto = $facebook_images['images'][0]['source'];
                    file_put_contents(CMS_ROOT . CACHE . $basename_foto, $this->curl_get_data($foto));
                }
                $feed['data'][$key]['images'] = $facebook_images['images'];
                $feed['data'][$key]['mainfoto'] = $foto;
                $feed['data'][$key]['mainfoto_local'] = $basename_foto;

            }
            $feed['data'][$key]['thumb'] = "";
            if (file_exists('./' . CACHE . $basename_foto) && is_file('./' . CACHE . $basename_foto)) {
                $thumb = gen_thumb_image('./' . CACHE . $basename_foto, (int)$foto_width, (int)$foto_height, $foto_resize_method);
                if (file_exists(CMS_ROOT . 'cache/' . basename($thumb)) && is_file(CMS_ROOT . 'cache/' . basename($thumb))) {
                    $feed['data'][$key]['thumb'] = $thumb; # PATH_CMS . CACHE . $thumb;
                    $feed['data'][$key]['size'] = getimagesize(CMS_ROOT . 'cache/' . basename($thumb));
                }
            }
            if ($feed['data'][$key]['thumb'] == "" && $feed['data'][$key]['picture'] != "") {
                $feed['data'][$key]['thumb'] = $feed['data'][$key]['picture'];
            }

            # return;
        }

        return $feed;
    }

    /*  function get_fotos_of_group_stream(&$feed, $foto_width, $foto_height, $foto_resize_method) {
    foreach ($feed['data'] as $key => $row) {
    list($groupid, $postid) = explode('_', $row['id']);
    $feed['data'][$key]['date_ger'] = date('d.m.Y H:i:s', strtotime($row['created_time']));
    $basename_foto = $foto_width . 'x' . $foto_width . '_' . md5($row['object_id'] . $row['id']) . '.jpg';
    $k++;
    if ($row['object_id'] != "") {
    $response = $this->facebook->get('/' . $groupid . '/photos');
    echo $groupid;
    echoarr($response);
    die;
    $facebook_images = $response->getDecodedBody();

    foreach ($facebook_images['images'] as $img) {

    $local_foto = $foto_width . 'x' . $foto_width . '_' . md5(basename($img['source'])) . '.jpg';
    if (!file_exists(CMS_ROOT . CACHE . $local_foto)) {
    file_put_contents(CMS_ROOT . CACHE . $local_foto, $this->curl_get_data($img['source']));
    }
    }
    }
    if (!file_exists(CMS_ROOT . CACHE . $basename_foto)) {
    $foto = $facebook_images['images'][0]['source'];
    file_put_contents(CMS_ROOT . CACHE . $basename_foto, $this->curl_get_data($foto));
    }
    $feed['data'][$key]['images'] = $facebook_images['images'];

    if (file_exists('./' . CACHE . $basename_foto) && is_file('./' . CACHE . $basename_foto)) {
    $thumb = gen_thumb_image('./' . CACHE . $basename_foto, (int)$foto_width, (int)$foto_height, $foto_resize_method);
    if (file_exists(CMS_ROOT . 'cache/' . basename($thumb)) && is_file(CMS_ROOT . 'cache/' . basename($thumb))) {
    $feed['data'][$key][thumb] = $thumb; # PATH_CMS . CACHE . $thumb;
    $feed['data'][$key]['size'] = getimagesize(CMS_ROOT . 'cache/' . basename($thumb));
    }
    }

    $feed['data'][$key]['post_link'] = 'https://www.facebook.com/' . $groupid . '/posts/' . $postid;
    $feed['data'][$key]['message'] = $this->hyperlink($feed['data'][$key]['message']);
    $feed['data'][$key]['message'] = trim($feed['data'][$key]['message']);
    }
    return $feed;
    }
    */

    /**
     * fbwp_class::load_group_stream()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_group_stream($PLUGIN_OPT) {
        $foto_width = ($PLUGIN_OPT['foto_width'] > 0) ? $PLUGIN_OPT['foto_width'] : $this->gblconfig->fb_fanpage_thumb_width;
        $foto_height = ($PLUGIN_OPT['foto_height'] > 0) ? $PLUGIN_OPT['foto_height'] : $this->gblconfig->fb_fanpagethumb_height;
        $foto_resize_method = ($PLUGIN_OPT['foto_resize_method'] != '') ? $PLUGIN_OPT['foto_resize_method'] : 'resize';
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_FBGROUPS . " WHERE g_groupid=" . (int)$PLUGIN_OPT['fbwpid'] . " ORDER BY g_created_time_int DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['images'] = unserialize($row['g_images']);
            $feed[] = $row;
        }
        return (array )$feed;
    }

    /**
     * fbwp_class::cmd_testfb()
     * 
     * @return void
     */
    function cmd_testfb() {
        $this->load_status_fanpage();
        $this->parse_to_smarty();
        die('X');
    }

    /**
     * fbwp_class::cmd_get_fbratings()
     * 
     * @return void
     */
    function cmd_get_fbratings() {
        $this->load_fb();
        $feed = $this->get_ratings($this->FBWP['WP']);
        echoarr($feed);
        die('X');
    }

    /**
     * fbwp_class::load_status_fanpage()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_status_fanpage($PLUGIN_OPT = array()) {

        $foto_width = ($PLUGIN_OPT['foto_width'] > 0) ? $PLUGIN_OPT['foto_width'] : $this->gblconfig->fb_fanpage_thumb_width;
        $foto_height = ($PLUGIN_OPT['foto_height'] > 0) ? $PLUGIN_OPT['foto_height'] : $this->gblconfig->fb_fanpagethumb_height;
        $foto_resize_method = ($PLUGIN_OPT['foto_resize_method'] != '') ? $PLUGIN_OPT['foto_resize_method'] : 'resize';
        if ($this->gbl_config['fb_fanpage_autoload'] == 1 && $this->facebook != null) {

            $cache_file = CMS_ROOT . CACHE . 'fbfanpagestatus.tmp';
            $time = 0;
            if ($this->gblconfig->fb_cacheactive == 0) {
                @unlink($cache_file);
            }
            #@unlink($cache_file);
            if (file_exists($cache_file))
                $time = filectime($cache_file);
            if (!file_exists($cache_file) || (time() - $time) > ($this->gblconfig->fb_fanpage_cachetime * 3600)) {

                if ($this->FBWP['WP']['fb_token'] != "" && $this->FBWP['WP']['fb_appid'] != "" && $this->FBWP['WP']['fb_secret'] != "") {

                    $this->facebook = new Facebook\Facebook(array(
                        'app_id' => trim($this->FBWP['WP']['fb_appid']),
                        'app_secret' => trim($this->FBWP['WP']['fb_secret']),
                        'default_access_token' => trim($this->FBWP['WP']['fb_token']),
                        'default_graph_version' => DEFAULT_GRAPH_VERSION));

                    try {
                        $fbresult = $this->facebook->get('/' . $this->gbl_config['fbwp_pageid'] .
                            '/feed?fields=name,link,type,status_type,actions,from,source,privacy,application,properties,icon,picture,object_id,created_time,story,id,message&type=large&limit=' .
                            $this->gblconfig->fb_fanpage_count);

                        $feed = $fbresult->getDecodedBody();

                        $feed['data'] = (array )$feed['data'];

                        $this->get_fotos_of_fanpage_stream($feed, $foto_width, $foto_height, $foto_resize_method);


                        foreach ($feed['data'] as $key => $row) {
                            list($pageid, $postid) = explode('_', $row['id']);
                            $feed['data'][$key]['pageid'] = $pageid;
                            $feed['data'][$key]['postid'] = $postid;
                            $feed['data'][$key]['isvideo'] = ($row['type'] == 'video') ? 1 : 0;
                            $ids = explode('_', $row['id']);
                            $feed['data'][$key]['post_link'] = 'https://www.facebook.com/' . $this->gbl_config['fbwp_pageid'] . '/posts/' . $ids[1];
                            $feed['data'][$key]['message'] = $this->hyperlink($feed['data'][$key]['message']);
                            $feed['data'][$key]['message'] = trim($feed['data'][$key]['message']);
                            if ($feed['data'][$key]['isvideo'] == 1 && ($this->gbl_config['fb_novideos'] == 1 || $PLUGIN_OPT['no_videos'] == 1)) {
                                unset($feed['data'][$key]);
                            }
                            else {
                                if ($feed['data'][$key]['isvideo'] == 1) {
                                    $feed['data'][$key]['source'] = str_replace('autoplay=1', 'autoplay=0', $feed['data'][$key]['source']);
                                    $feed['data'][$key]['video_type'] = (stripos($row['link'], 'www.youtube.com/watch')) ? 'YT' : 'FB';
                                    $feed['data'][$key]['video_link'] = $row['link'];
                                    $feed['data'][$key]['video_url'] = urlencode($row['link']);
                                    # echoarr($feed['data'][$key]);
                                }
                            }
                        }
                        file_put_contents($cache_file, serialize($feed));
                    }
                    catch (Exception $e) {
                        #error_log($e);
                        #$this->msge('Facebook API: ' . $e->getMessage());
                        $this->LOGCLASS->addLog('FACEBOOK', $e->getMessage());
                        $feed = self::load_stream_from_cache($cache_file);
                        $feed = $this->reset_feed_option_for_plugin_use($feed, $foto_width, $foto_height, $foto_resize_method);
                    }
                }
            }
            else {
                $feed = self::load_stream_from_cache($cache_file);
                $feed = $this->reset_feed_option_for_plugin_use($feed, $foto_width, $foto_height, $foto_resize_method);
            }

        }
        $this->FBWP['fanpage_status'] = (array )$feed;
        return $this->FBWP['fanpage_status'];
    }

    /**
     * fbwp_class::reset_feed_option_for_plugin_use()
     * 
     * @param mixed $feed
     * @param mixed $foto_width
     * @param mixed $foto_height
     * @param mixed $foto_resize_method
     * @return
     */
    function reset_feed_option_for_plugin_use($feed, $foto_width, $foto_height, $foto_resize_method) {
        if (isset($feed['data'])) {
            foreach ($feed['data'] as $key => $row) {
                $feed['data'][$key]['thumb'] = "";
                $basename_foto = $row['mainfoto_local'];
                if (file_exists(CMS_ROOT . 'cache/' . $basename_foto) && is_file(CMS_ROOT . 'cache/' . $basename_foto)) {
                    $thumb = gen_thumb_image('./' . CACHE . $basename_foto, (int)$foto_width, (int)$foto_height, $foto_resize_method);
                    if (file_exists(CMS_ROOT . 'cache/' . basename($thumb)) && is_file(CMS_ROOT . 'cache/' . basename($thumb))) {
                        $feed['data'][$key]['thumb'] = $thumb;
                        $feed['data'][$key]['size'] = getimagesize(CMS_ROOT . 'cache/' . basename($thumb));
                    }
                }
                if ($feed['data'][$key]['thumb'] == "" && $feed['data'][$key]['picture'] != "") {
                    $feed['data'][$key]['thumb'] = $feed['data'][$key]['picture'];
                }
            }
        }
        return $feed;
    }

    /**
     * fbwp_class::parse_facebookgroup()
     * 
     * @param mixed $params
     * @return
     */
    function parse_facebook($params) {
        $html = $params['html'];
        $langid = $params['langid']; // parse group
        if (strstr($html, '{TMPL_FACEBOOKGROUP_')) {
            preg_match_all("={TMPL_FACEBOOKGROUP_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->FBWP['feed'] = $this->load_group_stream($PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=FACEBOOKGROUP value=$TMPL_FACEBOOKGROUP_' . $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] .
                    '.tpl" %>', $html);
                $params['PLUGIN_OPT'] = $PLUGIN_OPT;
                $this->smarty->assign('TMPL_FACEBOOKGROUP_' . $cont_matrix_id, $this->FBWP);
            }
        }

        #parse ratings
        if (strstr($html, '{TMPL_FACEBOOKRATING_')) {
            preg_match_all("={TMPL_FACEBOOKRATING_(.*)}=siU", $html, $tpl_tag);
            foreach ($tpl_tag[1] as $key => $cont_matrix_id) {
                $PLUGIN_OPT = $this->load_plug_opt($cont_matrix_id);
                $TPL = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T WHERE T.id=" . (int)$PLUGIN_OPT['tplid']);
                $this->FBWP['fbratings'] = $this->load_rating_stream($PLUGIN_OPT);
                $html = str_replace($tpl_tag[0][$key], '<% assign var=FBROBJ value=$TMPL_FACEBOOKRATING_' . $cont_matrix_id . ' %><% include file="' . $TPL['tpl_name'] .
                    '.tpl" %>', $html);
                $params['PLUGIN_OPT'] = $PLUGIN_OPT;
                $this->smarty->assign('TMPL_FACEBOOKRATING_' . $cont_matrix_id, $this->FBWP);
            }
        }
        $params['html'] = $html;
        return $params;
    }

    /**
     * fbwp_class::load_rating_stream()
     * 
     * @param mixed $PLUGIN_OPT
     * @return
     */
    function load_rating_stream($PLUGIN_OPT) {
        $this->load_fb();
        $feed = $this->get_ratings($this->FBWP['WP'], $PLUGIN_OPT);
        return (array )$feed;
    }

    /**
     * fbwp_class::cronjob()
     * 
     * @param mixed $params
     * @return
     */
    function cronjob($params) {
        $this->sync_group(1);
        return $params;
    }
}
