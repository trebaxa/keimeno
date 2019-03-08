<?php




/**
 * @package    gallery
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */

# Or visit our hompage at www.trebaxa.com									  *

defined('IN_SIDE') or die('Access denied.');
$gid = 0;
if (isset($_GET['gid']))
    $gid = (int)$_GET['gid'];
$start = 0;
if (isset($_GET['start']))
    $start = (int)$_GET['start'];
$picid = 0;
if (isset($_GET['picid']))
    $picid = (int)$_GET['picid'];
$GAL_OBJ = new gal_class();
$GAL_OBJ->init_obj($GBL_LANGID, $user_object, $gid);
$GAL_OBJ->TCR->interpreterfe();

# **************************
# ******* SECURE ***********
# **************************
$protected_actions = array('delpic', 'edit');
if (in_array(isset($_REQUEST['aktion']) && $_REQUEST['aktion'], $protected_actions) &&
    CU_LOGGEDIN == false) {
    $TCMASTER->LOGCLASS->addLog('ILLEGAL', 'gallery call, aktion=' . $_REQUEST['aktion']);
    header('location:' . PATH_CMS . 'index.html');
    exit;
}
$protected_actions = array('uploadpic');
if (in_array(isset($_REQUEST['aktion']) && $_REQUEST['aktion'], $protected_actions) &&
    CU_LOGGEDIN == false && $gbl_config['gal_visitorupload'] == 0) {
    $TCMASTER->LOGCLASS->addLog('ILLEGAL', 'gallery call, aktion=' . $_REQUEST['aktion']);
    header('location:' . PATH_CMS . 'index.html');
    exit;
}


$GAL_OBJ->set_entry_point($TOPLEVEL_OBJ);
$GAL_OBJ->GID = $gid;

#*********************************
# GALLERY
#*********************************
$GAL_OBJ->load_pic_table($start);
if (isset($GAL_OBJ->GALLERY_OBJ['id']) && $GAL_OBJ->GALLERY_OBJ['id'] > 0) {
    $meta_title = $GAL_OBJ->GALLERY_OBJ['gallery_name'];
    $meta_description = $TCMASTER->gen_meta_description($GAL_OBJ->GALLERY_OBJ['GALDESC']);
    $meta_keywords = $TCMASTER->gen_meta_keywords($meta_title . ' ' . $GAL_OBJ->
        GALLERY_OBJ['GALDESC']);
}
if ($picid > 0 && $_GET['aktion'] == 'edit') {
    $GAL_OBJ->set_editor($picid);
}


#****************************************
#*************** GALLERY UPLOAD *********
#****************************************
if (isset($_POST['aktion']) && $_POST['aktion'] == "uploadpic") {
    $FORM = $_POST['FORM'];
    $FORM['pic_kid'] = (int)$user_object['kid'];
    $FORM['pic_username'] = $user_obj->user_obj['username'];
    $_POST['FORM_CON']['pic_title'] = $FORM['pic_title'];
    $_POST['FORM_CON']['pic_content'] = $FORM['pic_desc'];

    if ($_POST['FORM_CON']['pic_id'] == 0) {
        $feedback_arr = $GAL_OBJ->GALLERY_ADMIN->uploadPicAndSave($_FILES, $_POST['FORM_CON']['pic_id'],
            $FORM);
        if ($feedback_arr['msge'] == "") {
            $_POST['FORM_CON']['pic_id'] = $feedback_arr['id'];
            $GAL_OBJ->GALLERY_ADMIN->saveContent($_POST['FORM_CON'], $_POST['FORM_CON_ID']);
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_POST['page'] . '&gid=' .
                $FORM['group_id'] . '&start=' . $_POST['start'] . '&msg=' . base64_encode("{LBLA_SAVED}"));
        } else {
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_POST['page'] . '&gid=' .
                $FORM['group_id'] . '&start=' . $_POST['start'] . '&msge=' . base64_encode($feedback_arr['msge']));
        }
    } else {
        $GAL_OBJ->GALLERY_ADMIN->uploadPicAndSave($_FILES, $_POST['FORM_CON']['pic_id'],
            $FORM);
        update_table(TBL_CMS_GALPICS, 'id', $_POST['FORM_CON']['pic_id'], $FORM);
        $GAL_OBJ->GALLERY_ADMIN->saveContent($_POST['FORM_CON'], $_POST['FORM_CON_ID']);
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_POST['page'] . '&gid=' .
            $FORM['group_id'] . '&start=' . $_POST['start'] . '&msg=' . base64_encode("{LBLA_SAVED}"));
    }
    exit;
}

#****************************************
#*************** DELETE PIC *************
#****************************************
if (isset($_GET['aktion']) && $_GET['aktion'] == "delpic") {
    $PICTURE = $kdb->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " 
	WHERE id=" . (int)$_GET['picid'] . " LIMIT 1");
    if ($user_obj->user_obj['PERMOD']['gallery']['del'] == true || $user_obj->
        user_obj['kid'] == $PICTURE['pic_kid']) {
        $GAL_OBJ->GALLERY_ADMIN->deleteFotoById((int)$_GET['picid']);
    }
    #	else if ($PICTURE['id'] > 0) $GAL_OBJ->GALLERY_ADMIN->deleteFotoById($PICTURE['id']);
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&gid=' .
        $_GET['gid'] . '&start=' . $_GET['start'] . '&msg=' . base64_encode("{LBL_DELETED}"));
    exit;
}

?>