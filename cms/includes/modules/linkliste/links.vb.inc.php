<?php


# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    linkliste
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


DEFINE('IN_SIDE', 1);
DEFINE('NO_MODULES', 1);

$root = str_replace('includes/modules/linkliste', '', dirname(__file__) . '/');
include ($root . 'includes/system.corestartup.inc.php');

DEFINE('ICON_FIELD', 'attfileico');
DEFINE('ICON_PREFIX', 'LINKS_ICO');
DEFINE('TBL_CMS_LINKS', TBL_CMS_PREFIX . 'links');
DEFINE('TBL_CMS_LINKS_CATS', TBL_CMS_PREFIX . 'links_cats');
DEFINE('TBL_CMS_LINKS_TMATRIX', TBL_CMS_PREFIX . 'links_toplmatrix');
DEFINE('TBL_CMS_LINKS_TOPLSET', TBL_CMS_PREFIX . 'links_toplset');

include (CMS_ROOT . 'includes/modules/linkliste/links.class.php');

$LINKS_OBJ = new links_class();
$LINKS_OBJ->TCR->interpreterfe();
unset($LINKS_OBJ);

?>