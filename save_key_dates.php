
<?php

include('inc/inc.php');

$id_client = mysql_real_escape_string($_GET['id_client']);
$yearend = $_GET['yearend'] ? date('Y-m-d', strtotime($_GET['yearend'])) : '';
$supportend = $_GET['supportend'] ?  date('Y-m-d', strtotime($_GET['supportend'])) : '';
$first = $_GET['first']? date('Y-m-d', strtotime($_GET['first'])) : '';
$fifth = $_GET['fifth']? date('Y-m-d', strtotime($_GET['fifth'])) : '';
$ninth = $_GET['ninth']? date('Y-m-d', strtotime($_GET['ninth'])) : '';
$start = $_GET['start']? date('Y-m-d', strtotime($_GET['start'])) : '';
$accomend = $_GET['accomend']? date('Y-m-d', strtotime($_GET['accomend'])) : '';

$sql = "Replace into key_dates set id_client = $id_client, yearend = '$yearend', supportend = '$supportend', first = '$first', fifth = '$fifth', ninth = '$ninth', start = '$start', accomend = '$accomend'";
lcm_query($sql);
//echo $sql; die();
header("Location: client_det.php?client=$id_client&tab=key_dates");
