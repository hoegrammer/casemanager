<?php
include('inc/inc.php');
include_lcm('inc_acc');
include_lcm('inc_filters');

$cc = $_POST['cc'];
$colour = $_POST['colour'];
$description = $_POST['description'];
$ref = $_POST['ref'];
$q = 'INSERT INTO lcm_app SET 
		id_author = ' .	($GLOBALS['author_session']['id_author']) . ',
		title = "' . $cc . '", 
		colour = "' . $colour  . '", 
		description = "' . $description . '",
		date_creation = NOW()
		';
lcm_query($q);
$id_app = lcm_insert_id('lcm_app', 'id_app');
lcm_query("INSERT INTO lcm_author_app SET id_app=$id_app,id_author=" . $GLOBALS['author_session']['id_author']);

lcm_header("Location: ".$ref);
?>
