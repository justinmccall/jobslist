<?php

require 'db.php';

$insert = "INSERT INTO jobs(position,company,location,apply_url,date,jobUrl,source,easy_apply,status,work_type,sector,updatedDate) VALUES(
	'".$_POST['job_title']."',
	'".$_POST['company']."',
	'".$_POST['location']."',
	'".$_POST['apply_url']."',
	'".time()."',
	'".$_POST['job_url']."',
	'".$_POST['source']."',
	'0',
	'applied',
	'-',
	'-',
	'".time()."'
)";
$mysqli->query($insert);

// echo "Status Updated";

?>