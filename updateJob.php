<?php

require 'db.php';

$update = "UPDATE jobs SET status = '".$_GET['status']."', updatedDate = '".time()."' WHERE id = '".$_GET['id']."'";
$mysqli->query($update);

// echo "Status Updated";

?>