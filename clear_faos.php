<?php

require  'inc/inc.php';
include 'inc/DataModification.class.php';

DataModification::deleteAllFaos();

// Deals with the fact that casemanager might be on the doc root
// or might be in a directory below it.
$dir = dirname($_SERVER['PHP_SELF']);
if (strlen($dir) > 1) {
        header("Location: $dir/welfare_payments.php?tab=record", true, 303);
} else {
        header('Location: /welfare_payments.php?tab=record', true, 303);
}

