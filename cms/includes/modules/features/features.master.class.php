<?PHP
/**
 * @package    features
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

DEFINE('TBL_CMS_FEATURES', TBL_CMS_PREFIX . 'features');
DEFINE('TBL_CMS_FEATUREGROUPS', TBL_CMS_PREFIX . 'features_groups');

class features_master_class extends modules_class
{

    /**
     * features_master_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
    }


}

?>