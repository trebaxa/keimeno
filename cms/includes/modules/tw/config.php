<?php

/**
 * @package    tw
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

if (ISADMIN != 1) {
    #define('CONSUMER_KEY', $user_object['tw_consumerkey']);
    #define('CONSUMER_SECRET', $user_object['tw_consumersecret']);
    define('OAUTH_CALLBACK', 'http://www.' . FM_DOMAIN . '/includes/modules/tw/twcallback.php');
}
else {
    #	define('CONSUMER_KEY', $gbl_config['tw_consumerkey']);
    #	define('CONSUMER_SECRET', $gbl_config['tw_consumersecret']);
    define('OAUTH_CALLBACK', 'http://www.' . FM_DOMAIN . '/includes/modules/tw/twcallbackadmin.php');

}

?>