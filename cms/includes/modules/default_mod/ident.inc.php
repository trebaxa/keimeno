<?php

/**
 * @package    Keimeno::{IDENT}
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    {MODVERSION}
 * @since      {CREATEDATE}
 */
 
defined( 'IN_SIDE' ) or die( 'Access denied.' );

${IDENTUPPER} = new {IDENT}_class();
${IDENTUPPER}->TCR->interpreter();
${IDENTUPPER}->parse_to_smarty();
