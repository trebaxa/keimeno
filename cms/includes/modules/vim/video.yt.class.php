<?php

# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    vim
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class video_yt_class extends keimeno_class {


    /**
     * video_yt_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
    }

    /**
     * video_yt_class::findFlashUrl()
     * 
     * @param mixed $entry
     * @return
     */
    function findFlashUrl($entry) {
        foreach ($entry->mediaGroup->content as $content) {
            if ($content->type === 'application/x-shockwave-flash') {
                return $content->url;
            }
        }
        return null;
    }


    /**
     * video_yt_class::getTopRatedVideosByUser()
     * 
     * @param mixed $user
     * @return
     */
    function getTopRatedVideosByUser($user) {
        $userVideosUrl = 'http://gdata.youtube.com/feeds/users/' . $user . '/uploads';
        $yt = new Zend_Gdata_YouTube();
        $ytQuery = $yt->newVideoQuery($userVideosUrl);
        // order by the rating of the videos
        $ytQuery->setOrderBy('rating');
        // retrieve a maximum of 5 videos
        $ytQuery->setMaxResults(5);
        // retrieve only embeddable videos
        $ytQuery->setFormat(5);
        return $yt->getVideoFeed($ytQuery);
    }


    /**
     * video_yt_class::getRelatedVideos()
     * 
     * @param mixed $videoId
     * @return
     */
    function getRelatedVideos($videoId) {
        $yt = new Zend_Gdata_YouTube();
        $ytQuery = $yt->newVideoQuery();
        // show videos related to the specified video
        $ytQuery->setFeedType('related', $videoId);
        // order videos by rating
        $ytQuery->setOrderBy('rating');
        // retrieve a maximum of 5 videos
        $ytQuery->setMaxResults(5);
        // retrieve only embeddable videos
        $ytQuery->setFormat(5);
        return $yt->getVideoFeed($ytQuery);
    }


    /**
     * video_yt_class::start_request()
     * 
     * @return
     */
    function start_request() {
        global $content;
        if ($_REQUEST['YTOPTIONS']['startIndex'] < 1000)
            $this->sync($_REQUEST['YTOPTIONS']);
        if ($this->YT['TotalResults'] - $this->YT['FORM']['YTOPTIONS']['startIndex'] > 0 && $this->YT['FORM']['YTOPTIONS']['startIndex'] < 1000 && $this->YT['FORM']['YTOPTIONS']['startIndex'] <=
            $_REQUEST['YTOPTIONS']['maxTotalLimit']) {
            $url = $_SERVER['PHP_SELF'] . "?epage=" . $_REQUEST['epage'] . "&section=" . $_REQUEST['section'] . "&aktion=" . $_REQUEST['aktion'] . '&' . http_build_query($this->
                YT['FORM']);
            $smarty = $this->smarty;
            include (CMS_ROOT . 'admin/inc/smarty.inc.php');
            HEADER("Refresh: 1;  URL=" . $url);
            $this->parse_to_smarty();
            $content .= '<% include file="video.tpl" %>';
            ECHORESULT(kf::translate_admin(smarty_compile($content)));
            die;
        }
        else {
            header('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&section=' . $_REQUEST['section'] . '&msg=' . base64_encode('{LBL_DONE}'));
        }
        exit;
    }

    /**
     * video_yt_class::sync()
     * 
     * @param mixed $YTOPTIONS
     * @return
     */
    function sync($YTOPTIONS) {
        $queryType = isset($YTOPTIONS['queryType']) ? $YTOPTIONS['queryType'] : null;
        $searchTerm = $YTOPTIONS['searchTerm'];
        $startTime = $YTOPTIONS['time'];
        $excludeTerms = $YTOPTIONS['excludeTerms'];
        $startIndex = ((int)$YTOPTIONS['startIndex'] == 0) ? 1 : (int)$YTOPTIONS['startIndex'];
        $maxResults = (int)$YTOPTIONS['maxResults'];
        $ytcat = $YTOPTIONS['ytcat'];
        if ($YTOPTIONS['startIndex'] == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_CACHE . " WHERE yt_sid='" . session_id() . "' OR yt_sid=''");
        }
        if ($excludeTerms != "") {
            $list = explode(',', $excludeTerms);
            $searchTerm .= ' -' . implode(' -', $list);
        }

        $yt = new Zend_Gdata_YouTube();
        $yt->setMajorProtocolVersion(2);
        $query = $yt->newVideoQuery();
        $query->setQuery($searchTerm);
        $query->setStartIndex($startIndex);
        $query->setMaxResults($maxResults);
        $query->setOrderBy($YTOPTIONS['orderby']);
        if (!empty($YTOPTIONS['vp_lr']))
            $query->setParam('lr', $YTOPTIONS['vp_lr']);
        if ($ytcat != "")
            $query->category = $ytcat; # "/catname/suchbegriff"

        $query->setTime($startTime);

        if (!empty($YTOPTIONS['vp_author']))
            $query->setAuthor(trim($YTOPTIONS['vp_author']));

        /* check for one of the standard feeds, or list from 'all' videos */
        switch ($queryType) {
            case 'most_viewed':
                $query->setFeedType('most viewed');
                $query->setTime('this_week');
                $feed = $yt->getVideoFeed($query);
                break;
            case 'most_recent':
                $query->setFeedType('most recent');
                $feed = $yt->getVideoFeed($query);
                break;
            case 'recently_featured':
                $query->setFeedType('recently featured');
                $feed = $yt->getVideoFeed($query);
                break;
            case 'top_rated':
                $query->setFeedType('top rated');
                $query->setTime('this_week');
                $feed = $yt->getVideoFeed($query);
                break;
            case 'all':
                $feed = $yt->getVideoFeed($query);
                break;
            default:
                echo 'ERROR - unknown queryType - "' . $queryType . '"';
                die;
        }
        ;

        $this->YT['queryresult'] = $this->set_video_opt($feed, $queryid);
        $this->YT['FORM']['YTOPTIONS'] = $_REQUEST['YTOPTIONS'];
        $this->YT['FORM']['YTOPTIONS']['startIndex'] += $this->YT['FORM']['YTOPTIONS']['maxResults'];
        $this->YT['TotalResults'] = strval($feed->getTotalResults());
        $this->YT['YTOPTIONS']['doneProcent'] = round((100 / $this->YT['FORM']['YTOPTIONS']['maxTotalLimit']) * $this->YT['FORM']['YTOPTIONS']['startIndex'], 2);
        $this->move_to_db();
        $this->YT['vp_log']['count_added'] = (int)$_SESSION['vp_log'][$queryid]['count_added'];
        $this->YT['vp_log']['count_skipped'] = (int)$_SESSION['vp_log'][$queryid]['count_skipped'];
        return $this->YT;
    }

    /**
     * video_yt_class::move_to_db()
     * 
     * @return
     */
    function move_to_db() {
        if (is_array($this->YT['queryresult'])) {
            foreach ($this->YT['queryresult'] as $videoid => $video) {
                if (is_array($video)) {
                    foreach ($video as $key => $wert)
                        $video[$key] = $this->db->real_escape_string($video[$key]);
                    $video['yt_sid'] = session_id();
                    $this->db->query("DELETE FROM " . TBL_CMS_VIDEO_CACHE . " WHERE yt_videoid='" . trim($videoid) . "'");
                    insert_table(TBL_CMS_VIDEO_CACHE, $video);
                    $_SESSION['vp_log'][$video['yt_qid']]['count_added']++;
                }
                $_SESSION['vp_log'][$video['yt_qid']]['vq_order'] += 10;

            }
        }
    }


    /**
     * video_yt_class::set_video_opt()
     * 
     * @param mixed $feed
     * @param integer $queryid
     * @return
     */
    function set_video_opt($feed, $queryid = 0) {
        #echoarr($feed);die;
        foreach ($feed as $entry) {
            list($ld, $lt) = explode('T', strval($entry->getUpdated()));
            // TAGS
            $video_tags = array();
            if (trim($this->YT['query']['v_wlutags']) != "" && is_array($entry->getVideoTags())) {
                $wlu_tags = explode(',', $this->YT['query']['vp_wlutags']);
                $video_tags = array_merge($wlu_tags, $entry->getVideoTags());
            }
            else {
                $video_tags = $entry->getVideoTags();
            }
            list($upload_date, $upload_time) = explode('T', strval($entry->mediaGroup->getUploaded()));
            #echoarr( $uploaded);die;


            $res[$entry->getVideoId()] = array(
                'yt_videoid' => trim(strval($entry->getVideoId())),
                'ytthumbnailurl' => $entry->mediaGroup->kf::thumbnail[0]->url,
                'ytthumbnailwidth' => $entry->mediaGroup->kf::thumbnail[0]->width,
                'ytthumbnailheight' => $entry->mediaGroup->kf::thumbnail[0]->height,
                'yt_videotitle' => $entry->getVideoTitle(),
                'yt_videodescription' => $entry->getVideoDescription(),
                'yt_recorded' => $entry->getVideoRecorded(),
                'yt_geolocation' => ((is_array($entry->getVideoGeoLocation())) ? implode(';', $entry->getVideoGeoLocation()) : ''),
                'yt_ratinginfo' => serialize($entry->getVideoRatingInfo()),
                'yt_viewcount' => $entry->getVideoViewCount(),
                'yt_videoduration' => $entry->getVideoDuration(),
                'yt_flashplayerurl' => $entry->getFlashPlayerUrl(),
                'yt_watchpageurl' => $entry->getVideoWatchPageUrl(),
                'yt_videocategory' => $entry->getVideoCategory(),
                'yt_updated' => strval($entry->getUpdated()),
                'yt_lastupdate' => $ld,
                'yt_upload_date' => $upload_date,
                'yt_author_username' => $entry->author[0]->name->text,
                'yt_syncby' => 'YT');
        }
        return $res;
    }

    /**
     * video_yt_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->smarty->assign('YT', $this->YT);
    }

    /**
     * video_yt_class::load_all_ytcats()
     * 
     * @return
     */
    function load_all_ytcats() {
        if (is_array($_SESSION['vp_log']['yt_cats']) && count($_SESSION['vp_log']['yt_cats']) > 1) {
            $this->YT['yt_cats'] = $_SESSION['vp_log']['yt_cats'];
        }
        else {
            $catURL = 'http://gdata.youtube.com/schemas/2007/categories.cat';
            // retrieve category list using atom: namespace
            // note: you can cache this list to improve performance,
            // as it doesn't change very often!
            $cxml = simplexml_load_file($catURL);
            $cxml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
            $categories = $cxml->xpath('//atom:category');

            // iterate over category list
            foreach ($categories as $c) {
                // for each category
                // set feed URL
                $feedURL = "http://gdata.youtube.com/feeds/api/videos/-/" . $c['term'] . "?max-results=5&orderby=viewCount";

                // read feed into SimpleXML object
                $sxml = simplexml_load_file($feedURL);

                // get summary counts from opensearch: namespace
                $counts = $sxml->children('http://a9.com/-/spec/opensearchrss/1.0/');
                $total = $counts->totalResults;
                $CATS[md5($c['term'])] = array(
                    'label' => strval($c['label']),
                    'term' => strval($c['term']),
                    'total' => strval($total));
                /*
                foreach ($sxml->entry as $entry) {
                // get nodes in media: namespace for media information
                $media = $entry->children('http://search.yahoo.com/mrss/');
                
                // get video player URL
                $attrs = $media->group->player->attributes();
                $watch = $attrs['url']; 
                
                // get <yt:duration> node for video length
                $yt = $media->children('http://gdata.youtube.com/schemas/2007');
                $attrs = $yt->duration->attributes();
                $length = $attrs['seconds']; 
                
                // get <gd:rating> node for video ratings
                $gd = $entry->children('http://schemas.google.com/g/2005'); 
                if ($gd->rating) {
                $attrs = $gd->rating->attributes();
                $rating = $attrs['average']; 
                } else {
                $rating = 0; 
                }
                
                $v= array(
                'title' => strval($media->group->title),
                'watch_url' => strval($watch),
                'length_min' => round($length/60,2),
                'rating' => strval($rating),
                'description' => strval($media->group->description)
                
                );
                echoarr($v);die;
                $CATS[md5($c['term'])][] = $v;
                }*/
            }
            $CATS = sort_db_result($CATS, 'label', SORT_ASC, SORT_STRING);
            $_SESSION['vp_log']['yt_cats'] = $CATS;
            $this->YT['yt_cats'] = $CATS;
        }
    }


}

?>