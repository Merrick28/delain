<?php 
require "includes/classes.php";
$req = "select rguilde_libelle_rang,rguilde_rang_cod,rguilde_cod,rguilde_admin ";
	 		$req = $req . "from guilde_rang ";
	 		$req = $req . "where rguilde_guilde_cod = 829 ";
	 		$req = $req . "order by rguilde_cod ";
$db->query($req);
$test['O'] = "Oui";
$test['N'] = "Non";
while($db->next_record())
{
	$test_var = $db->f('rguilde_admin');
	echo "REsultat " , $test_var , " - " , $test[$test_var] , "<br>";
}
?>
