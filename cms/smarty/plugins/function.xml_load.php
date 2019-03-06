<?php

/*
* Smarty plugin
* -------------------------------------------------------------
* Trebaxa
* -------------------------------------------------------------
*/
function smarty_function_xml_load($params, &$smarty) {
    if ($params['file'] == '') {
        $smarty->trigger_error("xml_load: missing 'file' parameter");
        return;
    }
    if ($params['assign'] == '') {
        $smarty->trigger_error("xml_load: missing 'assign' parameter");
        return;
    }

    $cache_file = str_replace('//', '/', $smarty->cache_dir . '/' . md5($params['file']) . '.cache');
    if (isset($params['cache_lifetime'])) {
        $time = (int)$params['cache_lifetime'];
    }
    else {
        $time = 3600;
    }

    $cache_age = file_exists($cache_file) ? ceil((time() - filemtime($cache_file))) : 0;
    $cacheHasExpired = $time <= $cache_age;
    if ($cacheHasExpired == true || !is_file($cache_file)) {
        @unlink($cache_file);
        $xml_obj = simplexml_load_file($params['file']);
        $json = json_encode($xml_obj);
        if (!($cache = @fopen($cache_file, 'w'))) {
            $smarty->trigger_error('Could not write to cache file (<em>' . $cache_file . '</em>).  The path may be invalid or you may not have write permissions.');
            return false;
        }
        fwrite($cache, $json);
        fclose($cache);
    }
    else {
        $json = file_get_contents($cache_file);
    }


    $arr = json_decode($json, true);
    $smarty->assign($params['assign'], $arr);
}

?>