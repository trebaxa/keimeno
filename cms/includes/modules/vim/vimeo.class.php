<?php

# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    vim
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


DEFINE('TBL_CMS_VIDEOS', TBL_CMS_PREFIX . 'videos');
DEFINE('TBL_CMS_VIDEOCATS', TBL_CMS_PREFIX . 'videos_cats');
DEFINE('TBL_CMS_VIDEO_MATRIX', TBL_CMS_PREFIX . 'video_matrix');
DEFINE('TBL_CMS_VIDEO_CACHE', TBL_CMS_PREFIX . 'video_cache');
DEFINE('TBL_CMS_VIDEO_TOCAT', TBL_CMS_PREFIX . 'video_tocat');
DEFINE('VITHUMB_PATH', 'file_data/videothumbs/');

defined('IN_SIDE') or die('Access denied.');


class vimeocms_class extends modules_class {

    /**
     * vimeocms_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        require_once ('vimeocom.class.php');
        // Get a new request token
        $this->VIMEO = new phpVimeoAPI(trim($this->gbl_config['vm_consumerkey']), trim($this->gbl_config['vm_secret']));
        $this->VIMEO->enableCache(phpVimeoAPI::CACHE_FILE, CMS_ROOT . 'cache', 300);
        $this->state = $_SESSION['vimeo_state'];
        $this->request_token = $_SESSION['oauth_request_token'];
        $this->oauth_access_token = $this->gbl_config['vm_oauth_token']; #$_SESSION['oauth_access_token'];
        $this->oauth_access_token_secret = $this->gbl_config['vm_oauth_token_secret']; #$_SESSION['oauth_access_token_secret'];
        // If we have an access token, set it
        if ($this->oauth_access_token != null) {
            $this->VIMEO->setToken($this->oauth_access_token, $this->oauth_access_token_secret);
        }
        $this->TCR = new kcontrol_class($this);
    }


    /**
     * vimeocms_class::connect()
     * 
     * @return
     */
    function connect() {
        if ($_SESSION['vimeo_state'] == 'done') {
            return;
        }
        $token = $this->VIMEO->getRequestToken('http://www.' . FM_DOMAIN . '/includes/modules/vim/vimeo.callback.php');
        $_SESSION['oauth_request_token'] = $token['oauth_token'];
        $_SESSION['oauth_request_token_secret'] = $token['oauth_token_secret'];
        $_SESSION['vimeo_state'] = 'start';
    }

    /**
     * vimeocms_class::reconnect()
     * 
     * @return
     */
    function reconnect() {
        $this->logout();
        $this->connect();
    }

    /**
     * vimeocms_class::build_auth_link()
     * 
     * @return
     */
    function build_auth_link() {
        $this->connect();
        $this->authorize_link = $this->VIMEO->getAuthorizeUrl($_SESSION['oauth_request_token'], 'write');
        return $this->authorize_link;
    }

    /**
     * vimeocms_class::logout()
     * 
     * @return
     */
    function logout() {
        unset($_SESSION['oauth_request_token']);
        unset($_SESSION['oauth_request_token_secret']);
        unset($_SESSION['vimeo_state']);
        unset($_SESSION['oauth_access_token']);
    }

    /**
     * vimeocms_class::cmd_vi_logout()
     * 
     * @return
     */
    function cmd_vi_logout() {
        $this->logout();
        $this->TCR->add_msg('Vimeo logged out');
    }

    # Get a list of videos uploaded by a user.
    /**
     * vimeocms_class::vi_load_videos_by_user()
     * 
     * @param mixed $user_id
     * @return
     */
    function vi_load_videos_by_user($user_id) {
        $videos = $this->VIMEO->call('vimeo.videos.getUploaded', array('user_id' => $user_id));
    }

    # Get a list of the videos in a channel.
    /**
     * vimeocms_class::vi_load_videos_by_channel()
     * 
     * @param mixed $channel_id
     * @return
     */
    function vi_load_videos_by_channel($channel_id) {
        $videos = $this->VIMEO->call('vimeo.channels.getVideos', array('channel_id' => $channel_id));
    }

    # Get a list of all public channels.
    /**
     * vimeocms_class::vi_load_all_channel()
     * 
     * @return
     */
    function vi_load_all_channel() {
        $this->VIMEO['all_channels'] = $this->VIMEO->call('vimeo.channels.getAll', array('sort' => 'alphabetical'));
    }

    # Get lots of information on a video.
    /**
     * vimeocms_class::vi_load_single_video()
     * 
     * @param mixed $video_id
     * @return
     */
    function vi_load_single_video($video_id) {
        $video_id = str_replace('VI', '', $video_id);
        try {
            $ret = $this->VIMEO->call('vimeo.videos.getInfo', array('video_id' => $video_id));
        }
        catch (VimeoAPIExceptClass $e) {

        }
        return $ret;
    }

    /**
     * vimeocms_class::validate_video()
     * 
     * @param mixed $video_id
     * @return
     */
    function validate_video($video_id) {
        $V = $this->vi_load_single_video($video_id);
        return is_array($V->video);
    }

    #Get a list of videos that have the specified tag.
    /**
     * vimeocms_class::vi_load_videos_by_tag()
     * 
     * @param mixed $tag
     * @param mixed $page
     * @param mixed $per_page
     * @param mixed $sort
     * @return
     */
    function vi_load_videos_by_tag($tag, $page, $per_page, $sort) {
        $videos = $this->VIMEO->call('vimeo.videos.getByTag', array(
            'full_response' => 1,
            'tag' => $tag,
            'page' => $page,
            'per_page' => $per_page,
            'sort' => $sort));
        return $videos;
    }

    #Get a list of videos by author
    /**
     * vimeocms_class::vi_load_videos_by_author()
     * 
     * @param mixed $author
     * @param mixed $page
     * @param mixed $per_page
     * @param mixed $sort
     * @return
     */
    function vi_load_videos_by_author($author, $page, $per_page, $sort) {
        $videos = $this->VIMEO->call('vimeo.videos.getUploaded', array(
            'full_response' => 1,
            'user_id' => $author,
            'page' => $page,
            'per_page' => $per_page,
            'sort' => $sort));
        return $videos;
    }


    #Search for videos.
    /**
     * vimeocms_class::vi_search_videos()
     * 
     * @param mixed $searchTerm
     * @param mixed $page
     * @param mixed $per_page
     * @param mixed $sort
     * @return
     */
    function vi_search_videos($searchTerm, $page, $per_page, $sort) {
        $videos = $this->VIMEO->call('vimeo.videos.search', array(
            'full_response' => 1,
            'query' => $searchTerm,
            'page' => $page,
            'per_page' => $per_page,
            'sort' => $sort));
        return $videos;
    }

    # Get all videos
    /**
     * vimeocms_class::vi_getall_videos()
     * 
     * @param mixed $author
     * @param integer $page
     * @param integer $per_page
     * @param string $sort
     * @return
     */
    function vi_getall_videos($author, $page = 1, $per_page = 10, $sort = 'newest') {
        $videos = $this->VIMEO->call('vimeo.videos.getAll', array(
            'full_response' => 1,
            'user_id' => $author,
            'page' => $page,
            'per_page' => $per_page,
            'sort' => $sort));
        return $videos;
    }

    /**
     * vimeocms_class::vi_set_title()
     * 
     * @param mixed $sVideoTitle
     * @param mixed $iVideoID
     * @return
     */
    function vi_set_title($sVideoTitle, $iVideoID) {
        $aArgs = array(
            'oauth_token' => $this->oauth_access_token, #$this->gbl_config['vm_oauth_token'],
            'video_id' => $iVideoID,
            'title' => $sVideoTitle);
        return $this->VIMEO->call('vimeo.videos.setTitle', $aArgs);
    }

    # Get all videos
    /**
     * vimeocms_class::vi_get_ticket()
     * 
     * @return
     */
    function vi_get_ticket() {
        $ticket = $this->VIMEO->call('vimeo.videos.upload.getTicket', array('oauth_token' => $this->oauth_access_token));

        $ticket_arr = array(
            'id' => (string )$ticket->ticket->id,
            'host' => (string )$ticket->ticket->host,
            'endpoint' => (string )$ticket->ticket->endpoint,
            'max_file_size' => (string )$ticket->ticket->max_file_size);
        #echoarr($ticket_arr);die;
        return $ticket_arr;
    }

    /**
     * vimeocms_class::vi_upload()
     * 
     * @param mixed $file_path
     * @param bool $use_multiple_chunks
     * @param string $chunk_temp_dir
     * @param integer $size
     * @param mixed $replace_id
     * @return
     */
    function vi_upload($file_path, $use_multiple_chunks = false, $chunk_temp_dir = '.', $size = 2097152, $replace_id = null) {
        return $this->VIMEO->upload($file_path, $use_multiple_chunks, $chunk_temp_dir, $size, $replace_id);
    }


    /**
     * vimeocms_class::cmd_sync()
     * 
     * @return
     */
    function cmd_sync() {
        $RET['FORM']['YTOPTIONS'] = $_REQUEST['YTOPTIONS'];
        $RET['FORM']['YTOPTIONS']['maxTotalLimit'] = $RET['FORM']['YTOPTIONS']['maxTotalLimit'] == 0 ? 1000 : $RET['FORM']['YTOPTIONS']['maxTotalLimit'];
        $RET['FORM']['YTOPTIONS']['maxResults'] = $RET['FORM']['YTOPTIONS']['maxResults'] == 0 ? 50 : $RET['FORM']['YTOPTIONS']['maxResults'];
        if ($YTOPTIONS['startIndex'] == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_CACHE . " WHERE 1");
        }
        $startIndex = ((int)$YTOPTIONS['startIndex'] == 0) ? 1 : (int)$RET['FORM']['YTOPTIONS']['startIndex'];

        $feed = $this->vi_getall_videos($this->gbl_config['vm_author'], $startIndex, $RET['FORM']['YTOPTIONS']['maxResults']);
        $this->VI['videos'] = $this->map_vimeo_to_db($feed);
        #echoarr($this->VI['videos']);
        $RET['queryresult'] = $this->VI['videos'];
        $RET['FORM']['YTOPTIONS']['startIndex'] += 30;
        $RET['TotalResults'] = strval($feed->videos->total);
        $RET['FORM']['YTOPTIONS']['doneProcent'] = round((100 / $RET['FORM']['YTOPTIONS']['maxTotalLimit']) * $RET['FORM']['YTOPTIONS']['startIndex'], 2);
        $this->move_to_db($RET);
        $RET['count_added'] = (int)$_SESSION['video_log']['count_added'];
        $RET['count_skipped'] = (int)$_SESSION['video_log']['count_skipped'];

        if ($RET['TotalResults'] - $RET['FORM']['YTOPTIONS']['startIndex'] > 0 && $RET['FORM']['YTOPTIONS']['startIndex'] < 1000 && $RET['FORM']['YTOPTIONS']['startIndex'] <=
            $RET['FORM']['YTOPTIONS']['maxTotalLimit']) {
            $url = $_SERVER['PHP_SELF'] . "?epage=" . $_REQUEST['epage'] . "&section=" . $_REQUEST['section'] . "&cmd=" . $_REQUEST['cmd'] . '&' . http_build_query($RET['FORM']);
            $smarty = $this->smarty;
            include (CMS_ROOT . 'admin/inc/smarty.inc.php');
            HEADER("Refresh: 1;  URL=" . $url);
            $this->VIM['sync_status'] = $RET;
            $this->parse_to_smarty();
            $content = '<% include file="video.tpl" %>';
            ECHORESULT(kf::translate_admin(smarty_compile($content)));
            die;
        }
        else {
            header('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=load_via_videos&section=' . $_REQUEST['section'] . '&msg=' . base64_encode('{LBL_DONE}'));
        }
        exit;
    }


    /**
     * vimeocms_class::map_vimeo_to_db()
     * 
     * @param mixed $feed
     * @return
     */
    function map_vimeo_to_db($feed) {
        if (is_array($feed->videos->video)) {
            #	echoarr($feed->videos->video);die;
            foreach ($feed->videos->video as $entry) {
                list($ld, $lt) = explode(' ', strval($entry->modified_date));
                list($upload_date, $ltu) = explode(' ', strval($entry->upload_date));
                // TAGS
                $tags = $urls = array();
                if (is_array($entry->tags->tag)) {
                    foreach ($entry->tags->tag as $tag) {
                        $tags[] = $tag->_content;
                    }
                }
                if (is_array($entry->urls->url)) {
                    foreach ($entry->urls->url as $url) {
                        $urls[] = $url->_content;
                    }
                }
                $id = 'VI' . trim(strval($entry->id));
                $res[$id] = array(
                    'yt_syncby' => 'VIM',
                    'yt_videoid' => $id,
                    'ytthumbnailurl' => $entry->kf::thumbnails->kf::thumbnail[2]->_content,
                    'ytthumbnailwidth' => $entry->kf::thumbnails->kf::thumbnail[2]->width,
                    'ytthumbnailheight' => $entry->kf::thumbnails->kf::thumbnail[2]->height,
                    'yt_videotitle' => $entry->title,
                    'yt_videodescription' => $entry->description,
                    'yt_viewcount' => $entry->number_of_plays,
                    'yt_recorded' => $upload_date,
                    'yt_videoduration' => $entry->duration,
                    'yt_flashplayerurl' => 'http://player.vimeo.com/video/' . trim(strval($entry->id)),
                    'yt_watchpageurl' => $urls[0],
                    'yt_videotags' => (is_array($tags)) ? implode(';', $tags) : '',
                    'yt_updated' => strval($entry->modified_date),
                    'yt_lastupdate' => $ld,
                    'yt_upload_date' => $upload_date,
                    'yt_author_username' => $entry->owner->username,
                    'yt_author_realname' => $entry->owner->realname);

            }
        }
        return $res;
    }

    /**
     * vimeocms_class::move_to_db()
     * 
     * @param mixed $RET
     * @return
     */
    function move_to_db($RET) {
        if (is_array($RET['queryresult'])) {
            #	echoarr($RET['queryresult']);die;
            foreach ($RET['queryresult'] as $videoid => $video) {
                $video['v_syncby'] = 'VIA';
                foreach ($video as $key => $wert) {
                    $skey = str_replace('yt_', 'v_', $key);
                    unset($video[$key]);
                    $video[$skey] = $this->db->real_escape_string($wert);
                }
                if (get_data_count(TBL_CMS_VIDEOS, 'v_videoid', "v_videoid='" . $videoid . "'") == 0) {
                    $video['v_apptime'] = time();
                    insert_table(TBL_CMS_VIDEOS, $video);
                }
                else {
                    update_table(TBL_CMS_VIDEOS, 'v_videoid', $videoid, $video);
                }
            }
        }
    }

    /**
     * vimeocms_class::move_to_db_search()
     * 
     * @param mixed $RET
     * @return
     */
    function move_to_db_search($RET) {
        if (is_array($RET['queryresult'])) {
            #	echoarr($RET['queryresult']);die;
            foreach ($RET['queryresult'] as $videoid => $video) {
                if (get_data_count(TBL_CMS_VIDEO_CACHE, 'yt_videoid', "yt_videoid='" . $videoid . "'") == 0) {
                    if (is_array($video)) {
                        foreach ($video as $key => $wert)
                            $video[$key] = $this->db->real_escape_string($video[$key]);
                        insert_table(TBL_CMS_VIDEO_CACHE, $video);
                        $_SESSION['video_log'][$video['v_qid']]['count_added']++;
                    }
                }
                else {
                    $_SESSION['video_log'][$video['v_qid']]['count_skipped']++;
                }
                // Update Query Matrix
                $_SESSION['video_log'][$video['v_qid']]['vq_order'] += 10;
            }
        }
    }

    /**
     * vimeocms_class::approve_video()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function approve_video($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_VIDEOS . " SET v_approve=" . (int)$value . " WHERE v_videoid='" . $id . "' LIMIT 1");
    }


    /**
     * vimeocms_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        $parts = explode('-', $this->TCR->GET['id']);
        $id = $parts[1];
        $this->approve_video($this->TCR->GET['value'], $id);
        die;
    }

    /**
     * vimeocms_class::is_valid_url()
     * 
     * @param mixed $url
     * @return
     */
    function is_valid_url($url = NULL) {
        if ($url == NULL)
            return false;

        return (filter_var($url, FILTER_VALIDATE_URL));
    }

    /**
     * vimeocms_class::get_remote_file()
     * 
     * @param mixed $url
     * @return
     */
    function get_remote_file($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLINFO_NAMELOOKUP_TIME, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    /**
     * vimeocms_class::kf()
     * 
     * @return
     */
    function kf::gen_thumbnail($url, $id) {
        if ($url != "" && $this->is_valid_url($url) && in_array(strtolower($this->get_ext(basename($url))), array(
            'jpg',
            'png',
            'gif'))) {
            $fname = CMS_ROOT . CACHE . 'video_' . $id . '_' . basename($url);
            if (!file_exists($fname)) {
                #$img_binary = file_get_contents($url);
                $img_binary = $this->get_remote_file($url);
                file_put_contents($fname, $img_binary);
            }
            $G = new graphic_class();
            $img_name = $G->makeThumb($fname, $this->gbl_config['vimthumbwidth_fe'], $this->gbl_config['vimthumbheight_fe'], './' . CACHE, TRUE, 'crop');
            unset($G);
            return PATH_CMS . CACHE . basename($img_name);
        }
        else {
            $G = new graphic_class();
            $fname = CMS_ROOT . 'includes/modules/vim/images/no_picture.gif';
            $img_name = $G->makeThumb($fname, $this->gbl_config['vimthumbwidth_fe'], $this->gbl_config['vimthumbheight_fe'], './' . CACHE, TRUE, 'crop');
            unset($G);
            return PATH_CMS . CACHE . basename($img_name);
        }
    }

    /**
     * vimeocms_class::set_video_opt()
     * 
     * @param mixed $row
     * @return
     */
    function set_video_opt(&$row) {
        $row['v_recorded_ger'] = my_date('d.m.Y', $row['v_upload_date']);
        if (ISADMIN == 1) {
            $row['icons'][] = kf::gen_approve_icon($row['v_videoid'], $row['v_approve']);
        }
        $row['v_duration'] = $this->seconds_to_hms($row['v_videoduration']);
        $row['thumbnail'] = $this->kf::gen_thumbnail($row['vthumbnailurl'], $row['v_videoid']);
    }


    /**
     * vimeocms_class::get_new_ticket()
     * 
     * @return
     */
    function get_new_ticket() {
        $ticket = $this->vi_get_ticket();
        $this->VIM['ticket'] = $ticket;
    }

    /**
     * vimeocms_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        #   $this->VIM['loggedin'] = ($this->oauth_access_token != null);
        #   $this->VIM['state'] = $_SESSION['vimeo_state'];
        #   $this->VIM['vi_authlink'] = $this->build_auth_link();
        #  $this->smarty->assign('VIM', $this->VIM);
    }


    /**
     * vimeocms_class::search()
     * 
     * @param mixed $YTOPTIONS
     * @return
     */
    function search($YTOPTIONS) {
        #	$this->connect();
        $query = $this->TCR->REQUEST['FORM'];
        $searchTerm = $YTOPTIONS['searchTerm'];
        $startIndex = ((int)$YTOPTIONS['startIndex'] == 0) ? 1 : (int)$YTOPTIONS['startIndex'];
        $maxResults = (int)$YTOPTIONS['maxResults'];
        # $RET['FORM']['YTOPTIONS'] = $YTOPTIONS;
        if ($YTOPTIONS['startIndex'] == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_CACHE . " WHERE 1");
        }
        $startIndex = ((int)$YTOPTIONS['startIndex'] == 0) ? 1 : (int)$RET['FORM']['YTOPTIONS']['startIndex'];

        if ($query['vp_vitype'] == 'TAG') {
            $feed = $this->vi_load_videos_by_tag($searchTerm, $startIndex, $maxResults, $YTOPTIONS['orderby']);
        }
        else
            if ($query['vp_vitype'] == 'AUT') {
                $feed = $this->vi_load_videos_by_author($query['vp_author'], $startIndex, $maxResults, $YTOPTIONS['orderby']);
            }
            else {
                $feed = $this->vi_search_videos($searchTerm, $startIndex, $maxResults, $YTOPTIONS['orderby']);
            }
            $this->VI['videos'] = $this->map_vimeo_to_db($feed);
        #echoarr($this->VI['videos']);
        $RET['queryresult'] = $this->map_vimeo_to_db($feed, $query);
        $RET['FORM']['YTOPTIONS'] = $_REQUEST['YTOPTIONS'];
        $RET['FORM']['YTOPTIONS']['startIndex'] += $RET['FORM']['YTOPTIONS']['maxResults'];
        $RET['TotalResults'] = strval($feed->videos->total);
        $RET['YTOPTIONS']['doneProcent'] = round((100 / $RET['FORM']['YTOPTIONS']['maxTotalLimit']) * $RET['FORM']['YTOPTIONS']['startIndex'], 2);
        $this->move_to_db_search($RET);
        $RET['vp_log']['count_added'] = (int)$_SESSION['vp_log'][$queryid]['count_added'];
        $RET['vp_log']['count_skipped'] = (int)$_SESSION['vp_log'][$queryid]['count_skipped'];
        return $RET;
    }

}

?>