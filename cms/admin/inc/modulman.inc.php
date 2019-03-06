<?PHP

include (CMS_ROOT . 'admin/inc/modulman.class.php');

$MODMAN = new moduleman_class();
$MODMAN->gen_all_mod_xml();
$MODMAN->load_mods();
$MODMAN->TCR->interpreter();

$menu = array("Installierte Apps" => "", "App Database" => "cmd=load_pool&section=pool");
$ADMINOBJ->set_top_menu($menu);

$MODMAN->parse_to_smarty();

$ADMINOBJ->inc_tpl('modman');

?>