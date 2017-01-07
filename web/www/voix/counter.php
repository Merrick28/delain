<?php 
include "../includes/classes.php";
$db = new base_delain;
$req = "UPDATE hits_voix SET counter=counter + 1 WHERE page = '$PHP_SELF'";
$db->query($req);
if($db->affected_rows() == 0)
	$db->query("INSERT INTO hits_voix VALUES ('$PHP_SELF', 1)");
?>