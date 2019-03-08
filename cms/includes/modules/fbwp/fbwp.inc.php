<?php


# Scripting by Trebaxa Company(R) 2010    									*

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



if (IN_SIDE != 1) {
    header('location:/index.html');
    exit;
}

$FBWP = new fbwp_class();
$FBWP->TCR->interpreterfe();
#$FBWP->init();
$FBWP->load_fb();
$FBWP->parse_to_smarty();

ECHORESULTCOMPILEDFE($FBWP->FBWP['WP']['fb_content']);

?>