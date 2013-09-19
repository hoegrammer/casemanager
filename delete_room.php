<?php

include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');
$id_room = mysql_real_escape_string($_GET['id_room']);
$list_pos = mysql_real_escape_string($_GET['list_pos']);
lcm_query("delete from lcm_room where id_room = $id_room");
header("Location: listrooms.php?list_pos=$list_pos");
