<?php

/**
 * @package    Keimeno::{IDENT}
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    {MODVERSION}
 * @since      {CREATEDATE}
 */
 

${IDENTUPPER} = new {IDENT}_admin_class();
${IDENTUPPER}->TCR->interpreter();
${IDENTUPPER}->parse_to_smarty();
${IDENTUPPER}->add_tpl($content,'{IDENT}');
?>