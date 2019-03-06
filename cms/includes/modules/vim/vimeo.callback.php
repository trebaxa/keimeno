<?php

/**
 * @package    vim
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


DEFINE('IN_SIDE', 1);
DEFINE('NO_MODULES', 1);
DEFINE('ISADMIN', 1);

$root = str_replace('includes/modules/vim', '', dirname(__FILE__) . '/');
include ($root . 'includes/system.corestartup.inc.php');


include ('../../session.inc.php');
require '../../../class_db_zugriff.php';
require '../../settings.php';
include (SYSTEM_ROOT . 'includes/config.php');
require_once ('vimeocom.class.php');

// Create the object and enable caching
$vimeo = new phpVimeoAPI($gbl_config['vm_consumerkey'], $gbl_config['vm_secret']);
$vimeo->enableCache(phpVimeoAPI::CACHE_FILE, CMS_ROOT . 'cache', 300);

// Clear session
if ($_GET['clear'] == 'all') {

    session_destroy();
    session_start();
}

// Set up variables
$state = $_SESSION['vimeo_state'];
$request_token = $_SESSION['oauth_request_token'];
$access_token = $_SESSION['oauth_access_token'];

// Coming back
if ($_REQUEST['oauth_token'] != NULL && $_SESSION['vimeo_state'] === 'start') {
    $_SESSION['vimeo_state'] = $state = 'returned';
}

// If we have an access token, set it
if ($_SESSION['oauth_access_token'] != null) {
    $vimeo->setToken($_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);
}

switch ($_SESSION['vimeo_state']) {
        /*    default:

        // Get a new request token
        $token = $vimeo->getRequestToken('http://cms.trebaxa.com/includes/modules/wilinku/mods/wlu_collector/wlu_vcallback.php');

        // Store it in the session
        $_SESSION['oauth_request_token'] = $token['oauth_token'];
        $_SESSION['oauth_request_token_secret'] = $token['oauth_token_secret'];
        $_SESSION['vimeo_state'] = 'start';

        // Build authorize link
        $authorize_link = $vimeo->getAuthorizeUrl($token['oauth_token'], 'write');
        echo 'B';
        break;
        */

    case 'returned':

        // Store it
        if ($_SESSION['oauth_access_token'] === NULL && $_SESSION['oauth_access_token_secret'] === NULL) {
            // Exchange for an access token
            $vimeo->setToken($_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);
            $token = $vimeo->getAccessToken($_REQUEST['oauth_verifier']);

            // Store
            $_SESSION['oauth_access_token'] = $token['oauth_token'];
            $_SESSION['oauth_access_token_secret'] = $token['oauth_token_secret'];
            $_SESSION['vimeo_state'] = 'done';
            // Set the token
            $vimeo->setToken($_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);
            $kdb->query("UPDATE " . TBL_CMS_GBLCONFIG . "  SET config_value='" . ($token['oauth_token']) . "' 
            	WHERE config_name='vm_oauth_token'");
            $kdb->query("UPDATE " . TBL_CMS_GBLCONFIG . "  SET config_value='" . ($token['oauth_token_secret']) . "' 
            	WHERE config_name='vm_oauth_token_secret'");
        }

        // Do an authenticated call
        try {
            # $videos = $vimeo->call('vimeo.videos.getUploaded');
            # echo 'state: ' . $_SESSION['vimeo_state'];
            header('Location: /admin/run.php?epage=vimeo.inc&msg=' . base64_encode('application authenticated.'));
            exit;
        }
        catch (VimeoAPIExceptClass $e) {
            echo "Encountered an API error -- code {$e->getCode()} - {$e->getMessage()}";
        }

        break;
}

?>

