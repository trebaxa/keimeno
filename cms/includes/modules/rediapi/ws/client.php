<?php

require ('./classes/ws_clientconfig.class.php');
require ('./classes/ws_arraytoxml.class.php');
require ('./classes/ws_client.class.php');

$ws_config = new ws_clientconfig_class();
$client = new ws_client();
$client->connect($ws_config);
$client->interpreter();
unset($client);
unset($ws_config);

?>