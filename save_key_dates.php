
<?php

include('inc/inc.php');

$id_client = mysql_real_escape_string($_GET['id_client']);
$yearend = mysql_real_escape_string($_POST['yearend']);
$supportend = mysql_real_escape_string($_POST['supportend']);
$first = mysql_real_escape_string($_POST['first']);
$fifth = mysql_real_escape_string($_POST['fifth']);
$ninth = mysql_real_escape_string($_POST['ninth']);
$start = mysql_real_escape_string($_POST['start']);
$accomend = mysql_real_escape_string($_POST['accomend']);

$sql = "Replace into key_dates set id_client = $id_client, yearend = '$yearend', supportend = '$supportend', first = '$first', fifth = '$fifth', ninth = '$ninth', start = '$start', accomend = '$accomend'";
lcm_query($sql);

header("Location: client_det.php?client=$id_client&tab=key_dates");
