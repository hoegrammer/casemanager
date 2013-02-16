<?php
if (defined('_CONFIG_INC_CONNECT')) return;
define('_CONFIG_INC_CONNECT', '1');
$GLOBALS['lcm_connect_version'] = 0.1;
include_lcm('inc_db');
@lcm_connect_db('localhost','','root','polydisco','lcm');
$GLOBALS['db_ok'] = !!@lcm_num_rows(@lcm_query_db('SELECT COUNT(*) FROM lcm_meta'));
?>