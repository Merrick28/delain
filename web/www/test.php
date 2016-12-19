<?php 
//fichier de test
//deuxieme
// 3
// 4
//5
//6
//7
$data = array(
			 array('name' => 'Resultat', 'valeur' => '1'),
			 array('name' => 'Détail', 'valeur' => '2')
			 );
$data2 = array(
			 array('name' => 'Resultat', 'valeur' => '3'),
			 array('name' => 'Détail', 'valeur' => '4')
			 );		
$data = array_merge($data,$data2);	 
print_r($data);
?>

