<?PHP

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


require_once CMS_ROOT . 'includes/modules/fbwp/facebook-sdk-v5/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;


DEFINE('TBL_CMS_FBWPCONTENT', TBL_CMS_PREFIX . 'fbwp');
DEFINE('TBL_CMS_FBGROUPS', TBL_CMS_PREFIX . 'fbgroups');
DEFINE('DEFAULT_GRAPH_VERSION', 'v2.10');
#DEFINE('DEFAULT_GRAPH_VERSION', 'v3.2');


class fbwp_master_class extends modules_class {

    var $FBWP = array();

    /**
     * fbwp_master_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        parent::__construct();
        $this->FBWP = array();
    }

    /**
     * fbwp_master_class::get_token()
     * 
     * @param mixed $fbwpid
     * @return
     */
    function get_token($fbwpid) {
        $accessToken = "";
        $this->FBWP['WP'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " WHERE id=" . $fbwpid);
        if ($this->FBWP['WP']['fb_appid'] != "" && $this->FBWP['WP']['fb_secret'] != "") {
            $config = array(
                'app_id' => trim($this->FBWP['WP']['fb_appid']),
                'app_secret' => trim($this->FBWP['WP']['fb_secret']),
                'default_graph_version' => DEFAULT_GRAPH_VERSION);

            $this->facebook = new Facebook\Facebook($config);

            $helper = $this->facebook->getRedirectLoginHelper();
            try {
                $accessToken = $helper->getAccessToken(self::get_redirect_url($fbwpid));

            }
            catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            }
            catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

        }
        return $accessToken;
    }

    /**
     * fbwp_master_class::get_redirect_url()
     * 
     * @return
     */
    function get_redirect_url($id) {
        $protocol = 'https://';#isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . '/admin/run.php?fbwpid=' . $id . '&epage=fbwp.inc&section=start&cmd=set_token_fb';
    }

    /**
     * fbwp_master_class::sync_group()
     * 
     * @param mixed $id
     * @return
     */
    function sync_group($id) {
        $this->FBWP['WP'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_FBWPCONTENT . " WHERE id=" . $id);
        if (empty($this->FBWP['WP']['fb_groupid']))
            return;
        $accessToken = $this->get_token($id);
        if ($accessToken == "")
            return;

        $this->facebook = new Facebook\Facebook(array(
            'app_id' => trim($this->FBWP['WP']['fb_appid']),
            'app_secret' => trim($this->FBWP['WP']['fb_secret']),
            'default_access_token' => $accessToken,
            'default_graph_version' => DEFAULT_GRAPH_VERSION));
        if ($this->facebook != null) {
            # $this->facebook = new Facebook\Facebook(array(
            #    'app_id' => trim($this->FBWP['WP']['fb_appid']),
            #   'app_secret' => trim($this->FBWP['WP']['fb_secret']),
            #  'default_access_token' => trim($this->FBWP['WP']['fb_token']),
            # 'default_graph_version' => 'v2.4'));
            try {
                $response = $this->facebook->get('/' . $this->FBWP['WP']['fb_groupid'] .
                    '/feed?type=large&fields=attachments,picture,updated_time,object_id,created_time,story,id,message');
                $feed = $response->getDecodedBody();
                $feed['data'] = (array )$feed['data'];
                foreach ($feed['data'] as $key => $row) {
                    if (isset($row['attachments']['data'][0]['subattachments'])) {
                        foreach ($row['attachments']['data'][0]['subattachments']['data'] as $att) {
                            if ($att['media']['image']['height'] > 0) {
                                $feed['data'][$key]['images'][] = $att['media']['image'];
                            }
                        }
                    }
                }

                foreach ($feed['data'] as $key => $row) {
                    list($groupid, $postid) = explode('_', $row['id']);
                    $images = array();
                    foreach ((array )$row['images'] as $img) {
                        $images[] = (array )$img;
                    }

                    $arr = array(
                        'id' => $row['id'],
                        'g_groupid' => $id,
                        'g_created_time_int' => strtotime($row['created_time']),
                        'g_picture' => $row['picture'],
                        'g_updated_time' => $row['updated_time'],
                        'g_object_id' => $row['object_id'],
                        'g_created_time' => $row['created_time'],
                        'g_post_link' => $feed['data'][$key]['post_link'] = 'https://www.facebook.com/' . $groupid . '/posts/' . $postid,
                        'g_message' => $this->db->real_escape_string($row['message']),
                        'g_images' => serialize(self::real_escape((array )$images)),
                        #  'g_attachments' => json_encode(self::real_escape((array)$row['attachments'])),
                        );
                    if (get_data_count(TBL_CMS_FBGROUPS, '*', "id='" . $row['id'] . "'") == 0) {
                        insert_table(TBL_CMS_FBGROUPS, $arr);
                    }
                }

            }
            catch (FacebookApiException $e) {
                error_log($e);
                echoarr($e);
            }
        }
    }

    /**
     * fbwp_master_class::load_from_cache()
     * 
     * @param mixed $cache_file
     * @return
     */
    public static function load_from_cache($cache_file) {
        $feed = array();
        if (is_file($cache_file)) {
            $feed = unserialize(file_get_contents($cache_file));
        }
        return $feed;
    }
    
 
    /**
     * fbwp_master_class::get_ratings()
     * 
     * @param mixed $WP
     * @return
     */
    function get_ratings($WP, $PLUGIN_OPT = array()) {
        $cache_file = CMS_ROOT . CACHE . 'fbratingstream.tmp';
        $time = 0;
        if ($this->gblconfig->fb_cacheactive == 0) {
            @unlink($cache_file);
        }
        # @unlink($cache_file);
        if (file_exists($cache_file))
            $time = filectime($cache_file);
        if (!file_exists($cache_file) || (time() - $time) > ($this->gblconfig->fb_fanpage_cachetime * 3600)) {

            $this->facebook = new Facebook\Facebook(array(
                'app_id' => trim($WP['fb_appid']),
                'app_secret' => trim($WP['fb_secret']),
                'default_access_token' => trim($WP['fb_token']),
                'default_graph_version' => DEFAULT_GRAPH_VERSION));
            try {
                $overall_star_rating_result = $this->facebook->get('/' . $this->gbl_config['fbwp_pageid'] . '?fields=overall_star_rating');

                $overall_star_rating = $overall_star_rating_result->getDecodedBody();
                $rating_count_result = $this->facebook->get('/' . $this->gbl_config['fbwp_pageid'] . '?fields=rating_count');
                $rating_count = $rating_count_result->getDecodedBody();
                $fbresult = $this->facebook->get('/' . $this->gbl_config['fbwp_pageid'] . '/ratings?' . (isset($PLUGIN_OPT['limit']) ? 'limit=' . ($PLUGIN_OPT['limit']) : ""));
                $feed = $fbresult->getDecodedBody();
                $feed['total_rating_count'] = $rating_count['rating_count'];
                $feed['overall_star_rating'] = $overall_star_rating['overall_star_rating'];
                $feed['data'] = (array )$feed['data'];
                foreach ($feed['data'] as $key => $row) {
                    $feed['data'][$key]['date_ger'] = date('d.m.Y', strtotime($row['created_time']));
                    $feed['data'][$key]['create_time_int'] = strtotime($row['created_time']);
                    if (isset($PLUGIN_OPT['limit'])) {
                        if ($row['review_text'] == "") {
                            unset($feed['data'][$key]);
                        }
                    }
                }
                file_put_contents($cache_file, serialize($feed));
            }
            catch (Exception $e) {
                $this->LOGCLASS->addLog('FACEBOOK', $e->getMessage());
                $feed = self::load_from_cache($cache_file);
                return $feed;
            }
        }
        else {
            $feed = self::load_from_cache($cache_file);
        }

        return $feed;
    }

}
